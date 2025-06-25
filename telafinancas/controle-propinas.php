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

    } catch (PDOException $e) {
        echo "Erro ao buscar dados: " . $e->getMessage();
        $totalAlunos = $totalProfessores = $totalTurmas = 0;
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Propinas - Dashboard Financeiro</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
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
        
        <li class="active">
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
                    <input type="text" placeholder="Pesquisar aluno...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <div>
                        <h1>Controle de Propinas</h1>
                        <p>Gestão de acesso ao sistema para alunos inadimplentes</p>
                    </div>
                    <div class="page-actions">
                        <button class="btn-outline">
                            <span class="material-symbols-outlined">email</span>
                            Notificar Todos
                        </button>
                        <button class="btn-primary">
                            <span class="material-symbols-outlined">block</span>
                            Bloquear Acesso
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(244, 67, 54, 0.1);">
                            <span class="material-symbols-outlined" style="color: #f44336;">block</span>
                        </div>
                        <div class="stat-info">
                            <h3>63</h3>
                            <p>Acessos Bloqueados</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(255, 152, 0, 0.1);">
                            <span class="material-symbols-outlined" style="color: #ff9800;">warning</span>
                        </div>
                        <div class="stat-info">
                            <h3>28</h3>
                            <p>Avisos Enviados</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(76, 175, 80, 0.1);">
                            <span class="material-symbols-outlined" style="color: #4caf50;">lock_open</span>
                        </div>
                        <div class="stat-info">
                            <h3>12</h3>
                            <p>Acessos Liberados Hoje</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(33, 150, 243, 0.1);">
                            <span class="material-symbols-outlined" style="color: #2196f3;">attach_money</span>
                        </div>
                        <div class="stat-info">
                            <h3>AOA 45.200</h3>
                            <p>Propinas em Atraso</p>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="tabs">
                    <button class="tab-btn active">Todos os Alunos</button>
                    <button class="tab-btn">Acesso Bloqueado</button>
                    <button class="tab-btn">Aviso Enviado</button>
                    <button class="tab-btn">Acesso Liberado</button>
                </div>

                <!-- Filters -->
                <div class="filter-container">
                    <div class="filter-group">
                        <label>Dias de Atraso:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>1-15 dias</option>
                            <option>16-30 dias</option>
                            <option>31-60 dias</option>
                            <option>Mais de 60 dias</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Turma:</label>
                        <select class="filter-select">
                            <option>Todas as turmas</option>
                            <option>1º Ano</option>
                            <option>2º Ano</option>
                            <option>3º Ano</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Status de Acesso:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>Bloqueado</option>
                            <option>Liberado</option>
                            <option>Aviso Enviado</option>
                        </select>
                    </div>
                </div>

                <!-- Propinas Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Turma</th>
                                <th>Propinas em Atraso</th>
                                <th>Valor Total</th>
                                <th>Dias Atraso</th>
                                <th>Status de Acesso</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="student-name">
                                        <img src="https://via.placeholder.com/40" alt="Aluno">
                                        <div>
                                            <p>Carlos Mendes</p>
                                            <span class="text-muted">ID: 2025045</span>
                                        </div>
                                    </div>
                                </td>
                                <td>9º Ano A</td>
                                <td>2 meses</td>
                                <td>AOA 1.700,00</td>
                                <td>32 dias</td>
                                <td><span class="status-badge inactive">Bloqueado</span></td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn" title="Liberar Acesso">
                                            <span class="material-symbols-outlined">lock_open</span>
                                        </button>
                                        <button class="action-btn" title="Enviar Notificação">
                                            <span class="material-symbols-outlined">email</span>
                                        </button>
                                        <button class="action-btn" title="Ver Histórico">
                                            <span class="material-symbols-outlined">history</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="student-name">
                                        <img src="https://via.placeholder.com/40" alt="Aluno">
                                        <div>
                                            <p>Fernanda Lima</p>
                                            <span class="text-muted">ID: 2025067</span>
                                        </div>
                                    </div>
                                </td>
                                <td>7º Ano B</td>
                                <td>1 mês</td>
                                <td>AOA 780,00</td>
                                <td>27 dias</td>
                                <td><span class="status-badge" style="background-color: rgba(255, 152, 0, 0.1); color: #ff9800;">Aviso Enviado</span></td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn" title="Bloquear Acesso">
                                            <span class="material-symbols-outlined">block</span>
                                        </button>
                                        <button class="action-btn" title="Enviar Notificação">
                                            <span class="material-symbols-outlined">email</span>
                                        </button>
                                        <button class="action-btn" title="Ver Histórico">
                                            <span class="material-symbols-outlined">history</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="student-name">
                                        <img src="https://via.placeholder.com/40" alt="Aluno">
                                        <div>
                                            <p>Roberto Silva</p>
                                            <span class="text-muted">ID: 2025089</span>
                                        </div>
                                    </div>
                                </td>
                                <td>8º Ano C</td>
                                <td>1 mês</td>
                                <td>AOA 850,00</td>
                                <td>12 dias</td>
                                <td><span class="status-badge active">Liberado</span></td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn" title="Bloquear Acesso">
                                            <span class="material-symbols-outlined">block</span>
                                        </button>
                                        <button class="action-btn" title="Enviar Notificação">
                                            <span class="material-symbols-outlined">email</span>
                                        </button>
                                        <button class="action-btn" title="Ver Histórico">
                                            <span class="material-symbols-outlined">history</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="student-name">
                                        <img src="https://via.placeholder.com/40" alt="Aluno">
                                        <div>
                                            <p>Ana Pereira</p>
                                            <span class="text-muted">ID: 2025102</span>
                                        </div>
                                    </div>
                                </td>
                                <td>6º Ano A</td>
                                <td>3 meses</td>
                                <td>AOA 2.340,00</td>
                                <td>75 dias</td>
                                <td><span class="status-badge inactive">Bloqueado</span></td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn" title="Liberar Acesso">
                                            <span class="material-symbols-outlined">lock_open</span>
                                        </button>
                                        <button class="action-btn" title="Enviar Notificação">
                                            <span class="material-symbols-outlined">email</span>
                                        </button>
                                        <button class="action-btn" title="Ver Histórico">
                                            <span class="material-symbols-outlined">history</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
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
                    <button class="pagination-btn">7</button>
                    <button class="pagination-btn">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>

                <!-- Controle de Acesso -->
                <div class="dashboard-grid" style="margin-top: 30px;">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Configurações de Bloqueio</h2>
                        </div>
                        <div class="card-content">
                            <div class="block-settings">
                                <div class="setting-item">
                                    <div class="setting-icon">
                                        <span class="material-symbols-outlined">timer</span>
                                    </div>
                                    <div class="setting-details">
                                        <h4>Dias para Bloqueio Automático</h4>
                                        <p>Bloquear acesso após 30 dias de atraso</p>
                                    </div>
                                    <button class="btn-text">Editar</button>
                                </div>
                                <div class="setting-item">
                                    <div class="setting-icon">
                                        <span class="material-symbols-outlined">notifications</span>
                                    </div>
                                    <div class="setting-details">
                                        <h4>Avisos Automáticos</h4>
                                        <p>Enviar aviso 7 dias antes do bloqueio</p>
                                    </div>
                                    <button class="btn-text">Editar</button>
                                </div>
                                <div class="setting-item">
                                    <div class="setting-icon">
                                        <span class="material-symbols-outlined">visibility_off</span>
                                    </div>
                                    <div class="setting-details">
                                        <h4>Restrições de Acesso</h4>
                                        <p>Bloquear notas, materiais e avaliações</p>
                                    </div>
                                    <button class="btn-text">Editar</button>
                                </div>
                                <div class="setting-item">
                                    <div class="setting-icon">
                                        <span class="material-symbols-outlined">admin_panel_settings</span>
                                    </div>
                                    <div class="setting-details">
                                        <h4>Permissões de Liberação</h4>
                                        <p>Somente coordenadores e financeiro</p>
                                    </div>
                                    <button class="btn-text">Editar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Modelos de Notificação</h2>
                        </div>
                        <div class="card-content">
                            <div class="notification-templates">
                                <div class="template-item">
                                    <div class="template-icon">
                                        <span class="material-symbols-outlined">warning</span>
                                    </div>
                                    <div class="template-details">
                                        <h4>Aviso de Iminente Bloqueio</h4>
                                        <p>Notificação sobre bloqueio em 7 dias por falta de pagamento</p>
                                    </div>
                                    <button class="btn-text">Usar</button>
                                </div>
                                <div class="template-item">
                                    <div class="template-icon">
                                        <span class="material-symbols-outlined">block</span>
                                    </div>
                                    <div class="template-details">
                                        <h4>Notificação de Bloqueio</h4>
                                        <p>Comunicado sobre bloqueio de acesso ao sistema</p>
                                    </div>
                                    <button class="btn-text">Usar</button>
                                </div>
                                <div class="template-item">
                                    <div class="template-icon">
                                        <span class="material-symbols-outlined">lock_open</span>
                                    </div>
                                    <div class="template-details">
                                        <h4>Confirmação de Liberação</h4>
                                        <p>Aviso sobre liberação de acesso após pagamento</p>
                                    </div>
                                    <button class="btn-text">Usar</button>
                                </div>
                                <div class="template-item">
                                    <div class="template-icon">
                                        <span class="material-symbols-outlined">support_agent</span>
                                    </div>
                                    <div class="template-details">
                                        <h4>Instruções para Regularização</h4>
                                        <p>Orientações para pagamento e liberação de acesso</p>
                                    </div>
                                    <button class="btn-text">Usar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Bloqueios -->
                <div class="dashboard-card" style="margin-top: 20px;">
                    <div class="card-header">
                        <h2>Histórico de Bloqueios e Liberações</h2>
                    </div>
                    <div class="card-content">
                        <div class="block-history">
                            <div class="history-item">
                                <div class="history-icon" style="background-color: rgba(244, 67, 54, 0.1);">
                                    <span class="material-symbols-outlined" style="color: #f44336;">block</span>
                                </div>
                                <div class="history-details">
                                    <div class="history-header">
                                        <h4>Carlos Mendes - Bloqueio de Acesso</h4>
                                        <span>15/04/2025 09:30</span>
                                    </div>
                                    <p>Bloqueio automático após 30 dias de atraso no pagamento de propinas.</p>
                                    <div class="history-user">Por: Sistema</div>
                                </div>
                            </div>
                            <div class="history-item">
                                <div class="history-icon" style="background-color: rgba(76, 175, 80, 0.1);">
                                    <span class="material-symbols-outlined" style="color: #4caf50;">lock_open</span>
                                </div>
                                <div class="history-details">
                                    <div class="history-header">
                                        <h4>Juliana Ferreira - Liberação de Acesso</h4>
                                        <span>14/04/2025 14:15</span>
                                    </div>
                                    <p>Acesso liberado após confirmação de pagamento das propinas em atraso.</p>
                                    <div class="history-user">Por: Maria Santos</div>
                                </div>
                            </div>
                            <div class="history-item">
                                <div class="history-icon" style="background-color: rgba(255, 152, 0, 0.1);">
                                    <span class="material-symbols-outlined" style="color: #ff9800;">warning</span>
                                </div>
                                <div class="history-details">
                                    <div class="history-header">
                                        <h4>Fernanda Lima - Aviso de Bloqueio</h4>
                                        <span>13/04/2025 10:45</span>
                                    </div>
                                    <p>Aviso enviado sobre bloqueio iminente em 7 dias por falta de pagamento.</p>
                                    <div class="history-user">Por: Sistema</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

        // Tab functionality
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
<?php }else{
    header("Location: ../login.php");
    exit;
} ?>