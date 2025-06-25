<?php
session_start();

$sName = "localhost";
$uName = "root";
$pass = "";
$db_name = "escolabd";

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$idCoordinator = $_SESSION['id'] ?? null;

if (!$idCoordinator) {
    header("Location: login.php");
    exit;
}

// Verificar/Criar tabela de acessos se não existir
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS coordenador_acessos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        coordenador_id INT NOT NULL,
        codigo_acesso VARCHAR(50) NOT NULL,
        area VARCHAR(50) NOT NULL,
        data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
        data_expiracao DATETIME NOT NULL,
        utilizado TINYINT(1) DEFAULT 0,
        FOREIGN KEY (coordenador_id) REFERENCES coordenadores(id)
    )");
} catch (PDOException $e) {
    die("Erro ao verificar/criar tabela de acessos: " . $e->getMessage());
}

// Obter informações do coordenador
try {
    $stmt = $conn->prepare("SELECT fname, lname FROM coordenadores WHERE id = :id");
    $stmt->bindParam(':id', $idCoordinator, PDO::PARAM_INT);
    $stmt->execute();
    $coordinator = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$coordinator) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}

// Verificar se há acesso válido em sessão ou no banco de dados
$areaAutorizada = null;
$mostrarTodasAreas = true;

// Verificar se há uma área autorizada na sessão
if (isset($_SESSION['area_autorizada']) && isset($_SESSION['acesso_valido_ate'])) {
    if (strtotime($_SESSION['acesso_valido_ate']) > time()) {
        $areaAutorizada = $_SESSION['area_autorizada'];
        $mostrarTodasAreas = false;
    } else {
        // Acesso expirado
        unset($_SESSION['area_autorizada']);
        unset($_SESSION['acesso_valido_ate']);
    }
}

