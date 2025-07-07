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

// Aqui pega o id do encarregado da sessão
$idGuardian = $_SESSION['id'] ?? null;

if (!$idGuardian) {
    die("Encarregado não identificado.");
}

try {
    // Busca informações do encarregado
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

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boletins - Dashboard Encarregados</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
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
            position: fixed;
            height: 100vh;
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
            position: sticky;
            top: 0;
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

        /* Filters */
        .filter-container {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-group label {
            font-size: 0.9rem;
            color: var(--text-light);
            white-space: nowrap;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.9rem;
            background-color: var(--white);
            color: var(--text-color);
            cursor: pointer;
            min-width: 150px;
        }

        /* Bulletins Grid */
        .bulletins-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .bulletin-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            transition: all 0.2s ease;
            overflow: hidden;
        }

        .bulletin-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .bulletin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .bulletin-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bulletin-icon .material-symbols-outlined {
            color: var(--primary-color);
            font-size: 28px;
        }

        .bulletin-status {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .bulletin-status.available {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }

        .bulletin-info h3 {
            margin-bottom: 5px;
            font-size: 1.1rem;
            word-break: break-word;
        }

        .bulletin-info p {
            color: var(--text-light);
            margin-bottom: 15px;
            word-break: break-word;
        }

        .bulletin-stats {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .bulletin-stats .stat {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bulletin-stats .label {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .bulletin-stats .value {
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        .bulletin-stats .value.excellent {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }

        .bulletin-stats .value.good {
            background-color: rgba(33, 150, 243, 0.1);
            color: var(--primary-color);
        }

        .bulletin-stats .value.approved {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }

        .bulletin-actions {
            display: flex;
            gap: 10px;
            margin: 20px 0 15px 0;
        }

        .bulletin-actions button {
            flex: 1;
        }

        .bulletin-date {
            font-size: 0.8rem;
            color: var(--text-light);
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }

        /* Performance Chart */
        .dashboard-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .chart-filters {
            display: flex;
            gap: 10px;
        }

        .chart-filters button {
            padding: 8px 12px;
            font-size: 0.85rem;
        }

        .chart-filters button.active {
            background-color: var(--primary-light);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .performance-chart {
            padding: 20px;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--gray-100);
            border-radius: 8px;
        }

        .chart-placeholder {
            text-align: center;
            color: var(--text-light);
        }

        .chart-placeholder .material-symbols-outlined {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        /* Modal styles */
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
            background-color: white;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
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

        /* Bulletin document styles */
        .bulletin-document {
            background-color: white;
            padding: 30px;
            font-family: 'Times New Roman', serif;
        }

        .bulletin-doc-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .school-info h2 {
            margin: 0;
            color: #333;
            font-size: 1.5rem;
        }

        .school-info p {
            margin: 5px 0;
            color: #666;
            font-size: 0.9rem;
        }

        .bulletin-title h1 {
            margin: 20px 0 10px 0;
            color: var(--primary-color);
            font-size: 1.8rem;
        }

        #bulletinPeriod {
            font-weight: bold;
            color: #333;
        }

        .student-info-section {
            margin: 20px 0;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }

        .info-item {
            flex: 1;
        }

        .info-item strong {
            color: #333;
        }

        .grades-section {
            margin: 30px 0;
        }

        .grades-section h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 1.1rem;
        }

        .bulletin-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 0.9rem;
        }

        .bulletin-table th,
        .bulletin-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .bulletin-table th {
            background-color: var(--secondary-color);
            font-weight: bold;
        }

        .grade-average {
            font-weight: bold;
        }

        .grade-average.excellent {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }

        .grade-average.good {
            background-color: rgba(33, 150, 243, 0.1);
            color: var(--primary-color);
        }

        .grade-average.average {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning-color);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-badge.approved {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }

        .status-badge.recovery {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning-color);
        }

        .bulletin-summary {
            margin: 30px 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-approved {
            color: var(--success-color);
            font-weight: bold;
        }

        .observations-section {
            margin: 30px 0;
        }

        .observation-item {
            margin: 15px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .observation-item strong {
            color: #333;
        }

        .observation-item p {
            margin: 5px 0 0 0;
        }

        .signatures-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
        }

        .signature-item {
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            width: 200px;
            margin-bottom: 10px;
        }

        .issue-date {
            text-align: right;
            margin-top: 30px;
            font-style: italic;
        }

        /* Download options */
        .download-options {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .download-option {
            display: flex;
            align-items: center;
            padding: 20px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            transition: all 0.2s;
        }

        .download-option:hover {
            border-color: var(--primary-color);
            background-color: rgba(74, 111, 220, 0.02);
        }

        .option-icon {
            margin-right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .option-icon .material-symbols-outlined {
            color: var(--primary-color);
            font-size: 28px;
        }

        .option-info {
            flex: 1;
            margin-right: 20px;
        }

        .option-info h4 {
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .option-info p {
            color: var(--text-light);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Progress modal */
        .progress-modal {
            max-width: 400px;
        }

        .progress-content {
            text-align: center;
            padding: 20px;
        }

        .progress-icon {
            margin-bottom: 20px;
        }

        .progress-icon .material-symbols-outlined {
            font-size: 48px;
            color: var(--primary-color);
        }

        .rotating {
            animation: rotate 2s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: var(--border-color);
            border-radius: 4px;
            overflow: hidden;
            margin: 20px 0;
        }

        .progress-fill {
            height: 100%;
            background-color: var(--primary-color);
            transition: width 0.3s ease;
            width: 0%;
        }

        /* Download notification */
        .download-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 20px;
            z-index: 1001;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            border-left: 4px solid var(--success-color);
        }

        .download-notification.show {
            transform: translateX(0);
        }

        .notification-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .notification-content .material-symbols-outlined {
            color: var(--success-color);
            font-size: 24px;
        }

        .notification-content h4 {
            margin-bottom: 3px;
            font-size: 0.95rem;
        }

        .notification-content p {
            color: var(--text-light);
            font-size: 0.85rem;
            margin: 0;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .filter-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .filter-select {
                flex-grow: 1;
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

            .bulletins-grid {
                grid-template-columns: 1fr;
            }

            .bulletin-actions {
                flex-direction: column;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .chart-filters {
                width: 100%;
                justify-content: space-between;
            }

            .bulletin-summary {
                grid-template-columns: 1fr;
            }

            .signatures-section {
                flex-direction: column;
                gap: 30px;
            }

            .download-option {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .option-info {
                margin-right: 0;
            }
        }

        @media (max-width: 576px) {
            .search-container {
                width: 150px;
            }

            .bulletin-table {
                font-size: 0.8rem;
            }
            
            .bulletin-table th,
            .bulletin-table td {
                padding: 6px 4px;
            }
            
            .info-row {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Pítruca Camama</h2>
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
                    <li>
                        <a href="mensagens.php">
                            <span class="material-symbols-outlined">chat</span>
                            <span class="menu-text">Mensagens</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="boletins.php">
                            <span class="material-symbols-outlined">description</span>
                            <span class="menu-text">Boletins</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
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
                    <input type="text" placeholder="Pesquisar boletins...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Boletins Escolares</h1>
                    <div class="header-actions">
                        <button class="btn-outline">
                            <span class="material-symbols-outlined">download</span>
                            Baixar Todos
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-container">
                    <div class="filter-group">
                        <label>Aluno:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>Kelton Gonçalves</option>
                            <option>Steeve Salvador</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Ano Letivo:</label>
                        <select class="filter-select">
                            <option>2025</option>
                            <option>2024</option>
                            <option>2023</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Período:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>1º Trimestre</option>
                            <option>2º Trimestre</option>
                            <option>3º Trimestre</option>
                            <option>Final</option>
                        </select>
                    </div>
                </div>

                <!-- Bulletins Grid -->
                <div class="bulletins-grid">
                    <div class="bulletin-card" data-student="joao" data-period="2025-2">
                        <div class="bulletin-header">
                            <div class="bulletin-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="bulletin-status available">
                                <span class="material-symbols-outlined">check_circle</span>
                                Disponível
                            </div>
                        </div>
                        <div class="bulletin-info">
                            <h3>Joao Francisco Francisco - 2º Trimestre 2025</h3>
                            <p>Ensino Fundamental</p>
                            <div class="bulletin-stats">
                                <div class="stat">
                                    <span class="label">Média Geral:</span>
                                    <span class="value good">8.7</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Frequência:</span>
                                    <span class="value excellent">96%</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Situação:</span>
                                    <span class="value approved">Aprovado</span>
                                </div>
                            </div>
                        </div>
                        <div class="bulletin-actions">
                            <button class="btn-primary view-bulletin">
                                <span class="material-symbols-outlined">visibility</span>
                                Visualizar
                            </button>
                            <button class="btn-outline download-bulletin">
                                <span class="material-symbols-outlined">download</span>
                                Baixar PDF
                            </button>
                        </div>
                        <div class="bulletin-date">
                            Gerado em: 15/04/2025
                        </div>
                    </div>
                    <div class="bulletin-card" data-student="ana" data-period="2025-2">
                        <div class="bulletin-header">
                            <div class="bulletin-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="bulletin-status available">
                                <span class="material-symbols-outlined">check_circle</span>
                                Disponível
                            </div>
                        </div>
                        <div class="bulletin-info">
                            <h3>Joana Francisco - 2º Trimestre 2025</h3>
                            <p>6º Ano B - Ensino Fundamental</p>
                            <div class="bulletin-stats">
                                <div class="stat">
                                    <span class="label">Média Geral:</span>
                                    <span class="value good">8.3</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Frequência:</span>
                                    <span class="value good">94%</span>
                                </div>
                               
                                <div class="stat">
                                    <span class="label">Situação:</span>
                                    <span class="value approved">Aprovado</span>
                                </div>
                            </div>
                        </div>
                        <div class="bulletin-actions">
                            <button class="btn-primary view-bulletin">
                                <span class="material-symbols-outlined">visibility</span>
                                Visualizar
                            </button>
                            <button class="btn-outline download-bulletin">
                                <span class="material-symbols-outlined">download</span>
                                Baixar PDF
                            </button>
                        </div>
                        <div class="bulletin-date">
                            Gerado em: 15/04/2025
                        </div>
                    </div>
                    <!--
                    <div class="bulletin-card" data-student="joao" data-period="2025-1">
                        <div class="bulletin-header">
                            <div class="bulletin-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="bulletin-status available">
                                <span class="material-symbols-outlined">check_circle</span>
                                Disponível
                            </div>
                        </div>
                        <div class="bulletin-info">
                            <h3>Kelton Gonçalves - 1º Trimestre 2025</h3>
                            <p>9º Steeve Salvador- Ensino Fundamental</p>
                            <div class="bulletin-stats">
                                <div class="stat">
                                    <span class="label">Média Geral:</span>
                                    <span class="value good">8.2</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Frequência:</span>
                                    <span class="value excellent">98%</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Situação:</span>
                                    <span class="value approved">Aprovado</span>
                                </div>
                            </div>
                        </div>
                        <div class="bulletin-actions">
                            <button class="btn-primary view-bulletin">
                                <span class="material-symbols-outlined">visibility</span>
                                Visualizar
                            </button>
                            <button class="btn-outline download-bulletin">
                                <span class="material-symbols-outlined">download</span>
                                Baixar PDF
                            </button>
                        </div>
                        <div class="bulletin-date">
                            Gerado em: 15/03/2025
                        </div>
                    </div>
    -->
                    <!--
                    <div class="bulletin-card" data-student="ana" data-period="2025-1">
                        <div class="bulletin-header">
                            <div class="bulletin-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="bulletin-status available">
                                <span class="material-symbols-outlined">check_circle</span>
                                Disponível
                            </div>
                        </div>
                        <div class="bulletin-info">
                            <h3>Steeve Salvador - 1º Trimestre 2025</h3>
                            <p>6º Ano B - Ensino Fundamental</p>
                            <div class="bulletin-stats">
                                <div class="stat">
                                    <span class="label">Média Geral:</span>
                                    <span class="value good">8.5</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Frequência:</span>
                                    <span class="value good">92%</span>
                                </div>


                                                              <div class="stat">
                                    <span class="label">Situação:</span>
                                    <span class="value approved">Aprovado</span>
                                </div>
                            </div>
                        </div>
                        <div class="bulletin-actions">
                            <button class="btn-primary view-bulletin">
                                <span class="material-symbols-outlined">visibility</span>
                                Visualizar
                            </button>
                            <button class="btn-outline download-bulletin">
                                <span class="material-symbols-outlined">download</span>
                                Baixar PDF
                            </button>
                        </div>
                        <div class="bulletin-date">
                            Gerado em: 15/04/2025
                        </div>
                    </div>
                </div>
    -->
                <!-- Performance Chart 
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>Desempenho dos Alunos</h2>
                        <div class="chart-filters">
                            <button class="btn-outline active">Geral</button>
                            <button class="btn-outline">Português</button>
                            <button class="btn-outline">Matemática</button>
                            <button class="btn-outline">História</button>
                        </div>
                    </div>
                    <div class="performance-chart">
                        <div class="chart-placeholder">
                            <span class="material-symbols-outlined">bar_chart</span>
                            <p>Gráfico de desempenho dos alunos</p>
                        </div>
                    </div>
                </div>
                -->
            </div>
        </main>
    </div>

    <!-- Bulletin Modal -->
<div class="modal" id="bulletinModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Boletim Escolar</h3>
            <span class="modal-close">&times;</span>
        </div>

        <div class="modal-body">
            <div class="bulletin-document">
                <div class="bulletin-doc-header">
                    <div class="school-info">
                        <h2>ESCOLA PITRUCA CAMAMA</h2>
                        <p>Bairro BPC - Luanda, Angola</p>
                        <p>Telefone: 222 333 444 | Email: info@pitruca.edu.ao</p>
                    </div>
                    <div class="bulletin-title">
                        <h1>BOLETIM ESCOLAR</h1>
                        <p id="bulletinPeriod"><?php echo $trimestre . ' - ' . $anoLetivo; ?></p>
                    </div>
                </div>

                <div class="student-info-section">
                    <div class="info-row">
                        <div class="info-item">
                            <strong>Nome do Aluno:</strong> Joao Francisco Francisco
                        </div>
                        <div class="info-item">
                            <strong>Nº de Matrícula:</strong> 20250015
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-item">
                            <strong>Turma:</strong> 9º Ano A
                        </div>
                        <div class="info-item">
                            <strong>Turno:</strong> Manhã
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-item">
                            <strong>Encarregado:</strong> <?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?>
                        </div>
                        <div class="info-item">
                            <strong>Contacto:</strong> 923 456 789
                        </div>
                    </div>
                </div>

                <div class="grades-section">
                    <h3>Resultados Escolares</h3>
                    <table class="bulletin-table">
                        <thead>
                            <tr>
                                <th rowspan="2">Disciplina</th>
                                <th colspan="3">Avaliações</th>
                                <th rowspan="2">Média</th>
                                <th rowspan="2">Faltas</th>
                                <th rowspan="2">Situação</th>
                            </tr>
                            <tr>
                                <th>1ª</th>
                                <th>2ª</th>
                                <th>3ª</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Português</td>
                                <td>8.5</td>
                                <td>9.0</td>
                                <td>8.0</td>
                                <td class="grade-average good">8.5</td>
                                <td>2</td>
                                <td><span class="status-badge approved">Aprovado</span></td>
                            </tr>
                            <tr>
                                <td>Matemática</td>
                                <td>9.0</td>
                                <td>8.5</td>
                                <td>9.5</td>
                                <td class="grade-average excellent">9.0</td>
                                <td>1</td>
                                <td><span class="status-badge approved">Aprovado</span></td>
                            </tr>
                            <tr>
                                <td>História</td>
                                <td>7.5</td>
                                <td>8.0</td>
                                <td>7.0</td>
                                <td class="grade-average good">7.5</td>
                                <td>0</td>
                                <td><span class="status-badge approved">Aprovado</span></td>
                            </tr>
                            <tr>
                                <td>Geografia</td>
                                <td>8.0</td>
                                <td>8.5</td>
                                <td>9.0</td>
                                <td class="grade-average good">8.5</td>
                                <td>1</td>
                                <td><span class="status-badge approved">Aprovado</span></td>
                            </tr>
                            <tr>
                                <td>Ciências</td>
                                <td>9.5</td>
                                <td>9.0</td>
                                <td>8.5</td>
                                <td class="grade-average excellent">9.0</td>
                                <td>0</td>
                                <td><span class="status-badge approved">Aprovado</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bulletin-summary">
                    <div class="summary-item">
                        <span>Média Geral:</span>
                        <strong>8.7</strong>
                    </div>
                    <div class="summary-item">
                        <span>Frequência:</span>
                        <strong>96%</strong>
                    </div>
                    <div class="summary-item">
                        <span>Situação Final:</span>
                        <strong class="status-approved">Aprovado</strong>
                    </div>
                    <div class="summary-item">
                        <span>Posição na Turma:</span>
                        <strong>5º de 30 alunos</strong>
                    </div>
                </div>

                <div class="observations-section">
                    <h3>Observações</h3>
                    <div class="observation-item">
                        <strong>Comportamento:</strong>
                        <p>O aluno demonstra excelente comportamento em sala de aula, sendo respeitoso com colegas e professores.</p>
                    </div>
                    <div class="observation-item">
                        <strong>Desempenho:</strong>
                        <p>Bom desempenho geral, com destaque para Matemática e Ciências. Pode melhorar em História.</p>
                    </div>
                </div>

                <div class="signatures-section">
                    <div class="signature-item">
                        <div class="signature-line"></div>
                        <p>Professor Responsável</p>
                    </div>
                    <div class="signature-item">
                        <div class="signature-line"></div>
                        <p>Director de Turma</p>
                    </div>
                </div>

                <div class="issue-date">
                    Luanda, <?php echo $dataAtual; ?>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-outline" id="printBulletin">
                <span class="material-symbols-outlined">print</span>
                Imprimir
            </button>
            <button class="btn-primary" id="downloadModalBulletin">
                <span class="material-symbols-outlined">download</span>
                Baixar PDF
            </button>
        </div>
    </div>
</div>


    <!-- Download Progress Modal -->
    <div class="modal" id="downloadProgressModal">
        <div class="modal-content progress-modal">
            <div class="progress-content">
                <div class="progress-icon">
                    <span class="material-symbols-outlined rotating">download</span>
                </div>
                <h3>Preparando o boletim para download</h3>
                <p>Por favor aguarde enquanto geramos o PDF...</p>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Notification -->
    <div class="download-notification" id="downloadNotification">
        <div class="notification-content">
            <span class="material-symbols-outlined">check_circle</span>
            <div>
                <h4>Download concluído com sucesso!</h4>
                <p>O boletim foi baixado para a sua pasta de downloads.</p>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById('bulletinModal');
        const closeModal = document.querySelector('.modal-close');
        const viewButtons = document.querySelectorAll('.view-bulletin');
        const downloadButtons = document.querySelectorAll('.download-bulletin');
        const downloadModalBulletin = document.getElementById('downloadModalBulletin');
        const printBulletin = document.getElementById('printBulletin');
        const downloadProgressModal = document.getElementById('downloadProgressModal');
        const downloadNotification = document.getElementById('downloadNotification');
        const progressFill = document.getElementById('progressFill');

        // Open modal when view button is clicked
        viewButtons.forEach(button => {
            button.addEventListener('click', () => {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        });

        // Close modal
        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

        // Print bulletin
        printBulletin.addEventListener('click', () => {
            window.print();
        });

        // Download bulletin - both from card and modal
        function downloadBulletin() {
            // Show progress modal
            downloadProgressModal.style.display = 'flex';
            
            // Simulate progress
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                progressFill.style.width = `${progress}%`;
                
                if (progress >= 100) {
                    clearInterval(interval);
                    downloadProgressModal.style.display = 'none';
                    progressFill.style.width = '0%';
                    
                    // Show download notification
                    downloadNotification.classList.add('show');
                    setTimeout(() => {
                        downloadNotification.classList.remove('show');
                    }, 3000);
                    
                    // In a real application, this would trigger the actual PDF download
                    // For demo purposes, we'll simulate it
                    console.log('Downloading bulletin PDF...');
                    
                    // In a real implementation, you would:
                    // 1. Generate the PDF on the server
                    // 2. Return the PDF file for download
                    // Example:
                    // window.location.href = 'generate_pdf.php?student=joao&period=2025-2';
                }
            }, 200);
        }

        // Attach download function to all download buttons
        downloadButtons.forEach(button => {
            button.addEventListener('click', downloadBulletin);
        });

        downloadModalBulletin.addEventListener('click', downloadBulletin);
    </script>
</body>
</html>