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

        // Definir turmas baseadas na área de atuação
        $areaAtuacao = $coordinator['area_coordenacao'];
        $turmasPermitidas = [];
        $disciplinasArea = [];

        switch($areaAtuacao) {
            case 'I_ciclo':
                $turmasPermitidas = ['7A', '7B', '8A', '8B', '9A', '9B'];
                $disciplinasArea = ['Matemática', 'Português', 'História', 'Geografia', 'Ciências', 'Educação Física', 'Inglês', 'Artes'];
                break;
            case 'II_ciclo':
                $turmasPermitidas = ['10A', '10B', '11A', '11B', '12A', '12B'];
                $disciplinasArea = ['Matemática', 'Português', 'Física', 'Química', 'Biologia', 'História', 'Geografia', 'Filosofia', 'Sociologia', 'Inglês'];
                break;
            case 'PUNIV':
                $turmasPermitidas = ['PUNIV-A', 'PUNIV-B', 'PUNIV-C'];
                $disciplinasArea = ['Matemática', 'Português', 'Física', 'Química', 'Biologia', 'História', 'Geografia', 'Inglês', 'Preparação Universitária'];
                break;
            case 'Tecnico':
                $turmasPermitidas = ['TEC-INFO-A', 'TEC-INFO-B', 'TEC-ADM-A', 'TEC-ENF-A'];
                $disciplinasArea = ['Informática Básica', 'Programação', 'Administração', 'Contabilidade', 'Enfermagem', 'Anatomia', 'Português Técnico', 'Matemática Aplicada'];
                break;
        }

        /* Buscar dados de presença dos alunos da área
        $presencaAlunos = [];
        try {
            $placeholders = str_repeat('?,', count($turmasPermitidas) - 1) . '?';
            $stmt = $conn->prepare("
                SELECT 
                    e.id as estudante_id,
                    e.fname,
                    e.lname,
                    e.turma,
                    p.data_aula,
                    p.disciplina,
                    p.status_presenca,
                    p.observacoes,
                    p.justificativa
                FROM estudantes e 
                LEFT JOIN presenca p ON e.id = p.estudante_id 
                WHERE e.turma IN ($placeholders)
                ORDER BY e.turma, e.fname, p.data_aula DESC
            ");
            $stmt->execute($turmasPermitidas);
            $presencaAlunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar presenças: " . $e->getMessage();
        }
*/
        $dataAtual = '15 de Abril de 2025';
        $trimestre = '2º trimestre';
        $anoLetivo = '2025';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Presença - Dashboard de Coordenadores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Estilos específicos para coordenação de presença */
        .coordinator-area-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 10px;
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
        
        .attendance-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .overview-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
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
            height: 4px;
        }
        
        .overview-card.present::before {
            background: linear-gradient(90deg, #4caf50, #8bc34a);
        }
        
        .overview-card.absent::before {
            background: linear-gradient(90deg, #f44336, #e91e63);
        }
        
        .overview-card.justified::before {
            background: linear-gradient(90deg, #ff9800, #ffc107);
        }
        
        .overview-card.percentage::before {
            background: linear-gradient(90deg, #2196f3, #03a9f4);
        }
        
        .overview-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: block;
        }
        
        .overview-number.present { color: #4caf50; }
        .overview-number.absent { color: #f44336; }
        .overview-number.justified { color: #ff9800; }
        .overview-number.percentage { color: #2196f3; }
        
        .overview-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }
        
        .overview-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2rem;
            opacity: 0.3;
        }
        
        .attendance-filters {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }
        
        .filter-select, .date-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            background-color: #fff;
        }
        
        .filter-select:focus, .date-input:focus {
            outline: none;
            border-color: #3a5bb9;
            box-shadow: 0 0 0 3px rgba(58, 91, 185, 0.1);
        }
        
        .date-navigation {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .date-nav-btn {
            width: 40px;
            height: 40px;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .date-nav-btn:hover {
            border-color: #3a5bb9;
            background-color: #f8f9ff;
        }
        
        .current-date {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            min-width: 200px;
            text-align: center;
        }
        
        .attendance-table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        
        .attendance-table thead {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        
        .attendance-table th {
            padding: 18px 15px;
            text-align: center;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .attendance-table th.student-col {
            text-align: left;
            min-width: 200px;
        }
        
        .attendance-table td {
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
            vertical-align: middle;
            text-align: center;
        }
        
        .attendance-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .attendance-table tbody tr:hover {
            background-color: #f8f9ff;
        }
        
        .student-info {
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
        }
        
        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        
        .student-details h4 {
            margin: 0 0 2px 0;
            color: #333;
            font-size: 0.95rem;
            font-weight: 600;
        }
        
        .student-class {
            color: #666;
            font-size: 0.8rem;
        }
        
        .attendance-radio-group {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .attendance-radio {
            position: relative;
            cursor: pointer;
        }
        
        .attendance-radio input[type="radio"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }
        
        .attendance-radio .radio-custom {
            width: 24px;
            height: 24px;
            border: 2px solid #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .attendance-radio input[type="radio"]:checked + .radio-custom {
            border-color: #3a5bb9;
            background-color: #3a5bb9;
        }
        
        .attendance-radio input[type="radio"]:checked + .radio-custom::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: white;
        }
        
        .attendance-radio.present input[type="radio"]:checked + .radio-custom {
            border-color: #4caf50;
            background-color: #4caf50;
        }
        
        .attendance-radio.absent input[type="radio"]:checked + .radio-custom {
            border-color: #f44336;
            background-color: #f44336;
        }
        
        .attendance-radio.justified input[type="radio"]:checked + .radio-custom {
            border-color: #ff9800;
            background-color: #ff9800;
        }
        
        .attendance-percentage {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .percentage-high {
            background-color: #e8f5e8;
            color: #2e7d32;
        }
        
        .percentage-medium {
            background-color: #fff3e0;
            color: #f57c00;
        }
        
        .percentage-low {
            background-color: #ffebee;
            color: #d32f2f;
        }
        
        .attendance-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        
        .action-group {
            display: flex;
            gap: 10px;
        }
        
        .btn-action {
            padding: 10px 16px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: white;
            color: #495057;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-action:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
        }
        
        .btn-primary {
            background-color: #3a5bb9;
            color: white;
            border-color: #3a5bb9;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
            border-color: #ffc107;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }
        
        .attendance-analytics {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }
        
        .analytics-chart {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
        
        .alerts-panel {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        
        .alert-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid;
        }
        
        .alert-critical {
            background-color: #ffebee;
            border-left-color: #f44336;
        }
        
        .alert-warning {
            background-color: #fff3e0;
            border-left-color: #ff9800;
        }
        
        .alert-info {
            background-color: #e3f2fd;
            border-left-color: #2196f3;
        }
        
        .alert-icon {
            font-size: 1.2rem;
        }
        
        .alert-content h4 {
            margin: 0 0 4px 0;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .alert-content p {
            margin: 0;
            font-size: 0.8rem;
            color: #666;
        }
        
        .monthly-report {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-top: 20px;
            border: 1px solid #f0f0f0;
        }
        
        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .report-item {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        
        .report-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: block;
        }
        
        .report-label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state .material-symbols-outlined {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            margin-bottom: 10px;
            color: #495057;
        }
        
        @media (max-width: 768px) {
            .attendance-overview {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .attendance-table-container {
                overflow-x: auto;
            }
            
            .attendance-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .action-group {
                justify-content: center;
            }
            
            .attendance-analytics {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .attendance-overview {
                grid-template-columns: 1fr;
            }
            
            .date-navigation {
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
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar alunos e presenças..." id="searchInput">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Controle de Presença - <?php echo $areaLabels[$areaAtuacao] ?? $areaAtuacao; ?></h1>
                    <div class="header-actions">
                        <button class="btn-action" onclick="exportarRelatorio()">
                            <span class="material-symbols-outlined">file_download</span>
                            Exportar Relatório
                        </button>
                        <button class="btn-action btn-warning" onclick="enviarAlertas()">
                            <span class="material-symbols-outlined">notification_important</span>
                            Enviar Alertas
                        </button>
                        <button class="btn-action btn-primary" onclick="salvarPresencas()">
                            <span class="material-symbols-outlined">save</span>
                            Salvar Presenças
                        </button>
                    </div>
                </div>
                
                <!-- Visão Geral da Presença -->
                <div class="attendance-overview">
                    <div class="overview-card present">
                        <span class="material-symbols-outlined overview-icon">check_circle</span>
                        <span class="overview-number present" id="presentCount">0</span>
                        <span class="overview-label">Presenças Hoje</span>
                    </div>
                    <div class="overview-card absent">
                        <span class="material-symbols-outlined overview-icon">cancel</span>
                        <span class="overview-number absent" id="absentCount">0</span>
                        <span class="overview-label">Faltas Hoje</span>
                    </div>
                    <div class="overview-card justified">
                        <span class="material-symbols-outlined overview-icon">event_note</span>
                        <span class="overview-number justified" id="justifiedCount">0</span>
                        <span class="overview-label">Faltas Justificadas</span>
                    </div>
                    <div class="overview-card percentage">
                        <span class="material-symbols-outlined overview-icon">trending_up</span>
                        <span class="overview-number percentage" id="attendancePercentage">0%</span>
                        <span class="overview-label">Taxa de Presença</span>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="attendance-filters">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label for="turma-filter">Turma</label>
                            <select id="turma-filter" class="filter-select" onchange="filtrarPresencas()">
                                <option value="todas">Todas as Turmas</option>
                                <?php foreach($turmasPermitidas as $turma): ?>
                                    <option value="<?php echo $turma; ?>"><?php echo $turma; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="disciplina-filter">Disciplina</label>
                            <select id="disciplina-filter" class="filter-select" onchange="filtrarPresencas()">
                                <option value="todas">Todas as Disciplinas</option>
                                <?php foreach($disciplinasArea as $disciplina): ?>
                                    <option value="<?php echo $disciplina; ?>"><?php echo $disciplina; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="data-filter">Data</label>
                            <input type="date" id="data-filter" class="date-input" value="2025-04-15" onchange="filtrarPresencas()">
                        </div>
                        <div class="filter-group">
                            <label for="periodo-filter">Período</label>
                            <select id="periodo-filter" class="filter-select" onchange="filtrarPresencas()">
                                <option value="hoje">Hoje</option>
                                <option value="semana">Esta Semana</option>
                                <option value="mes">Este Mês</option>
                                <option value="trimestre">Este Trimestre</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Navegação de Data -->
                <div class="date-navigation">
                    <button class="date-nav-btn" onclick="navegarData(-1)">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <div class="current-date" id="currentDate">15 de Abril de 2025</div>
                    <button class="date-nav-btn" onclick="navegarData(1)">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
                
                <!-- Tabela de Presença -->
                <div class="attendance-table-container">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th class="student-col">Aluno</th>
                                <th>Presente</th>
                                <th>Ausente</th>
                                <th>Justificado</th>
                                <th>% Presença</th>
                                <th>Observações</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <!-- Presenças serão carregadas dinamicamente -->
                        </tbody>
                    </table>
                    
                    <div class="attendance-actions">
                        <div class="action-group">
                            <button class="btn-action" onclick="marcarTodosPresentes()">
                                <span class="material-symbols-outlined">check_box</span>
                                Marcar Todos Presentes
                            </button>
                            <button class="btn-action" onclick="importarPresencas()">
                                <span class="material-symbols-outlined">upload</span>
                                Importar Presenças
                            </button>
                        </div>
                        <div class="action-group">
                            <button class="btn-action btn-warning" onclick="gerarRelatorioFaltas()">
                                <span class="material-symbols-outlined">warning</span>
                                Relatório de Faltas
                            </button>
                            <button class="btn-action btn-success" onclick="aprovarPresencas()">
                                <span class="material-symbols-outlined">check</span>
                                Aprovar Presenças
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Analytics e Alertas -->
                <div class="attendance-analytics">
                    <div class="analytics-chart">
                        <div class="chart-header">
                            <h3 class="chart-title">Frequência por Turma</h3>
                            <select class="filter-select" style="width: auto;" onchange="atualizarGrafico()">
                                <option value="semana">Esta Semana</option>
                                <option value="mes">Este Mês</option>
                                <option value="trimestre">Este Trimestre</option>
                            </select>
                        </div>
                        <div id="frequencyChart">
                            <!-- Gráfico será implementado aqui -->
                            <div class="empty-state">
                                <span class="material-symbols-outlined">bar_chart</span>
                                <h3>Gráfico de Frequência</h3>
                                <p>Visualização da frequência por turma será exibida aqui</p>
                            </div>
                        </div>
                    </div>

                    <div class="alerts-panel">
                        <h3>Alertas de Frequência</h3>
                        <div id="attendanceAlerts">
                            <div class="alert-item alert-critical">
                                <span class="material-symbols-outlined alert-icon" style="color: #f44336;">error</span>
                                <div class="alert-content">
                                    <h4>Frequência Crítica</h4>
                                    <p>3 alunos com menos de 75% de presença</p>
                                </div>
                            </div>
                            <div class="alert-item alert-warning">
                                <span class="material-symbols-outlined alert-icon" style="color: #ff9800;">warning</span>
                                <div class="alert-content">
                                    <h4>Atenção Necessária</h4>
                                    <p>5 alunos com faltas consecutivas</p>
                                </div>
                            </div>
                            <div class="alert-item alert-info">
                                <span class="material-symbols-outlined alert-icon" style="color: #2196f3;">info</span>
                                <div class="alert-content">
                                    <h4>Melhoria</h4>
                                    <p>Taxa geral de presença: 87%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relatório Mensal -->
                <div class="monthly-report">
                    <h3>Relatório Mensal - Abril 2025</h3>
                    <div class="report-grid">
                        <div class="report-item">
                            <span class="report-number" style="color: #4caf50;">87%</span>
                            <span class="report-label">Taxa Geral</span>
                        </div>
                        <div class="report-item">
                            <span class="report-number" style="color: #2196f3;">1,245</span>
                            <span class="report-label">Total Presenças</span>
                        </div>
                        <div class="report-item">
                            <span class="report-number" style="color: #f44336;">187</span>
                            <span class="report-label">Total Faltas</span>
                        </div>
                        <div class="report-item">
                            <span class="report-number" style="color: #ff9800;">45</span>
                            <span class="report-label">Justificadas</span>
                        </div>
                        <div class="report-item">
                            <span class="report-number" style="color: #9c27b0;">12</span>
                            <span class="report-label">Alunos em Risco</span>
                        </div>
                        <div class="report-item">
                            <span class="report-number" style="color: #607d8b;">20</span>
                            <span class="report-label">Dias Letivos</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Dados simulados baseados na área do coordenador
        const areaAtuacao = '<?php echo $areaAtuacao; ?>';
        const turmasPermitidas = <?php echo json_encode($turmasPermitidas); ?>;
        const disciplinasArea = <?php echo json_encode($disciplinasArea); ?>;
        
        // Dados simulados de presença
        const presencaSimulada = [
            {
                estudante_id: 1,
                nome: 'Ana Beatriz Silva',
                turma: turmasPermitidas[0],
                presente: true,
                ausente: false,
                justificado: false,
                percentual_presenca: 92,
                observacoes: ''
            },
            {
                estudante_id: 2,
                nome: 'Bruno Santos Costa',
                turma: turmasPermitidas[0],
                presente: false,
                ausente: true,
                justificado: false,
                percentual_presenca: 78,
                observacoes: 'Falta não justificada'
            },
            {
                estudante_id: 3,
                nome: 'Carla Oliveira Mendes',
                turma: turmasPermitidas[1] || turmasPermitidas[0],
                presente: false,
                ausente: false,
                justificado: true,
                percentual_presenca: 85,
                observacoes: 'Atestado médico'
            }
        ];

        let dataAtual = new Date('2025-04-15');

        // Inicializar sistema
        function initAttendanceSystem() {
            carregarPresencas();
            atualizarEstatisticas();
            atualizarDataAtual();
        }

        function carregarPresencas() {
            const tbody = document.getElementById('attendanceTableBody');
            tbody.innerHTML = '';

            presencaSimulada.forEach((presenca, index) => {
                const row = document.createElement('tr');
                
                // Determinar classe do percentual
                let percentualClass = 'percentage-low';
                if (presenca.percentual_presenca >= 85) percentualClass = 'percentage-high';
                else if (presenca.percentual_presenca >= 75) percentualClass = 'percentage-medium';

                row.innerHTML = `
                    <td>
                        <div class="student-info">
                            <div class="student-avatar">
                                ${presenca.nome.split(' ').map(n => n[0]).join('').substring(0, 2)}
                            </div>
                            <div class="student-details">
                                <h4>${presenca.nome}</h4>
                                <div class="student-class">${presenca.turma}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="attendance-radio-group">
                            <label class="attendance-radio present">
                                <input type="radio" name="attendance-${index}" value="presente" ${presenca.presente ? 'checked' : ''} onchange="atualizarPresenca(${index}, 'presente')">
                                <div class="radio-custom"></div>
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="attendance-radio-group">
                            <label class="attendance-radio absent">
                                <input type="radio" name="attendance-${index}" value="ausente" ${presenca.ausente ? 'checked' : ''} onchange="atualizarPresenca(${index}, 'ausente')">
                                <div class="radio-custom"></div>
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="attendance-radio-group">
                            <label class="attendance-radio justified">
                                <input type="radio" name="attendance-${index}" value="justificado" ${presenca.justificado ? 'checked' : ''} onchange="atualizarPresenca(${index}, 'justificado')">
                                <div class="radio-custom"></div>
                            </label>
                        </div>
                    </td>
                    <td>
                        <span class="attendance-percentage ${percentualClass}">${presenca.percentual_presenca}%</span>
                    </td>
                    <td>
                        <input type="text" class="form-control" value="${presenca.observacoes}" placeholder="Observações..." onchange="atualizarObservacao(${index}, this.value)" style="width: 150px; padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                    </td>
                    <td>
                        <button class="btn-action" onclick="verHistoricoAluno(${presenca.estudante_id})" title="Ver histórico">
                            <span class="material-symbols-outlined">history</span>
                        </button>
                    </td>
                `;
                
                tbody.appendChild(row);
            });
        }

        function atualizarEstatisticas() {
            const presentes = presencaSimulada.filter(p => p.presente).length;
            const ausentes = presencaSimulada.filter(p => p.ausente).length;
            const justificados = presencaSimulada.filter(p => p.justificado).length;
            const total = presencaSimulada.length;
            const percentual = total > 0 ? Math.round((presentes / total) * 100) : 0;

            document.getElementById('presentCount').textContent = presentes;
            document.getElementById('absentCount').textContent = ausentes;
            document.getElementById('justifiedCount').textContent = justificados;
            document.getElementById('attendancePercentage').textContent = percentual + '%';
        }

        function atualizarDataAtual() {
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                weekday: 'long'
            };
            document.getElementById('currentDate').textContent = dataAtual.toLocaleDateString('pt-BR', options);
        }

        function navegarData(direcao) {
            dataAtual.setDate(dataAtual.getDate() + direcao);
            atualizarDataAtual();
            // Aqui você carregaria os dados da nova data
            console.log('Carregando dados para:', dataAtual.toISOString().split('T')[0]);
        }

        function atualizarPresenca(index, status) {
            // Resetar todos os status
            presencaSimulada[index].presente = false;
            presencaSimulada[index].ausente = false;
            presencaSimulada[index].justificado = false;
            
            // Definir o status selecionado
            presencaSimulada[index][status] = true;
            
            atualizarEstatisticas();
        }

        function atualizarObservacao(index, observacao) {
            presencaSimulada[index].observacoes = observacao;
        }

        function filtrarPresencas() {
            // Implementar filtros
            console.log('Aplicando filtros de presença...');
        }

        function marcarTodosPresentes() {
            if (confirm('Marcar todos os alunos como presentes?')) {
                presencaSimulada.forEach((presenca, index) => {
                    presenca.presente = true;
                    presenca.ausente = false;
                    presenca.justificado = false;
                });
                carregarPresencas();
                atualizarEstatisticas();
                alert('Todos os alunos marcados como presentes!');
            }
        }

        function exportarRelatorio() {
            alert('Relatório de presença exportado com sucesso!');
        }

        function enviarAlertas() {
            const alunosRisco = presencaSimulada.filter(p => p.percentual_presenca < 75).length;
            if (confirm(`Enviar alertas para ${alunosRisco} alunos com baixa frequência?`)) {
                alert('Alertas enviados com sucesso!');
            }
        }

        function salvarPresencas() {
            alert('Presenças salvas com sucesso!');
        }

        function importarPresencas() {
            alert('Funcionalidade de importação será implementada');
        }

        function gerarRelatorioFaltas() {
            alert('Relatório de faltas gerado com sucesso!');
        }

        function aprovarPresencas() {
            if (confirm('Aprovar todas as presenças do dia atual?')) {
                alert('Presenças aprovadas com sucesso!');
            }
        }

        function verHistoricoAluno(id) {
            alert(`Ver histórico de presença do aluno ID: ${id}`);
        }

        function atualizarGrafico() {
            console.log('Atualizando gráfico de frequência...');
        }

        // Busca
        document.getElementById('searchInput').addEventListener('input', function() {
            const termo = this.value.toLowerCase();
            const rows = document.querySelectorAll('#attendanceTableBody tr');
            
            rows.forEach(row => {
                const nome = row.querySelector('.student-details h4').textContent.toLowerCase();
                const turma = row.querySelector('.student-class').textContent.toLowerCase();
                
                if (nome.includes(termo) || turma.includes(termo)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            initAttendanceSystem();
        });
    </script>
</body>
</html>