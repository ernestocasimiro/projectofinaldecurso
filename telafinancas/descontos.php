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
    <title>Descontos e Bolsas - Dashboard Financeiro</title>
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
      
        <li class="active">
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
                    <input type="text" placeholder="Pesquisar bolsas e descontos...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <div>
                        <h1>Descontos e Bolsas de Estudo</h1>
                        <p>Gestão de benefícios financeiros para alunos</p>
                    </div>
                    <div class="page-actions">
                        <button class="btn-outline">
                            <span class="material-symbols-outlined">download</span>
                            Exportar
                        </button>
                        <button class="btn-primary">
                            <span class="material-symbols-outlined">add</span>
                            Novo Desconto
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(76, 175, 80, 0.1);">
                            <span class="material-symbols-outlined" style="color: #4caf50;">school</span>
                        </div>
                        <div class="stat-info">
                            <h3>187</h3>
                            <p>Alunos com Bolsa</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(33, 150, 243, 0.1);">
                            <span class="material-symbols-outlined" style="color: #2196f3;">percent</span>
                        </div>
                        <div class="stat-info">
                            <h3>15%</h3>
                            <p>Média de Desconto</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(255, 152, 0, 0.1);">
                            <span class="material-symbols-outlined" style="color: #ff9800;">price_change</span>
                        </div>
                        <div class="stat-info">
                            <h3>AOA 85.400</h3>
                            <p>Valor Total (Mês)</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: rgba(156, 39, 176, 0.1);">
                            <span class="material-symbols-outlined" style="color: #9c27b0;">auto_graph</span>
                        </div>
                        <div class="stat-info">
                            <h3>7,2%</h3>
                            <p>Impacto na Receita</p>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="tabs">
                    <button class="tab-btn active">Todos os Descontos</button>
                    <button class="tab-btn">Bolsas Sociais</button>
                    <button class="tab-btn">Descontos Comerciais</button>
                    <button class="tab-btn">Convênios</button>
                </div>

                <!-- Filters -->
                <div class="filter-container">
                    <div class="filter-group">
                        <label>Tipo:</label>
                        <select class="filter-select">
                            <option>Todos os tipos</option>
                            <option>Bolsa Social</option>
                            <option>Desconto Família</option>
                            <option>Convênio Empresa</option>
                            <option>Desconto Pontualidade</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Percentual:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>Até 10%</option>
                            <option>11% a 30%</option>
                            <option>31% a 50%</option>
                            <option>Acima de 50%</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Status:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>Ativo</option>
                            <option>Expirado</option>
                            <option>Pendente</option>
                        </select>
                    </div>
                </div>

                <!-- Descontos Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Tipo</th>
                                <th>Percentual</th>
                                <th>Valor Mensal</th>
                                <th>Validade</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="student-name">
                                        <img src="https://via.placeholder.com/40" alt="Aluno">
                                        <div>
                                            <p>Pedro Oliveira</p>
                                            <span class="text-muted">Matrícula: 2025045</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Bolsa Social</td>
                                <td>50%</td>
                                <td>AOA 425,00</td>
                                <td>31/12/2025</td>
                                <td><span class="status-badge active">Ativo</span></td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </button>
                                        <button class="action-btn">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                        <button class="action-btn">
                                            <span class="material-symbols-outlined">print</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="student-name">
                                        <img src="https://via.placeholder.com/40" alt="Aluno">
                                        <div>
                                            <p>Mariana Costa</p>
                                            <span class="text-muted">Matrícula: 2025067</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Desconto Família</td>
                                <td>15%</td>
                                <td>AOA 127,50</td>
                                <td>31/12/2025</td>
                                <td><span class="status-badge active">Ativo</span></td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </button>
                                        <button class="action-btn">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                        <button class="action-btn">
                                            <span class="material-symbols-outlined">print</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="student-name">
                                        <img src="https://via.placeholder.com/40" alt="Aluno">
                                        <div>
                                            <p>Lucas Santos</p>
                                            <span class="text-muted">Matrícula: 2025089</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Convênio Empresa</td>
                                <td>20%</td>
                                <td>AOA 170,00</td>
                                <td>31/12/2025</td>
                                <td><span class="status-badge active">Ativo</span></td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </button>
                                        <button class="action-btn">
                                            <span class="material-symbols-outlined">edit</span>
                                        </button>
                                        <button class="action-btn">
                                            <span class="material-symbols-outlined">print</span>
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
                    <button class="pagination-btn">10</button>
                    <button class="pagination-btn">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>

                <!-- Discount Types -->
                <div class="dashboard-grid" style="margin-top: 30px;">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Tipos de Descontos</h2>
                            <button class="btn-text">Gerenciar</button>
                        </div>
                        <div class="card-content">
                            <div class="discount-types">
                                <div class="discount-type-item">
                                    <div class="discount-type-icon" style="background-color: rgba(76, 175, 80, 0.1);">
                                        <span class="material-symbols-outlined" style="color: #4caf50;">volunteer_activism</span>
                                    </div>
                                    <div class="discount-type-details">
                                        <h4>Bolsa Social</h4>
                                        <p>Desconto de 30% a 100% baseado em análise socioeconômica</p>
                                        <div class="discount-type-stats">
                                            <span>45 alunos</span>
                                            <span>AOA 38.250/mês</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="discount-type-item">
                                    <div class="discount-type-icon" style="background-color: rgba(33, 150, 243, 0.1);">
                                        <span class="material-symbols-outlined" style="color: #2196f3;">family_restroom</span>
                                    </div>
                                    <div class="discount-type-details">
                                        <h4>Desconto Família</h4>
                                        <p>15% para segundo filho, 25% para terceiro filho ou mais</p>
                                        <div class="discount-type-stats">
                                            <span>78 alunos</span>
                                            <span>AOA 19.890/mês</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="discount-type-item">
                                    <div class="discount-type-icon" style="background-color: rgba(255, 152, 0, 0.1);">
                                        <span class="material-symbols-outlined" style="color: #ff9800;">business</span>
                                    </div>
                                    <div class="discount-type-details">
                                        <h4>Convênio Empresa</h4>
                                        <p>10% a 20% para filhos de funcionários de empresas conveniadas</p>
                                        <div class="discount-type-stats">
                                            <span>35 alunos</span>
                                            <span>AOA 12.750/mês</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Impacto Financeiro</h2>
                        </div>
                        <div class="card-content">
                            <div class="financial-impact">
                                <div class="impact-chart">
                                    <div class="chart-placeholder" style="height: 200px; background-color: #f5f7ff; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <span>Gráfico de Impacto de Descontos</span>
                                    </div>
                                </div>
                                <div class="impact-summary">
                                    <div class="impact-item">
                                        <div class="impact-label">Receita Bruta Potencial</div>
                                        <div class="impact-value">AOA 1.185.350</div>
                                    </div>
                                    <div class="impact-item">
                                        <div class="impact-label">Total de Descontos</div>
                                        <div class="impact-value">AOA 85.400</div>
                                    </div>
                                    <div class="impact-item">
                                        <div class="impact-label">Receita Líquida</div>
                                        <div class="impact-value">AOA 1.099.950</div>
                                    </div>
                                    <div class="impact-item">
                                        <div class="impact-label">Percentual de Desconto</div>
                                        <div class="impact-value">7,2%</div>
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