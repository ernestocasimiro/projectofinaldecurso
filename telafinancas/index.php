<?php
    session_start();

    if (isset($_SESSION['id']) && isset($_SESSION['role'])) {

    // Inclui a conexão com a base de dados
    require_once('dbconnection.php');

    // Código para obter os totais
    try {
        // Total de alunos
        $stmt = $conn->query("SELECT COUNT(*) AS total FROM estudantes");
        $totalAlunos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total de professores
        $stmt = $conn->query("SELECT COUNT(*) AS total FROM professores");
        $totalProfessores = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total de turmas
        $stmt = $conn->query("SELECT COUNT(*) AS total FROM turma");
        $totalTurmas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Obter dados financeiros
        $stmt = $conn->query("SELECT SUM(valor) AS total FROM mensalidades WHERE status = 'pago' AND MONTH(data_pagamento) = MONTH(CURRENT_DATE())");
        $mensalidadesRecebidas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $stmt = $conn->query("SELECT SUM(valor) AS total FROM mensalidades WHERE status = 'pendente' AND data_vencimento >= CURRENT_DATE()");
        $mensalidadesPendentes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $stmt = $conn->query("SELECT SUM(valor) AS total, COUNT(*) AS qtd FROM mensalidades WHERE status = 'atrasado'");
        $inadimplencia = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalInadimplencia = $inadimplencia['total'] ?? 0;
        $qtdInadimplentes = $inadimplencia['qtd'] ?? 0;

        $stmt = $conn->query("SELECT COUNT(*) AS total FROM matriculas WHERE MONTH(data_matricula) = MONTH(CURRENT_DATE())");
        $novasMatriculas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    } catch (PDOException $e) {
        echo "Erro ao buscar dados: " . $e->getMessage();
        $totalAlunos = $totalProfessores = $totalTurmas = 0;
        $mensalidadesRecebidas = $mensalidadesPendentes = $totalInadimplencia = $qtdInadimplentes = $novasMatriculas = 0;
    }
    
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Financeiro - Escola</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar transações, alunos...">
                </div>
                <div class="top-bar-actions">
                    <div class="notification-badge">
                        <span class="material-symbols-outlined notification">notifications</span>
                        <span class="badge">3</span>
                    </div>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="welcome-section">
                    <div class="welcome-text">
                        <h1>Gestão Financeira Escolar</h1>
                        <p>Período: <strong id="current-month-year"></strong> | Ano Letivo <strong>2025</strong> | <strong><?php echo $totalAlunos; ?> alunos</strong> | <strong><?php echo $totalProfessores; ?> professores</strong> | <strong><?php echo $totalTurmas; ?> turmas</strong></p>
                        <script> 
                            document.getElementById('current-month-year').textContent = new Date().toLocaleDateString('pt-PT', {month: 'long', year: 'numeric'}); 
                        </script>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn-primary" id="newTransactionBtn">
                            <span class="material-symbols-outlined">add</span>
                            Nova Transação
                        </button>
                        <button class="btn-secondary" style="
                            background-color: #4CAF50; 
                            color: white; 
                            border: none; 
                            padding: 10px 20px; 
                            border-radius: 6px; 
                            cursor: pointer; 
                            display: inline-flex; 
                            align-items: center; 
                            gap: 8px; 
                            font-size: 16px;
                            transition: background-color 0.3s ease;
                        " 
                        onmouseover="this.style.backgroundColor='#45a049'" 
                        onmouseout="this.style.backgroundColor='#4CAF50'">
                            <span class="material-symbols-outlined" style="font-size: 20px;">print</span>
                            Imprimir Relatório
                        </button>

                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(76, 175, 80, 0.1);">
                            <span class="material-symbols-outlined" style="color: #4caf50;">payments</span>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($mensalidadesRecebidas, 2, ',', '.'); ?> AOA</h3>
                            <p>Mensalidades Recebidas (Mês)</p>
                            <div class="stat-trend up">
                                <span class="material-symbols-outlined">trending_up</span>
                                <span>12% vs mês anterior</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(255, 152, 0, 0.1);">
                            <span class="material-symbols-outlined" style="color: #ff9800;">schedule</span>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($mensalidadesPendentes, 2, ',', '.'); ?> AOA</h3>
                            <p>Mensalidades Pendentes</p>
                            <div class="stat-trend down">
                                <span class="material-symbols-outlined">trending_down</span>
                                <span>5% vs mês anterior</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(244, 67, 54, 0.1);">
                            <span class="material-symbols-outlined" style="color: #f44336;">warning</span>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($totalInadimplencia, 2, ',', '.'); ?> AOA</h3>
                            <p>Inadimplência (<?php echo $qtdInadimplentes; ?> alunos)</p>
                            <div class="stat-trend up">
                                <span class="material-symbols-outlined">trending_up</span>
                                <span>8% vs mês anterior</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(33, 150, 243, 0.1);">
                            <span class="material-symbols-outlined" style="color: #2196f3;">person_add</span>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $novasMatriculas; ?></h3>
                            <p>Novas Matrículas (Mês)</p>
                            <div class="stat-trend up">
                                <span class="material-symbols-outlined">trending_up</span>
                                <span>15% vs mês anterior</span>
                            </div>
                        </div>
                    </div>
                </div>

    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .dashboard-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.07);
            font-family: 'Segoe UI', sans-serif;
        }

        .wide-card {
            width: 100%;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .card-header h2 {
            font-size: 18px;
            color: #333;
            margin: 0;
        }

        .time-filter select {
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            color: #333;
            background-color: #fafafa;
            transition: 0.2s ease-in-out;
        }

        .time-filter select:focus {
            outline: none;
            border-color: #3f51b5;
        }

        .card-content {
            margin-top: 10px;
        }

        canvas {
            width: 100% !important;
            height: auto !important;
        }
    </style>

<div class="dashboard-grid">
    <!-- Gráfico de Receitas e Despesas -->
    <div class="dashboard-card wide-card">
        <div class="card-header">
            <h2>Fluxo Financeiro Mensal</h2>
            <div class="time-filter">
                <select id="chartTimeFilter">
                    <option value="7">Últimos 7 dias</option>
                    <option value="30" selected>Últimos 30 dias</option>
                    <option value="90">Últimos 3 meses</option>
                </select>
            </div>
        </div>
        <div class="card-content">
            <canvas id="financeChart" height="250"></canvas>
        </div>
    </div>
</div>


                    <!-- Mensalidades Pendentes
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Mensalidades Pendentes</h2>
                            <a href="mensalidades.php" class="view-all">Ver todas</a>
                        </div>
                        <div class="card-content">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Aluno</th>
                                            <th>Turma</th>
                                            <th>Vencimento</th>
                                            <th>Valor</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            $stmt = $conn->query("SELECT m.id, e.nome, e.foto, e.matricula, t.nome as turma, m.data_vencimento, m.valor 
                                                                  FROM mensalidades m
                                                                  JOIN estudantes e ON m.estudante_id = e.id
                                                                  JOIN turma t ON e.turma_id = t.id
                                                                  WHERE m.status = 'pendente' AND m.data_vencimento >= CURRENT_DATE()
                                                                  ORDER BY m.data_vencimento ASC
                                                                  LIMIT 5");
                                            $mensalidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            
                                            if (count($mensalidades) > 0) {
                                                foreach ($mensalidades as $mensalidade) {
                                                    echo '<tr>
                                                            <td>
                                                                <div class="student-name">
                                                                    <img src="'.($mensalidade['foto'] ?: 'https://via.placeholder.com/40').'" alt="Aluno">
                                                                    <div>
                                                                        <p>'.$mensalidade['nome'].'</p>
                                                                        <span class="text-muted">Matrícula: '.$mensalidade['matricula'].'</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>'.$mensalidade['turma'].'</td>
                                                            <td>'.date('d/m/Y', strtotime($mensalidade['data_vencimento'])).'</td>
                                                            <td>'.number_format($mensalidade['valor'], 2, ',', '.').' AOA</td>
                                                            <td>
                                                                <div class="action-buttons">
                                                                    <button class="btn-icon success" title="Registrar Pagamento">
                                                                        <span class="material-symbols-outlined">check_circle</span>
                                                                    </button>
                                                                    <button class="btn-icon warning" title="Enviar Lembrete">
                                                                        <span class="material-symbols-outlined">mail</span>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="5" class="text-center">Nenhuma mensalidade pendente</td></tr>';
                                            }
                                        } catch (PDOException $e) {
                                            echo '<tr><td colspan="5" class="text-center">Erro ao carregar dados</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>  -->
<!--
<style>
                .dashboard-card {
                    background-color: #fff;
                    border-radius: 10px;
                    padding: 20px;
                    margin-bottom: 20px;
                    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.07);
                    font-family: 'Segoe UI', sans-serif;
                }

                .card-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 8px;
                    margin-bottom: 15px;
                }

                .card-header h2 {
                    font-size: 18px;
                    color: #333;
                }

                .view-all {
                    font-size: 13px;
                    text-decoration: none;
                    color: #3f51b5;
                    font-weight: 500;
                    transition: 0.2s;
                }

                .view-all:hover {
                    text-decoration: underline;
                }

                .chart-container {
                    width: 100%;
                    height: 200px;
                    position: relative;
                    margin-bottom: 15px;
                }

                .revenue-legend {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 15px;
                    padding: 10px 0;
                    border-top: 1px solid #f0f0f0;
                }

                .legend-item {
                    display: flex;
                    align-items: center;
                    font-size: 14px;
                    color: #444;
                }

                .legend-color {
                    display: inline-block;
                    width: 14px;
                    height: 14px;
                    border-radius: 3px;
                    margin-right: 8px;
                }
</style>

 Receitas por Categoria 
<div class="dashboard-card">
    <div class="card-header">
        <h2>Receitas por Categoria</h2>
        <a href="receitas.php" class="view-all">Ver todas</a>
    </div>
    <div class="card-content">
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
        <div class="revenue-legend">
            <div class="legend-item">
                <span class="legend-color" style="background-color: #4a6fdc;"></span>
                <span>Mensalidades</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #4caf50;"></span>
                <span>Cantina</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #ff9800;"></span>
                <span>Material Didático</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #9c27b0;"></span>
                <span>Eventos</span>
            </div>
        </div>
    </div>
</div>


<style>
                .dashboard-card {
                    background-color: #fff;
                    border-radius: 10px;
                    padding: 20px;
                    margin-bottom: 20px;
                    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.07);
                    font-family: 'Segoe UI', sans-serif;
                }

                .card-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 15px;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 8px;
                }

                .card-header h2 {
                    font-size: 18px;
                    color: #333;
                }

                .view-all {
                    font-size: 13px;
                    text-decoration: none;
                    color: #3f51b5;
                    font-weight: 500;
                    transition: 0.2s;
                }

                .view-all:hover {
                    text-decoration: underline;
                }

                .alerts-list {
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                }

                .alert-item {
                    display: flex;
                    align-items: flex-start;
                    background-color: #f9f9f9;
                    border-left: 4px solid #ccc;
                    border-radius: 6px;
                    padding: 12px 14px;
                }

                .alert-icon {
                    margin-right: 12px;
                    color: #555;
                    font-size: 20px;
                }

                .alert-content h4 {
                    font-size: 15px;
                    margin: 0 0 4px;
                    color: #222;
                }

                .alert-content p {
                    margin: 0;
                    font-size: 13px;
                    color: #555;
                }

                .alert-time {
                    font-size: 12px;
                    color: #888;
                    margin-top: 5px;
                    display: block;
                }

                /* Cores por nível */
                .alert-item.critical {
                    border-left-color: #d32f2f;
                    background-color: #fff5f5;
                }

                .alert-item.warning {
                    border-left-color: #fbc02d;
                    background-color: #fffef4;
                }

                .alert-item.info {
                    border-left-color: #1976d2;
                    background-color: #f4f9ff;
                }
