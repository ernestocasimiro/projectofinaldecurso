<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    header("Location: ../login/login.php");
    exit;
}

include('dbconnection.php');

// Inicializar variáveis para mensagens
$_SESSION['error'] = '';
$_SESSION['success'] = '';

// Buscar estatísticas financeiras
try {
    // Total de mensalidades pagas no mês atual
    $statsStmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_pagas,
            SUM(valor) as valor_pago,
            (SELECT COUNT(*) FROM mensalidades WHERE status = 'Pendente' AND data_vencimento >= CURDATE()) as total_pendentes,
            (SELECT COUNT(*) FROM mensalidades WHERE status = 'Pendente' AND data_vencimento < CURDATE()) as total_vencidas
        FROM mensalidades 
        WHERE status = 'Pago' 
        AND MONTH(data_pagamento) = MONTH(CURRENT_DATE())
        AND YEAR(data_pagamento) = YEAR(CURRENT_DATE())
    ");
    $statsStmt->execute();
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $stats = [
        'total_pagas' => 0,
        'valor_pago' => 0,
        'total_pendentes' => 0,
        'total_vencidas' => 0
    ];
}

// Buscar lista de alunos (da tabela oficial 'estudantes')
$alunosStmt = $conn->prepare("SELECT id, fname, lname FROM estudantes ORDER BY lname, fname");
$alunosStmt->execute();
$alunos = $alunosStmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar lista de turmas
$sqlTurmas = "SELECT id, class_name FROM turma ORDER BY class_name";
$stmtTurmas = $conn->query($sqlTurmas);
$turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_fee'])) {
        // Coletar e sanitizar dados do formulário
        $estudante_id = filter_input(INPUT_POST, 'aluno', FILTER_VALIDATE_INT);
        $turma_id = filter_input(INPUT_POST, 'turma', FILTER_VALIDATE_INT);
        $data_vencimento = filter_input(INPUT_POST, 'data_vencimento', FILTER_SANITIZE_STRING);
        $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
        $status = strtoupper(trim($_POST['status']));
        $data_pagamento = !empty($_POST['data_pagamento']) ? filter_input(INPUT_POST, 'data_pagamento', FILTER_SANITIZE_STRING) : null;

        // Validação
        if (empty($estudante_id)) {
            $_SESSION['error'] = "Selecione um aluno válido.";
        } elseif (empty($turma_id)) {
            $_SESSION['error'] = "Selecione uma turma válida.";
        } elseif (empty($data_vencimento)) {
            $_SESSION['error'] = "Data de vencimento é obrigatória.";
        } elseif (empty($valor) || $valor <= 0) {
            $_SESSION['error'] = "Valor deve ser maior que zero.";
        } else {
            try {
                // Inserção na tabela mensalidades
                $sql = "INSERT INTO mensalidades (
                            estudante_id, turma_id, valor, data_vencimento, status, data_pagamento
                        ) VALUES (
                            :estudante_id, :turma_id, :valor, :data_vencimento, :status, :data_pagamento
                        )";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':estudante_id', $estudante_id, PDO::PARAM_INT);
                $stmt->bindParam(':turma_id', $turma_id, PDO::PARAM_INT);
                $stmt->bindParam(':valor', $valor);
                $stmt->bindParam(':data_vencimento', $data_vencimento);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':data_pagamento', $data_pagamento);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Mensalidade registrada com sucesso!";
                    header("Location: ".$_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $_SESSION['error'] = "Erro ao adicionar mensalidade.";
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Erro no banco de dados: " . $e->getMessage();
            }
        }
    }
    // Processar pagamento de mensalidade
    elseif (isset($_POST['pay_fee'])) {
        $mensalidade_id = filter_input(INPUT_POST, 'mensalidade_id', FILTER_VALIDATE_INT);
        $data_pagamento = date('Y-m-d');
        
        try {
            $updateStmt = $conn->prepare("
                UPDATE mensalidades 
                SET status = 'PAGO', data_pagamento = :data_pagamento 
                WHERE id = :id
            ");
            $updateStmt->bindParam(':id', $mensalidade_id, PDO::PARAM_INT);
            $updateStmt->bindParam(':data_pagamento', $data_pagamento);
            
            if ($updateStmt->execute()) {
                $_SESSION['success'] = "Pagamento registrado com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao registrar pagamento.";
            }
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}

// Buscar listagem de mensalidades com filtros
$filtro_turma = isset($_GET['turma']) ? $_GET['turma'] : '';
$filtro_status = isset($_GET['status']) ? $_GET['status'] : '';
$filtro_mes = isset($_GET['mes']) ? $_GET['mes'] : date('Y-m');

try {
    $query = "
        SELECT 
            m.id, 
            m.estudante_id,
            e.fname, 
            e.lname, 
            t.class_name, 
            t.class_grade, 
            m.data_vencimento, 
            m.valor, 
            m.status, 
            m.data_pagamento
        FROM mensalidades m
        LEFT JOIN estudantes e ON m.estudante_id = e.id
        LEFT JOIN turma t ON m.turma_id = t.id
        WHERE 1=1
    ";
    
    $params = [];
    
    if (!empty($filtro_turma)) {
        $query .= " AND t.id = :turma_id";
        $params[':turma_id'] = $filtro_turma;
    }
    
    if (!empty($filtro_status)) {
        if ($filtro_status == 'VENCIDO') {
            $query .= " AND m.status = 'PENDENTE' AND m.data_vencimento < CURDATE()";
        } else {
            $query .= " AND m.status = :status";
            $params[':status'] = $filtro_status;
        }
    }
    
    if (!empty($filtro_mes)) {
        $query .= " AND DATE_FORMAT(m.data_vencimento, '%Y-%m') = :mes";
        $params[':mes'] = $filtro_mes;
    }
    
    $query .= " ORDER BY m.data_vencimento DESC";
    
    $mensalidadesStmt = $conn->prepare($query);
    $mensalidadesStmt->execute($params);
    $mensalidades = $mensalidadesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensalidades = [];
    $_SESSION['error'] = "Erro ao buscar mensalidades: " . $e->getMessage();
}

// Buscar meses disponíveis para filtro
try {
    $mesesStmt = $conn->query("
        SELECT DISTINCT DATE_FORMAT(data_vencimento, '%Y-%m') as mes 
        FROM mensalidades 
        ORDER BY mes DESC
    ");
    $meses = $mesesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $meses = [];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensalidades - Dashboard Financeiro</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        :root {
            --primary-color: #4a6baf;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        /* Layout melhorado */
        .container {
            display: flex;
            min-height: 100vh;
            background-color: #f5f7fa;
        }
        
        .content {
            flex: 1;
            padding: 20px;
            overflow-x: hidden;
        }
        
        .dashboard-content {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-top: 20px;
        }
        
        /* Cards de estatísticas */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary-color);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .stat-card h3 {
            font-size: 1.5rem;
            margin: 0 0 5px 0;
            color: var(--dark-color);
        }
        
        .stat-card p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Filtros */
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
            align-items: center;
            padding: 15px;
            background-color: var(--light-color);
            border-radius: 8px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 200px;
        }
        
        .filter-group label {
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .filter-select, .filter-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        /* Tabela */
        .scrollable-table {
            overflow-x: auto;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 500;
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .data-table tr:hover {
            background-color: rgba(74, 107, 175, 0.05);
        }
        
        .student-name {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .student-name img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        /* Badges de status */
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-badge.pago {
            background-color: #e8f5e9;
            color: var(--success-color);
        }
        
        .status-badge.pendente {
            background-color: #fff8e1;
            color: var(--warning-color);
        }
        
        .status-badge.vencido {
            background-color: #ffebee;
            color: var(--danger-color);
        }
        
        /* Botões de ação */
        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #6c757d;
            padding: 5px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        
        .action-btn:hover {
            background-color: rgba(0, 0, 0, 0.05);
            color: var(--primary-color);
        }
        
        .pay-btn {
            color: var(--success-color) !important;
        }
        
        /* Paginação */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }
        
        .pagination-btn {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .pagination-btn:hover:not(:disabled) {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .pagination-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Alertas */
        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: var(--success-color);
            border: 1px solid #c8e6c9;
        }
        
        .alert-error {
            background-color: #ffebee;
            color: var(--danger-color);
            border: 1px solid #ffcdd2;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
            animation: fadeIn 0.3s;
        }
        
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 90%;
            max-width: 600px;
            overflow: hidden;
        }
        
        .modal-header {
            padding: 20px 24px;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .close-modal {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .close-modal:hover {
            color: #d1d1d1;
        }
        
        .modal-form {
            padding: 24px;
        }
        
        .form-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .form-group {
            flex: 1;
            margin-bottom: 8px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 107, 175, 0.2);
            outline: none;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon .currency-symbol {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        
        .input-with-icon input {
            padding-left: 30px;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            padding: 16px 24px;
            background-color: #f9f9f9;
            border-top: 1px solid #eee;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #3a5a9f;
        }
        
        .btn-outline {
            background-color: transparent;
            border-color: #ccc;
            color: #333;
        }
        
        .btn-outline:hover {
            background-color: #f5f5f5;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .modal-content {
                margin: 10% auto;
                width: 95%;
            }
            
            .filter-group {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Pitruca <br>Camama</h2>
                <span class="material-symbols-outlined menu-toggle" id="menuToggle">menu</span>
            </div>
            <div class="profile">
                <div class="profile-info">
                    <span><?php echo htmlspecialchars($_SESSION['fname']); ?></span>
                    <p>Gerente Financeiro</p>
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
                        <a href="mensalidades.php">
                            <span class="material-symbols-outlined">payments</span>
                            <span class="menu-text">Mensalidades</span>
                        </a>
                    </li>
                    <li>
                        <a href="inadimplencia.php">
                            <span class="material-symbols-outlined">warning</span>
                            <span class="menu-text">Inadimplência</span>
                        </a>
                    </li>
                    <li>
                        <a href="descontos.php">
                            <span class="material-symbols-outlined">local_offer</span>
                            <span class="menu-text">Descontos/Bolsas</span>
                        </a>
                    </li>
                    <li>
                        <a href="controle-propinas.php">
                            <span class="material-symbols-outlined">block</span>
                            <span class="menu-text">Controle de Propinas</span>
                        </a>
                    </li>
                    <li>
                        <a href="relatorios.php">
                            <span class="material-symbols-outlined">summarize</span>
                            <span class="menu-text">Relatórios</span>
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
                    <input type="text" placeholder="Pesquisar aluno, matrícula...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <!-- Exibir mensagens de sucesso/erro -->
                <?php if (!empty($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <span class="material-symbols-outlined">check_circle</span>
                        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <span class="material-symbols-outlined">error</span>
                        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <div class="page-header">
                    <div>
                        <h1>Gestão de Mensalidades</h1>
                        <p>Controle completo das mensalidades escolares</p>
                    </div>
                    <div class="page-actions">
                        <button class="btn-outline" onclick="exportToExcel()">
                            <span class="material-symbols-outlined">download</span>
                            Exportar
                        </button>
                        <button id="generateFeesBtn" class="btn-primary">
                            <span class="material-symbols-outlined">add_circle</span>
                            Nova Mensalidade
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(40, 167, 69, 0.1);">
                            <span class="material-symbols-outlined" style="color: var(--success-color);">check_circle</span>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['total_pagas'] ?? 0; ?></h3>
                            <p>Mensalidades Pagas</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(255, 193, 7, 0.1);">
                            <span class="material-symbols-outlined" style="color: var(--warning-color);">schedule</span>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['total_pendentes'] ?? 0; ?></h3>
                            <p>Mensalidades Pendentes</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(220, 53, 69, 0.1);">
                            <span class="material-symbols-outlined" style="color: var(--danger-color);">error</span>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['total_vencidas'] ?? 0; ?></h3>
                            <p>Mensalidades Vencidas</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(74, 107, 175, 0.1);">
                            <span class="material-symbols-outlined" style="color: var(--primary-color);">payments</span>
                        </div>
                        <div class="stat-info">
                            <h3>AOA <?php echo number_format($stats['valor_pago'] ?? 0, 2, ',', '.'); ?></h3>
                            <p>Total Arrecadado</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <form method="get" class="filter-container">
                    <div class="form-group">
                        <label for="turma">Turma:</label>
                        <select id="turma" name="turma" class="form-control">
                            <option value="">Todas as turmas</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?php echo $turma['id']; ?>" <?php echo ($filtro_turma == $turma['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($turma['class_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">Todos os status</option>
                            <option value="PAGO" <?php echo ($filtro_status == 'PAGO') ? 'selected' : ''; ?>>Pago</option>
                            <option value="PENDENTE" <?php echo ($filtro_status == 'PENDENTE') ? 'selected' : ''; ?>>Pendente</option>
                            <option value="VENCIDO" <?php echo ($filtro_status == 'VENCIDO') ? 'selected' : ''; ?>>Vencido</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="mes">Mês:</label>
                        <select id="mes" name="mes" class="form-control">
                            <?php foreach ($meses as $mes): ?>
                                <option value="<?php echo $mes['mes']; ?>" <?php echo ($filtro_mes == $mes['mes']) ? 'selected' : ''; ?>>
                                    <?php echo date('F Y', strtotime($mes['mes'].'-01')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">
                        <span class="material-symbols-outlined">filter_list</span>
                        Filtrar
                    </button>
                    <button type="button" onclick="window.location.href='mensalidades.php'" class="btn-outline">
                        <span class="material-symbols-outlined">refresh</span>
                        Limpar
                    </button>
                </form>

                <!-- Mensalidades Table -->
                <div class="scrollable-table">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Turma</th>
                                <th>Prazo</th>
                                <th>Valor</th>
                                <th>Estado</th>
                                <th>Pagamento</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($mensalidades)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center;">Nenhuma mensalidade encontrada</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($mensalidades as $mensalidade): ?>
                                    <?php
                                    $status = strtolower($mensalidade['status']);
                                    $isVencido = $status == 'pendente' && strtotime($mensalidade['data_vencimento']) < time();
                                    $statusClass = $isVencido ? 'vencido' : $status;
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="student-name">
                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($mensalidade['fname'] . ' ' . $mensalidade['lname']); ?>&background=random" alt="Aluno">
                                                <div>
                                                    <p><?php echo htmlspecialchars($mensalidade['fname'] . ' ' . $mensalidade['lname']); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($mensalidade['class_name']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($mensalidade['data_vencimento'])); ?></td>
                                        <td>AOA <?php echo number_format($mensalidade['valor'], 2, ',', '.'); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo $isVencido ? 'Vencido' : htmlspecialchars(ucfirst($status)); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $mensalidade['data_pagamento'] ? date('d/m/Y', strtotime($mensalidade['data_pagamento'])) : '-'; ?></td>
                                        <td>
                                            <div class="actions">
                                                <button class="action-btn" title="Visualizar" onclick="viewPayment(<?php echo $mensalidade['id']; ?>)">
                                                    <span class="material-symbols-outlined">visibility</span>
                                                </button>
                                                <button class="action-btn" title="Imprimir" onclick="printReceipt(<?php echo $mensalidade['id']; ?>)">
                                                    <span class="material-symbols-outlined">print</span>
                                                </button>
                                                <?php if ($status == 'pendente'): ?>
                                                    <button class="action-btn pay-btn" title="Registrar Pagamento" onclick="showPayModal(<?php echo $mensalidade['id']; ?>)">
                                                        <span class="material-symbols-outlined">payments</span>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <button class="pagination-btn" disabled>
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button class="pagination-btn active">1</button>
                    <button class="pagination-btn">2</button>
                    <button class="pagination-btn">3</button>
                    <span class="pagination-ellipsis">...</span>
                    <button class="pagination-btn">15</button>
                    <button class="pagination-btn">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para Nova Mensalidade -->
    <div id="newFeeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Nova Mensalidade</h2>
                <span class="close-modal" id="closeFeeModal">&times;</span>
            </div>
            
            <form method="POST" action="" class="modal-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="aluno">Aluno:</label>
                        <select id="aluno" name="aluno" class="form-control" required>
                            <option value="">Selecione o aluno</option>
                            <?php foreach ($alunos as $aluno): ?>
                                <option value="<?php echo $aluno['id']; ?>"><?php echo htmlspecialchars($aluno['fname'] . ' ' . $aluno['lname']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="turma">Turma:</label>
                        <select id="turma" name="turma" class="form-control" required>
                            <option value="">Selecione a turma</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?php echo $turma['id']; ?>"><?php echo htmlspecialchars($turma['class_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="valor">Valor (AOA):</label>
                        <div class="input-with-icon">
                            <input type="number" id="valor" name="valor" step="0.01" min="0" class="form-control" placeholder="0,00" required>
                            <span class="currency-symbol">Kz</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_vencimento">Data de Vencimento:</label>
                        <input type="date" id="data_vencimento" name="data_vencimento" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="PENDENTE">Pendente</option>
                            <option value="PAGO">Pago</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="paymentDateGroup" style="display: none;">
                        <label for="data_pagamento">Data de Pagamento:</label>
                        <input type="date" id="data_pagamento" name="data_pagamento" class="form-control">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-outline" id="cancelFeeBtn">Cancelar</button>
                    <button type="submit" name="submit_fee" class="btn-primary">Salvar Mensalidade</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Pagamento -->
    <div id="payModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Registrar Pagamento</h2>
                <span class="close-modal" id="closePayModal">&times;</span>
            </div>
            
            <form method="POST" action="" class="modal-form">
                <input type="hidden" id="mensalidade_id" name="mensalidade_id">
                
                <div class="form-group">
                    <label>Data de Pagamento:</label>
                    <input type="text" class="form-control" value="<?php echo date('d/m/Y'); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="payment_value">Valor a Pagar (AOA):</label>
                    <div class="input-with-icon">
                        <input type="number" id="payment_value" class="form-control" disabled>
                        <span class="currency-symbol">Kz</span>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-outline" id="cancelPayBtn">Cancelar</button>
                    <button type="submit" name="pay_fee" class="btn-primary">Confirmar Pagamento</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    <script>
        // Inicializar datepickers
        flatpickr("#data_vencimento", {
            dateFormat: "Y-m-d",
            locale: "pt",
            minDate: "today"
        });
        
        flatpickr("#data_pagamento", {
            dateFormat: "Y-m-d",
            locale: "pt",
            maxDate: "today"
        });
        
        // Controle do modal de nova mensalidade
        const newFeeModal = document.getElementById('newFeeModal');
        const generateFeesBtn = document.getElementById('generateFeesBtn');
        const closeFeeModal = document.getElementById('closeFeeModal');
        const cancelFeeBtn = document.getElementById('cancelFeeBtn');
        
        generateFeesBtn.addEventListener('click', () => {
            newFeeModal.style.display = 'block';
        });
        
        closeFeeModal.addEventListener('click', () => {
            newFeeModal.style.display = 'none';
        });
        
        cancelFeeBtn.addEventListener('click', () => {
            newFeeModal.style.display = 'none';
        });
        
        // Fechar modal ao clicar fora
        window.addEventListener('click', (event) => {
            if (event.target === newFeeModal) {
                newFeeModal.style.display = 'none';
            }
            if (event.target === payModal) {
                payModal.style.display = 'none';
            }
        });
        
        // Mostrar/ocultar campo de data de pagamento conforme status
        document.getElementById('status').addEventListener('change', function() {
            const paymentDateGroup = document.getElementById('paymentDateGroup');
            if (this.value === 'PAGO') {
                paymentDateGroup.style.display = 'block';
            } else {
                paymentDateGroup.style.display = 'none';
            }
        });
        
        // Controle do modal de pagamento
        const payModal = document.getElementById('payModal');
        const closePayModal = document.getElementById('closePayModal');
        const cancelPayBtn = document.getElementById('cancelPayBtn');
        
        function showPayModal(mensalidadeId) {
            // Aqui você pode buscar os detalhes da mensalidade via AJAX se necessário
            document.getElementById('mensalidade_id').value = mensalidadeId;
            payModal.style.display = 'block';
        }
        
        closePayModal.addEventListener('click', () => {
            payModal.style.display = 'none';
        });
        
        cancelPayBtn.addEventListener('click', () => {
            payModal.style.display = 'none';
        });
        
        // Função para exportar para Excel
        function exportToExcel() {
            // Implementar lógica de exportação para Excel
            alert('Exportando dados para Excel...');
        }
        
        // Função para visualizar pagamento
        function viewPayment(id) {
            // Implementar visualização detalhada do pagamento
            alert('Visualizando pagamento ID: ' + id);
        }
        
        // Função para imprimir recibo
        function printReceipt(id) {
            // Implementar impressão de recibo
            alert('Imprimindo recibo para ID: ' + id);
        }
    </script>
</body>
</html>