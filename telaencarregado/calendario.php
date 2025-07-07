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
    <title>Calendário - Dashboard Encarregados</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #eef2ff;
            --secondary-color: #f8f9fa;
            --accent-color: #3f37c9;
            --text-color: #2b2d42;
            --text-light: #6c757d;
            --text-lighter: #adb5bd;
            --border-color: #e9ecef;
            --card-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            --card-shadow-hover: 0 5px 15px rgba(0, 0, 0, 0.1);
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fb;
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background-color: var(--white);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            z-index: 100;
            position: fixed;
            height: 100vh;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }

        .sidebar-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .profile {
            padding: 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .profile-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.2rem;
        }

        .profile-info h3 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .profile-info p {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        .menu {
            flex: 1;
            padding: 15px 0;
            overflow-y: auto;
        }

        .menu ul {
            list-style: none;
        }

        .menu li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .menu li a:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .menu li a .material-symbols-outlined {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .menu li.active a {
            background-color: var(--primary-light);
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
        }

        .sidebar-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--border-color);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            padding: 10px 0;
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar-footer a:hover {
            color: var(--primary-color);
        }

        .sidebar-footer a.logout:hover {
            color: var(--danger-color);
        }

        .sidebar-footer a .material-symbols-outlined {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Main Content Styles */
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-left: 280px;
            width: calc(100% - 280px);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            z-index: 10;
            position: sticky;
            top: 0;
        }

        .search-container {
            position: relative;
            width: 300px;
        }

        .search-container .material-symbols-outlined {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-lighter);
            font-size: 1.2rem;
        }

        .search-container input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .search-container input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .top-bar-actions .material-symbols-outlined {
            color: var(--text-light);
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1.4rem;
        }

        .top-bar-actions .material-symbols-outlined:hover {
            color: var(--primary-color);
        }

        .top-bar-actions .notification {
            position: relative;
        }

        .top-bar-actions .notification::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background-color: var(--danger-color);
            border-radius: 50%;
            border: 2px solid var(--white);
        }

        .dashboard-content {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            background-color: #f5f7fb;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-light);
        }

        .btn-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background-color: var(--primary-light);
        }

        /* Calendar Styles */
        .calendar-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .calendar-main {
            flex: 1;
            min-width: 0;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            overflow: hidden;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .calendar-actions {
            display: flex;
            gap: 10px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            padding: 8px;
            color: var(--text-light);
        }

        .calendar-day {
            aspect-ratio: 1;
            padding: 8px;
            border-radius: 8px;
            background-color: var(--gray-100);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .calendar-day.current-month {
            background-color: var(--white);
            border: 1px solid var(--border-color);
        }

        .calendar-day.today {
            background-color: var(--primary-light);
            border: 1px solid var(--primary-color);
        }

        .calendar-day-number {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .calendar-event {
            font-size: 0.7rem;
            padding: 2px 4px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 4px;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .calendar-sidebar {
            width: 300px;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px;
        }

        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .event-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .event-item {
            padding: 12px;
            border-radius: 8px;
            background-color: var(--gray-100);
        }

        .event-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .event-time {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        .event-type {
            display: inline-block;
            padding: 2px 6px;
            font-size: 0.7rem;
            border-radius: 4px;
            margin-top: 5px;
        }

        .event-type.meeting {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }

        .event-type.exam {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning-color);
        }

        .event-type.delivery {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success-color);
        }

        /* Responsive Styles */
        @media (max-width: 1200px) {
            .calendar-container {
                flex-direction: column;
            }
            
            .calendar-sidebar {
                width: 100%;
            }
        }

        @media (max-width: 992px) {
            .content {
                margin-left: 0;
                width: 100%;
            }
            
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1000;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .top-bar {
                padding: 12px 15px;
            }
            
            .search-container {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            .calendar-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            
            .calendar-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .search-container {
                width: 150px;
            }
        }

        @media (max-width: 576px) {
            .calendar-grid {
                gap: 5px;
            }
            
            .calendar-day {
                padding: 4px;
            }
            
            .calendar-event {
                display: none;
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
                <div class="profile-avatar">
                    <?php 
                        $names = explode(' ', $guardian['fname']);
                        $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                        echo $initials;
                    ?>
                </div>
                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?></h3>
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
                    <li>
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
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar eventos...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Calendário Escolar</h1>
                    <div class="header-actions">
                        <button class="btn btn-primary">
                            <span class="material-symbols-outlined">add</span>
                            Novo Evento
                        </button>
                        <button class="btn btn-outline">
                            <span class="material-symbols-outlined">download</span>
                            Exportar
                        </button>
                    </div>
                </div>

                <div class="calendar-container">
                    <div class="calendar-main">
                        <div class="calendar-header">
                            <div class="calendar-title">Abril 2025</div>
                            <div class="calendar-actions">
                                <button class="btn-outline">
                                    <span class="material-symbols-outlined">chevron_left</span>
                                </button>
                                <button class="btn-outline">Hoje</button>
                                <button class="btn-outline">
                                    <span class="material-symbols-outlined">chevron_right</span>
                                </button>
                            </div>
                        </div>

                        <div class="calendar-grid">
                            <!-- Dias da semana -->
                            <div class="calendar-day-header">Dom</div>
                            <div class="calendar-day-header">Seg</div>
                            <div class="calendar-day-header">Ter</div>
                            <div class="calendar-day-header">Qua</div>
                            <div class="calendar-day-header">Qui</div>
                            <div class="calendar-day-header">Sex</div>
                            <div class="calendar-day-header">Sáb</div>

                            <!-- Dias do mês anterior -->
                            <div class="calendar-day">30</div>
                            <div class="calendar-day">31</div>
                            
                            <!-- Dias do mês atual -->
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">1</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">2</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">3</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">4</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">5</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">6</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">7</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">8</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">9</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">10</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">11</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">12</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">13</div>
                            </div>
                            <div class="calendar-day current-month today">
                                <div class="calendar-day-number">14</div>
                                <div class="calendar-event">Festa Junina</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">15</div>
                                <div class="calendar-event">Prova Matemática</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">16</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">17</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">18</div>
                                <div class="calendar-event">Reunião de Pais</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">19</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">20</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">21</div>
                                <div class="calendar-event">Prova Português</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">22</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">23</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">24</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">25</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">26</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">27</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">28</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">29</div>
                            </div>
                            <div class="calendar-day current-month">
                                <div class="calendar-day-number">30</div>
                            </div>
                            
                            <!-- Dias do próximo mês -->
                            <div class="calendar-day">1</div>
                            <div class="calendar-day">2</div>
                            <div class="calendar-day">3</div>
                        </div>
                    </div>

                    <div class="calendar-sidebar">
                        <div class="sidebar-title">
                            Próximos Eventos
                            <span class="material-symbols-outlined">more_vert</span>
                        </div>
                        
                        <div class="event-list">
                            <div class="event-item">
                                <div class="event-title">Reunião de Pais - 9º Ano</div>
                                <div class="event-time">18/04/2025 - 19:00 - 21:00</div>
                                <span class="event-type meeting">Reunião</span>
                            </div>
                            
                            <div class="event-item">
                                <div class="event-title">Prova de Matemática</div>
                                <div class="event-time">15/04/2025 - 08:00 - 10:00</div>
                                <span class="event-type exam">Prova</span>
                            </div>
                            
                            <div class="event-item">
                                <div class="event-title">Entrega de Boletins</div>
                                <div class="event-time">21/04/2025 - 14:00 - 17:00</div>
                                <span class="event-type delivery">Entrega</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
        
        // Highlight clicked day
        document.querySelectorAll('.calendar-day').forEach(day => {
            day.addEventListener('click', function() {
                document.querySelectorAll('.calendar-day').forEach(d => {
                    d.classList.remove('selected');
                });
                this.classList.add('selected');
            });
        });
    </script>
</body>
</html>