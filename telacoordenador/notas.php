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

        /* Buscar notas dos alunos da área
        $notasAlunos = [];
        try {
            $placeholders = str_repeat('?,', count($turmasPermitidas) - 1) . '?';
            $stmt = $conn->prepare("
                SELECT 
                    e.id as estudante_id,
                    e.fname,
                    e.lname,
                    e.turma,
                    n.disciplina,
                    n.av1,
                    n.av2,
                    n.av3,
                    n.media_final,
                    n.situacao,
                    n.trimestre,
                    n.ano_letivo
                FROM estudantes e 
                LEFT JOIN notas n ON e.id = n.estudante_id 
                WHERE e.turma IN ($placeholders)
                ORDER BY e.turma, e.fname
            ");
            $stmt->execute($turmasPermitidas);
            $notasAlunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar notas: " . $e->getMessage();
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
    <title>Gestão de Notas - Dashboard de Coordenadores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Estilos específicos para coordenação de notas */
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
        
        .grades-overview {
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
        
        .overview-card.approved::before {
            background: linear-gradient(90deg, #4caf50, #8bc34a);
        }
        
        .overview-card.recovery::before {
            background: linear-gradient(90deg, #ff9800, #ffc107);
        }
        
        .overview-card.failed::before {
            background: linear-gradient(90deg, #f44336, #e91e63);
        }
        
        .overview-card.average::before {
            background: linear-gradient(90deg, #2196f3, #03a9f4);
        }
        
        .overview-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: block;
        }
        
        .overview-number.approved { color: #4caf50; }
        .overview-number.recovery { color: #ff9800; }
        .overview-number.failed { color: #f44336; }
        .overview-number.average { color: #2196f3; }
        
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
        
        .grades-filters {
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
        
        .filter-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            background-color: #fff;
        }
        
        .filter-select:focus {
            outline: none;
            border-color: #3a5bb9;
            box-shadow: 0 0 0 3px rgba(58, 91, 185, 0.1);
        }
        
        .grades-table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        
        .grades-table thead {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        
        .grades-table th {
            padding: 18px 15px;
            text-align: center;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .grades-table th.student-col {
            text-align: left;
            min-width: 200px;
        }
        
        .grades-table td {
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
            vertical-align: middle;
            text-align: center;
        }
        
        .grades-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .grades-table tbody tr:hover {
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
        
        .grade-input {
            width: 60px;
            padding: 8px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .grade-input:focus {
            outline: none;
            border-color: #3a5bb9;
            box-shadow: 0 0 0 3px rgba(58, 91, 185, 0.1);
        }
        
        .grade-input.modified {
            border-color: #ff9800;
            background-color: #fff8e1;
        }
        
        .grade-average {
            font-weight: 700;
            font-size: 1rem;
            padding: 10px;
            border-radius: 6px;
        }
        
        .grade-average.high {
            background-color: #e8f5e8;
            color: #2e7d32;
        }
        
        .grade-average.medium {
            background-color: #fff3e0;
            color: #f57c00;
        }
        
        .grade-average.low {
            background-color: #ffebee;
            color: #d32f2f;
        }
        
        .grade-status {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-approved {
            background-color: #e8f5e8;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        .status-recovery {
            background-color: #fff3e0;
            color: #f57c00;
            border: 1px solid #ffcc02;
        }
        
        .status-failed {
            background-color: #ffebee;
            color: #d32f2f;
            border: 1px solid #ffcdd2;
        }
        
        .grades-actions {
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
        
        .btn-primary:hover {
            background-color: #2d4494;
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
        
        .statistics-panel {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: block;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .class-performance {
            margin-top: 30px;
        }
        
        .performance-chart {
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
        
        .chart-legend {
            display: flex;
            gap: 15px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.8rem;
        }
        
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
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
            .grades-overview {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .grades-table-container {
                overflow-x: auto;
            }
            
            .grades-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .action-group {
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .grades-overview {
                grid-template-columns: 1fr;
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
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar alunos e notas..." id="searchInput">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Gestão de Notas - <?php echo $areaLabels[$areaAtuacao] ?? $areaAtuacao; ?></h1>
                    <div class="header-actions">
                        <button class="btn-action" onclick="exportarRelatorio()">
                            <span class="material-symbols-outlined">file_download</span>
                            Exportar Relatório
                        </button>
                        <button class="btn-action btn-warning" onclick="gerarBoletins()">
                            <span class="material-symbols-outlined">description</span>
                            Gerar Boletins
                        </button>
                        <button class="btn-action btn-primary" onclick="salvarAlteracoes()">
                            <span class="material-symbols-outlined">save</span>
                            Salvar Alterações
                        </button>
                    </div>
                </div>
                
                <!-- Visão Geral das Notas -->
                <div class="grades-overview">
                    <div class="overview-card approved">
                        <span class="material-symbols-outlined overview-icon">check_circle</span>
                        <span class="overview-number approved" id="approvedCount">0</span>
                        <span class="overview-label">Alunos Aprovados</span>
                    </div>
                    <div class="overview-card recovery">
                        <span class="material-symbols-outlined overview-icon">warning</span>
                        <span class="overview-number recovery" id="recoveryCount">0</span>
                        <span class="overview-label">Em Recuperação</span>
                    </div>
                    <div class="overview-card failed">
                        <span class="material-symbols-outlined overview-icon">cancel</span>
                        <span class="overview-number failed" id="failedCount">0</span>
                        <span class="overview-label">Reprovados</span>
                    </div>
                    <div class="overview-card average">
                        <span class="material-symbols-outlined overview-icon">trending_up</span>
                        <span class="overview-number average" id="generalAverage">0.0</span>
                        <span class="overview-label">Média Geral</span>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="grades-filters">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label for="turma-filter">Turma</label>
                            <select id="turma-filter" class="filter-select" onchange="filtrarNotas()">
                                <option value="todas">Todas as Turmas</option>
                                <?php foreach($turmasPermitidas as $turma): ?>
                                    <option value="<?php echo $turma; ?>"><?php echo $turma; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="disciplina-filter">Disciplina</label>
                            <select id="disciplina-filter" class="filter-select" onchange="filtrarNotas()">
                                <option value="todas">Todas as Disciplinas</option>
                                <?php foreach($disciplinasArea as $disciplina): ?>
                                    <option value="<?php echo $disciplina; ?>"><?php echo $disciplina; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="periodo-filter">Período</label>
                            <select id="periodo-filter" class="filter-select" onchange="filtrarNotas()">
                                <option value="1">1º Trimestre</option>
                                <option value="2" selected>2º Trimestre</option>
                                <option value="3">3º Trimestre</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="situacao-filter">Situação</label>
                            <select id="situacao-filter" class="filter-select" onchange="filtrarNotas()">
                                <option value="todas">Todas</option>
                                <option value="aprovado">Aprovados</option>
                                <option value="recuperacao">Recuperação</option>
                                <option value="reprovado">Reprovados</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas por Turma -->
                <div class="statistics-panel">
                    <h3>Desempenho por Turma</h3>
                    <div class="stats-grid" id="classStats">
                        <!-- Estatísticas serão carregadas dinamicamente -->
                    </div>
                </div>
                
                <!-- Tabela de Notas -->
                <div class="grades-table-container">
                    <table class="grades-table">
                        <thead>
                            <tr>
                                <th class="student-col">Aluno</th>
                                <th>Disciplina</th>
                                <th>AV1</th>
                                <th>AV2</th>
                                <th>AV3</th>
                                <th>Média</th>
                                <th>Situação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="gradesTableBody">
                            <!-- Notas serão carregadas dinamicamente -->
                        </tbody>
                    </table>
                    
                    <div class="grades-actions">
                        <div class="action-group">
                            <button class="btn-action" onclick="recalcularMedias()">
                                <span class="material-symbols-outlined">calculate</span>
                                Recalcular Médias
                            </button>
                            <button class="btn-action" onclick="importarNotas()">
                                <span class="material-symbols-outlined">upload</span>
                                Importar Notas
                            </button>
                        </div>
                        <div class="action-group">
                            <button class="btn-action btn-warning" onclick="enviarAlertas()">
                                <span class="material-symbols-outlined">notification_important</span>
                                Enviar Alertas
                            </button>
                            <button class="btn-action btn-success" onclick="aprovarNotas()">
                                <span class="material-symbols-outlined">check</span>
                                Aprovar Notas
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Performance -->
                <div class="class-performance">
                    <div class="performance-chart">
                        <div class="chart-header">
                            <h3 class="chart-title">Performance por Disciplina</h3>
                            <div class="chart-legend">
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: #4caf50;"></div>
                                    <span>Aprovados</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: #ff9800;"></div>
                                    <span>Recuperação</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: #f44336;"></div>
                                    <span>Reprovados</span>
                                </div>
                            </div>
                        </div>
                        <div id="performanceChart">
                            <!-- Gráfico será implementado aqui -->
                            <div class="empty-state">
                                <span class="material-symbols-outlined">bar_chart</span>
                                <h3>Gráfico de Performance</h3>
                                <p>Visualização das notas por disciplina será exibida aqui</p>
                            </div>
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
        
        // Dados simulados de notas
        const notasSimuladas = [
            {
                estudante_id: 1,
                nome: 'Ana Beatriz Silva',
                turma: turmasPermitidas[0],
                disciplina: disciplinasArea[0],
                av1: 8.5,
                av2: 7.8,
                av3: 9.2,
                media: 8.5,
                situacao: 'aprovado'
            },
            {
                estudante_id: 2,
                nome: 'Bruno Santos Costa',
                turma: turmasPermitidas[0],
                disciplina: disciplinasArea[0],
                av1: 6.2,
                av2: 5.8,
                av3: 7.1,
                media: 6.4,
                situacao: 'recuperacao'
            },
            {
                estudante_id: 3,
                nome: 'Carla Oliveira Mendes',
                turma: turmasPermitidas[1] || turmasPermitidas[0],
                disciplina: disciplinasArea[1] || disciplinasArea[0],
                av1: 4.5,
                av2: 5.2,
                av3: 4.8,
                media: 4.8,
                situacao: 'reprovado'
            }
        ];

        // Inicializar sistema
        function initGradesSystem() {
            carregarNotas();
            atualizarEstatisticas();
            carregarEstatisticasTurmas();
        }

        function carregarNotas() {
            const tbody = document.getElementById('gradesTableBody');
            tbody.innerHTML = '';

            notasSimuladas.forEach((nota, index) => {
                const row = document.createElement('tr');
                
                // Determinar classe da média
                let mediaClass = 'low';
                if (nota.media >= 7.0) mediaClass = 'high';
                else if (nota.media >= 6.0) mediaClass = 'medium';
                
                // Determinar situação
                let situacaoClass = 'status-failed';
                let situacaoText = 'Reprovado';
                
                if (nota.situacao === 'aprovado') {
                    situacaoClass = 'status-approved';
                    situacaoText = 'Aprovado';
                } else if (nota.situacao === 'recuperacao') {
                    situacaoClass = 'status-recovery';
                    situacaoText = 'Recuperação';
                }

                row.innerHTML = `
                    <td>
                        <div class="student-info">
                            <div class="student-avatar">
                                ${nota.nome.split(' ').map(n => n[0]).join('').substring(0, 2)}
                            </div>
                            <div class="student-details">
                                <h4>${nota.nome}</h4>
                                <div class="student-class">${nota.turma}</div>
                            </div>
                        </div>
                    </td>
                    <td>${nota.disciplina}</td>
                    <td><input type="number" class="grade-input" value="${nota.av1}" min="0" max="10" step="0.1" onchange="atualizarNota(${index}, 'av1', this.value)"></td>
                    <td><input type="number" class="grade-input" value="${nota.av2}" min="0" max="10" step="0.1" onchange="atualizarNota(${index}, 'av2', this.value)"></td>
                    <td><input type="number" class="grade-input" value="${nota.av3}" min="0" max="10" step="0.1" onchange="atualizarNota(${index}, 'av3', this.value)"></td>
                    <td><span class="grade-average ${mediaClass}">${nota.media.toFixed(1)}</span></td>
                    <td><span class="grade-status ${situacaoClass}">${situacaoText}</span></td>
                    <td>
                        <button class="btn-action" onclick="verDetalhesAluno(${nota.estudante_id})" title="Ver detalhes">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </td>
                `;
                
                tbody.appendChild(row);
            });
        }

        function atualizarEstatisticas() {
            const aprovados = notasSimuladas.filter(n => n.situacao === 'aprovado').length;
            const recuperacao = notasSimuladas.filter(n => n.situacao === 'recuperacao').length;
            const reprovados = notasSimuladas.filter(n => n.situacao === 'reprovado').length;
            const mediaGeral = notasSimuladas.reduce((acc, n) => acc + n.media, 0) / notasSimuladas.length;

            document.getElementById('approvedCount').textContent = aprovados;
            document.getElementById('recoveryCount').textContent = recuperacao;
            document.getElementById('failedCount').textContent = reprovados;
            document.getElementById('generalAverage').textContent = mediaGeral.toFixed(1);
        }

        function carregarEstatisticasTurmas() {
            const statsContainer = document.getElementById('classStats');
            statsContainer.innerHTML = '';

            turmasPermitidas.forEach(turma => {
                const alunosTurma = notasSimuladas.filter(n => n.turma === turma);
                const mediaTurma = alunosTurma.length > 0 ? 
                    alunosTurma.reduce((acc, n) => acc + n.media, 0) / alunosTurma.length : 0;

                const statDiv = document.createElement('div');
                statDiv.className = 'stat-item';
                statDiv.innerHTML = `
                    <span class="stat-number" style="color: ${mediaTurma >= 7 ? '#4caf50' : mediaTurma >= 6 ? '#ff9800' : '#f44336'}">${mediaTurma.toFixed(1)}</span>
                    <span class="stat-label">${turma}</span>
                `;
                statsContainer.appendChild(statDiv);
            });
        }

        function atualizarNota(index, campo, valor) {
            notasSimuladas[index][campo] = parseFloat(valor);
            
            // Recalcular média
            const nota = notasSimuladas[index];
            nota.media = (nota.av1 + nota.av2 + nota.av3) / 3;
            
            // Atualizar situação
            if (nota.media >= 7.0) {
                nota.situacao = 'aprovado';
            } else if (nota.media >= 6.0) {
                nota.situacao = 'recuperacao';
            } else {
                nota.situacao = 'reprovado';
            }
            
            // Marcar input como modificado
            event.target.classList.add('modified');
            
            // Recarregar dados
            carregarNotas();
            atualizarEstatisticas();
            carregarEstatisticasTurmas();
        }

        function filtrarNotas() {
            // Implementar filtros
            console.log('Aplicando filtros...');
        }

        function exportarRelatorio() {
            alert('Relatório de notas exportado com sucesso!');
        }

        function gerarBoletins() {
            alert('Boletins gerados com sucesso!');
        }

        function salvarAlteracoes() {
            alert('Alterações salvas com sucesso!');
        }

        function recalcularMedias() {
            notasSimuladas.forEach(nota => {
                nota.media = (nota.av1 + nota.av2 + nota.av3) / 3;
            });
            carregarNotas();
            atualizarEstatisticas();
            alert('Médias recalculadas com sucesso!');
        }

        function importarNotas() {
            alert('Funcionalidade de importação será implementada');
        }

        function enviarAlertas() {
            const reprovados = notasSimuladas.filter(n => n.situacao === 'reprovado').length;
            if (confirm(`Enviar alertas para ${reprovados} alunos com baixo desempenho?`)) {
                alert('Alertas enviados com sucesso!');
            }
        }

        function aprovarNotas() {
            if (confirm('Aprovar todas as notas do período atual?')) {
                alert('Notas aprovadas com sucesso!');
            }
        }

        function verDetalhesAluno(id) {
            alert(`Ver detalhes do aluno ID: ${id}`);
        }

        // Busca
        document.getElementById('searchInput').addEventListener('input', function() {
            const termo = this.value.toLowerCase();
            const rows = document.querySelectorAll('#gradesTableBody tr');
            
            rows.forEach(row => {
                const nome = row.querySelector('.student-details h4').textContent.toLowerCase();
                const disciplina = row.cells[1].textContent.toLowerCase();
                
                if (nome.includes(termo) || disciplina.includes(termo)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            initGradesSystem();
        });
    </script>
</body>
</html>