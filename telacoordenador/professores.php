<?php
session_start();

$sName = "localhost";
$uNname = "root";
$pass = "";
$db_name = "escolabd";

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uNname, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

$idCoordinator = $_SESSION['id'] ?? null;

if (!$idCoordinator) {
    die("Coordenador não identificado.");
}

try {
    $stmt = $conn->prepare("SELECT fname, lname FROM coordenadores WHERE id = :id");
    $stmt->bindParam(':id', $idCoordinator, PDO::PARAM_INT);
    $stmt->execute();
    $coordinator = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$coordinator) {
        die("Coordenador não encontrado.");
    }
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit;
}

// Processar formulário se for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_professor'])) {
    // Coleta e sanitização dos dados
    $fname = htmlspecialchars(trim($_POST['fname']));
    $lname = htmlspecialchars(trim($_POST['lname']));
    $genero = $_POST['genero'] ?? '';
    $data_nascimento = $_POST['dataa'];
    $numbi = $_POST['numbi'];
    $endereco = htmlspecialchars(trim($_POST['endereco']));
    $telefone = $_POST['telefone'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $disciplina = $_POST['disciplina'] ?? null;
    $nivel_academico = $_POST['nivel_academico'] ?? null;
    $status = $_POST['status'] ?? 'ativo';

    // Validação simples do status
    if (!in_array($status, ['ativo', 'inativo'])) {
        $status = 'ativo';
    }

    // Validações
    if (empty($fname) || empty($lname) || empty($genero) || empty($data_nascimento) || empty($numbi) || empty($endereco) || empty($telefone) || empty($email) || empty($area) || empty($disciplina) || empty($nivel_academico)) {
        $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
        header("Location: professores.php");
        exit;
    }

    if (!preg_match('/^\d{7}[A-Za-z]{2}\d{3}$/', $numbi)) {
        $_SESSION['error'] = "Formato do BI inválido. Use o formato: 0000000LA000";
        header("Location: professores.php");
        exit;
    }

    if (!preg_match('/^\d{9}$/', $telefone)) {
        $_SESSION['error'] = "Número de telefone inválido. Use 9 dígitos.";
        header("Location: professores.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Email inválido.";
        header("Location: professores.php");
        exit;
    }

    // Upload de arquivos
    if (!isset($_FILES['foto_bi1'], $_FILES['foto_bi2'], $_FILES['fotoperfil'])) {
        $_SESSION['error'] = "Faltam arquivos para upload.";
        header("Location: professores.php");
        exit;
    }

    function uploadFile($file, $targetDir = "uploads/", $allowedTypes = ['jpg', 'jpeg', 'png']) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedTypes)) {
            return false;
        }

        $uniqueName = uniqid('file_', true) . '.' . $fileExtension;
        $destination = $targetDir . $uniqueName;

        return move_uploaded_file($file['tmp_name'], $destination) ? $destination : false;
    }

    $uploadDir = "uploads/teachers/";
    $foto_bi1 = uploadFile($_FILES['foto_bi1'], $uploadDir);
    $foto_bi2 = uploadFile($_FILES['foto_bi2'], $uploadDir);
    $fotoperfil = uploadFile($_FILES['fotoperfil'], $uploadDir);

    if (!$foto_bi1 || !$foto_bi2 || !$fotoperfil) {
        $_SESSION['error'] = "Erro ao carregar as imagens. Use formatos JPG, JPEG ou PNG.";
        header("Location: professores.php");
        exit;
    }

    // Inserção no banco de dados (tabela professores)
    $sql = "INSERT INTO professores (
        fname, lname, genero, data_nascimento, num_bi, foto_bi1, foto_bi2, endereco,
        fotoperfil, telefone, email, disciplina, nivel_academico, status, password
    ) VALUES (
        :fname, :lname, :genero, :data_nascimento, :numbi, :foto_bi1, :foto_bi2, :endereco,
        :fotoperfil, :telefone, :email, :disciplina, :nivel_academico, :status, :password
    )";

    // Gerar senha padrão (data de nascimento no formato ddmmyyyy)
    $password = date('dmY', strtotime($data_nascimento));
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':genero', $genero);
    $stmt->bindParam(':data_nascimento', $data_nascimento);
    $stmt->bindParam(':numbi', $numbi);
    $stmt->bindParam(':foto_bi1', $foto_bi1);
    $stmt->bindParam(':foto_bi2', $foto_bi2);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':fotoperfil', $fotoperfil);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':disciplina', $disciplina);
    $stmt->bindParam(':nivel_academico', $nivel_academico);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':password', $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Professor adicionado com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao adicionar professor.";
    }
    
    header("Location: professores.php");
    exit;
}

