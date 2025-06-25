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
$idGuardian = $_SESSION['id'] ?? null;

if (!$idGuardian) {
    die("Encarregado não identificado.");
}

try {
    // Busca informações do encarregado
    $stmt = $conn->prepare("SELECT fname, lname FROM encarregados WHERE id = :id");
    $stmt->bindParam(':id', $idGuardian, PDO::PARAM_INT);
    $stmt->execute();
    $guardian = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$guardian) {
        die("Encarregado não encontrado.");
    }

    // Busca os estudantes (filhos) deste encarregado
    $stmt = $conn->prepare("
        SELECT e.id, e.fname, e.lname, e.fotoperfil, e.status, 
               t.class_name, t.class_grade, t.class_course
        FROM estudantes e
        LEFT JOIN turma t ON e.area = t.id
        WHERE e.encarregado_id = :encarregado_id
    ");
    $stmt->bindParam(':encarregado_id', $idGuardian, PDO::PARAM_INT);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit;
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
    <title>Meus Filhos - Dashboard Encarregados</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }
        .status-badge.active {
            background-color: #e3f9e5;
            color: #1b7052;
        }
        .status-badge.inactive {
            background-color: #ffe3e3;
            color: #ff4d4d;
        }
        .status-badge.pending {
            background-color: #fff8e3;
            color: #ffb700;
        }
        .stat-value {
            font-size: 20px;
            font-weight: 700;
        }
        .stat-value.excellent {
            color: #1b7052;
        }
        .stat-value.good {
            color: #4a8b2c;
        }
        .stat-value.average {
            color: #ffb700;
        }
        .stat-value.poor {
            color: #ff4d4d;
        }
        .subject-grade {
            font-weight: 600;
        }
        .subject-grade.excellent {
            color: #1b7052;
        }
        .subject-grade.good {
            color: #4a8b2c;
        }
        .subject-grade.average {
            color: #ffb700;
        }
        .subject-grade.poor {
            color: #ff4d4d;
        }
        .child-avatar img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
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
                    <h3><span><?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?></span></h1></h3>
                    <p>Encarregado/a de Educação</p>
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
                        <a href="filhos.php">
                            <span class="material-symbols-outlined">family_restroom</span>
                            <span class="menu-text">Meus Filhos</span>
                        </a>
                    </li>
                    <li>
                        <a href="notas.php">
                            <span class="material-symbols-outlined">grade</span>
                            <span class="menu-text">Notas</span>
                        </a>
                    </li>
                    <li>
                        <a href="frequencia.php">
                            <span class="material-symbols-outlined">fact_check</span>
                            <span class="menu-text">Frequência</span>
                        </a>
                    </li>
                    <li>
                        <a href="calendario.php">
                            <span class="material-symbols-outlined">calendar_month</span>
                            <span class="menu-text">Calendário</span>
                        </a>
                    </li>
                    <li>
                        <a href="comunicados.php">
                            <span class="material-symbols-outlined">campaign</span>
                            <span class="menu-text">Comunicados</span>
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
                    <input type="text" placeholder="Pesquisar...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Meus Filhos</h1>
                    <div class="header-actions">
                        <button class="btn-outline">
                            <span class="material-symbols-outlined">download</span>
                            Exportar Dados
                        </button>
                    </div>
                </div>

               <!-- Children Cards -->
<div class="children-grid">
    <?php if (count($students) > 0): ?>
        <?php foreach ($students as $student): ?>
            <div class="child-card">
                <div class="child-header">
                    <div class="child-avatar">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['fname'] . ' ' . $student['lname']); ?>&background=random" 
                             alt="<?php echo htmlspecialchars($student['fname'] . ' ' . $student['lname']); ?>" 
                             style="width: 80px; height: 80px; border-radius: 50%;">
                    </div>
                    <div class="child-basic-info">
                        <h2><?php echo htmlspecialchars($student['fname'] . ' ' . $student['lname']); ?></h2>
                        <?php if ($student['class_name']): ?>
                            <p><?php echo htmlspecialchars($student['class_name']); ?> - <?php echo htmlspecialchars($student['class_grade']); ?>ª Classe</p>
                        <?php else: ?>
                            <p>Turma não definida</p>
                        <?php endif; ?>
                        <span class="status-badge <?php echo htmlspecialchars($student['status'] ?? 'pending'); ?>">
                            <?php 
                                switch($student['status']) {
                                    case 'active': echo 'Ativo'; break;
                                    case 'inactive': echo 'Inativo'; break;
                                    default: echo 'Pendente'; break;
                                }
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="child-stats">
                    <div class="stat-item">
                        <span class="stat-label">Média Geral</span>
                        <span class="stat-value good">-</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Frequência</span>
                        <span class="stat-value excellent">-</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Disciplinas</span>
                        <span class="stat-value">-</span>
                    </div>
                </div>

                <div class="child-subjects">
                    <h4>Disciplinas com Menor Desempenho</h4>
                    <div class="subject-list">
                        <div class="subject-item">
                            <span class="subject-name">-</span>
                            <span class="subject-grade average">-</span>
                        </div>
                        <div class="subject-item">
                            <span class="subject-name">-</span>
                            <span class="subject-grade average">-</span>
                        </div>
                    </div>
                </div>

                <div class="child-actions">
                    <button class="btn-primary">Ver Detalhes</button>
                    <button class="btn-outline">Mensagem Professor</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-children">
            <p>Nenhum estudante vinculado a este encarregado.</p>
        </div>
    <?php endif; ?>
</div>


                <!-- Recent Activity -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>Atividade Recente</h2>
                        <div class="filter-container">
                            <select class="filter-select">
                                <option>Todos os filhos</option>
                                <?php foreach ($students as $student): ?>
                                    <option><?php echo htmlspecialchars($student['fname'] . ' ' . htmlspecialchars($student['lname'])); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon">
                                <span class="material-symbols-outlined">grade</span>
                            </div>
                            <div class="activity-content">
                                <h4>Nenhuma atividade recente</h4>
                                <p>Não há registros de atividades recentes para exibir</p>
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
    </script>
</body>
</html>