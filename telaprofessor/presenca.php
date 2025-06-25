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

        /* Buscar dados de presença
        $presencas = [];
        try {
            $stmt = $conn->prepare("
                SELECT 
                    e.id as estudante_id,
                    e.nome,
                    e.sobrenome,
                    e.turma,
                    p.data_aula,
                    p.status_presenca,
                    p.justificativa,
                    p.observacoes
                FROM estudantes e 
                LEFT JOIN presencas p ON e.id = p.estudante_id 
                ORDER BY e.turma, e.nome
            ");
            $stmt->execute();
            $presencas = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Controle de Presença - Dashboard de Professores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Estilos para o sistema de presença */
        .attendance-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }
        
        .attendance-section h2 {
            color: #5d5d5d;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .attendance-section h2 .material-symbols-outlined {
            font-size: 1.8rem;
            color: #3a5bb9;
        }
        
        .attendance-section .description {
            color: #666;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        
        .attendance-classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }
        
        .attendance-class-card {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s ease;
            border-left: 4px solid #3a5bb9;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            cursor: pointer;
        }
        
        .attendance-class-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        
        .attendance-class-card h3 {
            color: #444;
            margin-bottom: 12px;
            font-size: 1.2rem;
        }
        
        .attendance-class-card .class-info {
            color: #666;
            margin: 8px 0;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .attendance-class-card .class-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #e0e0e0;
        }
        
        .attendance-class-card .class-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            color: #777;
        }

        .attendance-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 6px;
        }

        .stat-item {
            text-align: center;
            flex: 1;
        }

        .stat-number {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 2px;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #666;
        }

        .stat-present .stat-number { color: #28a745; }
        .stat-absent .stat-number { color: #dc3545; }
        .stat-late .stat-number { color: #ffc107; }
        .stat-justified .stat-number { color: #17a2b8; }

        .alert-badge {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            margin-left: 5px;
        }

        /* Modal de presença */
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

        .date-navigation {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .date-nav-btn {
            background: none;
            border: 1px solid #ddd;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .date-nav-btn:hover {
            background-color: #3a5bb9;
            color: white;
            border-color: #3a5bb9;
        }

        .current-date {
            font-size: 1.2rem;
            font-weight: 600;
            color: #444;
            min-width: 200px;
            text-align: center;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .attendance-table th {
            background-color: #f5f5f5;
            color: #444;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            border-bottom: 1px solid #ddd;
            font-size: 0.9rem;
        }
        
        .attendance-table td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }

        .attendance-table tbody tr:hover {
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

        .student-number {
            font-size: 0.75rem;
            color: #666;
        }

        .attendance-radio {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .attendance-radio:checked {
            accent-color: #3a5bb9;
        }

        .justification-input {
            width: 100%;
            padding: 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        .justification-input:focus {
            outline: none;
            border-color: #3a5bb9;
        }

        .attendance-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .summary-item {
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 6px;
        }

        .summary-number {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .summary-label {
            font-size: 0.85rem;
            color: #666;
        }

        .summary-present .summary-number { color: #28a745; }
        .summary-absent .summary-number { color: #dc3545; }
        .summary-late .summary-number { color: #ffc107; }
        .summary-justified .summary-number { color: #17a2b8; }

        .attendance-actions {
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

        .btn-danger {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        .frequency-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .freq-excellent { background-color: #28a745; }
        .freq-good { background-color: #17a2b8; }
        .freq-warning { background-color: #ffc107; }
        .freq-danger { background-color: #dc3545; }

        .attendance-history {
            font-size: 0.8rem;
            color: #666;
            margin-top: 2px;
        }

        .quick-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .quick-btn {
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            color: #444;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .quick-btn:hover {
            background-color: #f5f5f5;
        }

        .quick-btn.active {
            background-color: #3a5bb9;
            color: white;
            border-color: #3a5bb9;
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
                    <input type="text" placeholder="Pesquisar turmas e alunos...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Controle de Presença</h1>
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
                        <label for="periodo-filter">Período:</label>
                        <select id="periodo-filter" class="filter-select">
                            <option value="1">1º Trimestre</option>
                            <option value="2" selected>2º Trimestre</option>
                            <option value="3">3º Trimestre</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="status-filter">Status:</label>
                        <select id="status-filter" class="filter-select">
                            <option value="todos">Todas</option>
                            <option value="pendentes">Chamadas Pendentes</option>
                            <option value="realizadas">Chamadas Realizadas</option>
                            <option value="alertas">Com Alertas</option>
                        </select>
                    </div>
                </div>

                <div class="classes-grid">
                    <!-- Turmas para presença serão carregadas dinamicamente -->
                </div>
                
                <!-- Seção de Turmas para Presença -->
                <div class="attendance-section">
                    <h2>
                        <span class="material-symbols-outlined">fact_check</span>
                        Minhas Turmas - Controle de Presença
                    </h2>
                    <p class="description">Gerencie a presença dos alunos em suas turmas. Realize chamadas diárias, acompanhe a frequência e identifique alunos com faltas excessivas.</p>
                    
                    <div class="attendance-classes-grid">
                        <!-- Matemática - 9º Ano A -->
                        <div class="attendance-class-card" onclick="openAttendanceModal('9A-MAT', '9º Ano A - Matemática', 'Matemática', '28')">
                            <h3>9º Ano A - Matemática</h3>
                            <p class="class-info"><strong>Disciplina:</strong> Matemática</p>
                            <p class="class-info"><strong>Horário:</strong> Segunda e Quarta, 8:00-9:30</p>
                            <p class="class-info"><strong>Última chamada:</strong> 14/04/2025</p>
                            <div class="attendance-stats">
                                <div class="stat-item stat-present">
                                    <div class="stat-number">24</div>
                                    <div class="stat-label">Presentes</div>
                                </div>
                                <div class="stat-item stat-absent">
                                    <div class="stat-number">3</div>
                                    <div class="stat-label">Ausentes</div>
                                </div>
                                <div class="stat-item stat-justified">
                                    <div class="stat-number">1</div>
                                    <div class="stat-label">Justificados</div>
                                </div>
                            </div>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    28 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">warning</span>
                                    2 alertas
                                    <span class="alert-badge">!</span>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Física - 10º Ano B -->
                        <div class="attendance-class-card" onclick="openAttendanceModal('10B-FIS', '10º Ano B - Física', 'Física', '32')">
                            <h3>10º Ano B - Física</h3>
                            <p class="class-info"><strong>Disciplina:</strong> Física</p>
                            <p class="class-info"><strong>Horário:</strong> Terça e Quinta, 14:00-15:30</p>
                            <p class="class-info"><strong>Última chamada:</strong> 15/04/2025</p>
                            <div class="attendance-stats">
                                <div class="stat-item stat-present">
                                    <div class="stat-number">30</div>
                                    <div class="stat-label">Presentes</div>
                                </div>
                                <div class="stat-item stat-absent">
                                    <div class="stat-number">1</div>
                                    <div class="stat-label">Ausentes</div>
                                </div>
                                <div class="stat-item stat-late">
                                    <div class="stat-number">1</div>
                                    <div class="stat-label">Atrasados</div>
                                </div>
                            </div>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    32 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">check_circle</span>
                                    Atualizada
                                </span>
                            </div>
                        </div>
                        
                        <!-- Química - 11º Ano C -->
                        <div class="attendance-class-card" onclick="openAttendanceModal('11C-QUI', '11º Ano C - Química', 'Química', '25')">
                            <h3>11º Ano C - Química</h3>
                            <p class="class-info"><strong>Disciplina:</strong> Química</p>
                            <p class="class-info"><strong>Horário:</strong> Segunda e Quarta, 19:00-20:30</p>
                            <p class="class-info"><strong>Última chamada:</strong> 12/04/2025</p>
                            <div class="attendance-stats">
                                <div class="stat-item stat-present">
                                    <div class="stat-number">20</div>
                                    <div class="stat-label">Presentes</div>
                                </div>
                                <div class="stat-item stat-absent">
                                    <div class="stat-number">4</div>
                                    <div class="stat-label">Ausentes</div>
                                </div>
                                <div class="stat-item stat-justified">
                                    <div class="stat-number">1</div>
                                    <div class="stat-label">Justificados</div>
                                </div>
                            </div>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    25 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">pending</span>
                                    Pendente hoje
                                    <span class="alert-badge">!</span>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Biologia - 10º Ano A -->
                        <div class="attendance-class-card" onclick="openAttendanceModal('10A-BIO', '10º Ano A - Biologia', 'Biologia', '29')">
                            <h3>10º Ano A - Biologia</h3>
                            <p class="class-info"><strong>Disciplina:</strong> Biologia</p>
                            <p class="class-info"><strong>Horário:</strong> Sexta, 10:00-12:00</p>
                            <p class="class-info"><strong>Última chamada:</strong> 12/04/2025</p>
                            <div class="attendance-stats">
                                <div class="stat-item stat-present">
                                    <div class="stat-number">27</div>
                                    <div class="stat-label">Presentes</div>
                                </div>
                                <div class="stat-item stat-absent">
                                    <div class="stat-number">2</div>
                                    <div class="stat-label">Ausentes</div>
                                </div>
                                <div class="stat-item stat-justified">
                                    <div class="stat-number">0</div>
                                    <div class="stat-label">Justificados</div>
                                </div>
                            </div>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    29 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">check_circle</span>
                                    Atualizada
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de Presença -->
    <div id="attendanceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalAttendanceTurma"></h2>
                <button class="close-modal" onclick="closeAttendanceModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="date-navigation">
                    <button class="date-nav-btn" onclick="changeDate(-1)">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <div class="current-date" id="currentDate">15 de Abril de 2025</div>
                    <button class="date-nav-btn" onclick="changeDate(1)">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>

                <div class="quick-actions">
                    <button class="quick-btn" onclick="markAllPresent()">Marcar Todos Presentes</button>
                    <button class="quick-btn" onclick="markAllAbsent()">Marcar Todos Ausentes</button>
                    <button class="quick-btn" onclick="copyPreviousDay()">Copiar Dia Anterior</button>
                    <button class="quick-btn" onclick="clearAll()">Limpar Tudo</button>
                </div>

                <div class="attendance-summary">
                    <div class="summary-item summary-present">
                        <div class="summary-number" id="presentCount">0</div>
                        <div class="summary-label">Presentes</div>
                    </div>
                    <div class="summary-item summary-absent">
                        <div class="summary-number" id="absentCount">0</div>
                        <div class="summary-label">Ausentes</div>
                    </div>
                    <div class="summary-item summary-late">
                        <div class="summary-number" id="lateCount">0</div>
                        <div class="summary-label">Atrasados</div>
                    </div>
                    <div class="summary-item summary-justified">
                        <div class="summary-number" id="justifiedCount">0</div>
                        <div class="summary-label">Justificados</div>
                    </div>
                </div>
                
                <table class="attendance-table" id="attendanceTable">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Aluno</th>
                            <th>Freq. Geral</th>
                            <th>Presente</th>
                            <th>Ausente</th>
                            <th>Atrasado</th>
                            <th>Justificado</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTableBody">
                        <!-- Alunos serão preenchidos via JavaScript -->
                    </tbody>
                </table>
                
                <div class="attendance-actions">
                    <div class="action-group">
                        <button class="btn-action" onclick="importarPresenca()">
                            <span class="material-symbols-outlined">upload</span>
                            Importar
                        </button>
                        <button class="btn-action" onclick="gerarRelatorio()">
                            <span class="material-symbols-outlined">assessment</span>
                            Relatório
                        </button>
                        <button class="btn-action btn-warning" onclick="enviarAlertas()">
                            <span class="material-symbols-outlined">notification_important</span>
                            Enviar Alertas
                        </button>
                    </div>
                    <div class="action-group">
                        <button class="btn-action" onclick="exportarPresenca()">
                            <span class="material-symbols-outlined">file_download</span>
                            Exportar
                        </button>
                        <button class="btn-action btn-success" onclick="salvarChamada()">
                            <span class="material-symbols-outlined">save</span>
                            Salvar Chamada
                        </button>
                        <button class="btn-action btn-primary" onclick="finalizarChamada()">
                            <span class="material-symbols-outlined">check</span>
                            Finalizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="dashboard-data.js"></script>
    <script>
        // Dados simulados específicos para presença
        const estudantesPresenca = [
            {
                id: 1, numero: 1, nome: "Ana Beatriz Silva", frequenciaGeral: 95,
                avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face",
                presenca: "presente", observacoes: ""
            },
            {
                id: 2, numero: 2, nome: "Bruno Santos Costa", frequenciaGeral: 88,
                avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face",
                presenca: "presente", observacoes: ""
            },
            {
                id: 3, numero: 3, nome: "Carla Oliveira Mendes", frequenciaGeral: 92,
                avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face",
                presenca: "presente", observacoes: ""
            },
            {
                id: 4, numero: 4, nome: "Diego Costa Ferreira", frequenciaGeral: 75,
                avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face",
                presenca: "ausente", observacoes: "Faltou sem justificativa"
            },
            {
                id: 5, numero: 5, nome: "Elena Rodriguez Lima", frequenciaGeral: 90,
                avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&h=150&fit=crop&crop=face",
                presenca: "atrasado", observacoes: "Chegou 15 min atrasado"
            }
        ];

        let currentDate = new Date();

        // Funções para o modal de presença
        function openAttendanceModal(id, nome, disciplina, totalAlunos) {
            const modal = document.getElementById('attendanceModal');
            document.getElementById('modalAttendanceTurma').textContent = `${nome} - ${disciplina}`;
            
            // Atualizar data atual
            updateCurrentDate();
            
            // Preencher tabela de presença
            const tbody = document.getElementById('attendanceTableBody');
            tbody.innerHTML = '';
            
            estudantesPresenca.forEach(estudante => {
                const row = document.createElement('tr');
                
                // Determinar classe da frequência
                let freqClass = 'freq-excellent';
                if (estudante.frequenciaGeral < 75) freqClass = 'freq-danger';
                else if (estudante.frequenciaGeral < 85) freqClass = 'freq-warning';
                else if (estudante.frequenciaGeral < 95) freqClass = 'freq-good';
                
                row.innerHTML = `
                    <td>${estudante.numero}</td>
                    <td>
                        <div class="student-info">
                            <img src="${estudante.avatar}" alt="${estudante.nome}" class="student-avatar">
                            <div>
                                <div class="student-name">${estudante.nome}</div>
                                <div class="student-number">Nº ${estudante.numero}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="frequency-indicator ${freqClass}"></span>
                        ${estudante.frequenciaGeral}%
                    </td>
                    <td>
                        <input type="radio" name="attendance-${estudante.id}" value="presente" class="attendance-radio" ${estudante.presenca === 'presente' ? 'checked' : ''} onchange="updateSummary()">
                    </td>
                    <td>
                        <input type="radio" name="attendance-${estudante.id}" value="ausente" class="attendance-radio" ${estudante.presenca === 'ausente' ? 'checked' : ''} onchange="updateSummary()">
                    </td>
                    <td>
                        <input type="radio" name="attendance-${estudante.id}" value="atrasado" class="attendance-radio" ${estudante.presenca === 'atrasado' ? 'checked' : ''} onchange="updateSummary()">
                    </td>
                    <td>
                        <input type="radio" name="attendance-${estudante.id}" value="justificado" class="attendance-radio" ${estudante.presenca === 'justificado' ? 'checked' : ''} onchange="updateSummary()">
                    </td>
                    <td>
                        <input type="text" class="justification-input" value="${estudante.observacoes}" placeholder="Observações...">
                    </td>
                `;
                
                tbody.appendChild(row);
            });
            
            // Atualizar resumo
            updateSummary();
            
            modal.style.display = 'block';
        }

        function closeAttendanceModal() {
            document.getElementById('attendanceModal').style.display = 'none';
        }

        function updateCurrentDate() {
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                weekday: 'long'
            };
            document.getElementById('currentDate').textContent = currentDate.toLocaleDateString('pt-BR', options);
        }

        function changeDate(days) {
            currentDate.setDate(currentDate.getDate() + days);
            updateCurrentDate();
            // Aqui você carregaria os dados de presença para a nova data
        }

        function updateSummary() {
            const radios = document.querySelectorAll('.attendance-radio:checked');
            let presente = 0, ausente = 0, atrasado = 0, justificado = 0;
            
            radios.forEach(radio => {
                switch(radio.value) {
                    case 'presente': presente++; break;
                    case 'ausente': ausente++; break;
                    case 'atrasado': atrasado++; break;
                    case 'justificado': justificado++; break;
                }
            });
            
            document.getElementById('presentCount').textContent = presente;
            document.getElementById('absentCount').textContent = ausente;
            document.getElementById('lateCount').textContent = atrasado;
            document.getElementById('justifiedCount').textContent = justificado;
        }

        function markAllPresent() {
            document.querySelectorAll('input[value="presente"]').forEach(radio => {
                radio.checked = true;
            });
            updateSummary();
        }

        function markAllAbsent() {
            document.querySelectorAll('input[value="ausente"]').forEach(radio => {
                radio.checked = true;
            });
            updateSummary();
        }

        function copyPreviousDay() {
            alert('Funcionalidade de copiar dia anterior será implementada');
        }

        function clearAll() {
            document.querySelectorAll('.attendance-radio').forEach(radio => {
                radio.checked = false;
            });
            document.querySelectorAll('.justification-input').forEach(input => {
                input.value = '';
            });
            updateSummary();
        }

        // Funções específicas do sistema de presença
        function importarPresenca() {
            alert('Funcionalidade de importação de presença será implementada');
        }

        function gerarRelatorio() {
            alert('Relatório de frequência gerado com sucesso!');
        }

        function enviarAlertas() {
            if (confirm('Enviar alertas para responsáveis de alunos com faltas excessivas?')) {
                alert('Alertas enviados com sucesso!');
            }
        }

        function exportarPresenca() {
            alert('Lista de presença exportada com sucesso!');
        }

        function salvarChamada() {
            alert('Chamada salva com sucesso!');
        }

        function finalizarChamada() {
            if (confirm('Tem certeza que deseja finalizar a chamada? Não será possível editar após finalizar.')) {
                alert('Chamada finalizada com sucesso!');
            }
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('attendanceModal');
            if (event.target == modal) {
                closeAttendanceModal();
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