</style>

<!-- Estilos para Alertas Financeiros -->
<style>
    .dashboard-card {
        background-color: #fff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        font-family: 'Segoe UI', sans-serif;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .card-header h2 {
        font-size: 20px;
        margin: 0;
        color: #333;
    }

    .card-header .view-all {
        font-size: 14px;
        color: #007BFF;
        text-decoration: none;
    }

    .card-header .view-all:hover {
        text-decoration: underline;
    }

    .alerts-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .alert-item {
        display: flex;
        align-items: center;
        border-radius: 8px;
        padding: 12px 15px;
        background-color: #f9f9f9;
    }

    .alert-item.critical {
        background-color: #ffe5e5;
        border-left: 5px solid #e53935;
    }

    .alert-item.warning {
        background-color: #fff8e1;
        border-left: 5px solid #fbc02d;
    }

    .alert-item.info {
        background-color: #e3f2fd;
        border-left: 5px solid #2196f3;
    }

    .alert-icon {
        margin-right: 15px;
        font-size: 30px;
        color: #555;
    }

    .alert-content h4 {
        margin: 0;
        font-size: 16px;
        color: #222;
    }

    .alert-content p {
        margin: 4px 0;
        font-size: 14px;
        color: #555;
    }

    .alert-time {
        font-size: 12px;
        color: #888;
    }
