<?php
<<<<<<< HEAD
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

$idGuardian = $_SESSION['id'] ?? null;

if (!$idGuardian) {
    die("Encarregado não identificado.");
}

try {
    $stmt = $conn->prepare("SELECT fname, lname FROM encarregados WHERE id = :id");
    $stmt->bindParam(':id', $idGuardian, PDO::PARAM_INT);
    $stmt->execute();

    $guardian = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$guardian) {
        die("Encarregado não encontrado.");
    }
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit;
}

$dataAtual = '15 de Abril de 2025';
$trimestre = '2º trimestre';
$anoLetivo = '2025';
=======
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

        // Aqui pega o id do encarregado da sessão (a chave é 'id' conforme você mencionou)
        $idGuardian = $_SESSION['id'] ?? null;

        if (!$idGuardian) {
            die("Encarregado não identificado.");
        }

        try {
            $stmt = $conn->prepare("SELECT fname, lname FROM encarregados WHERE id = :id");
            $stmt->bindParam(':id', $idGuardian, PDO::PARAM_INT);
            $stmt->execute();

            $guardian = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$guardian) {
                die("Encarregado não encontrado.");
            }
        } catch (PDOException $e) {
            echo "Erro na consulta: " . $e->getMessage();
            exit;
        }

        $dataAtual = '15 de Abril de 2025';
        $trimestre = '2º trimestre';
        $anoLetivo = '2025';

