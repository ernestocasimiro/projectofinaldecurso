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
        $idTeacher = $_SESSION['id'] ?? null;

        if (!$idTeacher) {
            die("professor não identificado.");
        }

        try {
            $stmt = $conn->prepare("SELECT fname, lname FROM professores WHERE id = :id");
            $stmt->bindParam(':id', $idTeacher, PDO::PARAM_INT);
            $stmt->execute();

            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$teacher) {
                die("professor não encontrado.");
            }
        } catch (PDOException $e) {
            echo "Erro na consulta: " . $e->getMessage();
            exit;
        }

        /* Buscar notas dos estudantes
        $notas = [];
        try {
            $stmt = $conn->prepare("
                SELECT 
                    e.id as estudante_id,
                    e.nome,
                    e.sobrenome,
                    e.turma,
                    n.disciplina,
                    n.av1,
                    n.av2,
                    n.av3,
                    n.media,
                    n.periodo,
                    n.status
                FROM estudantes e 
                LEFT JOIN notas n ON e.id = n.estudante_id 
                ORDER BY e.turma, e.nome
            ");
            $stmt->execute();
            $notas = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Notas - Dashboard de Professores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Estilos para o sistema de notas */
        .grades-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }
        
        .grades-section h2 {
            color: #5d5d5d;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .grades-section h2 .material-symbols-outlined {
            font-size: 1.8rem;
            color: #3a5bb9;
        }
        
        .grades-section .description {
            color: #666;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        
        .grades-classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }
        
        .grades-class-card {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s ease;
            border-left: 4px solid #3a5bb9;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            cursor: pointer;
        }
        
        .grades-class-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        
        .grades-class-card h3 {
            color: #444;
            margin-bottom: 12px;
            font-size: 1.2rem;
        }
        
        .grades-class-card .class-info {
            color: #666;
            margin: 8px 0;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .grades-class-card .class-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #e0e0e0;
        }
        
        .grades-class-card .class-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            color: #777;
        }

        .progress-indicator {
            background-color: #e9ecef;
            border-radius: 10px;
            height: 8px;
            margin-top: 8px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .progress-excellent { background-color: #28a745; }
        .progress-good { background-color: #17a2b8; }
        .progress-average { background-color: #ffc107; }
        .progress-poor { background-color: #dc3545; }

        /* Modal de notas */
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
            margin: 2% auto;
            padding: 30px;
            border-radius: 10px;
            width: 95%;
            max-width: 1400px;
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
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #777;
        }

        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .evaluation-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .tab-button {
            padding: 10px 20px;
            border: none;
            background: none;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            font-weight: 500;
            color: #666;
        }

        .tab-button.active {
            color: #3a5bb9;
            border-bottom-color: #3a5bb9;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .grades-table th {
            background-color: #f5f5f5;
            color: #444;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            border-bottom: 1px solid #ddd;
            font-size: 0.9rem;
        }
        
        .grades-table td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }

        .grades-table tbody tr:hover {
            background-color: #f9f9f9;
        }
        
        .student-info {
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }

        .student-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        .student-name {
            font-weight: 600;
            color: #444;
            font-size: 0.9rem;
        }

        .student-id {
            font-size: 0.75rem;
            color: #666;
        }
        
        .grade-input {
            width: 50px;
            padding: 4px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        
        .grade-input:focus {
            outline: none;
            border-color: #3a5bb9;
        }

        .grade-input.invalid {
            border-color: #dc3545;
            background-color: #fff5f5;
        }
        
        .grade-average {
            font-weight: 600;
            color: #444;
        }
        
        .grade-status {
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-recovery {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-pending {
            background-color: #e2e3e5;
            color: #495057;
        }
        
        .grades-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .action-group {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            color: #444;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-action:hover {
            background-color: #f5f5f5;
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

        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .stat-item {
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 6px;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #666;
        }

        .stat-approved .stat-number { color: #28a745; }
        .stat-recovery .stat-number { color: #ffc107; }
        .stat-failed .stat-number { color: #dc3545; }
        .stat-pending .stat-number { color: #6c757d; }

        .attendance-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .present { background-color: #28a745; }
        .absent { background-color: #dc3545; }
        .late { background-color: #ffc107; }

        .grade-history {
            font-size: 0.8rem;
            color: #666;
            margin-top: 2px;
        }

        .notification-badge {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            margin-left: 5px;
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
                    <h3><?php echo htmlspecialchars($teacher['fname'] . ' ' . $teacher['lname']); ?></h3>
                    <p>Professor/a</p>
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
                    <input type="text" placeholder="Pesquisar turmas e disciplinas...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Lançamento de Notas</h1>
                    <div class="view-options">
                        <button class="view-btn active">
                            <span class="material-symbols-outlined">grid_view</span>
                        </button>
                        <button class="view-btn">
                            <span class="material-symbols-outlined">view_list</span>
                        </button>
                    </div>
                </div>
                
                <div class="filter-container">
                    <div class="filter-group">
                        <label for="ano-filter">Ano Letivo:</label>
                        <select id="ano-filter" class="filter-select">
                            <option value="2025" selected>2025</option>
                            <option value="2024">2024</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="periodo-filter">Período Avaliativo:</label>
                        <select id="periodo-filter" class="filter-select">
                            <option value="1">1º Trimestre</option>
                            <option value="2" selected>2º Trimestre</option>
                            <option value="3">3º Trimestre</option>
                            <option value="final">Avaliação Final</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="status-filter">Status das Notas:</label>
                        <select id="status-filter" class="filter-select">
                            <option value="todos">Todas</option>
                            <option value="pendentes">Pendentes</option>
                            <option value="lancadas">Lançadas</option>
                            <option value="fechadas">Fechadas</option>
                        </select>
                    </div>
                </div>

                <div class="classes-grid">
                    <!-- Turmas para notas serão carregadas dinamicamente -->
                </div>
                
                <!-- Seção de Turmas para Notas -->
                <div class="grades-section">
                    <h2>
                        <span class="material-symbols-outlined">grade</span>
                        Minhas Disciplinas - Lançamento de Notas
                    </h2>
                    <p class="description">Selecione uma turma e disciplina para lançar, editar ou revisar as notas dos alunos. Acompanhe o desempenho acadêmico e mantenha os registros sempre atualizados.</p>
                    
                    <div class="grades-classes-grid">
                        <!-- Matemática - 9º Ano A -->
                        <div class="grades-class-card" onclick="openGradesModal('9A-MAT', '9º Ano A - Matemática', 'Matemática', '28', '85', 'pendentes')">
                            <h3>9º Ano A - Matemática</h3>
                            <p class="class-info"><strong>Disciplina:</strong> Matemática</p>
                            <p class="class-info"><strong>Período:</strong> 2º Trimestre 2025</p>
                            <p class="class-info"><strong>Última atualização:</strong> 10/04/2025</p>
                            <div class="progress-indicator">
                                <div class="progress-bar progress-good" style="width: 85%"></div>
                            </div>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    28 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">pending</span>
                                    4 pendentes
                                    <span class="notification-badge">!</span>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Física - 10º Ano B -->
                        <div class="grades-class-card" onclick="openGradesModal('10B-FIS', '10º Ano B - Física', 'Física', '32', '100', 'lancadas')">
                            <h3>10º Ano B - Física</h3>
                            <p class="class-info"><strong>Disciplina:</strong> Física</p>
                            <p class="class-info"><strong>Período:</strong> 2º Trimestre 2025</p>
                            <p class="class-info"><strong>Última atualização:</strong> 14/04/2025</p>
                            <div class="progress-indicator">
                                <div class="progress-bar progress-excellent" style="width: 100%"></div>
                            </div>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    32 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">check_circle</span>
                                    Completo
                                </span>
                            </div>
                        </div>
                        
                        <!-- Química - 11º Ano C -->
                        <div class="grades-class-card" onclick="openGradesModal('11C-QUI', '11º Ano C - Química', 'Química', '25', '60', 'pendentes')">
                            <h3>11º Ano C - Química</h3>
                            <p class="class-info"><strong>Disciplina:</strong> Química</p>
                            <p class="class-info"><strong>Período:</strong> 2º Trimestre 2025</p>
                            <p class="class-info"><strong>Última atualização:</strong> 08/04/2025</p>
                            <div class="progress-indicator">
                                <div class="progress-bar progress-average" style="width: 60%"></div>
                            </div>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    25 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">pending</span>
                                    10 pendentes
                                    <span class="notification-badge">!</span>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Matemática - 12º Ano A -->
                        <div class="grades-class-card" onclick="openGradesModal('12A-MAT', '12º Ano A - Matemática', 'Matemática', '30', '40', 'pendentes')">
                            <h3>12º Ano A - Matemática</h3>
                            <p class="class-info"><strong>Disciplina:</strong> Matemática</p>
                            <p class="class-info"><strong>Período:</strong> 2º Trimestre 2025</p>
                            <p class="class-info"><strong>Última atualização:</strong> 05/04/2025</p>
                            <div class="progress-indicator">
                                <div class="progress-bar progress-poor" style="width: 40%"></div>
                            </div>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    30 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">warning</span>
                                    18 pendentes
                                    <span class="notification-badge">!</span>
                                </span>
                            </div>
                        </div>

                        <!-- Biologia - 10º Ano A -->
                        <div class="grades-class-card" onclick="openGradesModal('10A-BIO', '10º Ano A - Biologia', 'Biologia', '29', '90', 'lancadas')">
                            <h3>10º Ano A - Biologia</h3>
                            <p class="class-info"><strong>Disciplina:</strong> Biologia</p>
                            <p class="class-info"><strong>Período:</strong> 2º Trimestre 2025</p>
                            <p class="class-info"><strong>Última atualização:</strong> 13/04/2025</p>
                            <div class="progress-indicator">
                                <div class="progress-bar progress-excellent" style="width: 90%"></div>
                            </div>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    29 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">pending</span>
                                    3 pendentes
                                </span>
                            </div>
                        </div>

                        <!-- Geografia - 9º Ano B -->
                        <div class="grades-class-card" onclick="openGradesModal('9B-GEO', '9º Ano B - Geografia', 'Geografia', '26', '75', 'lancadas')">
                            <h3>9º Ano B - Geografia</h3>
                            <p class="class-info"><strong>Disciplina:</strong> Geografia</p>
                            <p class="class-info"><strong>Período:</strong> 2º Trimestre 2025</p>
                            <p class="class-info"><strong>Última atualização:</strong> 12/04/2025</p>
                            <div class="progress-indicator">
                                <div class="progress-bar progress-good" style="width: 75%"></div>
                            </div>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    26 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">pending</span>
                                    7 pendentes
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de Notas -->
    <div id="gradesModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalGradesTurma"></h2>
                <button class="close-modal" onclick="closeGradesModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="evaluation-tabs">
                    <button class="tab-button active" onclick="switchTab('av1')">1ª Avaliação</button>
                    <button class="tab-button" onclick="switchTab('av2')">2ª Avaliação</button>
                    <button class="tab-button" onclick="switchTab('av3')">3ª Avaliação</button>
                    <button class="tab-button" onclick="switchTab('final')">Média Final</button>
                </div>

                <div class="stats-summary">
                    <div class="stat-item stat-approved">
                        <div class="stat-number" id="approvedCount">0</div>
                        <div class="stat-label">Aprovados</div>
                    </div>
                    <div class="stat-item stat-recovery">
                        <div class="stat-number" id="recoveryCount">0</div>
                        <div class="stat-label">Recuperação</div>
                    </div>
                    <div class="stat-item stat-failed">
                        <div class="stat-number" id="failedCount">0</div>
                        <div class="stat-label">Reprovados</div>
                    </div>
                    <div class="stat-item stat-pending">
                        <div class="stat-number" id="pendingCount">0</div>
                        <div class="stat-label">Pendentes</div>
                    </div>
                </div>
                
                <table class="grades-table" id="gradesTable">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Aluno</th>
                            <th>Freq.</th>
                            <th>AV1</th>
                            <th>AV2</th>
                            <th>AV3</th>
                            <th>Média</th>
                            <th>Situação</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody id="gradesTableBody">
                        <!-- Notas serão preenchidas via JavaScript -->
                    </tbody>
                </table>
                
                <div class="grades-actions">
                    <div class="action-group">
                        <button class="btn-action" onclick="importarNotas()">
                            <span class="material-symbols-outlined">upload</span>
                            Importar
                        </button>
                        <button class="btn-action" onclick="validarNotas()">
                            <span class="material-symbols-outlined">fact_check</span>
                            Validar
                        </button>
                        <button class="btn-action" onclick="recalcularMedias()">
                            <span class="material-symbols-outlined">refresh</span>
                            Recalcular
                        </button>
                    </div>
                    <div class="action-group">
                        <button class="btn-action" onclick="exportarNotas()">
                            <span class="material-symbols-outlined">file_download</span>
                            Exportar
                        </button>
                        <button class="btn-action btn-warning" onclick="salvarRascunho()">
                            <span class="material-symbols-outlined">draft</span>
                            Salvar Rascunho
                        </button>
                        <button class="btn-action btn-success" onclick="publicarNotas()">
                            <span class="material-symbols-outlined">publish</span>
                            Publicar Notas
                        </button>
                        <button class="btn-action btn-primary" onclick="fecharPeriodo()">
                            <span class="material-symbols-outlined">lock</span>
                            Fechar Período
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="dashboard-data.js"></script>
    <script>
        // Dados simulados específicos para notas
        const estudantesNotas = [
            {
                id: 1, numero: 1, nome: "Ana Beatriz Silva", frequencia: 95,
                avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face",
                notas: { av1: 8.5, av2: 7.2, av3: 9.0, media: 8.2 },
                observacoes: "Excelente participação"
            },
            {
                id: 2, numero: 2, nome: "Bruno Santos Costa", frequencia: 88,
                avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face",
                notas: { av1: 6.0, av2: 5.5, av3: 6.8, media: 6.1 },
                observacoes: "Precisa melhorar"
            },
            {
                id: 3, numero: 3, nome: "Carla Oliveira Mendes", frequencia: 92,
                avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face",
                notas: { av1: 9.2, av2: 8.8, av3: 9.5, media: 9.2 },
                observacoes: "Destaque da turma"
            },
            {
                id: 4, numero: 4, nome: "Diego Costa Ferreira", frequencia: 75,
                avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face",
                notas: { av1: 4.5, av2: 5.0, av3: 4.8, media: 4.8 },
                observacoes: "Necessita recuperação"
            },
            {
                id: 5, numero: 5, nome: "Elena Rodriguez Lima", frequencia: 90,
                avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&h=150&fit=crop&crop=face",
                notas: { av1: 7.8, av2: 8.2, av3: 7.5, media: 7.8 },
                observacoes: "Bom desempenho"
            }
        ];

        let currentTab = 'final';

        // Funções para o modal de notas
        function openGradesModal(id, nome, disciplina, totalAlunos, progresso, status) {
            const modal = document.getElementById('gradesModal');
            document.getElementById('modalGradesTurma').textContent = `${nome} - ${disciplina}`;
            
            // Preencher tabela de notas
            const tbody = document.getElementById('gradesTableBody');
            tbody.innerHTML = '';
            
            let aprovados = 0, recuperacao = 0, reprovados = 0, pendentes = 0;
            
            estudantesNotas.forEach(estudante => {
                const row = document.createElement('tr');
                
                let statusSituacao = 'approved';
                let statusText = 'Aprovado';
                let statusClass = 'status-approved';
                
                if (estudante.notas.media === 0) {
                    statusSituacao = 'pending';
                    statusText = 'Pendente';
                    statusClass = 'status-pending';
                    pendentes++;
                } else if (estudante.notas.media < 6.0) {
                    statusSituacao = 'failed';
                    statusText = 'Reprovado';
                    statusClass = 'status-failed';
                    reprovados++;
                } else if (estudante.notas.media < 7.0) {
                    statusSituacao = 'recovery';
                    statusText = 'Recuperação';
                    statusClass = 'status-recovery';
                    recuperacao++;
                } else {
                    aprovados++;
                }

                // Indicador de frequência
                let freqClass = 'present';
                if (estudante.frequencia < 75) freqClass = 'absent';
                else if (estudante.frequencia < 85) freqClass = 'late';
                
                row.innerHTML = `
                    <td>${estudante.numero}</td>
                    <td>
                        <div class="student-info">
                            <img src="${estudante.avatar}" alt="${estudante.nome}" class="student-avatar">
                            <div>
                                <div class="student-name">${estudante.nome}</div>
                                <div class="student-id">ID: ${estudante.id}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="attendance-indicator ${freqClass}"></span>
                        ${estudante.frequencia}%
                    </td>
                    <td><input type="number" class="grade-input" value="${estudante.notas.av1.toFixed(1)}" min="0" max="10" step="0.1" data-student="${estudante.id}" data-grade="av1" onchange="recalcularMedia(this)" onblur="validarNota(this)"></td>
                    <td><input type="number" class="grade-input" value="${estudante.notas.av2.toFixed(1)}" min="0" max="10" step="0.1" data-student="${estudante.id}" data-grade="av2" onchange="recalcularMedia(this)" onblur="validarNota(this)"></td>
                    <td><input type="number" class="grade-input" value="${estudante.notas.av3.toFixed(1)}" min="0" max="10" step="0.1" data-student="${estudante.id}" data-grade="av3" onchange="recalcularMedia(this)" onblur="validarNota(this)"></td>
                    <td class="grade-average">${estudante.notas.media.toFixed(1)}</td>
                    <td><span class="grade-status ${statusClass}">${statusText}</span></td>
                    <td>
                        <input type="text" value="${estudante.observacoes}" style="width: 120px; font-size: 0.8rem; padding: 2px;">
                    </td>
                `;
                
                tbody.appendChild(row);
            });
            
            // Atualizar estatísticas
            document.getElementById('approvedCount').textContent = aprovados;
            document.getElementById('recoveryCount').textContent = recuperacao;
            document.getElementById('failedCount').textContent = reprovados;
            document.getElementById('pendingCount').textContent = pendentes;
            
            modal.style.display = 'block';
        }

        function closeGradesModal() {
            document.getElementById('gradesModal').style.display = 'none';
        }

        function switchTab(tab) {
            currentTab = tab;
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Aqui você pode implementar a lógica para mostrar diferentes avaliações
            console.log('Mudou para aba:', tab);
        }

        function validarNota(input) {
            const valor = parseFloat(input.value);
            if (valor < 0 || valor > 10 || isNaN(valor)) {
                input.classList.add('invalid');
                alert('Nota deve estar entre 0 e 10');
            } else {
                input.classList.remove('invalid');
            }
        }

        function recalcularMedia(input) {
            const row = input.closest('tr');
            const inputs = row.querySelectorAll('.grade-input');
            const mediaCell = row.querySelector('.grade-average');
            const statusCell = row.querySelector('.grade-status');
            
            let soma = 0;
            let count = 0;
            inputs.forEach(inp => {
                const val = parseFloat(inp.value);
                if (!isNaN(val)) {
                    soma += val;
                    count++;
                }
            });
            
            const media = count > 0 ? soma / count : 0;
            mediaCell.textContent = media.toFixed(1);
            
            // Atualizar status
            let statusText = 'Aprovado';
            let statusClass = 'status-approved';
            
            if (media === 0) {
                statusText = 'Pendente';
                statusClass = 'status-pending';
            } else if (media < 6.0) {
                statusText = 'Reprovado';
                statusClass = 'status-failed';
            } else if (media < 7.0) {
                statusText = 'Recuperação';
                statusClass = 'status-recovery';
            }
            
            statusCell.textContent = statusText;
            statusCell.className = `grade-status ${statusClass}`;
            
            // Recalcular estatísticas
            atualizarEstatisticas();
        }

        function atualizarEstatisticas() {
            const statusElements = document.querySelectorAll('.grade-status');
            let aprovados = 0, recuperacao = 0, reprovados = 0, pendentes = 0;
            
            statusElements.forEach(status => {
                if (status.classList.contains('status-approved')) aprovados++;
                else if (status.classList.contains('status-recovery')) recuperacao++;
                else if (status.classList.contains('status-failed')) reprovados++;
                else if (status.classList.contains('status-pending')) pendentes++;
            });
            
            document.getElementById('approvedCount').textContent = aprovados;
            document.getElementById('recoveryCount').textContent = recuperacao;
            document.getElementById('failedCount').textContent = reprovados;
            document.getElementById('pendingCount').textContent = pendentes;
        }

        // Funções específicas do sistema de notas
        function importarNotas() {
            alert('Funcionalidade de importação de notas será implementada');
        }

        function validarNotas() {
            const inputs = document.querySelectorAll('.grade-input');
            let invalidas = 0;
            
            inputs.forEach(input => {
                const valor = parseFloat(input.value);
                if (valor < 0 || valor > 10 || isNaN(valor)) {
                    input.classList.add('invalid');
                    invalidas++;
                } else {
                    input.classList.remove('invalid');
                }
            });
            
            if (invalidas > 0) {
                alert(`${invalidas} notas inválidas encontradas. Corrija antes de continuar.`);
            } else {
                alert('Todas as notas estão válidas!');
            }
        }

        function recalcularMedias() {
            const inputs = document.querySelectorAll('.grade-input');
            inputs.forEach(input => recalcularMedia(input));
            alert('Médias recalculadas com sucesso!');
        }

        function salvarRascunho() {
            alert('Notas salvas como rascunho!');
        }

        function publicarNotas() {
            if (confirm('Tem certeza que deseja publicar as notas? Esta ação tornará as notas visíveis para alunos e responsáveis.')) {
                alert('Notas publicadas com sucesso!');
            }
        }

        function fecharPeriodo() {
            if (confirm('Tem certeza que deseja fechar este período? Após o fechamento, não será possível editar as notas.')) {
                alert('Período fechado com sucesso!');
            }
        }

        function exportarNotas() {
            alert('Relatório de notas exportado com sucesso!');
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('gradesModal');
            if (event.target == modal) {
                closeGradesModal();
            }
        }

        // Toggle sidebar on mobile
        document.getElementById('menuToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

        // Adicionar funcionalidade aos botões de visualização
        document.querySelectorAll('.view-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.view-btn').forEach(function(b) {
                    b.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>