</style>

<!-- Alertas Financeiros -->
<div class="dashboard-card">
    <div class="card-header">
        <h2>Alertas Financeiros</h2>
        <a href="alertas.php" class="view-all">Ver todos</a>
    </div>
    <div class="card-content">
        <div class="alerts-list">
            <div class="alert-item critical">
                <div class="alert-icon">
                    <span class="material-symbols-outlined">warning</span>
                </div>
                <div class="alert-content">
                    <h4><?php echo $qtdInadimplentes; ?> mensalidades vencidas</h4>
                    <p>Total de <?php echo number_format($totalInadimplencia, 2, ',', '.'); ?> AOA em atraso</p>
                    <span class="alert-time">Hoje</span>
                </div>
            </div>
            <div class="alert-item warning">
                <div class="alert-icon">
                    <span class="material-symbols-outlined">schedule</span>
                </div>
                <div class="alert-content">
                    <h4>Pagamento de fornecedores</h4>
                    <p>Vencimento em 3 dias - 25.400,00 AOA</p>
                    <span class="alert-time"><?php echo date('d/m/Y', strtotime('+3 days')); ?></span>
                </div>
            </div>
            <div class="alert-item info">
                <div class="alert-icon">
                    <span class="material-symbols-outlined">info</span>
                </div>
                <div class="alert-content">
                    <h4>Relatório mensal disponível</h4>
                    <p>Relatório de <?php echo date('F', strtotime('last month')); ?> já pode ser gerado</p>
                    <span class="alert-time">Ontem</span>
                </div>
            </div>
        </div>
    </div>
