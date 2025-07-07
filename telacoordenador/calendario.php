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
            die("coordenador não identificado.");
        }

        try {
            // Buscar dados do coordenador incluindo área de atuação
            $stmt = $conn->prepare("SELECT fname, lname, area_coordenacao FROM coordenadores WHERE id = :id");
            $stmt->bindParam(':id', $idCoordinator, PDO::PARAM_INT);
            $stmt->execute();
            $coordinator = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$coordinator) {
                die("coordenador não encontrado.");
            }
        } catch (PDOException $e) {
            echo "Erro na consulta: " . $e->getMessage();
            exit;
        }

        // Definir turmas e horários baseados na área de atuação
        $areaAtuacao = $coordinator['area_coordenacao'];
        $turmasPermitidas = [];
        $disciplinasArea = [];
        $horariosArea = [];

        switch($areaAtuacao) {
            case 'I_ciclo':
                $turmasPermitidas = ['7A', '7B', '8A', '8B', '9A', '9B'];
                $disciplinasArea = ['Matemática', 'Português', 'História', 'Geografia', 'Ciências', 'Educação Física', 'Inglês', 'Artes'];
                $horariosArea = ['07:30-08:20', '08:20-09:10', '09:30-10:20', '10:20-11:10', '11:10-12:00'];
                break;
            case 'II_ciclo':
                $turmasPermitidas = ['10A', '10B', '11A', '11B', '12A', '12B'];
                $disciplinasArea = ['Matemática', 'Português', 'Física', 'Química', 'Biologia', 'História', 'Geografia', 'Filosofia', 'Sociologia', 'Inglês'];
                $horariosArea = ['07:30-08:20', '08:20-09:10', '09:30-10:20', '10:20-11:10', '11:10-12:00', '13:30-14:20', '14:20-15:10'];
                break;
            case 'PUNIV':
                $turmasPermitidas = ['PUNIV-A', 'PUNIV-B', 'PUNIV-C'];
                $disciplinasArea = ['Matemática', 'Português', 'Física', 'Química', 'Biologia', 'História', 'Geografia', 'Inglês', 'Preparação Universitária'];
                $horariosArea = ['07:30-08:20', '08:20-09:10', '09:30-10:20', '10:20-11:10', '11:10-12:00', '13:30-14:20', '14:20-15:10', '15:30-16:20'];
                break;
            case 'Tecnico':
                $turmasPermitidas = ['TEC-INFO-A', 'TEC-INFO-B', 'TEC-ADM-A', 'TEC-ENF-A'];
                $disciplinasArea = ['Informática Básica', 'Programação', 'Administração', 'Contabilidade', 'Enfermagem', 'Anatomia', 'Português Técnico', 'Matemática Aplicada'];
                $horariosArea = ['07:30-08:20', '08:20-09:10', '09:30-10:20', '10:20-11:10', '11:10-12:00', '13:30-14:20', '14:20-15:10', '15:30-16:20', '16:20-17:10'];
                break;
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
    <title>Calendário Acadêmico - Dashboard de Coordenadores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Estilos compactos para coordenação de calendário */
        .coordinator-area-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-left: 8px;
        }
        
        .area-I-ciclo {
            background-color: #e3f2fd;
            color: #1976d2;
            border: 1px solid #bbdefb;
        }
        
        .area-II-ciclo {
            background-color: #f3e5f5;
            color: #7b1fa2;
            border: 1px solid #e1bee7;
        }
        
        .area-PUNIV {
            background-color: #fff3e0;
            color: #f57c00;
            border: 1px solid #ffcc02;
        }
        
        .area-Tecnico {
            background-color: #e8f5e8;
            color: #388e3c;
            border: 1px solid #c8e6c9;
        }
        
        .calendar-overview {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .overview-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            position: relative;
            overflow: hidden;
        }
        
        .overview-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }
        
        .overview-card.classes::before {
            background: linear-gradient(90deg, #2196f3, #03a9f4);
        }
        
        .overview-card.assessments::before {
            background: linear-gradient(90deg, #f44336, #e91e63);
        }
        
        .overview-card.meetings::before {
            background: linear-gradient(90deg, #ff9800, #ffc107);
        }
        
        .overview-card.events::before {
            background: linear-gradient(90deg, #4caf50, #8bc34a);
        }
        
        .overview-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 3px;
            display: block;
        }
        
        .overview-number.classes { color: #2196f3; }
        .overview-number.assessments { color: #f44336; }
        .overview-number.meetings { color: #ff9800; }
        .overview-number.events { color: #4caf50; }
        
        .overview-label {
            font-size: 0.8rem;
            color: #666;
            font-weight: 500;
        }
        
        .overview-icon {
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 1.5rem;
            opacity: 0.3;
        }
        
        .calendar-container {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .calendar-main {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .calendar-nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .calendar-nav-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #e9ecef;
            border-radius: 50%;
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .calendar-nav-btn:hover {
            border-color: #3a5bb9;
            background-color: #f8f9ff;
        }
        
        .calendar-month {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            min-width: 150px;
            text-align: center;
        }
        
        .view-options {
            display: flex;
            gap: 5px;
        }
        
        .view-btn {
            padding: 6px 12px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
            color: #495057;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }
        
        .view-btn.active {
            background-color: #3a5bb9;
            color: white;
            border-color: #3a5bb9;
        }
        
        .view-btn:hover:not(.active) {
            background-color: #f8f9fa;
        }
        
        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            margin-bottom: 1px;
        }
        
        .calendar-weekday {
            text-align: center;
            font-weight: 600;
            padding: 8px 5px;
            background-color: #f8f9fa;
            color: #495057;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background-color: #e9ecef;
        }
        
        .calendar-day {
            min-height: 80px;
            background: white;
            padding: 6px;
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .calendar-day:hover {
            background-color: #f8f9ff;
        }
        
        .calendar-day.other-month {
            background-color: #f8f9fa;
            opacity: 0.6;
        }
        
        .calendar-day.weekend {
            background-color: #fff8f0;
        }
        
        .calendar-day.current-day {
            background-color: #e3f2fd;
            border: 2px solid #2196f3;
        }
        
        .calendar-day.has-events {
            border-left: 3px solid #3a5bb9;
        }
        
        .day-number {
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
            color: #333;
            font-size: 0.9rem;
        }
        
        .calendar-event {
            font-size: 0.65rem;
            padding: 2px 4px;
            margin-bottom: 2px;
            border-radius: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 2px solid;
        }
        
        .calendar-event:hover {
            transform: scale(1.02);
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        
        .calendar-event.aula {
            background-color: #e3f2fd;
            color: #1976d2;
            border-left-color: #2196f3;
        }
        
        .calendar-event.avaliacao {
            background-color: #ffebee;
            color: #c62828;
            border-left-color: #f44336;
        }
        
        .calendar-event.reuniao {
            background-color: #fff3e0;
            color: #ef6c00;
            border-left-color: #ff9800;
        }
        
        .calendar-event.evento {
            background-color: #e8f5e8;
            color: #2e7d32;
            border-left-color: #4caf50;
        }
        
        .calendar-event.feriado {
            background-color: #f3e5f5;
            color: #7b1fa2;
            border-left-color: #9c27b0;
        }
        
        .calendar-event.prazo {
            background-color: #fff8e1;
            color: #f57f17;
            border-left-color: #ffeb3b;
        }
        
        .calendar-sidebar {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .sidebar-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
        }
        
        .sidebar-card h3 {
            margin: 0 0 12px 0;
            color: #333;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .calendar-filters {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #495057;
            font-size: 0.8rem;
        }
        
        .filter-select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            background-color: #fff;
        }
        
        .filter-select:focus {
            outline: none;
            border-color: #3a5bb9;
            box-shadow: 0 0 0 2px rgba(58, 91, 185, 0.1);
        }
        
        .upcoming-events {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .event-item {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 8px;
            border-left: 3px solid;
            background-color: #f8f9fa;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .event-item:hover {
            transform: translateX(3px);
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        
        .event-item.aula {
            border-left-color: #2196f3;
            background-color: #e3f2fd;
        }
        
        .event-item.avaliacao {
            border-left-color: #f44336;
            background-color: #ffebee;
        }
        
        .event-item.reuniao {
            border-left-color: #ff9800;
            background-color: #fff3e0;
        }
        
        .event-item.evento {
            border-left-color: #4caf50;
            background-color: #e8f5e8;
        }
        
        .event-item.feriado {
            border-left-color: #9c27b0;
            background-color: #f3e5f5;
        }
        
        .event-date {
            font-size: 0.7rem;
            color: #666;
            margin-bottom: 3px;
            font-weight: 500;
        }
        
        .event-title {
            font-weight: 600;
            margin-bottom: 3px;
            color: #333;
            font-size: 0.85rem;
        }
        
        .event-details {
            font-size: 0.75rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .event-location, .event-class {
            display: flex;
            align-items: center;
            gap: 3px;
        }
        
        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .quick-action-btn {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: white;
            color: #495057;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: left;
        }
        
        .quick-action-btn:hover {
            background-color: #f8f9fa;
            border-color: #3a5bb9;
            transform: translateY(-1px);
        }
        
        .schedule-overview {
            margin-top: 20px;
        }
        
        .schedule-grid {
            display: grid;
            grid-template-columns: 80px repeat(5, 1fr);
            gap: 1px;
            background-color: #e9ecef;
            border-radius: 6px;
            overflow: hidden;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .schedule-header {
            background-color: #f8f9fa;
            padding: 10px 8px;
            text-align: center;
            font-weight: 600;
            color: #495057;
            font-size: 0.8rem;
        }
        
        .schedule-time {
            background-color: #f8f9fa;
            padding: 8px;
            text-align: center;
            font-size: 0.7rem;
            font-weight: 600;
            color: #666;
        }
        
        .schedule-cell {
            background: white;
            padding: 6px;
            min-height: 50px;
            position: relative;
            font-size: 0.7rem;
        }
        
        .schedule-class {
            background-color: #e3f2fd;
            color: #1976d2;
            padding: 3px 5px;
            border-radius: 3px;
            margin-bottom: 2px;
            font-weight: 500;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 3% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .modal-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
        
        .close {
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            color: #999;
        }
        
        .close:hover {
            color: #333;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3a5bb9;
            box-shadow: 0 0 0 2px rgba(58, 91, 185, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .btn-modal {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-primary {
            background-color: #3a5bb9;
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 20px;
        }
        
        @media (max-width: 1200px) {
            .calendar-container {
                grid-template-columns: 1fr;
            }
            
            .calendar-sidebar {
                order: -1;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .calendar-overview {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .calendar-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .calendar-nav {
                order: 2;
            }
            
            .view-options {
                order: 1;
            }
            
            .calendar-day {
                min-height: 60px;
            }
            
            .schedule-grid {
                grid-template-columns: 60px repeat(3, 1fr);
            }
            
            .calendar-sidebar {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .calendar-overview {
                grid-template-columns: 1fr;
            }
            
            .calendar-nav {
                flex-direction: column;
                gap: 8px;
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
                    <li>
<<<<<<< HEAD
                        <a href="professores.php">
                            <span class="material-symbols-outlined">group</span>
                            <span class="menu-text">Professores</span>
                        </a>
                    </li>
                    <li>
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
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
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar eventos..." id="searchInput">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Calendário - <?php echo $areaLabels[$areaAtuacao] ?? $areaAtuacao; ?></h1>
                    <div class="header-actions">
                        <button class="btn-action" onclick="exportarCalendario()">
                            <span class="material-symbols-outlined">file_download</span>
                            Exportar
                        </button>
                        <button class="btn-action btn-primary" onclick="abrirModalEvento()">
                            <span class="material-symbols-outlined">add</span>
                            Novo Evento
                        </button>
                    </div>
                </div>
                
                <!-- Visão Geral Compacta -->
                <div class="calendar-overview">
                    <div class="overview-card classes">
                        <span class="material-symbols-outlined overview-icon">school</span>
                        <span class="overview-number classes" id="classesCount">15</span>
                        <span class="overview-label">Aulas/Semana</span>
                    </div>
                    <div class="overview-card assessments">
                        <span class="material-symbols-outlined overview-icon">quiz</span>
                        <span class="overview-number assessments" id="assessmentsCount">3</span>
                        <span class="overview-label">Avaliações</span>
                    </div>
                    <div class="overview-card meetings">
                        <span class="material-symbols-outlined overview-icon">groups</span>
                        <span class="overview-number meetings" id="meetingsCount">2</span>
                        <span class="overview-label">Reuniões</span>
                    </div>
                    <div class="overview-card events">
                        <span class="material-symbols-outlined overview-icon">event</span>
                        <span class="overview-number events" id="eventsCount">8</span>
                        <span class="overview-label">Eventos</span>
                    </div>
                </div>
                
                <!-- Container Principal Compacto -->
                <div class="calendar-container">
                    <div class="calendar-main">
                        <div class="calendar-header">
                            <div class="calendar-nav">
                                <button class="calendar-nav-btn" onclick="navegarMes(-1)">
                                    <span class="material-symbols-outlined">chevron_left</span>
                                </button>
                                <div class="calendar-month" id="currentMonth">Abril 2025</div>
                                <button class="calendar-nav-btn" onclick="navegarMes(1)">
                                    <span class="material-symbols-outlined">chevron_right</span>
                                </button>
                            </div>
                            <div class="view-options">
                                <button class="view-btn active" onclick="alterarVisualizacao('mes')">Mês</button>
                                <button class="view-btn" onclick="alterarVisualizacao('semana')">Semana</button>
                            </div>
                        </div>
                        
                        <div class="calendar-weekdays">
                            <div class="calendar-weekday">Dom</div>
                            <div class="calendar-weekday">Seg</div>
                            <div class="calendar-weekday">Ter</div>
                            <div class="calendar-weekday">Qua</div>
                            <div class="calendar-weekday">Qui</div>
                            <div class="calendar-weekday">Sex</div>
                            <div class="calendar-weekday">Sáb</div>
                        </div>
                        
                        <div class="calendar-grid" id="calendarGrid">
                            <!-- Dias do calendário serão carregados dinamicamente -->
                        </div>
                    </div>
                    
                    <div class="calendar-sidebar">
                        <!-- Filtros Compactos -->
                        <div class="sidebar-card">
                            <h3>Filtros</h3>
                            <div class="calendar-filters">
                                <div class="filter-group">
                                    <label for="turma-filter">Turma</label>
                                    <select id="turma-filter" class="filter-select" onchange="filtrarEventos()">
                                        <option value="todas">Todas</option>
                                        <?php foreach($turmasPermitidas as $turma): ?>
                                            <option value="<?php echo $turma; ?>"><?php echo $turma; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="filter-group">
                                    <label for="tipo-filter">Tipo</label>
                                    <select id="tipo-filter" class="filter-select" onchange="filtrarEventos()">
                                        <option value="todos">Todos</option>
                                        <option value="aula">Aulas</option>
                                        <option value="avaliacao">Avaliações</option>
                                        <option value="reuniao">Reuniões</option>
                                        <option value="evento">Eventos</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Próximos Eventos Compactos -->
                        <div class="sidebar-card">
                            <h3>Próximos</h3>
                            <div class="upcoming-events" id="upcomingEvents">
                                <div class="event-item avaliacao">
                                    <div class="event-date">18 Abr, 09:00</div>
                                    <div class="event-title">Prova Matemática</div>
                                    <div class="event-details">
                                        <div class="event-class">9º A</div>
                                    </div>
                                </div>
                                <div class="event-item reuniao">
                                    <div class="event-date">20 Abr, 14:00</div>
                                    <div class="event-title">Conselho Classe</div>
                                    <div class="event-details">
                                        <div class="event-location">Sala Reuniões</div>
                                    </div>
                                </div>
                                <div class="event-item evento">
                                    <div class="event-date">25 Abr, 10:00</div>
                                    <div class="event-title">Feira Ciências</div>
                                    <div class="event-details">
                                        <div class="event-location">Pátio</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ações Rápidas -->
                        <div class="sidebar-card">
                            <h3>Ações</h3>
                            <div class="quick-actions">
                                <button class="quick-action-btn" onclick="agendarReuniao()">
                                    <span class="material-symbols-outlined">groups</span>
                                    Reunião
                                </button>
                                <button class="quick-action-btn" onclick="criarAvaliacao()">
                                    <span class="material-symbols-outlined">quiz</span>
                                    Avaliação
                                </button>
                                <button class="quick-action-btn" onclick="visualizarHorarios()">
                                    <span class="material-symbols-outlined">table_view</span>
                                    Horários
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Horários Compactos -->
                <div class="schedule-overview">
                    <div class="sidebar-card">
                        <h3>Horários </h3>
                        <div class="schedule-grid" id="scheduleGrid">
                            <div class="schedule-header">Hora</div>
                            <div class="schedule-header">Seg</div>
                            <div class="schedule-header">Ter</div>
                            <div class="schedule-header">Qua</div>
                            <div class="schedule-header">Qui</div>
                            <div class="schedule-header">Sex</div>
                            <!-- Horários serão carregados dinamicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Compacto -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Novo Evento</h2>
                <span class="close" onclick="fecharModal()">&times;</span>
            </div>
            <form id="eventForm">
                <div class="form-group">
                    <label for="event-title">Título</label>
                    <input type="text" id="event-title" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="event-type">Tipo</label>
                        <select id="event-type" class="form-control" required>
                            <option value="">Selecione</option>
                            <option value="aula">Aula</option>
                            <option value="avaliacao">Avaliação</option>
                            <option value="reuniao">Reunião</option>
                            <option value="evento">Evento</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="event-date">Data</label>
                        <input type="date" id="event-date" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="event-time">Horário</label>
                        <input type="time" id="event-time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="event-class">Turma</label>
                        <select id="event-class" class="form-control">
                            <option value="">Selecione</option>
                            <?php foreach($turmasPermitidas as $turma): ?>
                                <option value="<?php echo $turma; ?>"><?php echo $turma; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="event-location">Local</label>
                    <input type="text" id="event-location" class="form-control" placeholder="Ex: Sala 101">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-modal btn-secondary" onclick="fecharModal()">Cancelar</button>
                    <button type="submit" class="btn-modal btn-primary">
                        <span class="material-symbols-outlined">save</span>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Dados simulados baseados na área do coordenador
        const areaAtuacao = '<?php echo $areaAtuacao; ?>';
        const turmasPermitidas = <?php echo json_encode($turmasPermitidas); ?>;
        const disciplinasArea = <?php echo json_encode($disciplinasArea); ?>;
        const horariosArea = <?php echo json_encode($horariosArea); ?>;
        
        let mesAtual = new Date('2025-04-15');
        let visualizacaoAtual = 'mes';
        
        // Dados simulados de eventos
        const eventosSimulados = [
            {
                id: 1,
                titulo: 'Matemática',
                tipo: 'aula',
                data: '2025-04-15',
                hora_inicio: '08:00',
                turma: turmasPermitidas[0],
                local: 'Sala 101'
            },
            {
                id: 2,
                titulo: 'Prova Port.',
                tipo: 'avaliacao',
                data: '2025-04-18',
                hora_inicio: '09:00',
                turma: turmasPermitidas[0],
                local: 'Sala 102'
            },
            {
                id: 3,
                titulo: 'Conselho',
                tipo: 'reuniao',
                data: '2025-04-20',
                hora_inicio: '14:00',
                turma: 'Todas',
                local: 'Reuniões'
            }
        ];

        // Inicializar sistema
        function initCalendarSystem() {
            carregarCalendario();
            carregarHorarios();
            atualizarMesAtual();
        }

        function carregarCalendario() {
            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = '';
            
            const primeiroDia = new Date(mesAtual.getFullYear(), mesAtual.getMonth(), 1);
            const ultimoDia = new Date(mesAtual.getFullYear(), mesAtual.getMonth() + 1, 0);
            const diasAntes = primeiroDia.getDay();
            const diasMes = ultimoDia.getDate();
            
            // Dias do mês anterior
            for (let i = diasAntes - 1; i >= 0; i--) {
                const dia = new Date(primeiroDia);
                dia.setDate(dia.getDate() - i - 1);
                criarDiaCalendario(dia, true);
            }
            
            // Dias do mês atual
            for (let dia = 1; dia <= diasMes; dia++) {
                const data = new Date(mesAtual.getFullYear(), mesAtual.getMonth(), dia);
                criarDiaCalendario(data, false);
            }
            
            // Completar a grade
            const totalCelulas = grid.children.length;
            const celulasRestantes = 42 - totalCelulas;
            
            for (let i = 1; i <= celulasRestantes; i++) {
                const dia = new Date(ultimoDia);
                dia.setDate(dia.getDate() + i);
                criarDiaCalendario(dia, true);
            }
        }

        function criarDiaCalendario(data, outroMes) {
            const grid = document.getElementById('calendarGrid');
            const diaDiv = document.createElement('div');
            diaDiv.className = 'calendar-day';
            
            if (outroMes) diaDiv.classList.add('other-month');
            if (data.getDay() === 0 || data.getDay() === 6) diaDiv.classList.add('weekend');
            
            const hoje = new Date('2025-04-15');
            if (data.toDateString() === hoje.toDateString()) {
                diaDiv.classList.add('current-day');
            }
            
            const dataStr = data.toISOString().split('T')[0];
            const eventosData = eventosSimulados.filter(e => e.data === dataStr);
            
            if (eventosData.length > 0) {
                diaDiv.classList.add('has-events');
            }
            
            diaDiv.innerHTML = `
                <span class="day-number">${data.getDate()}</span>
                ${eventosData.map(evento => `
                    <div class="calendar-event ${evento.tipo}" onclick="verEvento(${evento.id})" title="${evento.titulo}">
                        ${evento.titulo}
                    </div>
                `).join('')}
            `;
            
            grid.appendChild(diaDiv);
        }

        function carregarHorarios() {
            const grid = document.getElementById('scheduleGrid');
            const horariosExistentes = grid.querySelectorAll('.schedule-time, .schedule-cell');
            horariosExistentes.forEach(el => el.remove());

            horariosArea.slice(0, 5).forEach(horario => { // Limitar a 5 horários
                const timeDiv = document.createElement('div');
                timeDiv.className = 'schedule-time';
                timeDiv.textContent = horario.split('-')[0];
                grid.appendChild(timeDiv);

                for (let dia = 0; dia < 5; dia++) {
                    const cellDiv = document.createElement('div');
                    cellDiv.className = 'schedule-cell';
                    
                    if (Math.random() > 0.7) {
                        const turmaAleatoria = turmasPermitidas[Math.floor(Math.random() * turmasPermitidas.length)];
                        const disciplinaAleatoria = disciplinasArea[Math.floor(Math.random() * disciplinasArea.length)];
                        
                        cellDiv.innerHTML = `
                            <div class="schedule-class">
                                ${disciplinaAleatoria.substring(0, 8)}<br>
                                <small>${turmaAleatoria}</small>
                            </div>
                        `;
                    }
                    
                    grid.appendChild(cellDiv);
                }
            });
        }

        function atualizarMesAtual() {
            const options = { year: 'numeric', month: 'long' };
            document.getElementById('currentMonth').textContent = 
                mesAtual.toLocaleDateString('pt-BR', options);
        }

        function navegarMes(direcao) {
            mesAtual.setMonth(mesAtual.getMonth() + direcao);
            carregarCalendario();
            atualizarMesAtual();
        }

        function alterarVisualizacao(tipo) {
            visualizacaoAtual = tipo;
            document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }

        function filtrarEventos() {
            carregarCalendario();
        }

        function verEvento(id) {
            const evento = eventosSimulados.find(e => e.id === id);
            if (evento) {
                alert(`${evento.titulo}\n${evento.data} - ${evento.hora_inicio}\n${evento.local}`);
            }
        }

        function abrirModalEvento() {
            document.getElementById('eventModal').style.display = 'block';
        }

        function fecharModal() {
            document.getElementById('eventModal').style.display = 'none';
            document.getElementById('eventForm').reset();
        }

        function exportarCalendario() {
            alert('Calendário exportado!');
        }

        function agendarReuniao() {
            abrirModalEvento();
            document.getElementById('event-type').value = 'reuniao';
        }

        function criarAvaliacao() {
            abrirModalEvento();
            document.getElementById('event-type').value = 'avaliacao';
        }

        function visualizarHorarios() {
            document.querySelector('.schedule-overview').scrollIntoView({ behavior: 'smooth' });
        }

        // Submissão do formulário
        document.getElementById('eventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const novoEvento = {
                id: eventosSimulados.length + 1,
                titulo: document.getElementById('event-title').value,
                tipo: document.getElementById('event-type').value,
                data: document.getElementById('event-date').value,
                hora_inicio: document.getElementById('event-time').value,
                turma: document.getElementById('event-class').value || 'Todas',
                local: document.getElementById('event-location').value
            };
            
            eventosSimulados.push(novoEvento);
            fecharModal();
            carregarCalendario();
            alert('Evento criado!');
        });

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('eventModal');
            if (event.target === modal) {
                fecharModal();
            }
        }

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            initCalendarSystem();
        });
    </script>
</body>
</html>