// Se não há na sessão, verificar no banco de dados
if ($mostrarTodasAreas) {
    try {
        $stmt = $conn->prepare("SELECT area, data_expiracao FROM coordenador_acessos 
                              WHERE coordenador_id = :id 
                              AND utilizado = 1
                              AND data_expiracao > NOW()
                              ORDER BY data_expiracao DESC
                              LIMIT 1");
        $stmt->bindParam(':id', $idCoordinator, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($acesso = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $areaAutorizada = $acesso['area'];
            $mostrarTodasAreas = false;
            $_SESSION['area_autorizada'] = $areaAutorizada;
            $_SESSION['acesso_valido_ate'] = $acesso['data_expiracao'];
        }
    } catch (PDOException $e) {
        die("Erro ao verificar acessos válidos: " . $e->getMessage());
    }
}

// Verificar código de acesso se o formulário foi submetido
$error = null;
$showAccessModal = false;
$selectedArea = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['access_code'])) {
    $accessCode = trim($_POST['access_code']);
    $area = $_POST['area'] ?? '';
    
    try {
        // Verificar código válido e não expirado
        $stmt = $conn->prepare("SELECT * FROM coordenador_acessos 
                              WHERE coordenador_id = :id 
                              AND area = :area 
                              AND codigo_acesso = :codigo
                              AND utilizado = 0
                              AND data_expiracao > NOW()");
        $stmt->bindParam(':id', $idCoordinator, PDO::PARAM_INT);
        $stmt->bindParam(':area', $area, PDO::PARAM_STR);
        $stmt->bindParam(':codigo', $accessCode, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($access = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Marcar código como utilizado
            $updateStmt = $conn->prepare("UPDATE coordenador_acessos SET utilizado = 1 WHERE id = :id");
            $updateStmt->bindParam(':id', $access['id'], PDO::PARAM_INT);
            $updateStmt->execute();
            
            $_SESSION['area_autorizada'] = $area;
            $_SESSION['acesso_valido_ate'] = $access['data_expiracao'];
            
            // Redirecionar para evitar reenvio do formulário
            header("Location: turmas.php");
            exit;
        } else {
            $error = "Código de acesso inválido ou expirado para esta área.";
            $showAccessModal = true;
            $selectedArea = $area;
        }
    } catch (PDOException $e) {
        $error = "Erro ao verificar código de acesso: " . $e->getMessage();
    }
} elseif (isset($_GET['select_area'])) {
    // Quando o coordenador seleciona uma área, verificar se tem acesso
    $selectedArea = $_GET['select_area'];
    
    // Verificar se já tem acesso a esta área
    if ($areaAutorizada !== $selectedArea) {
        $showAccessModal = true;
    } else {
        // Já tem acesso, pode prosseguir
        $areaAutorizada = $selectedArea;
    }
}

// Resetar acesso se solicitado
if (isset($_GET['reset_access'])) {
    unset($_SESSION['area_autorizada']);
    unset($_SESSION['acesso_valido_ate']);
    header("Location: turmas.php");
    exit;
}

// Verificar se foi solicitado um ano específico ou curso
$anoSelecionado = $_GET['ano'] ?? null;
$cursoSelecionado = $_GET['curso'] ?? null;

// Verificar se foi solicitado adicionar um aluno a uma turma
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_aluno'])) {
    $alunoId = $_POST['estudante_id'];
    $turmaId = $_POST['turma_id'];
    
    try {
        // Primeiro verificar se o aluno já está em alguma turma
        $stmt = $conn->prepare("SELECT area FROM alunos WHERE id = :estudante_id");
        $stmt->bindParam(':estudante_id', $alunoId, PDO::PARAM_INT);
        $stmt->execute();
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$aluno) {
            throw new Exception("Aluno não encontrado.");
        }
        
        // Verificar se a turma pertence à mesma área do aluno
        $stmt = $conn->prepare("SELECT area FROM turmas WHERE id = :turma_id");
        $stmt->bindParam(':turma_id', $turmaId, PDO::PARAM_INT);
        $stmt->execute();
        $turma = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$turma) {
            throw new Exception("Turma não encontrada.");
        }
        
        if ($aluno['area'] !== $turma['area']) {
            throw new Exception("O aluno não pode ser adicionado a uma turma de outra área de ensino.");
        }
        
        // Verificar se o aluno já está na turma
        $stmt = $conn->prepare("SELECT * FROM estudante_turma WHERE estudante_id = :estudante_id AND turma_id = :turma_id");
        $stmt->bindParam(':estudante_id', $alunoId, PDO::PARAM_INT);
        $stmt->bindParam(':turma_id', $turmaId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            throw new Exception("O aluno já está nesta turma.");
        }
        
        // Adicionar aluno à turma
        $stmt = $conn->prepare("INSERT INTO estudante_turma (estudante_id, turma_id, data_inscricao) VALUES (:estudante_id, :turma_id, NOW())");
        $stmt->bindParam(':estudante_id', $alunoId, PDO::PARAM_INT);
        $stmt->bindParam(':turma_id', $turmaId, PDO::PARAM_INT);
        $stmt->execute();
        
        $_SESSION['success_message'] = "Aluno adicionado à turma com sucesso!";
        header("Location: turmas.php");
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$dataAtual = date('d \d\e F \d\e Y');
$trimestre = '2º trimestre';
$anoLetivo = date('Y');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turmas - Dashboard de Professores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Estilos melhorados para a seção de turmas */
        .classes-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .classes-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .view-options {
            display: flex;
            background: #f5f5f5;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .view-btn {
            padding: 8px 12px;
            border: none;
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .view-btn.active {
            background: #3a5bb9;
            color: white;
        }
        
        .filter-select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            background: white;
            font-size: 0.9rem;
        }
        
        /* Estilos para as turmas */
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .class-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s ease;
            border-left: 4px solid #3a5bb9;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            opacity: 0.5;
            filter: grayscale(80%);
        }
        
        .class-card.accessible {
            opacity: 1;
            filter: grayscale(0%);
        }
        
        .class-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .class-card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .class-card h3 .material-symbols-outlined {
            font-size: 1.5rem;
            color: #3a5bb9;
        }
        
        .class-info {
            color: #555;
            margin: 10px 0;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .class-info strong {
            color: #444;
            font-weight: 500;
        }
        
        .class-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }
        
        .class-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .class-meta-item .material-symbols-outlined {
            font-size: 1.1rem;
        }
        
        .class-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #f0f7ff;
            color: #3a5bb9;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Modal de detalhes da turma */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow-y: auto;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 80%;
            max-width: 900px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h2 {
            color: #444;
            margin: 0;
            font-size: 1.8rem;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #777;
            transition: color 0.2s;
        }

        .close-modal:hover {
            color: #333;
        }

        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .modal-section {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
        }

        .modal-section h3 {
            color: #555;
            margin-top: 0;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.3rem;
        }

        .modal-section h3 .material-symbols-outlined {
            font-size: 1.5rem;
        }

        .disciplinas-list, .alunos-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
        }

        .disciplina-card, .aluno-card {
            background-color: #fff;
            border-radius: 6px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .disciplina-card:hover, .aluno-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }

        .disciplina-card h4, .aluno-card h4 {
            margin: 0 0 5px 0;
            color: #444;
            font-size: 1.1rem;
        }

        .disciplina-card p, .aluno-card p {
            margin: 5px 0;
            color: #666;
            font-size: 0.9rem;
        }

        .turma-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .info-item {
            margin-bottom: 10px;
            background: white;
            padding: 12px;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .info-item strong {
            display: block;
            color: #555;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .info-item span {
            color: #666;
        }

        /* Modal de código de acesso */
        .access-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow-y: auto;
        }

        .access-modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 30px;
            border-radius: 10px;
            width: 50%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .access-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .access-modal-header h2 {
            color: #444;
            margin: 0;
            font-size: 1.5rem;
        }

        .close-access-modal {
            background: none;
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #777;
            transition: color 0.2s;
        }

        .close-access-modal:hover {
            color: #333;
        }

        .access-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            color: #555;
            font-weight: 500;
        }

        .form-group input {
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .btn-primary {
            background-color: #3a5bb9;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2d4a9e;
        }

        .btn-secondary {
            background-color: #f0f0f0;
            color: #555;
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
        }

        .error-message {
            color: #d32f2f;
            margin-top: 10px;
            font-size: 0.9rem;
        }

        /* Áreas de ensino */
        .teaching-areas {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .area-card {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            text-align: center;
            border-top: 4px solid #3a5bb9;
        }

        .area-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .area-card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .area-card p {
            color: #666;
            margin-bottom: 20px;
        }

        .area-icon {
            font-size: 2.5rem;
            color: #3a5bb9;
            margin-bottom: 15px;
        }

        /* Botão para voltar às áreas */
        .back-to-areas {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 15px;
            background-color: #f0f0f0;
            color: #555;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .back-to-areas:hover {
            background-color: #e0e0e0;
        }

        .back-to-areas .material-symbols-outlined {
            vertical-align: middle;
            margin-right: 5px;
        }

        /* Informação de acesso válido */
        .access-info {
            background-color: #f0f7ff;
            color: #3a5bb9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .access-info .material-symbols-outlined {
            margin-right: 10px;
        }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: #3a5bb9;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb .separator {
            color: #777;
        }

        /* Modal para adicionar aluno */
        .add-student-form {
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .add-student-form h3 {
            margin-top: 0;
            color: #444;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-row select, .form-row input {
            flex: 1;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .form-row button {
            padding: 10px 20px;
            background-color: #3a5bb9;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .form-row button:hover {
            background-color: #2d4a9e;
        }

        /* Mensagem de sucesso */
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu (mantido original) -->
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
                <a href="configuracoes.php">
                    <span class="material-symbols-outlined">settings</span>
                    <span class="menu-text">Configurações</span>
                </a>
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
                    <input type="text" placeholder="Pesquisar turmas...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Turmas</h1>
                    <p class="page-description">Selecione a sua área de ensino para visualizar as turmas</p>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                        <?php unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div style="color: red; margin-bottom: 20px;"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($areaAutorizada): ?>
                    <!-- Mostrar informações do acesso válido -->
                    <div class="access-info">
                        <div>
                            <span class="material-symbols-outlined">verified</span>
                            Você tem acesso à área de <strong><?php echo htmlspecialchars($areaAutorizada); ?></strong> até <?php echo date('d/m/Y H:i', strtotime($_SESSION['acesso_valido_ate'])); ?>
                        </div>
                        <a href="turmas.php?reset_access=1" class="btn btn-secondary">Sair da área</a>
                    </div>

                    <!-- Breadcrumb -->
                    <div class="breadcrumb">
                        <a href="turmas.php"><?php echo htmlspecialchars($areaAutorizada); ?></a>
                        <?php if ($anoSelecionado || $cursoSelecionado): ?>
                            <span class="separator">/</span>
                            <?php if ($anoSelecionado): ?>
                                <span><?php echo htmlspecialchars($anoSelecionado); ?></span>
                            <?php elseif ($cursoSelecionado): ?>
                                <span><?php echo htmlspecialchars($cursoSelecionado); ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (!$anoSelecionado && !$cursoSelecionado): ?>
                        <!-- Mostrar os anos/cursos da área autorizada -->
                        <div class="classes-header">
                            <h2>Turmas - <?php echo htmlspecialchars($areaAutorizada); ?></h2>
                        </div>

                        <div class="classes-grid">
                            <?php 
                            // Mostrar diferentes opções dependendo da área selecionada
                            switch ($areaAutorizada) {
                                case 'Iº Ciclo':
                                    // Mostrar cartões de 1ª a 6ª classe
                                    for ($i = 1; $i <= 6; $i++): ?>
                                        <div class="class-card accessible" onclick="window.location.href='turmas_classe.php?classe=<?php echo urlencode($i . 'ª Classe'); ?>'">
                                            <h3><span class="material-symbols-outlined">school</span> <?php echo $i; ?>ª Classe</h3>
                                            <p class="class-info">Turmas do <?php echo $i; ?>º ano do ensino primário</p>
                                            <div class="class-meta">
                                                <span class="class-meta-item">
                                                    <span class="material-symbols-outlined">group</span>
                                                    Várias turmas
                                                </span>
                                            </div>
                                        </div>
                                    <?php endfor;
                                    break;
                                case 'IIº Ciclo':
                                    // Mostrar cartões de 7ª a 9ª classe
                                    for ($i = 7; $i <= 9; $i++): ?>
                                        <div class="class-card accessible" onclick="window.location.href='turmas_ciclo.php?classe=<?php echo urlencode($i . 'ª Classe'); ?>'">
                                            <h3><span class="material-symbols-outlined">school</span> <?php echo $i; ?>ª Classe</h3>
                                            <p class="class-info">Turmas do <?php echo $i; ?>º ano do ensino básico</p>
                                            <div class="class-meta">
                                                <span class="class-meta-item">
                                                    <span class="material-symbols-outlined">group</span>
                                                    Várias turmas
                                                </span>
                                            </div>
                                        </div>
                                    <?php endfor;
                                    break;
                                    
                                case 'Cursos Puniv':
                                    // Mostrar cartões dos cursos pré-universitários ?>
                                    <div class="class-card accessible" onclick="window.location.href='turmas_puniv.php?curso=<?php echo urlencode('Ciências Físicas e Biológicas'); ?>'">
                                        <h3><span class="material-symbols-outlined">science</span> Ciências Físicas e Biológicas</h3>
                                        <p class="class-info">Curso pré-universitário de ciências exatas e biológicas</p>
                                        <div class="class-meta">
                                            <span class="class-meta-item">
                                                <span class="material-symbols-outlined">group</span>
                                                10º, 11º e 12º anos
                                            </span>
                                        </div>
                                    </div>
                                    <div class="class-card accessible" onclick="window.location.href='turmas_puniv.php?curso=<?php echo urlencode('Curso de Económicas e Jurídicas'); ?>'">
                                        <h3><span class="material-symbols-outlined">gavel</span> Económicas e Jurídicas</h3>
                                        <p class="class-info">Curso pré-universitário de ciências sociais</p>
                                        <div class="class-meta">
                                            <span class="class-meta-item">
                                                <span class="material-symbols-outlined">group</span>
                                                10º, 11º e 12º anos
                                            </span>
                                        </div>
                                    </div>
                                    <?php break;
                                    
                                case 'Cursos Técnicos':
                                    // Mostrar cartões dos cursos técnicos ?>
                                    <div class="class-card accessible" onclick="window.location.href='turmas_tecnicos.php?curso=<?php echo urlencode('Informática'); ?>'">
                                        <h3><span class="material-symbols-outlined">computer</span> Informática</h3>
                                        <p class="class-info">Curso técnico de informática e programação</p>
                                        <div class="class-meta">
                                            <span class="class-meta-item">
                                                <span class="material-symbols-outlined">group</span>
                                                10º, 11º, 12º e 13º anos
                                            </span>
                                        </div>
                                    </div>
                                    <div class="class-card accessible" onclick="window.location.href='turmas_tecnicos.php?curso=<?php echo urlencode('Contabilidade e Gestão'); ?>'">
                                        <h3><span class="material-symbols-outlined">calculate</span> Contabilidade e Gestão</h3>
                                        <p class="class-info">Curso técnico de gestão financeira</p>
                                        <div class="class-meta">
                                            <span class="class-meta-item">
                                                <span class="material-symbols-outlined">group</span>
                                                10º, 11º, 12º e 13º anos
                                            </span>
                                        </div>
                                    </div>
                                    <div class="class-card accessible" onclick="window.location.href='turmas_tecnicos.php?curso=<?php echo urlencode('Enfermagem'); ?>'">
                                                                           <div class="class-card accessible" onclick="window.location.href='turmas_tecnicos.php?curso=<?php echo urlencode('Enfermagem'); ?>'">
                                        <h3><span class="material-symbols-outlined">medical_services</span> Enfermagem</h3>
                                        <p class="class-info">Curso técnico de saúde e enfermagem</p>
                                        <div class="class-meta">
                                            <span class="class-meta-item">
                                                <span class="material-symbols-outlined">group</span>
                                                10º, 11º, 12º e 13º anos
                                            </span>
                                        </div>
                                    </div>
                                    <?php break;
                                    
                                default:
                                    // Mostrar mensagem caso a área não tenha configuração específica ?>
                                    <div class="class-card">
                                        <h3>Área sem turmas configuradas</h3>
                                        <p>Esta área de ensino ainda não possui turmas cadastradas no sistema.</p>
                                    </div>
                            <?php } ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Mostrar áreas de ensino para seleção -->
                    <div class="teaching-areas">
                        <div class="area-card" onclick="selectArea('Iº Ciclo')">
                            <div class="area-icon">
                                <span class="material-symbols-outlined">child_care</span>
                            </div>
                            <h3>Iº Ciclo</h3>
                            <p>Ensino Primário - 1ª à 6ª Classe</p>
                        </div>
                        
                        <div class="area-card" onclick="selectArea('IIº Ciclo')">
                            <div class="area-icon">
                                <span class="material-symbols-outlined">school</span>
                            </div>
                            <h3>IIº Ciclo</h3>
                            <p>Ensino Básico - 7ª à 9ª Classe</p>
                        </div>
                        
                        <div class="area-card" onclick="selectArea('Cursos Puniv')">
                            <div class="area-icon">
                                <span class="material-symbols-outlined">science</span>
                            </div>
                            <h3>Cursos Puniv</h3>
                            <p>Cursos Pré-Universitários - 10ª à 12ª Classe</p>
                        </div>
                        
                        <div class="area-card" onclick="selectArea('Cursos Técnicos')">
                            <div class="area-icon">
                                <span class="material-symbols-outlined">engineering</span>
                            </div>
                            <h3>Cursos Técnicos</h3>
                            <p>Formação Técnica - 10ª à 13ª Classe</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Modal de Código de Acesso -->
    <div id="accessModal" class="access-modal" style="<?php echo $showAccessModal ? 'display: block;' : 'display: none;' ?>">
        <div class="access-modal-content">
            <div class="access-modal-header">
                <h2>Acesso à Área de Ensino</h2>
                <button class="close-access-modal" onclick="closeAccessModal()">&times;</button>
            </div>
            <form class="access-form" method="POST" action="turmas.php">
                <input type="hidden" name="area" value="<?php echo htmlspecialchars($selectedArea); ?>">
                <div class="form-group">
                    <label for="access_code">Insira o código de acesso para a área de <?php echo htmlspecialchars($selectedArea); ?></label>
                    <input type="text" id="access_code" name="access_code" required placeholder="Digite o código de acesso">
                </div>
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeAccessModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Acessar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Detalhes da Turma (mantido para referência) -->
    <div id="classModal" class="modal">
        <div class="modal-content">
            <!-- Conteúdo do modal de detalhes -->
        </div>
    </div>

    <script>
        // Função para selecionar área de ensino
        function selectArea(area) {
            window.location.href = 'turmas.php?select_area=' + encodeURIComponent(area);
        }

        // Funções para controlar o modal de acesso
        function closeAccessModal() {
            document.getElementById('accessModal').style.display = 'none';
            window.location.href = 'turmas.php';
        }

        // Mostrar modal se necessário
        <?php if ($showAccessModal): ?>
            document.getElementById('accessModal').style.display = 'block';
        <?php endif; ?>

        // Função para mostrar detalhes da turma (exemplo)
        function showClassDetails(classId) {
            // Implementação do modal de detalhes
            console.log('Mostrar detalhes da turma:', classId);
        }
    </script>
</body>
</html>