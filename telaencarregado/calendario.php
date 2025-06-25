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

        // Aqui pega o id do encarregado da sessão (a chave é 'id' conforme você mencionou)
        $idGuardian = $_SESSION['id'] ?? null;

        if (!$idGuardian) {
            die("Encarregado não identificado.");
        }

        try {
            $stmt = $conn->prepare("SELECT fname, lname FROM encarregados WHERE id = :id");
            $stmt->bindParam(':id', $idGuardian, PDO::PARAM_INT);
            $stmt->execute();

            $guardian = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$guardian) {
                die("Encarregado não encontrado.");
            }
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
                        <button class="btn-outline">
                            <span class="material-symbols-outlined">download</span>
                            Exportar Calendário
                        </button>
                        <button class="btn-primary">
                            <span class="material-symbols-outlined">add</span>
                            Novo Evento
                        </button>
                    </div>
                </div>

                <!-- Calendar View Toggle -->
                <div class="calendar-controls">
                    <div class="view-toggle">
                        <button class="btn-outline active">Mês</button>
                        <button class="btn-outline">Semana</button>
                        <button class="btn-outline">Dia</button>
                    </div>
                    <div class="calendar-navigation">
                        <button class="calendar-nav-btn">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                        <h2 class="current-month">Abril 2025</h2>
                        <button class="calendar-nav-btn">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    </div>
                    <button class="btn-outline">
                        <span class="material-symbols-outlined">today</span>
                        Hoje
                    </button>
                </div>

                <div class="calendar-layout">
                    <!-- Main Calendar -->
                    <div class="main-calendar">
                        <div class="calendar-card">
                            <div class="calendar-header">
                                <div class="weekday">Dom</div>
                                <div class="weekday">Seg</div>
                                <div class="weekday">Ter</div>
                                <div class="weekday">Qua</div>
                                <div class="weekday">Qui</div>
                                <div class="weekday">Sex</div>
                                <div class="weekday">Sáb</div>
                            </div>
                            <div class="calendar-grid">
                                <div class="calendar-day other-month">30</div>
                                <div class="calendar-day other-month">31</div>
                                <div class="calendar-day">
                                    <span class="day-number">1</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">2</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">3</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">4</span>
                                </div>
                                <div class="calendar-day weekend">
                                    <span class="day-number">5</span>
                                </div>
                                <div class="calendar-day weekend">
                                    <span class="day-number">6</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">7</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">8</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">9</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">10</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">11</span>
                                </div>
                                <div class="calendar-day weekend">
                                    <span class="day-number">12</span>
                                </div>
                                <div class="calendar-day weekend">
                                    <span class="day-number">13</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">14</span>
                                </div>
                                <div class="calendar-day current-day">
                                    <span class="day-number">15</span>
                                    <div class="event-dot event-exam"></div>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">16</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">17</span>
                                </div>
                                <div class="calendar-day has-event">
                                    <span class="day-number">18</span>
                                    <div class="event-dot event-meeting"></div>
                                </div>
                                <div class="calendar-day weekend">
                                    <span class="day-number">19</span>
                                </div>
                                <div class="calendar-day weekend">
                                    <span class="day-number">20</span>
                                </div>
                                <div class="calendar-day has-event">
                                    <span class="day-number">21</span>
                                    <div class="event-dot event-exam"></div>
                                </div>
                                <div class="calendar-day has-event">
                                    <span class="day-number">22</span>
                                    <div class="event-dot event-delivery"></div>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">23</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">24</span>
                                </div>
                                <div class="calendar-day has-event">
                                    <span class="day-number">25</span>
                                    <div class="event-dot event-holiday"></div>
                                </div>
                                <div class="calendar-day weekend">
                                    <span class="day-number">26</span>
                                </div>
                                <div class="calendar-day weekend">
                                    <span class="day-number">27</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">28</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">29</span>
                                </div>
                                <div class="calendar-day">
                                    <span class="day-number">30</span>
                                </div>
                                <div class="calendar-day other-month">1</div>
                                <div class="calendar-day other-month">2</div>
                            </div>
                        </div>
                    </div>

                    <!-- Events Sidebar -->
                    <div class="events-sidebar">
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h3>Próximos Eventos</h3>
                            </div>
                            <div class="events-list">
                                <div class="event-item">
                                    <div class="event-date">
                                        <span class="day">18</span>
                                        <span class="month">Abr</span>
                                    </div>
                                    <div class="event-details">
                                        <h4>Reunião de Pais - 9º Ano</h4>
                                        <p>19:00 - 21:00</p>
                                        <span class="event-type meeting">Reunião</span>
                                    </div>
                                </div>
                                <div class="event-item">
                                    <div class="event-date">
                                        <span class="day">21</span>
                                        <span class="month">Abr</span>
                                    </div>
                                    <div class="event-details">
                                        <h4>Prova de Matemática</h4>
                                        <p>08:00 - 10:00</p>
                                        <span class="event-type exam">Prova</span>
                                    </div>
                                </div>
                                <div class="event-item">
                                    <div class="event-date">
                                        <span class="day">22</span>
                                        <span class="month">Abr</span>
                                    </div>
                                    <div class="event-details">
                                        <h4>Entrega de Boletins</h4>
                                        <p>14:00 - 17:00</p>
                                        <span class="event-type delivery">Entrega</span>
                                    </div>
                                </div>
                                <div class="event-item">
                                    <div class="event-date">
                                        <span class="day">25</span>
                                        <span class="month">Abr</span>
                                    </div>
                                    <div class="event-details">
                                        <h4>Feriado - Dia do Trabalhador</h4>
                                        <p>Não há aulas</p>
                                        <span class="event-type holiday">Feriado</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Event Legend -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h3>Legenda</h3>
                            </div>
                            <div class="event-legend">
                                <div class="legend-item">
                                    <div class="legend-color event-exam"></div>
                                    <span>Provas</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color event-meeting"></div>
                                    <span>Reuniões</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color event-delivery"></div>
                                    <span>Entregas</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color event-holiday"></div>
                                    <span>Feriados</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color event-activity"></div>
                                    <span>Atividades</span>
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

        // Calendar navigation
        document.querySelectorAll('.calendar-nav-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Add navigation logic here
                console.log('Navigate calendar');
            });
        });

        // View toggle
        document.querySelectorAll('.view-toggle button').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.view-toggle button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>

    <style>
        .calendar-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .view-toggle {
            display: flex;
            gap: 5px;
        }

        .calendar-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
        }

        .calendar-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }

        .calendar-day {
            min-height: 100px;
            border-right: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            padding: 10px;
            position: relative;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .calendar-day:hover {
            background-color: rgba(74, 111, 220, 0.05);
        }

        .calendar-day:nth-child(7n) {
            border-right: none;
        }

        .calendar-day.has-event {
            background-color: rgba(74, 111, 220, 0.02);
        }

        .event-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            position: absolute;
            bottom: 5px;
            right: 5px;
        }

        .event-dot.event-exam {
            background-color: #f44336;
        }

        .event-dot.event-meeting {
            background-color: #2196f3;
        }

        .event-dot.event-delivery {
            background-color: #ff9800;
        }

        .event-dot.event-holiday {
            background-color: #4caf50;
        }

        .event-dot.event-activity {
            background-color: #9c27b0;
        }

        .events-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .event-type {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .event-type.exam {
            background-color: rgba(244, 67, 54, 0.1);
            color: #f44336;
        }

        .event-type.meeting {
            background-color: rgba(33, 150, 243, 0.1);
            color: #2196f3;
        }

        .event-type.delivery {
            background-color: rgba(255, 152, 0, 0.1);
            color: #ff9800;
        }

        .event-type.holiday {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .event-legend {
            padding: 15px 20px;
        }

        @media (max-width: 768px) {
            .calendar-layout {
                grid-template-columns: 1fr;
            }

            .calendar-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .calendar-day {
                min-height: 60px;
                padding: 5px;
            }
        }
    </style>
</body>
</html>