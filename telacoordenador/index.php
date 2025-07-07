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
    $stmt = $conn->prepare("SELECT fname, lname FROM coordenadores WHERE id = :id");
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
// Buscar alunos recentes (últimos 5 cadastrados)
try {
    // Consulta modificada para ser mais genérica
   $stmtAlunos = $conn->prepare("SELECT 
                                e.id, 
                                CONCAT(e.fname, ' ') as fname, e.lname,
                                t.class_name as class_name, e.created_at
                             FROM estudantes e
                             LEFT JOIN turma t ON e.id = t.id
                             ORDER BY e.created_at DESC 
                             LIMIT 5");
    $stmtAlunos->execute();
    $alunosRecentes = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar alunos recentes: " . $e->getMessage();
    $alunosRecentes = []; // Se houver erro, define como array vazio
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
    <title>Dashboard de Professores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* NOVOS ESTILOS PARA AS SEÇÕES ESPECÍFICAS */
        
        /* Minhas Turmas - Estilização melhorada */
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
            padding: 20px;
        }
        
        .class-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 18px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid #4361ee;
            transition: all 0.3s ease;
        }
        
        .class-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .class-info h3 {
            margin: 0 0 5px;
            font-size: 17px;
            color: #2b2d42;
            font-weight: 600;
        }
        
        .class-info p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        
        .class-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            font-size: 14px;
            color: #4361ee;
            font-weight: 500;
        }
        
        .class-stats span:first-child {
            background-color: rgba(67, 97, 238, 0.1);
            padding: 4px 10px;
            border-radius: 12px;
        }
        
        /* Alunos Recentes - Estilização melhorada */
        .students-table-container {
            padding: 0 20px 20px;
        }
        
        .students-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        .students-table thead th {
            text-align: left;
            padding: 12px 15px;
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
        }
        
        .students-table tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }
        
        .students-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .students-table tbody td {
            padding: 12px 15px;
            color: #495057;
        }
        
        .students-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .activity-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            margin-right: 8px;
        }
        
        .activity-badge.success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .activity-badge.warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .activity-badge.primary {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        
        .activity-badge.info {
            background-color: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }
        
        .activity-date {
            color: #6c757d;
            font-size: 13px;
        }
        
        .student-avatar {
            display: inline-flex;
            margin-right: 8px;
            vertical-align: middle;
        }
        
        /* Layout do conteúdo principal */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        
        .dashboard-card {
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h2 {
            margin: 0;
            font-size: 18px;
            color: #2b2d42;
            font-weight: 600;
        }
        
        .view-all {
            color: #4361ee;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .view-all:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu (MANTIDO EXATAMENTE COMO ESTAVA) -->
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
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="welcome-section">
                    <div class="welcome-text">
                        <h1>Bem-vindo, <span><?php echo htmlspecialchars($coordinator['fname'] . ' ' . $coordinator['lname']); ?></span>!</h1>
                        <p>Hoje é <span data-dynamic="current-date" data-format="full">15 de Abril de 2025</span> | <span data-dynamic="current-period">2º trimestre</span> | Ano Letivo <span data-dynamic="school-year">2025</span></p><br>
                    </div>
                    <div class="welcome-actions">
                        <button class="btn-primary">
                            <span class="material-symbols-outlined">add</span>
                            Nova Atividade
                        </button>
                    </div>
                </div><br>

                <div class="dashboard-grid">
                    <!-- Seção Minhas Turmas - Estilização Melhorada -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Turmas Disponíveis</h2>
                            <a href="turmas.php" class="view-all">
                                Ver todas
                                <span class="material-symbols-outlined" style="font-size:18px">chevron_right</span>
                            </a>
                        </div>
                        <div class="classes-grid">
                            <div class="class-card">
                                <div class="class-info">
                                    <h3>10ª Classe - A</h3>
                                    <p>Matemática</p>
                                </div>
                                <div class="class-stats">
                                    <span>32 Alunos</span>
                                    <span class="material-symbols-outlined">chevron_right</span>
                                </div>
                            </div>
                            <div class="class-card">
                                <div class="class-info">
                                    <h3>11ª Classe - B</h3>
                                    <p>Física</p>
                                </div>
                                <div class="class-stats">
                                    <span>28 Alunos</span>
                                    <span class="material-symbols-outlined">chevron_right</span>
                                </div>
                            </div>
                            <div class="class-card">
                                <div class="class-info">
                                    <h3>9ª Classe - C</h3>
                                    <p>Química</p>
                                </div>
                                <div class="class-stats">
                                    <span>35 Alunos</span>
                                    <span class="material-symbols-outlined">chevron_right</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção Alunos Recentes - Estilização Melhorada -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Alunos Recentes</h2>
                            <a href="alunos.php" class="view-all">
                                Ver todos
                                <span class="material-symbols-outlined" style="font-size:18px">chevron_right</span>
                            </a>
                        </div>
                        <div class="students-table-container">
                            <table class="students-table">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($alunosRecentes)): ?>
                                        <?php foreach ($alunosRecentes as $aluno): ?>
                                            <tr>
                                                <td>
                                                    <div class="student-avatar">
                                                        <span class="material-symbols-outlined">account_circle</span>
                                                    </div>
                                                    <?php echo htmlspecialchars($aluno['fname'] . ' ' . $aluno['lname']); ?>
                                                </td>
                                                <td>
                                                    <span class="activity-badge success">Novo</span>
                                                    <span class="activity-date">Recém-cadastrado</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" style="text-align: center;">Nenhum aluno recente encontrado</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.createElement('div');
            menuToggle.id = 'menuToggle';
            menuToggle.innerHTML = '<span class="material-symbols-outlined">menu</span>';
            menuToggle.style.position = 'fixed';
            menuToggle.style.top = '15px';
            menuToggle.style.left = '15px';
            menuToggle.style.zIndex = '1000';
            menuToggle.style.cursor = 'pointer';
            menuToggle.style.display = 'none';
            document.body.appendChild(menuToggle);
            
            menuToggle.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('collapsed');
                document.querySelector('.content').classList.toggle('expanded');
            });
            
            function checkScreenSize() {
                if (window.innerWidth <= 992) {
                    menuToggle.style.display = 'block';
                    document.querySelector('.sidebar').classList.add('collapsed');
                    document.querySelector('.content').classList.add('expanded');
                } else {
                    menuToggle.style.display = 'none';
                    document.querySelector('.sidebar').classList.remove('collapsed');
                    document.querySelector('.content').classList.remove('expanded');
                }
            }
            
            window.addEventListener('resize', checkScreenSize);
            checkScreenSize();
        });
    </script>
</body>
</html>