// Buscar listagem de professores
try {
    $professoresStmt = $conn->prepare("
        SELECT 
            p.id, p.fname, p.lname, p.genero, p.data_nascimento, p.num_bi, p.endereco, 
            p.telefone, p.email, p.status, p.nivel_academico,
            (SELECT COUNT(*) FROM turma t WHERE t.class_director_id = p.id) AS num_turmas
        FROM professores p
        ORDER BY p.lname, p.fname
    ");
    $professoresStmt->execute();
    $professores = $professoresStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $professores = [];
    $_SESSION['error'] = "Erro ao buscar professores: " . $e->getMessage();
}

$dataAtual = '15 de Abril de 2025';
$trimestre = '2º trimestre';
$anoLetivo = '2025';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professores - Dashboard de Coordenadores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Adicione isso na seção de estilos */
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .alert .material-symbols-outlined {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Reset e Estilos Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Main Content */
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .top-bar-actions .material-symbols-outlined {
            cursor: pointer;
            color: #7f8c8d;
            transition: all 0.3s;
        }

        .top-bar-actions .material-symbols-outlined:hover {
            color: #3498db;
        }

        .notification {
            position: relative;
        }

        .notification::after {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background-color: #e74c3c;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        /* Dashboard Content */
        .dashboard-content {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            background-color: #f5f7fa;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Botões */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-1px);
        }

        .btn-primary .material-symbols-outlined {
            margin-right: 8px;
            font-size: 1.2rem;
        }

        /* Filtros */
        .filter-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .filter-group label {
            margin-bottom: 5px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #7f8c8d;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
            background-color: #fff;
            transition: all 0.3s;
        }

        .filter-select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        /* Tabela de Professores */
        .teachers-table-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .teachers-table {
            width: 100%;
            border-collapse: collapse;
        }

        .teachers-table thead {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }

        .teachers-table th {
            padding: 15px;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 600;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .teachers-table tbody tr {
            border-bottom: 1px solid #eee;
            transition: all 0.3s;
        }

        .teachers-table tbody tr:last-child {
            border-bottom: none;
        }

        .teachers-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .teachers-table td {
            padding: 15px;
            font-size: 0.9rem;
            color: #333;
        }

        /* Informações do Professor */
        .teacher-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .teacher-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #eee;
        }

        .teacher-name {
            font-weight: 500;
            color: #2c3e50;
        }

        .teacher-id {
            font-size: 0.8rem;
            color: #7f8c8d;
        }

        /* Status */
        .teacher-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-inactive {
            background-color: #fff3e0;
            color: #e65100;
        }

        /* Tooltip */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 180px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.8rem;
            font-weight: normal;
            text-transform: none;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        /* Botões de Ação */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 50%;
            background-color: transparent;
            color: #7f8c8d;
            cursor: pointer;
            transition: all 0.3s;
            margin-right: 5px;
        }

        .action-btn:hover {
            background-color: #f0f0f0;
            color: #3498db;
        }

        .action-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .action-btn:disabled:hover {
            background-color: transparent;
            color: #7f8c8d;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            width: 800px;
            max-height: 90vh;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #7f8c8d;
            cursor: pointer;
            transition: all 0.3s;
        }

        .close-btn:hover {
            color: #e74c3c;
        }

        .modal-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        /* Formulário */
        .form-section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .form-section h3 {
            margin-bottom: 15px;
            font-size: 1.1rem;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #7f8c8d;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .file-input-container {
            margin-bottom: 15px;
        }

        .file-preview {
            margin-top: 10px;
            width: 100%;
            height: 120px;
            border: 1px dashed #ddd;
            border-radius: 6px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .file-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary {
            background-color: #f0f0f0;
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
        }

        .btn-success {
            background-color: #2ecc71;
            color: white;
        }

        .btn-success:hover {
            background-color: #27ae60;
        }

        /* Responsividade */
        @media (max-width: 1200px) {
            .sidebar {
                width: 250px;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .menu-text, .profile-info h3, .sidebar-header h2 {
                display: none;
            }
            
            .menu li a {
                justify-content: center;
            }
            
            .menu li a .material-symbols-outlined {
                margin-right: 0;
                font-size: 1.6rem;
            }
            
            .sidebar-footer a {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 10px;
            }
            
            .modal-content {
                width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Pitruca Camama</h2>
            </div>
            <div class="profile">
                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($coordinator['fname'] . ' ' . $coordinator['lname']); ?></h3>
                    <p>Coordenador/a</p>
                </div>
            </div>
            <nav class="menu">
                <ul>
                    <li>
                        <a href="index.php">
                            <span class="material-symbols-outlined">dashboard</span>
                            <span class="menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="alunos.php">
                            <span class="material-symbols-outlined">group</span>
                            <span class="menu-text">Alunos</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="professores.php">
                            <span class="material-symbols-outlined">group</span>
                            <span class="menu-text">Professores</span>
                        </a>
                    </li>
                    <li>
                        <a href="turmas.php">
                            <span class="material-symbols-outlined">school</span>
                            <span class="menu-text">Turmas</span>
                        </a>
                    </li>
                    <li>
                        <a href="notas.php">
                            <span class="material-symbols-outlined">grade</span>
                            <span class="menu-text">Notas</span>
                        </a>
                    </li>
                    <li>
                        <a href="presenca.php">
                            <span class="material-symbols-outlined">fact_check</span>
                            <span class="menu-text">Presença</span>
                        </a>
                    </li>
                    <li>
                        <a href="calendario.php">
                            <span class="material-symbols-outlined">calendar_month</span>
                            <span class="menu-text">Calendário</span>
                        </a>
                    </li>
                    <li>
                        <a href="materiais.php">
                            <span class="material-symbols-outlined">book</span>
                            <span class="menu-text">Materiais</span>
                        </a>
                    </li>
                    <li>
                        <a href="mensagens.php">
                            <span class="material-symbols-outlined">chat</span>
                            <span class="menu-text">Mensagens</span>
                        </a>
                    </li>
                    <li>
                        <a href="boletins.php">
                            <span class="material-symbols-outlined">description</span>
                            <span class="menu-text">Boletins</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="minipauta.php">
                            <span class="material-symbols-outlined">summarize</span>
                            <span class="menu-text">Minipautas</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <!--
                <a href="configuracoes.php">
                    <span class="material-symbols-outlined">settings</span>
                    <span class="menu-text">Configurações</span>
                </a>
    -->
                <a href="logout.php" class="logout">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="menu-text">Sair</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <header class="top-bar">
                <div class="search-container">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" placeholder="Pesquisar professores...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Professores</h1>
                    <button id="addTeacherBtn" class="btn-primary">
                        <span class="material-symbols-outlined">add</span>
                        Adicionar Professor
                    </button>
                </div>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <span class="material-symbols-outlined">error</span>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <span class="material-symbols-outlined">check_circle</span>
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="filter-container">
                    <div class="filter-group">
                        <label for="area-filter">Área:</label>
                        <select id="area-filter" class="filter-select">
                            <option value="todos">Todas as Áreas</option>
                            <option value="I Ciclo">I Ciclo</option>
                            <option value="II Ciclo">II Ciclo</option>
                            <option value="Curso PUNIV">Curso PUNIV</option>
                            <option value="Curso Técnico">Curso Técnico</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="disciplina-filter">Disciplina:</label>
                        <select id="disciplina-filter" class="filter-select">
                            <option value="todos">Todas Disciplinas</option>
                            <option value="Matemática">Matemática</option>
                            <option value="Português">Português</option>
                            <option value="História">História</option>
                            <option value="Geografia">Geografia</option>
                            <option value="Física">Física</option>
                            <option value="Química">Química</option>
                            <option value="Biologia">Biologia</option>
                            <option value="Inglês">Inglês</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="status-filter">Status:</label>
                        <select id="status-filter" class="filter-select">
                            <option value="todos">Todos</option>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                </div>

                <div class="teachers-table-container">
                    <table class="teachers-table">
                        <thead>
                            <tr>
                                <th>Professor</th>
                                <th>Número BI</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody data-dynamic="teachers-container">
                            <?php foreach ($professores as $professor): ?>
                            <tr>
                                <td>
                                    <div class="teacher-info">
                                        <img src="<?php echo htmlspecialchars($professor['fotoperfil'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($professor['fname'] . '+' . $professor['lname']) . '&background=random'); ?>" class="teacher-avatar" alt="<?php echo htmlspecialchars($professor['fname'] . ' ' . $professor['lname']); ?>">
                                        <div>
                                            <div class="teacher-name"><?php echo htmlspecialchars($professor['fname'] . ' ' . $professor['lname']); ?></div>
                                            <div class="teacher-id"><?php echo htmlspecialchars($professor['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($professor['num_bi']); ?></td>
                                <td>
                                    <button class="action-btn" title="Editar">
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                    <button class="action-btn" title="Visualizar">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    <button class="action-btn" title="Remover">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para adicionar professor -->
    <div id="addTeacherModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Adicionar Novo Professor</h3>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="professores.php" enctype="multipart/form-data">
                    <!-- Informações Pessoais -->
                    <div class="form-section">
                        <h3>Informações Pessoais</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fname">Nome*</label>
                                <input type="text" id="fname" name="fname" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="lname">Sobrenome*</label>
                                <input type="text" id="lname" name="lname" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="dataa">Data de Nascimento*</label>
                                <input type="date" id="dataa" name="dataa" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="genero">Gênero*</label>
                                <select id="genero" name="genero" class="form-control" required>
                                    <option value="">Selecione</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Feminino</option>
                                    <option value="O">Outro</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="numbi">Número de BI*</label>
                            <input type="text" id="numbi" name="numbi" class="form-control" required placeholder="Formato: 0000000LA000">
                        </div>

                        <div class="form-row">
                            <div class="file-input-container">
                                <label for="foto_bi1">Foto do BI (Frente)*</label>
                                <input type="file" id="foto_bi1" name="foto_bi1" class="form-control" accept="image/*" required>
                                <div class="file-preview" id="foto_bi1-preview"></div>
                            </div>
                            <div class="file-input-container">
                                <label for="foto_bi2">Foto do BI (Verso)*</label>
                                <input type="file" id="foto_bi2" name="foto_bi2" class="form-control" accept="image/*" required>
                                <div class="file-preview" id="foto_bi2-preview"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Contato e Endereço -->
                    <div class="form-section">
                        <h3>Contato e Endereço</h3>
                        <div class="form-group">
                            <label for="endereco">Endereço Completo*</label>
                                                        <input type="text" id="endereco" name="endereco" class="form-control" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefone">Telefone*</label>
                                <input type="tel" id="telefone" name="telefone" class="form-control" required placeholder="9 dígitos">
                            </div>
                            <div class="form-group">
                                <label for="email">Email*</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <!-- Informações Acadêmicas -->
                    <div class="form-section">
                        <h3>Informações Acadêmicas</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="area">Área de Ensino*</label>
                                <select id="area" name="area" class="form-control" required>
                                    <option value="">Selecione a Área</option>
                                    <option value="I Ciclo">I Ciclo</option>
                                    <option value="II Ciclo">II Ciclo</option>
                                    <option value="Curso PUNIV">Curso PUNIV</option>
                                    <option value="Curso Técnico">Curso Técnico</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="disciplina">Disciplina Principal*</label>
                                <select id="disciplina" name="disciplina" class="form-control" required>
                                    <option value="">Selecione a Disciplina</option>
                                    <option value="Matemática">Matemática</option>
                                    <option value="Português">Português</option>
                                    <option value="História">História</option>
                                    <option value="Geografia">Geografia</option>
                                    <option value="Física">Física</option>
                                    <option value="Química">Química</option>
                                    <option value="Biologia">Biologia</option>
                                    <option value="Inglês">Inglês</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nivel_academico">Nível Acadêmico*</label>
                            <select id="nivel_academico" name="nivel_academico" class="form-control" required>
                                <option value="">Selecione o Nível</option>
                                <option value="Licenciatura">Licenciatura</option>
                                <option value="Bacharelado">Bacharelado</option>
                                <option value="Mestrado">Mestrado</option>
                                <option value="Doutorado">Doutorado</option>
                            </select>
                        </div>
                    </div>

                    <!-- Foto de Perfil -->
                    <div class="form-section">
                        <h3>Foto de Perfil</h3>
                        <div class="file-input-container">
                            <label for="fotoperfil">Foto*</label>
                            <input type="file" id="fotoperfil" name="fotoperfil" class="form-control" accept="image/*" required>
                            <div class="file-preview" id="fotoperfil-preview"></div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-section">
                        <h3>Status</h3>
                        <div class="form-group">
                            <label for="status">Status do Professor*</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="ativo" selected>Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary close-btn">Cancelar</button>
                        <button type="submit" name="adicionar_professor" class="btn btn-success">Adicionar Professor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Scripts para manipulação do modal e preview de imagens
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('addTeacherModal');
            const openBtn = document.getElementById('addTeacherBtn');
            const closeBtns = document.querySelectorAll('.close-btn');
            
            // Abrir modal
            openBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
            });
            
            // Fechar modal
            closeBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    modal.style.display = 'none';
                });
            });
            
            // Fechar ao clicar fora do modal
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Preview de imagens
            function setupImagePreview(inputId, previewId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
            
            setupImagePreview('foto_bi1', 'foto_bi1-preview');
            setupImagePreview('foto_bi2', 'foto_bi2-preview');
            setupImagePreview('fotoperfil', 'fotoperfil-preview');
            
            // Validação do formulário
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const numbi = document.getElementById('numbi').value;
                const telefone = document.getElementById('telefone').value;
                
                // Validar formato do BI
                if (!/^\d{7}[A-Za-z]{2}\d{3}$/.test(numbi)) {
                    alert('Formato do BI inválido. Use o formato: 0000000LA000');
                    e.preventDefault();
                    return;
                }
                
                // Validar telefone
                if (!/^\d{9}$/.test(telefone)) {
                    alert('Número de telefone inválido. Use 9 dígitos.');
                    e.preventDefault();
                    return;
                }
            });
        });
    </script>
</body>
</html>