<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    header("Location: ..login/login.php");
    exit;
}

// Inclui a conexão com a base de dados
require_once('dbconnection.php');

// Inicializa variáveis para mensagens
$_SESSION['error'] = '';
$_SESSION['success'] = '';

// Buscar alunos inadimplentes (com mensalidades vencidas)
try {
    // Query para buscar alunos com mensalidades vencidas
    $sqlInadimplentes = "
        SELECT 
            e.id AS estudante_id,
            e.fname,
            e.lname,
            t.class_name,
            m.data_vencimento,
            m.valor,
            m.status,
            DATEDIFF(CURDATE(), m.data_vencimento) AS dias_atraso,
            m.data_pagamento
        FROM mensalidades m
        JOIN estudantes e ON m.estudante_id = e.id
        JOIN turma t ON m.turma_id = t.id
        WHERE m.status = 'Vencido'
        ORDER BY dias_atraso DESC
    ";
    
    $stmtInadimplentes = $conn->prepare($sqlInadimplentes);
    $stmtInadimplentes->execute();
    $inadimplentes = $stmtInadimplentes->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular estatísticas
    $totalInadimplentes = count($inadimplentes);
    $valorTotalAtraso = 0;
    $diasAtrasoTotal = 0;
    
    foreach ($inadimplentes as $aluno) {
        $valorTotalAtraso += $aluno['valor'];
        $diasAtrasoTotal += $aluno['dias_atraso'];
    }
    
    $diasMedioAtraso = $totalInadimplentes > 0 ? round($diasAtrasoTotal / $totalInadimplentes) : 0;
    
    // Buscar estatísticas gerais
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM estudantes");
    $totalAlunos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM professores");
    $totalProfessores = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM turma");
    $totalTurmas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Buscar estatísticas de ações de cobrança (exemplo)
    $emailsEnviados = $conn->query("SELECT COUNT(*) FROM log_cobranca WHERE tipo = 'email' AND data >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetchColumn();
    $ligacoesRealizadas = $conn->query("SELECT COUNT(*) FROM log_cobranca WHERE tipo = 'call' AND data >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetchColumn();
    $smsEnviados = $conn->query("SELECT COUNT(*) FROM log_cobranca WHERE tipo = 'sms' AND data >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetchColumn();
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Erro ao buscar dados: " . $e->getMessage();
    $inadimplentes = [];
    $totalInadimplentes = $valorTotalAtraso = $diasMedioAtraso = 0;
    $emailsEnviados = $ligacoesRealizadas = $smsEnviados = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inadimplência - Dashboard Financeiro</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-badge.inactive {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .status-badge.warning {
            background-color: #fff8e1;
            color: #ff8f00;
        }
        
        .status-badge.alert {
            background-color: #fce4ec;
            color: #ad1457;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 16px;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        
        .student-name img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        
        .text-muted {
            color: #666;
            font-size: 0.8rem;
        }
        
        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            margin: 0 5px;
            color: #555;
            transition: color 0.2s;
        }
        
        .action-btn:hover {
            color: #4a6baf;
        }
        
        .next-action-date {
            background-color: #f5f5f5;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            min-width: 50px;
            margin-right: 15px;
        }
        
        .next-action-date .day {
            font-size: 1.2rem;
            font-weight: bold;
            display: block;
        }
        
        .next-action-date .month {
            font-size: 0.8rem;
            text-transform: uppercase;
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
                    <span><?php echo $_SESSION['fname']; ?></span>
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
                    <li>
                        <a href="mensalidades.php">
                            <span class="material-symbols-outlined">payments</span>
                            <span class="menu-text">Mensalidades</span>
                        </a>
                    </li>
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar inadimplente..." id="searchInput">
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
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <div class="page-header">
                    <div>
                        <h1>Gestão de Inadimplência</h1>
                        <p>Controle de alunos em atraso e ações de cobrança</p>
                    </div>
                    <div class="page-actions">
                        <button class="btn-outline" id="sendEmailsBtn">
                            <span class="material-symbols-outlined">email</span>
                            Enviar Avisos
                        </button>
                        <button class="btn-primary" id="startCollectionBtn">
                            <span class="material-symbols-outlined">call</span>
                            Iniciar Cobrança
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(244, 67, 54, 0.1);">
                            <span class="material-symbols-outlined" style="color: #f44336;">warning</span>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalInadimplentes; ?></h3>
                            <p>Alunos Inadimplentes</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(255, 152, 0, 0.1);">
                            <span class="material-symbols-outlined" style="color: #ff9800;">attach_money</span>
                        </div>
                        <div class="stat-info">
                            <h3>AOA <?php echo number_format($valorTotalAtraso, 2, ',', '.'); ?></h3>
                            <p>Valor Total em Atraso</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(156, 39, 176, 0.1);">
                            <span class="material-symbols-outlined" style="color: #9c27b0;">schedule</span>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $diasMedioAtraso; ?></h3>
                            <p>Dias Médio de Atraso</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(76, 175, 80, 0.1);">
                            <span class="material-symbols-outlined" style="color: #4caf50;">trending_up</span>
                        </div>
                        <div class="stat-info">
                            <h3>12</h3>
                            <p>Recuperados (Mês)</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-container">
                    <div class="filter-group">
                        <label>Dias de Atraso:</label>
                        <select class="filter-select" id="diasAtrasoFilter">
                            <option value="all">Todos</option>
                            <option value="1-15">1-15 dias</option>
                            <option value="16-30">16-30 dias</option>
                            <option value="31-60">31-60 dias</option>
                            <option value="60+">Mais de 60 dias</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Turma:</label>
                        <select class="filter-select" id="turmaFilter">
                            <option value="all">Todas as turmas</option>
                            <?php
                            // Buscar turmas distintas dos inadimplentes
                            $turmasDistintas = [];
                            foreach ($inadimplentes as $aluno) {
                                if (!in_array($aluno['class_name'], array_column($turmasDistintas, 'class_name'))) {
                                    $turmasDistintas[] = [
                                        'class_name' => $aluno['class_name']
                                    ];
                                }
                            }
                            
                            foreach ($turmasDistintas as $turma): ?>
                                <option value="<?php echo htmlspecialchars($turma['class_name']); ?>">
                                    <?php echo htmlspecialchars($turma['class_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Valor:</label>
                        <select class="filter-select" id="valorFilter">
                            <option value="all">Todos os valores</option>
                            <option value="0-500">Até AOA 500.000</option>
                            <option value="500-1000">AOA 500.000 - AOA 1.000.000</option>
                            <option value="1000+">Acima de AOA 1.000.000</option>
                        </select>
                    </div>
                    <button class="btn-outline" id="applyFiltersBtn">
                        <span class="material-symbols-outlined">filter_list</span>
                        Aplicar
                    </button>
                    <button class="btn-outline" id="resetFiltersBtn">
                        <span class="material-symbols-outlined">refresh</span>
                        Limpar
                    </button>
                </div>

                <!-- Inadimplentes Table -->
                <div class="table-container">
                    <table class="data-table" id="inadimplentesTable">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Turma</th>
                                <th>Vencimento</th>
                                <th>Dias Atraso</th>
                                <th>Valor</th>
                                <th>Última Ação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inadimplentes as $aluno): 
                                // Determinar classe CSS baseada nos dias de atraso
                                $diasAtraso = $aluno['dias_atraso'];
                                if ($diasAtraso > 60) {
                                    $statusClass = 'inactive';
                                } elseif ($diasAtraso > 30) {
                                    $statusClass = 'alert';
                                } else {
                                    $statusClass = 'warning';
                                }
                            ?>
                                <tr data-dias-atraso="<?php echo $diasAtraso; ?>" 
                                    data-turma="<?php echo htmlspecialchars($aluno['class_name']); ?>"
                                    data-valor="<?php echo $aluno['valor']; ?>">
                                    <td>
                                        <div class="student-name">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($aluno['fname'] . ' ' . $aluno['lname']); ?>&background=random" alt="Aluno">
                                            <div>
                                                <p><?php echo htmlspecialchars($aluno['fname'] . ' ' . $aluno['lname']); ?></p>
                                               </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($aluno['class_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($aluno['data_vencimento'])); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo $diasAtraso; ?> dias
                                        </span>
                                    </td>
                                    <td>AOA <?php echo number_format($aluno['valor'], 2, ',', '.'); ?></td>
                                    <td>-</td>
                                    <td>
                                        <div class="actions">
                                            <button class="action-btn call-btn" data-id="<?php echo $aluno['estudante_id']; ?>">
                                                <span class="material-symbols-outlined">call</span>
                                            </button>
                                            <button class="action-btn email-btn" data-id="<?php echo $aluno['estudante_id']; ?>">
                                                <span class="material-symbols-outlined">email</span>
                                            </button>
                                            <button class="action-btn negotiate-btn" data-id="<?php echo $aluno['estudante_id']; ?>">
                                                <span class="material-symbols-outlined">handshake</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($inadimplentes)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center;">Nenhum aluno inadimplente encontrado</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Action Summary -->
                <div class="dashboard-grid" style="margin-top: 30px;">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Ações de Cobrança</h2>
                        </div>
                        <div class="card-content">
                            <div class="action-summary">
                                <div class="action-item">
                                    <div class="action-icon" style="background-color: rgba(33, 150, 243, 0.1);">
                                        <span class="material-symbols-outlined" style="color: #2196f3;">email</span>
                                    </div>
                                    <div class="action-details">
                                        <h4>Emails Enviados</h4>
                                        <p><?php echo $emailsEnviados; ?> emails esta semana</p>
                                    </div>
                                </div>
                                <div class="action-item">
                                    <div class="action-icon" style="background-color: rgba(76, 175, 80, 0.1);">
                                        <span class="material-symbols-outlined" style="color: #4caf50;">call</span>
                                    </div>
                                    <div class="action-details">
                                        <h4>Ligações Realizadas</h4>
                                        <p><?php echo $ligacoesRealizadas; ?> ligações esta semana</p>
                                    </div>
                                </div>
                                <div class="action-item">
                                    <div class="action-icon" style="background-color: rgba(255, 152, 0, 0.1);">
                                        <span class="material-symbols-outlined" style="color: #ff9800;">sms</span>
                                    </div>
                                    <div class="action-details">
                                        <h4>SMS Enviados</h4>
                                        <p><?php echo $smsEnviados; ?> SMS esta semana</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Próximas Ações</h2>
                        </div>
                        <div class="card-content">
                            <div class="next-actions">
                                <div class="next-action-item">
                                    <div class="next-action-date">
                                        <span class="day"><?php echo date('d'); ?></span>
                                        <span class="month"><?php echo date('M'); ?></span>
                                    </div>
                                    <div class="next-action-details">
                                        <h4>Envio de Notificações</h4>
                                        <p>
                                            <?php 
                                                $count = 0;
                                                foreach ($inadimplentes as $aluno) {
                                                    if ($aluno['dias_atraso'] <= 15) $count++;
                                                }
                                                echo $count;
                                            ?> 
                                            alunos com até 15 dias de atraso
                                        </p>
                                    </div>
                                </div>
                                <div class="next-action-item">
                                    <div class="next-action-date">
                                        <span class="day"><?php echo date('d', strtotime('+2 days')); ?></span>
                                        <span class="month"><?php echo date('M', strtotime('+2 days')); ?></span>
                                    </div>
                                    <div class="next-action-details">
                                        <h4>Ligações de Cobrança</h4>
                                        <p>
                                            <?php 
                                                $count = 0;
                                                foreach ($inadimplentes as $aluno) {
                                                    if ($aluno['dias_atraso'] > 15 && $aluno['dias_atraso'] <= 30) $count++;
                                                }
                                                echo $count;
                                            ?> 
                                            alunos com 16-30 dias de atraso
                                        </p>
                                    </div>
                                </div>
                                <div class="next-action-item">
                                    <div class="next-action-date">
                                        <span class="day"><?php echo date('d', strtotime('+5 days')); ?></span>
                                        <span class="month"><?php echo date('M', strtotime('+5 days')); ?></span>
                                    </div>
                                    <div class="next-action-details">
                                        <h4>Reunião com Responsáveis</h4>
                                        <p>
                                            <?php 
                                                $count = 0;
                                                foreach ($inadimplentes as $aluno) {
                                                    if ($aluno['dias_atraso'] > 30) $count++;
                                                }
                                                echo $count;
                                            ?> 
                                            casos críticos (>30 dias)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Menu toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

        // Filtros
        document.getElementById('applyFiltersBtn').addEventListener('click', function() {
            const diasAtrasoFilter = document.getElementById('diasAtrasoFilter').value;
            const turmaFilter = document.getElementById('turmaFilter').value;
            const valorFilter = document.getElementById('valorFilter').value;
            
            const rows = document.querySelectorAll('#inadimplentesTable tbody tr');
            
            rows.forEach(row => {
                const diasAtraso = parseInt(row.getAttribute('data-dias-atraso'));
                const turma = row.getAttribute('data-turma');
                const valor = parseFloat(row.getAttribute('data-valor'));
                
                let showRow = true;
                
                // Aplicar filtro de dias de atraso
                if (diasAtrasoFilter !== 'all') {
                    if (diasAtrasoFilter === '1-15' && (diasAtraso < 1 || diasAtraso > 15)) {
                        showRow = false;
                    } else if (diasAtrasoFilter === '16-30' && (diasAtraso < 16 || diasAtraso > 30)) {
                        showRow = false;
                    } else if (diasAtrasoFilter === '31-60' && (diasAtraso < 31 || diasAtraso > 60)) {
                        showRow = false;
                    } else if (diasAtrasoFilter === '60+' && diasAtraso <= 60) {
                        showRow = false;
                    }
                }
                
                // Aplicar filtro de turma
                if (showRow && turmaFilter !== 'all' && turma !== turmaFilter) {
                    showRow = false;
                }
                
                // Aplicar filtro de valor
                if (showRow && valorFilter !== 'all') {
                    if (valorFilter === '0-500' && valor > 500) {
                        showRow = false;
                    } else if (valorFilter === '500-1000' && (valor < 500 || valor > 1000)) {
                        showRow = false;
                    } else if (valorFilter === '1000+' && valor <= 1000) {
                        showRow = false;
                    }
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        });
        
        // Resetar filtros
        document.getElementById('resetFiltersBtn').addEventListener('click', function() {
            document.getElementById('diasAtrasoFilter').value = 'all';
            document.getElementById('turmaFilter').value = 'all';
            document.getElementById('valorFilter').value = 'all';
            
            const rows = document.querySelectorAll('#inadimplentesTable tbody tr');
            rows.forEach(row => {
                row.style.display = '';
            });
        });
        
        // Pesquisa rápida
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#inadimplentesTable tbody tr');
            
            rows.forEach(row => {
                const studentName = row.querySelector('.student-name p').textContent.toLowerCase();
                const matricula = row.querySelector('.text-muted').textContent.toLowerCase();
                
                if (studentName.includes(searchTerm) || matricula.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Botões de ação
        document.querySelectorAll('.call-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const studentId = this.getAttribute('data-id');
                alert(`Iniciar chamada para o aluno ID: ${studentId}`);
                // Aqui você pode implementar a lógica para registrar a chamada
            });
        });
        
        document.querySelectorAll('.email-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const studentId = this.getAttribute('data-id');
                alert(`Enviar email para o aluno ID: ${studentId}`);
                // Aqui você pode implementar a lógica para enviar email
            });
        });
        
        document.querySelectorAll('.negotiate-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const studentId = this.getAttribute('data-id');
                alert(`Negociar pagamento com o aluno ID: ${studentId}`);
                // Aqui você pode implementar a lógica para negociação
            });
        });
        
        // Botões principais
        document.getElementById('sendEmailsBtn').addEventListener('click', function() {
            alert('Enviando emails para todos os inadimplentes...');
            // Implementar lógica para enviar emails em massa
        });
        
        document.getElementById('startCollectionBtn').addEventListener('click', function() {
            alert('Iniciando processo de cobrança...');
            // Implementar lógica para iniciar cobrança
        });
    </script>
</body>
</html>