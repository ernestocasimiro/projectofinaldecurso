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

        // Buscar lista de estudantes
        $estudantes = [];
        try {
            $stmt = $conn->prepare("SELECT fname, lname, data_nascimento, genero, num_bi, endereco, status, telefone, area FROM estudantes ORDER BY fname ASC");
            $stmt->execute();
            $estudantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar estudantes: " . $e->getMessage();
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
    <title>Alunos - Dashboard de Professores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Estilos melhorados para a lista de estudantes */
        .students-section {
            margin-top: 20px;
        }
        
        .students-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.active-students {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-card.total-students {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .stat-card.inactive-students {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        
        .stat-card.new-students {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #444;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: block;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .stat-icon {
            float: right;
            font-size: 2rem;
            opacity: 0.7;
        }
        
        .view-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .view-btn {
            padding: 8px 16px;
            border: 1px solid #ddd;
            background-color: #fff;
            color: #666;
            cursor: pointer;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .view-btn.active {
            background-color: #3a5bb9;
            color: white;
            border-color: #3a5bb9;
        }
        
        .view-btn:hover:not(.active) {
            background-color: #f5f5f5;
        }
        
        /* Visualização em Cards */
        .students-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .student-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
            position: relative;
            overflow: hidden;
        }
        
        .student-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3a5bb9, #667eea);
        }
        
        .student-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .student-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .student-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            flex-shrink: 0;
        }
        
        .student-info h3 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .student-id {
            color: #666;
            font-size: 0.85rem;
        }
        
        .student-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #666;
        }
        
        .detail-item .material-symbols-outlined {
            font-size: 1rem;
            color: #3a5bb9;
        }
        
        .student-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .student-actions {
            display: flex;
            gap: 5px;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            background-color: #f8f9fa;
            color: #666;
        }
        
        .action-btn:hover {
            background-color: #e9ecef;
            transform: scale(1.1);
        }
        
        .action-btn.view { color: #17a2b8; }
        .action-btn.edit { color: #28a745; }
        .action-btn.delete { color: #dc3545; }
        
        .action-btn.view:hover { background-color: #d1ecf1; }
        .action-btn.edit:hover { background-color: #d4edda; }
        .action-btn.delete:hover { background-color: #f8d7da; }
        
        /* Visualização em Tabela Melhorada */
        .students-table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        
        .students-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        
        .students-table thead {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        
        .students-table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .students-table td {
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
            vertical-align: middle;
        }
        
        .students-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .students-table tbody tr:hover {
            background-color: #f8f9ff;
            transform: scale(1.01);
        }
        
        .table-student-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .table-avatar {
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
        }
        
        .table-student-details h4 {
            margin: 0 0 2px 0;
            color: #333;
            font-size: 0.95rem;
            font-weight: 600;
        }
        
        .table-student-id {
            color: #666;
            font-size: 0.8rem;
        }
        
        .table-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        
        .filter-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }
        
        .filter-row {
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
        
        .filter-select, .search-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            background-color: #fff;
        }
        
        .filter-select:focus, .search-input:focus {
            outline: none;
            border-color: #3a5bb9;
            box-shadow: 0 0 0 3px rgba(58, 91, 185, 0.1);
        }
        
        .search-group {
            position: relative;
        }
        
        .search-input {
            padding-left: 45px;
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.2rem;
        }
        
        .results-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #495057;
        }
        
        .results-count {
            font-weight: 600;
        }
        
        .sort-options {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .sort-btn {
            padding: 6px 12px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .sort-btn:hover {
            background-color: #f8f9fa;
        }
        
        .sort-btn.active {
            background-color: #3a5bb9;
            color: white;
            border-color: #3a5bb9;
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
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }
        
        .pagination-btn {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }
        
        .pagination-btn:hover:not(.disabled) {
            background-color: #f8f9fa;
        }
        
        .pagination-btn.active {
            background-color: #3a5bb9;
            color: white;
            border-color: #3a5bb9;
        }
        
        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        @media (max-width: 768px) {
            .students-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .students-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
            }
            
            .students-table-container {
                overflow-x: auto;
            }
            
            .view-toggle {
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .students-stats {
                grid-template-columns: 1fr;
            }
            
            .student-details {
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
                    <li class="active">
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
               
=======
                <a href="configuracoes.php">
                    <span class="material-symbols-outlined">settings</span>
                    <span class="menu-text">Configurações</span>
                </a>
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
                    <input type="text" placeholder="Pesquisar alunos...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Gestão de Alunos</h1>
<<<<<<< HEAD
=======
                    <button class="btn-primary" onclick="adicionarAluno()">
                        <span class="material-symbols-outlined">person_add</span>
                        Adicionar Aluno
                    </button>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                </div>
                
                <!-- Estatísticas dos Alunos -->
                <div class="students-stats">
                    <div class="stat-card total-students">
                        <span class="material-symbols-outlined stat-icon">groups</span>
                        <span class="stat-number"><?php echo count($estudantes); ?></span>
                        <span class="stat-label">Total de Alunos</span>
                    </div>
                    <div class="stat-card active-students">
                        <span class="material-symbols-outlined stat-icon">person_check</span>
                        <span class="stat-number"><?php echo count(array_filter($estudantes, function($e) { return $e['status'] == 'ativo'; })); ?></span>
                        <span class="stat-label">Alunos Ativos</span>
                    </div>
                    <div class="stat-card inactive-students">
                        <span class="material-symbols-outlined stat-icon">person_off</span>
                        <span class="stat-number"><?php echo count(array_filter($estudantes, function($e) { return $e['status'] == 'inativo'; })); ?></span>
                        <span class="stat-label">Alunos Inativos</span>
                    </div>
                    <div class="stat-card new-students">
                        <span class="material-symbols-outlined stat-icon">person_add</span>
                        <span class="stat-number">12</span>
                        <span class="stat-label">Novos este Mês</span>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="filter-container">
                    <div class="filter-row">
                        <div class="search-group">
                            <label>Buscar Aluno</label>
                            <span class="material-symbols-outlined search-icon">search</span>
                            <input type="text" id="search-input" class="search-input" placeholder="Digite o nome do aluno..." onkeyup="filtrarAlunos()">
                        </div>
                        <div class="filter-group">
                            <label for="turma-filter">Filtrar por Turma</label>
                            <select id="turma-filter" class="filter-select" onchange="filtrarAlunos()">
                                <option value="todos">Todas as Turmas</option>
                                <option value="9A">9º Ano A</option>
                                <option value="9B">9º Ano B</option>
                                <option value="10A">10º Ano A</option>
                                <option value="10B">10º Ano B</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="status-filter">Status</label>
                            <select id="status-filter" class="filter-select" onchange="filtrarAlunos()">
                                <option value="todos">Todos os Status</option>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="genero-filter">Gênero</label>
                            <select id="genero-filter" class="filter-select" onchange="filtrarAlunos()">
                                <option value="todos">Todos</option>
                                <option value="masculino">Masculino</option>
                                <option value="feminino">Feminino</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Toggle de Visualização -->
                <div class="view-toggle">
                    <button class="view-btn active" onclick="toggleView('cards')" id="cards-view">
                        <span class="material-symbols-outlined">grid_view</span>
                        Cards
                    </button>
                    <button class="view-btn" onclick="toggleView('table')" id="table-view">
                        <span class="material-symbols-outlined">table_rows</span>
                        Tabela
                    </button>
                </div>
                
                <!-- Informações dos Resultados -->
                <div class="results-info">
                    <div class="results-count">
                        Exibindo <strong id="visible-count"><?php echo count($estudantes); ?></strong> de <strong><?php echo count($estudantes); ?></strong> alunos
                    </div>
                    <div class="sort-options">
                        <span>Ordenar por:</span>
                        <button class="sort-btn active" onclick="sortStudents('name')">
                            <span class="material-symbols-outlined">sort_by_alpha</span>
                            Nome
                        </button>
                        <button class="sort-btn" onclick="sortStudents('status')">
                            <span class="material-symbols-outlined">swap_vert</span>
                            Status
                        </button>
                    </div>
                </div>

                <!-- Visualização em Cards -->
                <div class="students-grid" id="students-cards">
                    <?php if(empty($estudantes)): ?>
                        <div class="empty-state">
                            <span class="material-symbols-outlined">school</span>
                            <h3>Nenhum aluno encontrado</h3>
                            <p>Não há alunos cadastrados no sistema.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($estudantes as $index => $estudante): ?>
                            <div class="student-card" 
                                 data-nome="<?php echo htmlspecialchars(strtolower($estudante['fname'] . ' ' . $estudante['lname'])); ?>"
<<<<<<< HEAD
=======
                                 data-status="<?php echo htmlspecialchars($estudante['status']); ?>"
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                                 data-genero="<?php echo htmlspecialchars($estudante['genero']); ?>">
                                <div class="student-header">
                                    <div class="student-avatar">
                                        <?php echo strtoupper(substr($estudante['fname'], 0, 1) . substr($estudante['lname'], 0, 1)); ?>
                                    </div>
                                    <div class="student-info">
                                        <h3><?php echo htmlspecialchars($estudante['fname'] . ' ' . $estudante['lname']); ?></h3>
                                        <div class="student-id">ID: <?php echo str_pad($index + 1, 4, '0', STR_PAD_LEFT); ?></div>
                                    </div>
                                </div>
                                
                                <div class="student-details">
                                    <div class="detail-item">
                                        <span class="material-symbols-outlined">cake</span>
                                        <?php echo date('d/m/Y', strtotime($estudante['data_nascimento'])); ?>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-symbols-outlined">person</span>
                                        <?php echo ucfirst($estudante['genero']); ?>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-symbols-outlined">phone</span>
                                        <?php echo htmlspecialchars($estudante['telefone']); ?>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-symbols-outlined">email</span>
                                        <?php echo htmlspecialchars($estudante['area']); ?>
                                    </div>
                                </div>
                                
                                <div class="student-status">
<<<<<<< HEAD
=======
                                    <span class="status-badge <?php echo $estudante['status'] == 'ativo' ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo ucfirst($estudante['status']); ?>
                                    </span>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                                    <div class="student-actions">
                                        <button class="action-btn view" title="Ver detalhes" onclick="verDetalhes(<?php echo $index; ?>)">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </button>
<<<<<<< HEAD
                                        
=======
                                        <button class="action-btn edit" title="Editar" onclick="editarAluno(<?php echo $index; ?>)">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                        <button class="action-btn delete" title="Excluir" onclick="excluirAluno(<?php echo $index; ?>)">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Visualização em Tabela -->
                <div class="students-table-container" id="students-table" style="display: none;">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Turma</th>
<<<<<<< HEAD
=======
                                <th>Status</th>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                                <th>Contato</th>
                                <th>Data Nasc.</th>
                                <th>Gênero</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="students-table-body">
                            <?php if(!empty($estudantes)): ?>
                                <?php foreach($estudantes as $index => $estudante): ?>
                                    <tr class="student-row" 
                                        data-nome="<?php echo htmlspecialchars(strtolower($estudante['fname'] . ' ' . $estudante['lname'])); ?>"
<<<<<<< HEAD
=======
                                        data-status="<?php echo htmlspecialchars($estudante['status']); ?>"
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                                        data-genero="<?php echo htmlspecialchars($estudante['genero']); ?>">
                                        <td>
                                            <div class="table-student-info">
                                                <div class="table-avatar">
                                                    <?php echo strtoupper(substr($estudante['fname'], 0, 1) . substr($estudante['lname'], 0, 1)); ?>
                                                </div>
                                                <div class="table-student-details">
                                                    <h4><?php echo htmlspecialchars($estudante['fname'] . ' ' . $estudante['lname']); ?></h4>
                                                    <div class="table-student-id">ID: <?php echo str_pad($index + 1, 4, '0', STR_PAD_LEFT); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>9º Ano A</td>
                                        <td>
<<<<<<< HEAD
=======
                                            <span class="status-badge <?php echo $estudante['status'] == 'ativo' ? 'status-active' : 'status-inactive'; ?>">
                                                <?php echo ucfirst($estudante['status']); ?>
                                            </span>
                                        </td>
                                        <td>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                                            <div><?php echo htmlspecialchars($estudante['telefone']); ?></div>
                                            <div style="font-size: 0.8rem; color: #666;"><?php echo htmlspecialchars($estudante['area']); ?></div>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($estudante['data_nascimento'])); ?></td>
                                        <td><?php echo ucfirst($estudante['genero']); ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <button class="action-btn view" title="Ver detalhes" onclick="verDetalhes(<?php echo $index; ?>)">
                                                    <span class="material-symbols-outlined">visibility</span>
                                                </button>
<<<<<<< HEAD
                                                
=======
                                                <button class="action-btn edit" title="Editar" onclick="editarAluno(<?php echo $index; ?>)">
                                                    <span class="material-symbols-outlined">edit</span>
                                                </button>
                                                <button class="action-btn delete" title="Excluir" onclick="excluirAluno(<?php echo $index; ?>)">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        let currentView = 'cards';
        let currentSort = 'name';
        
        function toggleView(view) {
            currentView = view;
            
            // Atualizar botões
            document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(view + '-view').classList.add('active');
            
            // Mostrar/esconder visualizações
            if (view === 'cards') {
                document.getElementById('students-cards').style.display = 'grid';
                document.getElementById('students-table').style.display = 'none';
            } else {
                document.getElementById('students-cards').style.display = 'none';
                document.getElementById('students-table').style.display = 'block';
            }
        }
        
        function filtrarAlunos() {
            const searchInput = document.getElementById('search-input').value.toLowerCase();
            const turmaFilter = document.getElementById('turma-filter').value;
            const statusFilter = document.getElementById('status-filter').value;
            const generoFilter = document.getElementById('genero-filter').value;
            
            let visibleCount = 0;
            
            // Filtrar cards
            const cards = document.querySelectorAll('.student-card');
            cards.forEach(card => {
                const nome = card.getAttribute('data-nome');
                const status = card.getAttribute('data-status');
                const genero = card.getAttribute('data-genero');
                
                let showCard = true;
                
                if (searchInput && !nome.includes(searchInput)) showCard = false;
                if (statusFilter !== 'todos' && status !== statusFilter) showCard = false;
                if (generoFilter !== 'todos' && genero !== generoFilter) showCard = false;
                
                if (showCard) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Filtrar tabela
            const rows = document.querySelectorAll('.student-row');
            rows.forEach(row => {
                const nome = row.getAttribute('data-nome');
                const status = row.getAttribute('data-status');
                const genero = row.getAttribute('data-genero');
                
                let showRow = true;
                
                if (searchInput && !nome.includes(searchInput)) showRow = false;
                if (statusFilter !== 'todos' && status !== statusFilter) showRow = false;
                if (generoFilter !== 'todos' && genero !== generoFilter) showRow = false;
                
                row.style.display = showRow ? '' : 'none';
            });
            
            // Atualizar contador
            document.getElementById('visible-count').textContent = visibleCount;
        }
        
        function sortStudents(criteria) {
            currentSort = criteria;
            
            // Atualizar botões de ordenação
            document.querySelectorAll('.sort-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Implementar ordenação (simulada)
            console.log('Ordenando por:', criteria);
        }
        
        function adicionarAluno() {
            alert('Funcionalidade de adicionar aluno será implementada');
        }
        
        function verDetalhes(index) {
            alert('Ver detalhes do aluno #' + (index + 1));
        }
        
        function editarAluno(index) {
            alert('Editar aluno #' + (index + 1));
        }
        
        function excluirAluno(index) {
            if (confirm('Tem certeza que deseja excluir este aluno?')) {
                alert('Aluno #' + (index + 1) + ' excluído com sucesso!');
            }
        }
        
        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar eventos
            console.log('Sistema de gestão de alunos carregado');
        });
    </script>
</body>
</html>