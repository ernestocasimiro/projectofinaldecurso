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

     
// Define o fuso horário (Angola: África/Luanda)
date_default_timezone_set('Africa/Luanda');

// Captura a data atual
$dataAtual = date('d') . ' de ' . ucfirst(strftime('%B')) . ' de ' . date('Y');

// Alternativa se strftime não funcionar:
setlocale(LC_TIME, 'pt_PT.UTF-8', 'pt_BR.UTF-8', 'Portuguese_Portugal'); // tenta usar português
$mesExtenso = strftime('%B'); // mês em extenso (ex: abril)
$dataAtual = date('d') . ' de ' . ucfirst($mesExtenso) . ' de ' . date('Y');

// Determina o trimestre com base no mês atual
$mes = (int)date('m');
if ($mes >= 1 && $mes <= 3) {
    $trimestre = '1º trimestre';
} elseif ($mes >= 4 && $mes <= 6) {
    $trimestre = '2º trimestre';
} elseif ($mes >= 7 && $mes <= 9) {
    $trimestre = '3º trimestre';
} else {
    $trimestre = '4º trimestre';
}

// Ano letivo (pode ser só o ano atual)
$anoLetivo = date('Y');


?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Encarregados de Educação</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

     <style>

                    .stats-container {
                    display: flex;
                    gap: 20px;
                    margin-bottom: 30px;
                    flex-wrap: wrap;
                }

                .stat-card {
                    flex: 1 1 200px;
                    background-color: #fff;
                    border-radius: 12px;
                    padding: 20px;
                    display: flex;
                    align-items: center;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.07);
                    transition: transform 0.2s ease;
                }

                .stat-card:hover {
                    transform: translateY(-3px);
                }

                .stat-icon {
                    font-size: 36px;
                    color: #4a90e2;
                    margin-right: 15px;
                }

                .stat-info h3 {
                    margin: 0;
                    font-size: 22px;
                    font-weight: bold;
                    color: #333;
                }

                .stat-info p {
                    margin: 2px 0 0;
                    font-size: 14px;
                    color: #666;
                }

                .dashboard-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 20px;
                }

                .dashboard-card {
                    background-color: #fff;
                    border-radius: 12px;
                    padding: 20px;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
                }

                .card-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 15px;
                }

                .card-header h2 {
                    font-size: 18px;
                    margin: 0;
                    color: #333;
                }

                .view-all {
                    font-size: 13px;
                    color: #4a90e2;
                    text-decoration: none;
                }

                .view-all:hover {
                    text-decoration: underline;
                }

                .student-item {
                    display: flex;
                    justify-content: space-between;
                    background-color: #f9f9f9;
                    padding: 12px 15px;
                    border-radius: 10px;
                    margin-bottom: 10px;
                }

                .student-info h4 {
                    margin: 0;
                    font-size: 16px;
                    color: #222;
                }

                .student-info p {
                    margin: 3px 0;
                    font-size: 13px;
                    color: #666;
                }

                .student-stats .stat {
                    margin-right: 10px;
                    font-size: 12px;
                    color: #555;
                }

                .student-status .status-badge {
                    background-color: #28a745;
                    color: white;
                    padding: 5px 10px;
                    font-size: 12px;
                    border-radius: 20px;
                }

                .events-list .event {
                    display: flex;
                    align-items: center;
                    background-color: #f1f5f9;
                    border-radius: 10px;
                    padding: 12px;
                    margin-bottom: 10px;
                }

                .event-date {
                    width: 50px;
                    text-align: center;
                    margin-right: 15px;
                }

                .event-date .day {
                    font-size: 20px;
                    font-weight: bold;
                    color: #2c3e50;
                }

                .event-date .month {
                    font-size: 12px;
                    color: #7f8c8d;
                }

                .event-details h3 {
                    font-size: 15px;
                    margin: 0;
                    color: #333;
                }

                .event-details p {
                    margin: 5px 0 0;
                    font-size: 13px;
                    color: #555;
                }

                .grades-container .grade-item {
                    display: flex;
                    justify-content: space-between;
                    background-color: #f6f8fa;
                    padding: 10px 15px;
                    border-radius: 8px;
                    margin-bottom: 10px;
                }

                .grade-subject h4 {
                    margin: 0;
                    font-size: 15px;
                    color: #333;
                }

                .grade-subject p {
                    font-size: 12px;
                    color: #666;
                }

                .grade-value .grade {
                    font-weight: bold;
                    font-size: 18px;
                    padding: 6px 10px;
                    border-radius: 8px;
                    color: white;
                }

                .grade.good {
                    background-color: #3498db;
                }

                .grade.excellent {
                    background-color: #2ecc71;
                }

                .announcements-list .announcement-item {
                    display: flex;
                    background-color: #fefefe;
                    padding: 12px;
                    border-left: 5px solid #4a90e2;
                    border-radius: 8px;
                    margin-bottom: 10px;
                }

                .announcement-icon {
                    font-size: 30px;
                    color: #4a90e2;
                    margin-right: 15px;
                }

                .announcement-content h4 {
                    margin: 0;
                    font-size: 14px;
                    color: #2c3e50;
                }

                .announcement-content p {
                    font-size: 13px;
                    color: #555;
                    margin: 4px 0;
                }

                .announcement-date {
                    font-size: 12px;
                    color: #999;
                }

                @media (max-width: 600px) {
                    .stats-container {
                        flex-direction: column;
                    }
                }
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
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .sidebar-header h2 {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
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

        /* Welcome Section */
        .welcome-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
        }

        .welcome-text h1 {
            font-size: 1.5rem;
            margin-bottom: 8px;
            color: var(--text-color);
        }

        .welcome-text h1 span {
            color: var(--primary-color);
        }

        .welcome-text p {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .welcome-text p span {
            font-weight: 500;
            color: var(--text-color);
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: var(--white);
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--card-shadow-hover);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary-color);
        }

        .stat-icon .material-symbols-outlined {
            font-size: 1.5rem;
        }

        .stat-info h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-color);
        }

        .stat-info p {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }

        .dashboard-card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
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
            font-size: 1.1rem;
            font-weight: 600;
        }

        .card-header .view-all {
            font-size: 0.8rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .card-header .view-all:hover {
            text-decoration: underline;
        }

        .card-content {
            padding: 20px;
        }

        /* Student Item */
        .student-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .student-item:last-child {
            border-bottom: none;
        }

        .student-info h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .student-info p {
            font-size: 0.8rem;
            color: var(--text-light);
            margin-bottom: 8px;
        }

        .student-stats {
            display: flex;
            gap: 15px;
        }

        .student-stats .stat {
            font-size: 0.75rem;
            color: var(--text-light);
        }

        .status-badge {
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 500;
        }

        .status-badge.active {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success-color);
        }

        /* Events List */
        .events-list {
            padding: 5px 0;
        }

        .event {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .event:last-child {
            border-bottom: none;
        }

        .event-date {
            width: 50px;
            height: 50px;
            background-color: var(--primary-light);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary-color);
        }

        .event-date .day {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .event-date .month {
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        .event-details h3 {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .event-details p {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        /* Grades */
        .grades-container {
            padding: 5px 0;
        }

        .grade-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .grade-item:last-child {
            border-bottom: none;
        }

        .grade-subject h4 {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .grade-subject p {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        .grade-value .grade {
            display: inline-block;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .grade-value .grade.excellent {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success-color);
        }

        .grade-value .grade.good {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning-color);
        }

        /* Announcements */
        .announcements-list {
            padding: 5px 0;
        }

        .announcement-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .announcement-item:last-child {
            border-bottom: none;
        }

        .announcement-icon {
            width: 40px;
            height: 40px;
            background-color: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary-color);
        }

        .announcement-icon .material-symbols-outlined {
            font-size: 1.2rem;
        }

        .announcement-content {
            flex: 1;
        }

        .announcement-content h4 {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .announcement-content p {
            font-size: 0.8rem;
            color: var(--text-light);
            margin-bottom: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .announcement-date {
            font-size: 0.7rem;
            color: var(--text-lighter);
        }

        /* Responsive Styles */
        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr 1fr;
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

            .welcome-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: 1fr 1fr;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .search-container {
                width: 200px;
            }

            .top-bar {
                padding: 12px 15px;
            }
        }

        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }

            .search-container {
                width: 150px;
            }

            .welcome-text h1 {
                font-size: 1.2rem;
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
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="welcome-section">
                    <div class="welcome-text">
                        <h1>Bem-vindo/a, <span><?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?></span>!</h1>
                        <p>Hoje é <span><?php echo $dataAtual; ?></span> | <span><?php echo $trimestre; ?></span> | Ano Letivo <span><?php echo $anoLetivo; ?></span></p>
                    </div>

                    <div class="welcome-actions">
                        <a href="mensagens.php" style="color: inherit; text-decoration: none;">
                            <button class="btn-primary">
                                <span class="material-symbols-outlined">message</span>
                                Nova Mensagem
                            </button>
                        </a>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span class="material-symbols-outlined">family_restroom</span>
                        </div>
                        <div class="stat-info">
                            <h3>2</h3>
                            <p>Filhos Matriculados</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span class="material-symbols-outlined">grade</span>
                        </div>
                        <div class="stat-info">
                            <h3>8.5</h3>
                            <p>Média Geral</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span class="material-symbols-outlined">check_circle</span>
                        </div>
                        <div class="stat-info">
                            <h3>95%</h3>
                            <p>Frequência</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <div class="stat-info">
                            <h3>3</h3>
                            <p>Mensagens Não Lidas</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Meus Filhos</h2>
                            <a href="filhos.php" class="view-all">Ver todos</a>
                        </div>
                        <div class="card-content">
                            <div class="student-item">
                                <div class="student-info">
                                    <h4>Joao Francisco</h4>
                                    <p>9º Ano A - Ensino Fundamental</p>
                                    <div class="student-stats">
                                        <span class="stat">Média: 8.7</span>
                                        <span class="stat">Frequência: 96%</span>
                                    </div>
                                </div>
                                <div class="student-status">
                                    <span class="status-badge active">Ativo</span>
                                </div>
                            </div>
                            <div class="student-item">
                                <div class="student-info">
                                    <h4>Joana Francisco</h4>
                                    <p>6º Ano B - Ensino Fundamental</p>
                                    <div class="student-stats">
                                        <span class="stat">Média: 8.3</span>
                                        <span class="stat">Frequência: 94%</span>
                                    </div>
                                </div>
                                <div class="student-status">
                                    <span class="status-badge active">Ativo</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Próximos Eventos</h2>
                            <a href="calendario.php" class="view-all">Ver todos</a>
                        </div>
                        <div class="events-list">
                            <div class="event">
                                <div class="event-date">
                                    <span class="day">18</span>
                                    <span class="month">Abr</span>
                                </div>
                                <div class="event-details">
                                    <h3>Reunião de Pais - 9º Ano</h3>
                                    <p>19:00 - 21:00 | Auditório</p>
                                </div>
                            </div>
                            <div class="event">
                                <div class="event-date">
                                    <span class="day">20</span>
                                    <span class="month">Abr</span>
                                </div>
                                <div class="event-details">
                                    <h3>Prova de Matemática - João</h3>
                                    <p>08:00 - 10:00 | Sala 105</p>
                                </div>
                            </div>
                            <div class="event">
                                <div class="event-date">
                                    <span class="day">22</span>
                                    <span class="month">Abr</span>
                                </div>
                                <div class="event-details">
                                    <h3>Entrega de Boletins</h3>
                                    <p>14:00 - 17:00 | Secretaria</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Notas Recentes</h2>
                            <a href="notas.php" class="view-all">Ver todas</a>
                        </div>
                        <div class="grades-container">
                            <div class="grade-item">
                                <div class="grade-subject">
                                    <h4>Matemática</h4>
                                    <p>Steeve Salvador - Prova Bimestral</p>
                                </div>
                                <div class="grade-value">
                                    <span class="grade good">8.5</span>
                                </div>
                            </div>
                            <div class="grade-item">
                                <div class="grade-subject">
                                    <h4>Português</h4>
                                    <p>Kelton Gonçalves - Trabalho</p>
                                </div>
                                <div class="grade-value">
                                    <span class="grade excellent">9.2</span>
                                </div>
                            </div>
                            <div class="grade-item">
                                <div class="grade-subject">
                                    <h4>História</h4>
                                    <p>Steeve Salvador - Seminário</p>
                                </div>
                                <div class="grade-value">
                                    <span class="grade good">8.0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Comunicados Recentes</h2>
                            <a href="comunicados.php" class="view-all">Ver todos</a>
                        </div>
                        <div class="announcements-list">
                            <div class="announcement-item">
                                <div class="announcement-icon">
                                    <span class="material-symbols-outlined">campaign</span>
                                </div>
                                <div class="announcement-content">
                                    <h4>Reunião de Pais - 9º Ano</h4>
                                    <p>Convocamos todos os pais do 9º ano para reunião sobre o projeto de formatura.</p>
                                    <span class="announcement-date">Há 2 horas</span>
                                </div>
                            </div>
                            <div class="announcement-item">
                                <div class="announcement-icon">
                                    <span class="material-symbols-outlined">event</span>
                                </div>
                                <div class="announcement-content">
                                    <h4>Festa Junina 2025</h4>
                                    <p>Venha participar da nossa tradicional festa junina no dia 25 de junho.</p>
                                    <span class="announcement-date">Ontem</span>
                                </div>
                            </div>
                            <div class="announcement-item">
                                <div class="announcement-icon">
                                    <span class="material-symbols-outlined">school</span>
                                </div>
                                <div class="announcement-content">
                                    <h4>Calendário de Provas</h4>
                                    <p>Confira o calendário atualizado das provas do 2º trimestre.</p>
                                    <span class="announcement-date">2 dias atrás</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

        // Add notification badge animation
        document.querySelector('.notification').addEventListener('click', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'bell-ring 0.5s ease-in-out';
            }, 10);
        });
    </script>
</body>
</html>