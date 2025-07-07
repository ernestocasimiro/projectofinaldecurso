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

// Buscar encarregados
$sqlEncarregados = "SELECT id, fname, lname FROM encarregados ORDER BY fname";
$stmtEncarregados = $conn->query($sqlEncarregados);
$encarregados = $stmtEncarregados->fetchAll(PDO::FETCH_ASSOC);

// Processar formulário se for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_aluno'])) {
    // Coleta e sanitização dos dados
    $fname = htmlspecialchars(trim($_POST['fname']));
    $lname = htmlspecialchars(trim($_POST['lname']));
    $genero = $_POST['genero'] ?? '';
    $data_nascimento = $_POST['dataa'];
    $numbi = $_POST['numbi'];
    $endereco = htmlspecialchars(trim($_POST['endereco']));
    $telefone = $_POST['telefone'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $encarregado_id = $_POST['encarregado'] ?? null;
    $area = $_POST['area'] ?? null;
    $status = $_POST['status'] ?? 'ativo';

    // Validação simples do status
    if (!in_array($status, ['ativo', 'inativo'])) {
        $status = 'ativo';
    }

    // Validações
    if (empty($fname) || empty($lname) || empty($genero) || empty($data_nascimento) || empty($numbi) || empty($endereco) || empty($telefone) || empty($email) || empty($encarregado_id) || empty($area)) {
        $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
        header("Location: alunos.php");
        exit;
    }

    if (!preg_match('/^\d{7}[A-Za-z]{2}\d{3}$/', $numbi)) {
        $_SESSION['error'] = "Formato do BI inválido. Use o formato: 0000000LA000";
        header("Location: alunos.php");
        exit;
    }

    if (!preg_match('/^\d{9}$/', $telefone)) {
        $_SESSION['error'] = "Número de telefone inválido. Use 9 dígitos.";
        header("Location: alunos.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Email inválido.";
        header("Location: alunos.php");
        exit;
    }

    // Upload de arquivos
    if (!isset($_FILES['foto_bi1'], $_FILES['foto_bi2'], $_FILES['fotoperfil'])) {
        $_SESSION['error'] = "Faltam arquivos para upload.";
        header("Location: alunos.php");
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

    $uploadDir = "uploads/students/";
    $foto_bi1 = uploadFile($_FILES['foto_bi1'], $uploadDir);
    $foto_bi2 = uploadFile($_FILES['foto_bi2'], $uploadDir);
    $fotoperfil = uploadFile($_FILES['fotoperfil'], $uploadDir);

    if (!$foto_bi1 || !$foto_bi2 || !$fotoperfil) {
        $_SESSION['error'] = "Erro ao carregar as imagens. Use formatos JPG, JPEG ou PNG.";
        header("Location: alunos.php");
        exit;
    }

    // Inserção no banco de dados
    $sql = "INSERT INTO estudantes (
        fname, lname, genero, data_nascimento, num_bi, foto_bi1, foto_bi2, endereco,
        fotoperfil, telefone, email, encarregado_id, area, status, password
    ) VALUES (
        :fname, :lname, :genero, :data_nascimento, :numbi, :foto_bi1, :foto_bi2, :endereco,
        :fotoperfil, :telefone, :email, :encarregado_id, :area, :status, :password
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
    $stmt->bindParam(':encarregado_id', $encarregado_id);
    $stmt->bindParam(':area', $area);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':password', $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Aluno adicionado com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao adicionar aluno.";
    }
    
    header("Location: alunos.php");
    exit;
}

// Buscar listagem de alunos com verificação de status de pagamento
try {
    $alunosStmt = $conn->prepare("
        SELECT 
            e.id AS estudante_id, e.fname, e.lname, e.genero, e.data_nascimento, e.num_bi, e.endereco, 
            e.telefone, e.email, e.status, e.area,
            en.fname AS encarregado_fname, en.lname AS encarregado_lname,
            (SELECT COUNT(*) FROM mensalidades m 
             WHERE m.estudante_id = e.id AND m.status = 'vencido' AND m.data_vencimento < CURDATE()) AS pagamentos_vencidos,
            (SELECT GROUP_CONCAT(m.id SEPARATOR ', ') FROM mensalidades m 
             WHERE m.estudante_id = e.id AND m.status = 'vencido' AND m.data_vencimento < CURDATE()) AS ids_mensalidades_vencidas
        FROM estudantes e
        LEFT JOIN encarregados en ON e.encarregado_id = en.id
        ORDER BY e.lname, e.fname
    ");
    $alunosStmt->execute();
    $alunos = $alunosStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Adicionar status de bloqueio por pagamento vencido
    foreach ($alunos as &$aluno) {
        $aluno['bloqueado'] = $aluno['pagamentos_vencidos'] > 0;
    }
    unset($aluno); // Quebra a referência
    
} catch (PDOException $e) {
    $alunos = [];
    $_SESSION['error'] = "Erro ao buscar alunos: " . $e->getMessage();
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
    <title>Alunos - Dashboard de Professores</title>
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

        /* Tabela de Alunos */
        .students-table-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
        }

        .students-table thead {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }

        .students-table th {
            padding: 15px;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 600;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .students-table tbody tr {
            border-bottom: 1px solid #eee;
            transition: all 0.3s;
        }

        .students-table tbody tr:last-child {
            border-bottom: none;
        }

        .students-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .students-table tbody tr.aluno-bloqueado {
            background-color: #fff9f9;
        }

        .students-table tbody tr.aluno-bloqueado:hover {
            background-color: #fff0f0;
        }

        .students-table td {
            padding: 15px;
            font-size: 0.9rem;
            color: #333;
        }

        /* Informações do Aluno */
        .student-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #eee;
        }

        .student-name {
            font-weight: 500;
            color: #2c3e50;
        }

        .student-id {
            font-size: 0.8rem;
            color: #7f8c8d;
        }

        /* Status */
        .student-status {
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

        .status-blocked {
            background-color: #ffebee;
            color: #c62828;
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
                    <li class="active">
                        <a href="alunos.php">
                            <span class="material-symbols-outlined">group</span>
                            <span class="menu-text">Alunos</span>
                        </a>
                    </li>
                    <li>
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
                    <input type="text" placeholder="Pesquisar alunos...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Alunos</h1>
                    <button id="addStudentBtn" class="btn-primary">
                        <span class="material-symbols-outlined">add</span>
                        Adicionar Aluno
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
                        <label for="status-filter">Status:</label>
                        <select id="status-filter" class="filter-select">
                            <option value="todos">Todos</option>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                            <option value="bloqueado">Bloqueado</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="order-filter">Ordenar por:</label>
                        <select id="order-filter" class="filter-select">
                            <option value="nome">Nome A-Z</option>
                            <option value="area">Área</option>
                            <option value="numero">Número</option>
                        </select>
                    </div>
                </div>

                <div class="students-table-container">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Número BI</th>
                                <th>Área</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody data-dynamic="students-container">
                            <?php foreach ($alunos as $aluno): ?>
                            <tr class="<?php echo $aluno['bloqueado'] ? 'aluno-bloqueado' : ''; ?>">
                                <td>
                                    <div class="student-info">
                                        <img src="<?php echo htmlspecialchars($aluno['fotoperfil'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($aluno['fname'] . '+' . $aluno['lname']) . '&background=random'); ?>" class="student-avatar" alt="<?php echo htmlspecialchars($aluno['fname'] . ' ' . $aluno['lname']); ?>">
                                        <div>
                                            <div class="student-name"><?php echo htmlspecialchars($aluno['fname'] . ' ' . $aluno['lname']); ?></div>
                                            <div class="student-id"><?php echo htmlspecialchars($aluno['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($aluno['num_bi']); ?></td>
                                <td><?php echo htmlspecialchars($aluno['area']); ?></td>
                                <td>
                                    <?php if ($aluno['bloqueado']): ?>
                                        <div class="tooltip">
                                            <span class="student-status status-blocked">
                                                Bloqueado
                                            </span>
                                            <span class="tooltiptext">Pagamento não efetuado</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="student-status <?php echo $aluno['status'] === 'ativo' ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo htmlspecialchars(ucfirst($aluno['status'])); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="action-btn" title="Editar" <?php echo $aluno['bloqueado'] ? 'disabled' : ''; ?>>
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                    <button class="action-btn" title="Visualizar">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    <button class="action-btn" title="Remover" <?php echo $aluno['bloqueado'] ? 'disabled' : ''; ?>>
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

    <!-- Modal para adicionar aluno -->
    <div id="addStudentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Adicionar Novo Aluno</h3>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="alunos.php" enctype="multipart/form-data">
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
                                <input type="tel" id="telefone" name="telefone" class="form-control" required pattern="\d{9}" placeholder="9 dígitos">
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
                                <label for="area">Área*</label>
                                <select id="area" name="area" class="form-control" required>
                                    <option value="">Selecione uma área</option>
                                    <option value="I Ciclo">I Ciclo</option>
                                    <option value="II Ciclo">II Ciclo</option>
                                    <option value="Curso PUNIV">Curso PUNIV</option>
                                    <option value="Curso Técnico">Curso Técnico</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Estado*</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                </select>
                            </div>
                        </div>

                        <div class="file-input-container">
                            <label for="fotoperfil">Foto do Aluno*</label>
                            <input type="file" id="fotoperfil" name="fotoperfil" class="form-control" accept="image/*" required>
                            <div class="file-preview" id="fotoperfil-preview"></div>
                        </div>
                    </div>

                    <!-- Encarregado -->
                    <div class="form-section">
                        <h3>Encarregado de Educação</h3>
                        <div class="form-group">
                            <label for="encarregado">Encarregado*</label>
                            <select id="encarregado" name="encarregado" class="form-control" required>
                                <option value="">Selecione um encarregado</option>
                                <?php foreach ($encarregados as $encarregado): ?>
                                    <option value="<?php echo $encarregado['id']; ?>">
                                        <?php echo htmlspecialchars($encarregado['fname'] . ' ' . $encarregado['lname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="form-actions">
                        <button type="reset" class="btn btn-secondary">
                            Limpar
                        </button>
                        <button type="submit" name="adicionar_aluno" class="btn btn-success">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Script para controlar o modal
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('addStudentModal');
            const addBtn = document.getElementById('addStudentBtn');
            const closeBtn = document.querySelector('.close-btn');
            
            // Abrir modal
            addBtn.addEventListener('click', function() {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden'; // Impede scroll na página principal
            });
            
            // Fechar modal
            function closeModal() {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Restaura scroll na página principal
            }
            
            closeBtn.addEventListener('click', closeModal);
            
            // Fechar ao clicar fora do modal
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            // Preview de imagens selecionadas
            function setupImagePreview(inputId, previewId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        
                        reader.addEventListener('load', function() {
                            preview.innerHTML = '';
                            const img = document.createElement('img');
                            img.src = this.result;
                            img.style.maxWidth = '100%';
                            img.style.maxHeight = '100%';
                            preview.appendChild(img);
                        });
                        
                        reader.readAsDataURL(file);
                    }
                });
            }
            
            // Configurar previews para todos os inputs de imagem
            setupImagePreview('foto_bi1', 'foto_bi1-preview');
            setupImagePreview('foto_bi2', 'foto_bi2-preview');
            setupImagePreview('fotoperfil', 'fotoperfil-preview');

            // Filtros da tabela
            const areaFilter = document.getElementById('area-filter');
            const statusFilter = document.getElementById('status-filter');
            const orderFilter = document.getElementById('order-filter');
            const studentsContainer = document.querySelector('[data-dynamic="students-container"]');
            const students = Array.from(studentsContainer.querySelectorAll('tr'));
            
            function filterStudents() {
                const areaValue = areaFilter.value;
                const statusValue = statusFilter.value;
                const orderValue = orderFilter.value;

                let filteredStudents = students.filter(student => {
                    const areaCell = student.querySelector('td:nth-child(3)').textContent.trim();
                    const statusCell = student.querySelector('td:nth-child(4)').textContent.trim().toLowerCase();
                    
                    const matchesArea = areaValue === 'todos' || areaCell === areaValue;
                    const matchesStatus = statusValue === 'todos' || statusCell.includes(statusValue);
                    
                    return matchesArea && matchesStatus;
                });

                // Ordenar alunos
                if (orderValue === 'nome') {
                    filteredStudents.sort((a, b) => {
                        const nameA = a.querySelector('td:nth-child(1)').textContent.trim().toLowerCase();
                        const nameB = b.querySelector('td:nth-child(1)').textContent.trim().toLowerCase();
                        return nameA.localeCompare(nameB);
                    });
                } else if (orderValue === 'area') {
                    filteredStudents.sort((a, b) => {
                        const areaA = a.querySelector('td:nth-child(3)').textContent.trim().toLowerCase();
                        const areaB = b.querySelector('td:nth-child(3)').textContent.trim().toLowerCase();
                        return areaA.localeCompare(areaB);
                    });
                } else if (orderValue === 'numero') {
                    filteredStudents.sort((a, b) => {
                        const numA = a.querySelector('td:nth-child(2)').textContent.trim().toLowerCase();
                        const numB = b.querySelector('td:nth-child(2)').textContent.trim().toLowerCase();
                        return numA.localeCompare(numB);
                    });
                }

                // Atualizar tabela
                studentsContainer.innerHTML = '';
                filteredStudents.forEach(student => studentsContainer.appendChild(student));
            }
            
            areaFilter.addEventListener('change', filterStudents);
            statusFilter.addEventListener('change', filterStudents);
            orderFilter.addEventListener('change', filterStudents);
            
            // Inicializar filtro
            filterStudents();
        });
    </script>
</body>
</html>