>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequência - Dashboard Encarregados</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<<<<<<< HEAD
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
            position: fixed;
            height: 100vh;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60px;
        }

        .sidebar-header h2 {
            font-size: 20px;
            margin: 0;
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
            margin-left: 280px;
            width: calc(100% - 280px);
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

        /* Attendance Styles */
        .attendance-summary {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .attendance-stat {
            flex: 1;
            background-color: var(--white);
            border-radius: 10px;
            padding: 20px;
            display: flex;
            align-items: center;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease;
        }

        .attendance-stat:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }

        .attendance-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .attendance-icon.present {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success-color);
        }

        .attendance-icon.absent {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--danger-color);
        }

        .attendance-icon.late {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning-color);
        }

        .attendance-icon.justified {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }

        .attendance-count h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .attendance-count p {
            font-size: 0.85rem;
            color: var(--text-light);
        }

        /* Filters */
        .filter-container {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            color: var(--text-light);
            font-weight: 500;
        }

        .filter-select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            background-color: var(--white);
            transition: all 0.2s;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        /* Table Styles */
        .table-container {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .data-table th {
            background-color: var(--gray-100);
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .student-name p {
            font-weight: 500;
            margin-bottom: 3px;
        }

        .text-muted {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        .attendance-label {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .attendance-label.present {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success-color);
        }

        .attendance-label.absent {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--danger-color);
        }

        .attendance-label.late {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning-color);
        }

        .attendance-label.justified {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }

        /* Calendar Styles */
        .dashboard-card {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .calendar-navigation {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .calendar-nav-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .calendar-nav-btn:hover {
            background-color: var(--gray-200);
            color: var(--text-color);
        }

        .current-month {
            font-weight: 500;
            min-width: 120px;
            text-align: center;
        }

        .attendance-calendar {
            padding: 20px;
        }

        .calendar-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            margin-bottom: 15px;
            text-align: center;
            font-weight: 500;
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 10px;
            border-radius: 8px;
            background-color: var(--gray-100);
            position: relative;
        }

        .calendar-day.other-month {
            opacity: 0.5;
        }

        .calendar-day.weekend {
            background-color: var(--white);
            color: var(--text-light);
        }

        .calendar-day.current-day {
            background-color: var(--primary-light);
            color: var(--primary-color);
            font-weight: 600;
        }

        .day-number {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .attendance-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }

        .attendance-indicator.present {
            background-color: var(--success-color);
        }

        .attendance-indicator.absent {
            background-color: var(--danger-color);
        }

        .attendance-indicator.late {
            background-color: var(--warning-color);
        }

        .attendance-indicator.justified {
            background-color: var(--primary-color);
        }

        .calendar-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 15px 20px;
            border-top: 1px solid var(--border-color);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
        }

        .legend-color {
            width: 15px;
            height: 15px;
            border-radius: 50%;
        }

        .legend-color.present {
            background-color: var(--success-color);
        }

        .legend-color.absent {
            background-color: var(--danger-color);
        }

        .legend-color.late {
            background-color: var(--warning-color);
        }

        .legend-color.justified {
            background-color: var(--primary-color);
        }

        /* Responsive Styles */
        @media (max-width: 1200px) {
            .attendance-summary {
                flex-wrap: wrap;
            }
            
            .attendance-stat {
                min-width: calc(50% - 10px);
            }
        }

        @media (max-width: 992px) {
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

            .content {
                margin-left: 70px;
                width: calc(100% - 70px);
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
        }

        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .filter-group {
                min-width: 100%;
            }
            
            .data-table {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 576px) {
            .search-container {
                width: 150px;
            }
            
            .attendance-stat {
                min-width: 100%;
            }
            
            .calendar-legend {
                flex-wrap: wrap;
                gap: 10px;
            }
        }
    </style>
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Pitruca Camama</h2>
            </div>
            <div class="profile">
<<<<<<< HEAD
                <div class="profile-avatar">
                    <?php 
                        $names = explode(' ', $guardian['fname']);
                        $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                        echo $initials;
                    ?>
                </div>
                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?></h3>
=======
                <div class="profile-info">
                    <h3><span><?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?></span></h1></h3>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
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
                    <li class="active">
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
<<<<<<< HEAD
                </ul>
            </nav>
            <div class="sidebar-footer">
                <!--
=======
                  
                </ul>
            </nav>
            <div class="sidebar-footer">
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
                    <input type="text" placeholder="Pesquisar...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Frequência</h1>
                    <div class="header-actions">
                        <button class="btn-outline">
                            <span class="material-symbols-outlined">download</span>
                            Relatório de Frequência
                        </button>
                    </div>
                </div>

                <!-- Attendance Summary -->
                <div class="attendance-summary">
                    <div class="attendance-stat">
                        <div class="attendance-icon present">
                            <span class="material-symbols-outlined">check_circle</span>
                        </div>
                        <div class="attendance-count">
                            <h3>186</h3>
                            <p>Presenças</p>
                        </div>
                    </div>
                    <div class="attendance-stat">
                        <div class="attendance-icon absent">
                            <span class="material-symbols-outlined">cancel</span>
                        </div>
                        <div class="attendance-count">
                            <h3>8</h3>
                            <p>Faltas</p>
                        </div>
                    </div>
                    <div class="attendance-stat">
                        <div class="attendance-icon late">
                            <span class="material-symbols-outlined">schedule</span>
                        </div>
                        <div class="attendance-count">
                            <h3>3</h3>
                            <p>Atrasos</p>
                        </div>
                    </div>
                    <div class="attendance-stat">
                        <div class="attendance-icon justified">
                            <span class="material-symbols-outlined">verified</span>
                        </div>
                        <div class="attendance-count">
                            <h3>5</h3>
                            <p>Faltas Justificadas</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-container">
                    <div class="filter-group">
                        <label>Filho:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>Steeve Salvador</option>
                            <option>Kelton Gonçalves</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Período:</label>
                        <select class="filter-select">
                            <option>Abril 2025</option>
                            <option>Março 2025</option>
                            <option>Fevereiro 2025</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Disciplina:</label>
                        <select class="filter-select">
                            <option>Todas</option>
                            <option>Matemática</option>
                            <option>Português</option>
                            <option>História</option>
                        </select>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Aluno</th>
                                <th>Disciplina</th>
                                <th>Professor</th>
                                <th>Horário</th>
                                <th>Status</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>15/04/2025</td>
                                <td>
                                    <div class="student-name">
                                        <div>
                                            <p>Steeve Salvador</p>
                                            <span class="text-muted">9º Ano A</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Matemática</td>
                                <td>Prof. Carlos Silva</td>
                                <td>07:30 - 08:20</td>
                                <td><span class="attendance-label present">Presente</span></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>15/04/2025</td>
                                <td>
                                    <div class="student-name">
                                        <div>
                                            <p>Kelton Gonçalves</p>
                                            <span class="text-muted">6º Ano B</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Português</td>
                                <td>Prof. Fernanda Silva</td>
                                <td>08:20 - 09:10</td>
                                <td><span class="attendance-label late">Atraso</span></td>
                                <td>Chegou 10 min atrasada</td>
                            </tr>
                            <tr>
                                <td>14/04/2025</td>
                                <td>
                                    <div class="student-name">
                                        <div>
                                            <p>Steeve Salvador</p>
                                            <span class="text-muted">9º Ano A</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Física</td>
                                <td>Prof. Roberto Lima</td>
                                <td>09:10 - 10:00</td>
                                <td><span class="attendance-label absent">Falta</span></td>
                                <td>Consulta médica</td>
                            </tr>
                            <tr>
                                <td>14/04/2025</td>
                                <td>
                                    <div class="student-name">
                                        <div>
                                            <p>Kelton Gonçalves</p>
                                            <span class="text-muted">6º Ano B</span>
                                        </div>
                                    </div>
                                </td>
                                <td>História</td>
                                <td>Prof. Lucia Santos</td>
                                <td>10:20 - 11:10</td>
                                <td><span class="attendance-label present">Presente</span></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>13/04/2025</td>
                                <td>
                                    <div class="student-name">
                                        <div>
                                            <p>Steeve Salvador</p>
                                            <span class="text-muted">9º Ano A</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Inglês</td>
                                <td>Prof. Michael Johnson</td>
                                <td>11:10 - 12:00</td>
                                <td><span class="attendance-label justified">Justificada</span></td>
                                <td>Atestado médico apresentado</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Monthly Calendar -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>Calendário de Frequência - Abril 2025</h2>
                        <div class="calendar-navigation">
                            <button class="calendar-nav-btn">
                                <span class="material-symbols-outlined">chevron_left</span>
                            </button>
                            <span class="current-month">Abril 2025</span>
                            <button class="calendar-nav-btn">
                                <span class="material-symbols-outlined">chevron_right</span>
                            </button>
                        </div>
                    </div>
                    <div class="attendance-calendar">
                        <div class="calendar-header">
                            <div class="weekday">Dom</div>
                            <div class="weekday">Seg</div>
                            <div class="weekday">Ter</div>
                            <div class="weekday">Qua</div>
                            <div class="weekday">Qui</div>
                            <div class="weekday">Sex</div>
                            <div class="weekday">Sáb</div>
                        </div>
                        <div class="calendar-grid">
                            <div class="calendar-day other-month">30</div>
                            <div class="calendar-day other-month">31</div>
                            <div class="calendar-day">
                                <span class="day-number">1</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">2</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">3</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">4</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day weekend">5</div>
                            <div class="calendar-day weekend">6</div>
                            <div class="calendar-day">
                                <span class="day-number">7</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">8</span>
                                <div class="attendance-indicator absent"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">9</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">10</span>
                                <div class="attendance-indicator late"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">11</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day weekend">12</div>
                            <div class="calendar-day weekend">13</div>
                            <div class="calendar-day">
                                <span class="day-number">14</span>
                                <div class="attendance-indicator justified"></div>
                            </div>
                            <div class="calendar-day current-day">
                                <span class="day-number">15</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                        </div>
                    </div>
                    <div class="calendar-legend">
                        <div class="legend-item">
                            <div class="legend-color present"></div>
                            <span>Presente</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color absent"></div>
                            <span>Falta</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color late"></div>
                            <span>Atraso</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color justified"></div>
                            <span>Justificada</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });
    </script>
</body>
</html>