</div>



                    <!-- Inadimplência por Turma
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Inadimplência por Turma</h2>
                            <a href="inadimplencia.php" class="view-all">Ver detalhes</a>
                        </div>
                        <div class="card-content">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Turma</th>
                                            <th>Alunos</th>
                                            <th>Inadimplentes</th>
                                            <th>Taxa</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            $stmt = $conn->query("SELECT t.nome as turma, 
                                                                 COUNT(e.id) as total_alunos,
                                                                 SUM(CASE WHEN m.status = 'atrasado' THEN 1 ELSE 0 END) as inadimplentes,
                                                                 SUM(CASE WHEN m.status = 'atrasado' THEN m.valor ELSE 0 END) as valor
                                                                 FROM turma t
                                                                 JOIN estudantes e ON t.id = e.turma_id
                                                                 LEFT JOIN mensalidades m ON e.id = m.estudante_id
                                                                 GROUP BY t.nome
                                                                 ORDER BY inadimplentes DESC
                                                                 LIMIT 5");
                                            $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            
                                            if (count($turmas) > 0) {
                                                foreach ($turmas as $turma) {
                                                    $taxa = $turma['total_alunos'] > 0 ? round(($turma['inadimplentes'] / $turma['total_alunos']) * 100) : 0;
                                                    echo '<tr>
                                                            <td>'.$turma['turma'].'</td>
                                                            <td>'.$turma['total_alunos'].'</td>
                                                            <td>'.$turma['inadimplentes'].'</td>
                                                            <td>
                                                                <div class="progress-bar">
                                                                    <div class="progress" style="width: '.$taxa.'%; background-color: '.($taxa > 30 ? '#f44336' : ($taxa > 15 ? '#ff9800' : '#4caf50')).';"></div>
                                                                    <span>'.$taxa.'%</span>
                                                                </div>
                                                            </td>
                                                            <td>'.number_format($turma['valor'], 2, ',', '.').' AOA</td>
                                                        </tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="5" class="text-center">Nenhum dado disponível</td></tr>';
                                            }
                                        } catch (PDOException $e) {
                                            echo '<tr><td colspan="5" class="text-center">Erro ao carregar dados</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Nova Transação -->
    <div class="modal" id="transactionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Nova Transação</h3>
                <span class="modal-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="transactionForm">
                    <div class="form-group">
                        <label for="transactionType">Tipo de Transação</label>
                        <select id="transactionType" required>
                            <option value="">Selecione...</option>
                            <option value="receita">Receita</option>
                            <option value="despesa">Despesa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transactionCategory">Categoria</label>
                        <select id="transactionCategory" required>
                            <option value="">Selecione...</option>
                            <option value="mensalidade">Mensalidade</option>
                            <option value="material">Material Didático</option>
                            <option value="cantina">Cantina</option>
                            <option value="evento">Evento</option>
                            <option value="salario">Salários</option>
                            <option value="fornecedor">Fornecedores</option>
                            <option value="manutencao">Manutenção</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transactionAmount">Valor (AOA)</label>
                        <input type="number" id="transactionAmount" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="transactionDate">Data</label>
                        <input type="date" id="transactionDate" required>
                    </div>
                    <div class="form-group">
                        <label for="transactionDescription">Descrição</label>
                        <textarea id="transactionDescription" rows="3"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-secondary modal-close">Cancelar</button>
                        <button type="submit" class="btn-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

        // Modal functionality
        const modal = document.getElementById('transactionModal');
        const modalClose = document.querySelectorAll('.modal-close');
        const newTransactionBtn = document.getElementById('newTransactionBtn');

        newTransactionBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
            document.getElementById('transactionDate').valueAsDate = new Date();
        });

        modalClose.forEach(function(element) {
            element.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        });

        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

        // Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Finance Chart (Line)
            const financeCtx = document.getElementById('financeChart').getContext('2d');
            const financeChart = new Chart(financeCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [
                        {
                            label: 'Receitas',
                            data: [1200000, 1500000, 1800000, 1600000, 1900000, 2200000],
                            borderColor: '#4caf50',
                            backgroundColor: 'rgba(76, 175, 80, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Despesas',
                            data: [800000, 950000, 1100000, 1050000, 1200000, 1150000],
                            borderColor: '#f44336',
                            backgroundColor: 'rgba(244, 67, 54, 0.1)',
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw.toLocaleString('pt-PT') + ' AOA';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('pt-PT') + ' AOA';
                                }
                            }
                        }
                    }
                }
            });

            // Revenue Chart (Doughnut)
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Mensalidades', 'Cantina', 'Material Didático', 'Eventos'],
                    datasets: [{
                        data: [86.5, 7.2, 3.9, 2.4],
                        backgroundColor: [
                            '#4a6fdc',
                            '#4caf50',
                            '#ff9800',
                            '#9c27b0'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw + '%';
                                }
                            }
                        }
                    }
                }
            });

            // Time filter for finance chart
            document.getElementById('chartTimeFilter').addEventListener('change', function() {
                // In a real application, you would fetch new data based on the selected time period
                console.log('Time filter changed to:', this.value);
            });
        });

        // Form submission
        document.getElementById('transactionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would handle the form submission (AJAX or regular form submit)
            alert('Transação registrada com sucesso!');
            modal.style.display = 'none';
            this.reset();
        });

        // Simulate real-time data updates
        function updateStats() {
            // In a real application, you would fetch updated data from the server
            console.log('Atualizando estatísticas...');
        }

        // Update every 30 seconds
        setInterval(updateStats, 30000);
    </script>
</body>
</html>
<?php }else{
    header("Location: ..login/login.php");
    exit;
} ?>