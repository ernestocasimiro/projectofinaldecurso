<?php
session_start();

// Verifica se o encarregado está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$sName = "localhost";
$uName = "root";
$pass = "";
$db_name = "escolabd";

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Página de erro mais amigável
    die("<div style='padding: 20px; font-family: Arial; text-align: center;'>
            <h2 style='color: #dc3545;'>Erro de Conexão</h2>
            <p>Não foi possível conectar ao banco de dados. Por favor, tente novamente mais tarde.</p>
            <p><a href='index.php' style='color: #4361ee;'>Voltar à página inicial</a></p>
         </div>");
}

$idGuardian = $_SESSION['id'];

try {
    $stmt = $conn->prepare("SELECT fname, lname FROM encarregados WHERE id = :id");
    $stmt->bindParam(':id', $idGuardian, PDO::PARAM_INT);
    $stmt->execute();

    $guardian = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$guardian) {
        // Página de erro mais amigável para encarregado não encontrado
        die("<div style='padding: 20px; font-family: Arial; text-align: center;'>
                <h2 style='color: #dc3545;'>Conta não encontrada</h2>
                <p>Seus dados de encarregado não foram encontrados no sistema.</p>
                <p>Por favor, entre em contato com a administração da escola.</p>
                <p><a href='logout.php' style='color: #4361ee;'>Sair do sistema</a></p>
             </div>");
    }
} catch (PDOException $e) {
    die("<div style='padding: 20px; font-family: Arial; text-align: center;'>
            <h2 style='color: #dc3545;'>Erro no sistema</h2>
            <p>Ocorreu um erro ao recuperar seus dados. Por favor, tente novamente mais tarde.</p>
            <p><a href='index.php' style='color: #4361ee;'>Voltar à página inicial</a></p>
         </div>");
}

$dataAtual = date('d \d\e F \d\e Y'); // Formato mais dinâmico
$trimestre = '2º trimestre';
$anoLetivo = date('Y');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - Dashboard Encarregados</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #eef2ff;
            --secondary-color: #f8f9fa;
            --accent-color: #3f37c9;
            --text-color: #2b2d42;
            --text-light: #6c757d;
            --text-lighter: #adb5bd;
            --border-color: #e9ecef;
            --card-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            --card-shadow-hover: 0 5px 15px rgba(0, 0, 0, 0.1);
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fb;
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background-color: var(--white);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }

        .sidebar-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .profile {
            padding: 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .profile-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.2rem;
        }

        .profile-info h3 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .profile-info p {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        .menu {
            flex: 1;
            padding: 15px 0;
            overflow-y: auto;
        }

        .menu ul {
            list-style: none;
        }

        .menu li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .menu li a:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .menu li a .material-symbols-outlined {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .menu li.active a {
            background-color: var(--primary-light);
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
        }

        .sidebar-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--border-color);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            padding: 10px 0;
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar-footer a:hover {
            color: var(--primary-color);
        }

        .sidebar-footer a.logout:hover {
            color: var(--danger-color);
        }

        .sidebar-footer a .material-symbols-outlined {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Main Content Styles */
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
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .search-container {
            position: relative;
            width: 300px;
        }

        .search-container .material-symbols-outlined {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-lighter);
            font-size: 1.2rem;
        }

        .search-container input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .search-container input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .top-bar-actions .material-symbols-outlined {
            color: var(--text-light);
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1.4rem;
        }

        .top-bar-actions .material-symbols-outlined:hover {
            color: var(--primary-color);
        }

        .top-bar-actions .notification {
            position: relative;
        }

        .top-bar-actions .notification::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background-color: var(--danger-color);
            border-radius: 50%;
            border: 2px solid var(--white);
        }

        .dashboard-content {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            background-color: #f5f7fb;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-light);
        }

        .btn-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background-color: var(--primary-light);
        }

        /* Messages Layout */
        .messages-layout {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 20px;
            height: calc(100vh - 180px);
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .messages-sidebar {
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .messages-filters {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            padding: 0 15px;
        }

        .filter-btn {
            flex: 1;
            padding: 15px;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-light);
            position: relative;
            text-align: center;
        }

        .filter-btn.active {
            color: var(--primary-color);
        }

        .filter-btn.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 3px;
            background-color: var(--primary-color);
            border-radius: 3px 3px 0 0;
        }

        .messages-list {
            flex: 1;
            overflow-y: auto;
        }

        .message-item {
            display: flex;
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            background-color: var(--white);
        }

        .message-item:hover {
            background-color: var(--gray-100);
        }

        .message-item.active {
            background-color: var(--primary-light);
            border-left: 3px solid var(--primary-color);
        }

        .message-item.unread {
            background-color: rgba(67, 97, 238, 0.03);
        }

        .message-preview {
            flex: 1;
            min-width: 0;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .message-header h4 {
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0;
            color: var(--text-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .message-time {
            font-size: 0.75rem;
            color: var(--text-light);
            white-space: nowrap;
            margin-left: 10px;
        }

        .message-subject {
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 4px;
            color: var(--text-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .message-excerpt {
            font-size: 0.8rem;
            color: var(--text-light);
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }

        .unread-indicator {
            width: 8px;
            height: 8px;
            background-color: var(--primary-color);
            border-radius: 50%;
            position: absolute;
            top: 20px;
            right: 15px;
        }

        /* Message Content */
        .message-content {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .message-header-full {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
        }

        .sender-info {
            display: flex;
            align-items: center;
        }

        .sender-info img {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            margin-right: 16px;
            object-fit: cover;
            border: 2px solid var(--primary-light);
        }

        .sender-info h3 {
            margin-bottom: 5px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .sender-info p {
            color: var(--text-light);
            font-size: 0.85rem;
            margin: 0;
        }

        .message-actions {
            display: flex;
            gap: 10px;
        }

        .message-details {
            margin-bottom: 25px;
        }

        .message-details h2 {
            margin-bottom: 10px;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .message-meta {
            display: flex;
            gap: 20px;
            color: var(--text-light);
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .message-body {
            line-height: 1.7;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .message-body p {
            margin-bottom: 15px;
        }

        .message-body ul {
            margin: 15px 0 15px 20px;
            padding-left: 15px;
        }

        .message-body li {
            margin-bottom: 8px;
        }

        .message-reply {
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
            margin-top: auto;
        }

        .message-reply h4 {
            margin-bottom: 15px;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .message-reply textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            resize: vertical;
            margin-bottom: 15px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            min-height: 120px;
            transition: all 0.2s;
        }

        .message-reply textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .reply-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: var(--white);
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .modal-close {
            font-size: 24px;
            cursor: pointer;
            color: var(--text-light);
            transition: all 0.2s;
        }

        .modal-close:hover {
            color: var(--danger-color);
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px;
            border-top: 1px solid var(--border-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .form-control::placeholder {
            color: var(--text-lighter);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .messages-layout {
                grid-template-columns: 1fr;
                height: auto;
            }

            .messages-sidebar {
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }

            .message-content {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }

            .sidebar-header h2, 
            .profile-info, 
            .menu-text, 
            .sidebar-footer .menu-text {
                display: none;
            }

            .profile {
                justify-content: center;
                padding: 15px 0;
            }

            .profile-avatar {
                margin-right: 0;
            }

            .menu li a {
                justify-content: center;
                padding: 15px 0;
            }

            .menu li a .material-symbols-outlined {
                margin-right: 0;
                font-size: 1.4rem;
            }

            .sidebar-footer {
                padding: 10px 0;
            }

            .sidebar-footer a {
                justify-content: center;
                padding: 10px 0;
            }

            .sidebar-footer a .material-symbols-outlined {
                margin-right: 0;
                font-size: 1.4rem;
            }

            .top-bar {
                padding: 12px 15px;
            }

            .search-container {
                width: 200px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .message-header-full {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .message-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }

        @media (max-width: 576px) {
            .search-container {
                width: 150px;
            }

            .messages-filters {
                flex-direction: column;
                padding: 0;
            }

            .filter-btn {
                padding: 12px;
                text-align: left;
            }

            .filter-btn.active::after {
                left: 0;
                transform: none;
                width: 3px;
                height: 100%;
                border-radius: 0;
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
<<<<<<< HEAD
             <style>
                    .sidebar-header {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 60px; /* ou ajuste conforme necessário */
                    }

                    .sidebar-header h2 {
                        margin: 0;
                        font-size: 20px;
                    }
           </style>

                <div class="sidebar-header">
                    <h2>Pitruca Camama</h2>
                </div>
=======
            <div class="sidebar-header">
                <h2>Pitruca Camama</h2>
            </div>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            <div class="profile">
                <div class="profile-avatar">
                    <?php 
                        $names = explode(' ', $guardian['fname']);
                        $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                        echo $initials;
                    ?>
                </div>
                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?></h3>
                    <p>Encarregado/a de Educação</p>
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
                        <a href="filhos.php">
                            <span class="material-symbols-outlined">family_restroom</span>
                            <span class="menu-text">Meus Filhos</span>
                        </a>
                    </li>
                    <li>
                        <a href="notas.php">
                            <span class="material-symbols-outlined">grade</span>
                            <span class="menu-text">Notas</span>
                        </a>
                    </li>
                    <li>
                        <a href="frequencia.php">
                            <span class="material-symbols-outlined">fact_check</span>
                            <span class="menu-text">Frequência</span>
                        </a>
                    </li>
                    <li>
                        <a href="calendario.php">
                            <span class="material-symbols-outlined">calendar_month</span>
                            <span class="menu-text">Calendário</span>
                        </a>
                    </li>
                    <li>
                        <a href="comunicados.php">
                            <span class="material-symbols-outlined">campaign</span>
                            <span class="menu-text">Comunicados</span>
                        </a>
                    </li>
                    <li class="active">
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
                </ul>
            </nav>
            <div class="sidebar-footer">
<<<<<<< HEAD
                <!--
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                <a href="configuracoes.php">
                    <span class="material-symbols-outlined">settings</span>
                    <span class="menu-text">Configurações</span>
                </a>
<<<<<<< HEAD
                -->
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
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
                    <input type="text" placeholder="Pesquisar mensagens...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Mensagens</h1>
                    <div class="header-actions">
                        <button class="btn btn-primary" id="newMessageBtn">
                            <span class="material-symbols-outlined">add</span>
                            Nova Mensagem
                        </button>
                    </div>
                </div>

                <div class="messages-layout">
                    <!-- Messages List -->
                    <div class="messages-sidebar">
                        <div class="messages-filters">
                            <button class="filter-btn active" data-filter="all">Todas</button>
                            <button class="filter-btn" data-filter="unread">Não Lidas</button>
                            <button class="filter-btn" data-filter="sent">Enviadas</button>
                        </div>

                        <div class="messages-list">
                            <div class="message-item unread active" data-message="1">
                                <div class="message-preview">
                                    <div class="message-header">
                                        <h4>Prof. Carlos Silva</h4>
                                        <span class="message-time">14:30</span>
                                    </div>
                                    <p class="message-subject">Sobre o desempenho do João em Matemática</p>
                                    <p class="message-excerpt">Gostaria de conversar sobre o progresso do João nas últimas avaliações. Notamos uma melhoria significativa, mas há ainda alguns pontos que precisam ser trabalhados.</p>
                                </div>
                                <div class="unread-indicator"></div>
                            </div>

                            <div class="message-item unread" data-message="2">
                                <div class="message-preview">
                                    <div class="message-header">
                                        <h4>Coordenação Pedagógica</h4>
                                        <span class="message-time">Ontem</span>
                                    </div>
                                    <p class="message-subject">Reunião de Pais - Confirmação</p>
                                    <p class="message-excerpt">Confirmamos sua presença na reunião do dia 18/04 às 18h30. Por favor, chegar com 15 minutos de antecedência.</p>
                                </div>
                                <div class="unread-indicator"></div>
                            </div>

                            <div class="message-item read" data-message="3">
                                <div class="message-preview">
                                    <div class="message-header">
                                        <h4>Prof. Ana Maria</h4>
                                        <span class="message-time">12/04</span>
                                    </div>
                                    <p class="message-subject">Projeto de Leitura - Ana Santos</p>
                                    <p class="message-excerpt">A Ana está se destacando no projeto de leitura. Sua interpretação do livro foi uma das melhores da turma!</p>
                                </div>
                            </div>

                            <div class="message-item read" data-message="4">
                                <div class="message-preview">
                                    <div class="message-header">
                                        <h4>Secretaria</h4>
                                        <span class="message-time">10/04</span>
                                    </div>
                                    <p class="message-subject">Documentos Pendentes</p>
                                    <p class="message-excerpt">Lembramos que há documentos pendentes para entrega até o dia 20/04. A falta pode acarretar em suspensão das atividades.</p>
                                </div>
                            </div>

                            <div class="message-item read" data-message="5">
                                <div class="message-preview">
                                    <div class="message-header">
                                        <h4>Direção</h4>
                                        <span class="message-time">05/04</span>
                                    </div>
                                    <p class="message-subject">Novas Medidas de Segurança</p>
                                    <p class="message-excerpt">Informamos sobre as novas medidas de segurança implementadas na escola. Solicitamos a colaboração de todos.</p>
                                </div>
                            </div>

                            <div class="message-item read" data-message="6">
                                <div class="message-preview">
                                    <div class="message-header">
                                        <h4>Prof. Ricardo Almeida</h4>
                                        <span class="message-time">02/04</span>
                                    </div>
                                    <p class="message-subject">Trabalho de Ciências</p>
                                    <p class="message-excerpt">O trabalho de Ciências foi adiado para a próxima semana. Novas instruções serão passadas em aula.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div class="message-content">
                        <div class="message-header-full">
                            <div class="sender-info">
                                <img src="https://ui-avatars.com/api/?name=Carlos+Silva&background=4361ee&color=fff" alt="Prof. Carlos Silva">
                                <div>
                                    <h3>Prof. Carlos Silva</h3>
                                    <p>Professor de Matemática - 9º Ano A</p>
                                </div>
                            </div>
                            <div class="message-actions">
                                <button class="btn btn-outline">
                                    <span class="material-symbols-outlined">reply</span>
                                    Responder
                                </button>
                                <button class="btn btn-outline">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </div>

                        <div class="message-details">
                            <h2>Sobre o desempenho do João em Matemática</h2>
                            <div class="message-meta">
                                <span>Para: <?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?></span>
                                <span><?php echo $dataAtual; ?>, 14:30</span>
                            </div>
                        </div>

                        <div class="message-body">
                            <p>Prezado(a) Sr(a). <?php echo htmlspecialchars($guardian['fname']); ?>,</p>
                            
                            <p>Espero que esteja bem. Gostaria de conversar sobre o progresso do João nas últimas avaliações de Matemática.</p>
                            
                            <p>O João tem demonstrado muito empenho e dedicação nas aulas, e isso se reflete em suas notas. Na última prova, ele obteve nota 8.5, mostrando uma evolução significativa em relação ao trimestre anterior.</p>
                            
                            <p>Gostaria de destacar alguns pontos positivos:</p>
                            <ul>
                                <li>Maior participação nas aulas</li>
                                <li>Melhoria na resolução de problemas</li>
                                <li>Organização dos estudos</li>
                                <li>Comprometimento com as tarefas</li>
                            </ul>
                            
                            <p>Para continuar esse progresso, sugiro que mantenham a rotina de estudos em casa e, se possível, pratiquem exercícios extras nos finais de semana. Estou disponível para esclarecer qualquer dúvida que o João possa ter.</p>
                            
                            <p>Também gostaria de marcar uma breve conversa para discutirmos estratégias para potencializar ainda mais seu desempenho. Você estaria disponível na próxima semana?</p>
                            
                            <p>Atenciosamente,<br>Prof. Carlos Silva</p>
                        </div>

                        <div class="message-reply">
                            <h4>Responder</h4>
                            <textarea placeholder="Digite sua resposta..."></textarea>
                            <div class="reply-actions">
                                <button class="btn btn-primary">
                                    <span class="material-symbols-outlined">send</span>
                                    Enviar
                                </button>
                                <button class="btn btn-outline">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- New Message Modal -->
    <div class="modal" id="newMessageModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Nova Mensagem</h3>
                <span class="modal-close">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Para:</label>
                    <select class="form-control">
                        <option>Selecione o destinatário</option>
                        <option>Prof. Carlos Silva - Matemática</option>
                        <option>Prof. Ana Maria - Português</option>
                        <option>Coordenação Pedagógica</option>
                        <option>Secretaria</option>
                        <option>Direção</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Assunto:</label>
                    <input type="text" class="form-control" placeholder="Digite o assunto">
                </div>
                <div class="form-group">
                    <label>Mensagem:</label>
                    <textarea class="form-control" rows="6" placeholder="Digite sua mensagem"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                               <button class="btn btn-outline">Cancelar</button>
                <button class="btn btn-primary">
                    <span class="material-symbols-outlined">send</span>
                    Enviar Mensagem
                </button>
            </div>
        </div>
    </div>

    <script>
        // Script para funcionalidades da tela de mensagens
        document.addEventListener('DOMContentLoaded', function() {
            // Abrir modal de nova mensagem
            const newMessageBtn = document.getElementById('newMessageBtn');
            const newMessageModal = document.getElementById('newMessageModal');
            const modalClose = document.querySelector('.modal-close');
            
            newMessageBtn.addEventListener('click', function() {
                newMessageModal.style.display = 'flex';
            });
            
            modalClose.addEventListener('click', function() {
                newMessageModal.style.display = 'none';
            });
            
            // Fechar modal ao clicar fora
            window.addEventListener('click', function(event) {
                if (event.target === newMessageModal) {
                    newMessageModal.style.display = 'none';
                }
            });
            
            // Filtros de mensagens
            const filterBtns = document.querySelectorAll('.filter-btn');
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filter = this.dataset.filter;
                    const messageItems = document.querySelectorAll('.message-item');
                    
                    messageItems.forEach(item => {
                        item.style.display = 'flex';
                        
                        if (filter === 'unread' && !item.classList.contains('unread')) {
                            item.style.display = 'none';
                        } else if (filter === 'sent' && !item.classList.contains('sent')) {
                            item.style.display = 'none';
                        }
                    });
                });
            });
            
            // Selecionar mensagem na lista
            const messageItems = document.querySelectorAll('.message-item');
            messageItems.forEach(item => {
                item.addEventListener('click', function() {
                    const messageId = this.dataset.message;
                    
                    messageItems.forEach(i => i.classList.remove('active'));
                    this.classList.remove('unread');
                    this.querySelector('.unread-indicator')?.remove();
                    this.classList.add('active');
                    
                    // Aqui você poderia carregar o conteúdo da mensagem via AJAX
                    // ou alternar entre diferentes conteúdos pré-carregados
                });
            });
            
            // Simular envio de mensagem
            const replyBtn = document.querySelector('.reply-actions .btn-primary');
            if (replyBtn) {
                replyBtn.addEventListener('click', function() {
                    const replyTextarea = document.querySelector('.message-reply textarea');
                    if (replyTextarea.value.trim() === '') {
                        alert('Por favor, digite sua resposta antes de enviar.');
                        return;
                    }
                    
                    // Simular envio
                    alert('Mensagem enviada com sucesso!');
                    replyTextarea.value = '';
                });
            }
        });
    </script>
</body>
</html>