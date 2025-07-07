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

        /* Buscar eventos do calendário
        $eventos = [];
        try {
            $stmt = $conn->prepare("
                SELECT 
                    e.id,
                    e.titulo,
                    e.descricao,
                    e.data_inicio,
                    e.data_fim,
                    e.tipo_evento,
                    e.turma_id,
                    e.disciplina,
                    e.local,
                    e.status
                FROM eventos_calendario e 
                WHERE e.professor_id = :professor_id
                ORDER BY e.data_inicio
            ");
            $stmt->bindParam(':professor_id', $idTeacher, PDO::PARAM_INT);
            $stmt->execute();
            $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar eventos: " . $e->getMessage();
        }
*/
        $dataAtual = '15 de Abril de 2025';
        $trimestre = '2º trimestre';
        $anoLetivo = '2025';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário Acadêmico - Dashboard de Professores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Estilos para o calendário acadêmico */
        .calendar-container {
            display: flex;
            gap: 1.5rem;
            margin-top: 20px;
        }
        
        .calendar-sidebar {
            width: 350px;
            flex-shrink: 0;
        }
        
        .calendar-main {
            flex: 1;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
        }
        
        .calendar-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .calendar-nav button {
            background: none;
            border: 1px solid #ddd;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .calendar-nav button:hover {
            background-color: #3a5bb9;
            color: white;
            border-color: #3a5bb9;
        }
        
        .calendar-month {
            font-size: 1.5rem;
            font-weight: 600;
            color: #444;
            min-width: 200px;
            text-align: center;
        }
        
        .view-options {
            display: flex;
            gap: 5px;
        }

        .view-btn {
            padding: 8px 16px;
            border: 1px solid #ddd;
            background-color: #fff;
            color: #444;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .view-btn:first-child {
            border-radius: 4px 0 0 4px;
        }

        .view-btn:last-child {
            border-radius: 0 4px 4px 0;
        }

        .view-btn.active {
            background-color: #3a5bb9;
            color: white;
            border-color: #3a5bb9;
        }
        
        .calendar-weekdays {
            margin-bottom: 10px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background-color: #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .calendar-weekday {
            text-align: center;
            font-weight: 600;
            padding: 12px;
            background-color: #f5f5f5;
            color: #666;
            font-size: 0.9rem;
        }
        
        .calendar-day {
            min-height: 120px;
            background-color: #fff;
            padding: 8px;
            position: relative;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .calendar-day:hover {
            background-color: #f9f9f9;
        }
        
        .calendar-day.other-month {
            background-color: #f8f8f8;
            opacity: 0.6;
        }
        
        .calendar-day.weekend {
            background-color: #fafafa;
        }
        
        .calendar-day.current-day {
            background-color: #e3f2fd;
            border: 2px solid #3a5bb9;
        }
        
        .day-number {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
            color: #444;
        }

        .calendar-day.other-month .day-number {
            color: #999;
        }
        
        .calendar-event {
            font-size: 0.75rem;
            padding: 2px 6px;
            margin-bottom: 2px;
            border-radius: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 3px solid;
        }

        .calendar-event:hover {
            transform: translateX(2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .calendar-event.aula {
            background-color: #e3f2fd;
            color: #1976d2;
            border-left-color: #1976d2;
        }
        
        .calendar-event.avaliacao {
            background-color: #ffebee;
            color: #d32f2f;
            border-left-color: #d32f2f;
        }
        
        .calendar-event.reuniao {
            background-color: #fff3e0;
            color: #f57c00;
            border-left-color: #f57c00;
        }
        
        .calendar-event.prazo {
            background-color: #f3e5f5;
            color: #7b1fa2;
            border-left-color: #7b1fa2;
        }
        
        .calendar-event.feriado {
            background-color: #e8f5e8;
            color: #388e3c;
            border-left-color: #388e3c;
        }

        .calendar-event.evento-escolar {
            background-color: #fff8e1;
            color: #fbc02d;
            border-left-color: #fbc02d;
        }

        .calendar-event.formacao {
            background-color: #e0f2f1;
            color: #00796b;
            border-left-color: #00796b;
        }
        
        .event-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            margin-right: 4px;
            background-color: currentColor;
        }

        .event-count {
            position: absolute;
            bottom: 4px;
            right: 4px;
            background-color: #3a5bb9;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .upcoming-events {
            margin-top: 1rem;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .event-item {
            padding: 12px;
            border-left: 4px solid;
            background-color: #f9f9f9;
            margin-bottom: 8px;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .event-item:hover {
            background-color: #f0f0f0;
            transform: translateX(2px);
        }
        
        .event-item.aula {
            border-left-color: #1976d2;
        }
        
        .event-item.avaliacao {
            border-left-color: #d32f2f;
        }
        
        .event-item.reuniao {
            border-left-color: #f57c00;
        }
        
        .event-item.prazo {
            border-left-color: #7b1fa2;
        }
        
        .event-item.feriado {
            border-left-color: #388e3c;
        }

        .event-item.evento-escolar {
            border-left-color: #fbc02d;
        }

        .event-item.formacao {
            border-left-color: #00796b;
        }
        
        .event-date {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .event-title {
            font-weight: 600;
            margin-bottom: 4px;
            color: #444;
        }
        
        .event-details {
            font-size: 0.85rem;
            color: #666;
            line-height: 1.3;
        }

        .event-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 6px;
            font-size: 0.75rem;
            color: #777;
        }

        .event-type-badge {
            background-color: #e0e0e0;
            color: #555;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.7rem;
        }

        .quick-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .quick-btn {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            color: #444;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .quick-btn:hover {
            background-color: #f5f5f5;
        }

        .quick-btn.active {
            background-color: #3a5bb9;
            color: white;
            border-color: #3a5bb9;
        }

        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 6px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.8rem;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }

        /* Modal para eventos */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow-y: auto;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h2 {
            color: #444;
            margin: 0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #777;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #444;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .form-control:focus {
            outline: none;
            border-color: #3a5bb9;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn {
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: #3a5bb9;
            color: white;
            border-color: #3a5bb9;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border-color: #6c757d;
        }

        .schedule-summary {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .schedule-summary h3 {
            margin-bottom: 10px;
            color: #444;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
        }

        .stat-item {
            text-align: center;
            padding: 8px;
            background-color: white;
            border-radius: 4px;
        }

        .stat-number {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #666;
        }

        @media (max-width: 992px) {
            .calendar-container {
                flex-direction: column;
            }
            
            .calendar-sidebar {
                width: 100%;
            }

            .calendar-header {
                flex-direction: column;
                gap: 15px;
            }

            .quick-actions {
                flex-wrap: wrap;
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
                    <li>
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
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar eventos e aulas...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Calendário Acadêmico</h1>
                    <button class="btn-primary" onclick="openEventModal()">
                        <span class="material-symbols-outlined">add</span>
                        Novo Evento
                    </button>
                </div>

                <div class="quick-actions">
                    <button class="quick-btn active" onclick="filterEvents('todos')">
                        <span class="material-symbols-outlined">calendar_month</span>
                        Todos
                    </button>
                    <button class="quick-btn" onclick="filterEvents('aula')">
                        <span class="material-symbols-outlined">school</span>
                        Aulas
                    </button>
                    <button class="quick-btn" onclick="filterEvents('avaliacao')">
                        <span class="material-symbols-outlined">quiz</span>
                        Avaliações
                    </button>
                    <button class="quick-btn" onclick="filterEvents('reuniao')">
                        <span class="material-symbols-outlined">groups</span>
                        Reuniões
                    </button>
                    <button class="quick-btn" onclick="filterEvents('prazo')">
                        <span class="material-symbols-outlined">schedule</span>
                        Prazos
                    </button>
                </div>
                
                <div class="calendar-container">
                    <div class="calendar-main">
                        <div class="calendar-header">
                            <div class="calendar-nav">
                                <button id="prev-month" onclick="changeMonth(-1)">
                                    <span class="material-symbols-outlined">chevron_left</span>
                                </button>
                                <span class="calendar-month" id="currentMonth">Abril 2025</span>
                                <button id="next-month" onclick="changeMonth(1)">
                                    <span class="material-symbols-outlined">chevron_right</span>
                                </button>
                            </div>
                            <div class="view-options">
                                <button class="view-btn active" onclick="changeView('month')">Mês</button>
                                <button class="view-btn" onclick="changeView('week')">Semana</button>
                                <button class="view-btn" onclick="changeView('day')">Dia</button>
                            </div>
                        </div>
                        
                        <div class="legend">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #1976d2;"></div>
                                <span>Aulas</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #d32f2f;"></div>
                                <span>Avaliações</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #f57c00;"></div>
                                <span>Reuniões</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #7b1fa2;"></div>
                                <span>Prazos</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #388e3c;"></div>
                                <span>Feriados</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #00796b;"></div>
                                <span>Formação</span>
                            </div>
                        </div>
                        
                        <div class="calendar-weekdays">
                            <div class="calendar-grid">
                                <div class="calendar-weekday">Dom</div>
                                <div class="calendar-weekday">Seg</div>
                                <div class="calendar-weekday">Ter</div>
                                <div class="calendar-weekday">Qua</div>
                                <div class="calendar-weekday">Qui</div>
                                <div class="calendar-weekday">Sex</div>
                                <div class="calendar-weekday">Sáb</div>
                            </div>
                        </div>
                        
                        <div class="calendar-grid" id="calendarGrid">
                            <!-- Dias do calendário serão carregados dinamicamente -->
                        </div>
                    </div>
                    
                    <div class="calendar-sidebar">
                        <div class="card">
                            <div class="card-header">
                                <h3>Resumo da Semana</h3>
                            </div>
                            <div class="card-content">
                                <div class="schedule-summary">
                                    <div class="summary-stats">
                                        <div class="stat-item">
                                            <div class="stat-number" style="color: #1976d2;">18</div>
                                            <div class="stat-label">Aulas</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number" style="color: #d32f2f;">3</div>
                                            <div class="stat-label">Provas</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number" style="color: #f57c00;">2</div>
                                            <div class="stat-label">Reuniões</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number" style="color: #7b1fa2;">5</div>
                                            <div class="stat-label">Prazos</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3>Próximos Eventos</h3>
                            </div>
                            <div class="card-content">
                                <div class="filter-group">
                                    <label for="event-filter">Filtrar por:</label>
                                    <select id="event-filter" class="filter-select" onchange="filterSidebarEvents()">
                                        <option value="todos">Todos os Eventos</option>
                                        <option value="aula">Aulas</option>
                                        <option value="avaliacao">Avaliações</option>
                                        <option value="reuniao">Reuniões</option>
                                        <option value="prazo">Prazos</option>
                                        <option value="feriado">Feriados</option>
                                        <option value="formacao">Formação</option>
                                    </select>
                                </div>
                                
                                <div class="upcoming-events" id="upcomingEvents">
                                    <!-- Eventos próximos -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para Eventos -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Novo Evento</h2>
                <button class="close-modal" onclick="closeEventModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <div class="form-group">
                        <label for="eventTitle">Título do Evento</label>
                        <input type="text" id="eventTitle" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="eventType">Tipo de Evento</label>
                        <select id="eventType" class="form-control" required>
                            <option value="aula">Aula</option>
                            <option value="avaliacao">Avaliação</option>
                            <option value="reuniao">Reunião</option>
                            <option value="prazo">Prazo</option>
                            <option value="feriado">Feriado</option>
                            <option value="evento-escolar">Evento Escolar</option>
                            <option value="formacao">Formação</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="eventDate">Data</label>
                        <input type="date" id="eventDate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="eventTime">Horário</label>
                        <input type="time" id="eventTime" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="eventClass">Turma</label>
                        <select id="eventClass" class="form-control">
                            <option value="">Selecione uma turma</option>
                            <option value="9A">9º Ano A</option>
                            <option value="10B">10º Ano B</option>
                            <option value="11C">11º Ano C</option>
                            <option value="12A">12º Ano A</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="eventLocation">Local</label>
                        <input type="text" id="eventLocation" class="form-control" placeholder="Sala, laboratório, etc.">
                    </div>
                    <div class="form-group">
                        <label for="eventDescription">Descrição</label>
                        <textarea id="eventDescription" class="form-control" rows="3" placeholder="Detalhes do evento..."></textarea>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary" onclick="closeEventModal()">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Evento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="dashboard-data.js"></script>
    <script>
        // Dados simulados para eventos acadêmicos
        const eventosAcademicos = [
            {
                id: 1,
                titulo: "Matemática - 9º Ano A",
                tipo: "aula",
                data: "2025-04-15",
                horario: "08:00",
                turma: "9º Ano A",
                local: "Sala 105",
                descricao: "Geometria Espacial - Prismas e Pirâmides"
            },
            {
                id: 2,
                titulo: "Prova de Física",
                tipo: "avaliacao",
                data: "2025-04-18",
                horario: "14:00",
                turma: "10º Ano B",
                local: "Sala 203",
                descricao: "Avaliação sobre Cinemática e Dinâmica"
            },
            {
                id: 3,
                titulo: "Conselho de Classe",
                tipo: "reuniao",
                data: "2025-04-20",
                horario: "13:30",
                turma: "",
                local: "Sala de Reuniões",
                descricao: "Avaliação do desempenho dos alunos do 2º trimestre"
            },
            {
                id: 4,
                titulo: "Entrega de Trabalhos",
                tipo: "prazo",
                data: "2025-04-25",
                horario: "",
                turma: "11º Ano C",
                local: "",
                descricao: "Trabalho sobre Química Orgânica"
            },
            {
                id: 5,
                titulo: "Dia do Livro",
                tipo: "feriado",
                data: "2025-04-23",
                horario: "",
                turma: "",
                local: "",
                descricao: "Feriado Nacional - Dia Mundial do Livro"
            },
            {
                id: 6,
                titulo: "Formação Continuada",
                tipo: "formacao",
                data: "2025-04-26",
                horario: "19:00",
                turma: "",
                local: "Auditório",
                descricao: "Metodologias Ativas no Ensino de Ciências"
            }
        ];

        let currentDate = new Date();
        let currentView = 'month';
        let currentFilter = 'todos';

        // Inicializar calendário
        function initCalendar() {
            updateCalendarHeader();
            renderCalendar();
            renderUpcomingEvents();
        }

        function updateCalendarHeader() {
            const monthNames = [
                'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
            ];
            document.getElementById('currentMonth').textContent = 
                `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
        }

        function renderCalendar() {
            const calendarGrid = document.getElementById('calendarGrid');
            calendarGrid.innerHTML = '';

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            // Primeiro dia do mês e último dia do mês anterior
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            // Renderizar 42 dias (6 semanas)
            for (let i = 0; i < 42; i++) {
                const cellDate = new Date(startDate);
                cellDate.setDate(startDate.getDate() + i);
                
                const dayElement = createDayElement(cellDate, month);
                calendarGrid.appendChild(dayElement);
            }
        }

        function createDayElement(date, currentMonth) {
            const dayDiv = document.createElement('div');
            dayDiv.className = 'calendar-day';
            
            // Adicionar classes especiais
            if (date.getMonth() !== currentMonth) {
                dayDiv.classList.add('other-month');
            }
            if (date.getDay() === 0 || date.getDay() === 6) {
                dayDiv.classList.add('weekend');
            }
            if (isToday(date)) {
                dayDiv.classList.add('current-day');
            }

            // Número do dia
            const dayNumber = document.createElement('span');
            dayNumber.className = 'day-number';
            dayNumber.textContent = date.getDate();
            dayDiv.appendChild(dayNumber);

            // Eventos do dia
            const dayEvents = getEventsForDate(date);
            let visibleEvents = 0;
            const maxVisible = 3;

            dayEvents.forEach(evento => {
                if (visibleEvents < maxVisible && shouldShowEvent(evento)) {
                    const eventDiv = document.createElement('div');
                    eventDiv.className = `calendar-event ${evento.tipo}`;
                    eventDiv.textContent = evento.titulo;
                    eventDiv.onclick = () => showEventDetails(evento);
                    dayDiv.appendChild(eventDiv);
                    visibleEvents++;
                }
            });

            // Mostrar contador se houver mais eventos
            const totalEvents = dayEvents.filter(e => shouldShowEvent(e)).length;
            if (totalEvents > maxVisible) {
                const countDiv = document.createElement('div');
                countDiv.className = 'event-count';
                countDiv.textContent = `+${totalEvents - maxVisible}`;
                dayDiv.appendChild(countDiv);
            }

            dayDiv.onclick = () => openDayView(date);
            
            return dayDiv;
        }

        function getEventsForDate(date) {
            const dateStr = date.toISOString().split('T')[0];
            return eventosAcademicos.filter(evento => evento.data === dateStr);
        }

        function shouldShowEvent(evento) {
            return currentFilter === 'todos' || evento.tipo === currentFilter;
        }

        function isToday(date) {
            const today = new Date();
            return date.toDateString() === today.toDateString();
        }

        function renderUpcomingEvents() {
            const container = document.getElementById('upcomingEvents');
            container.innerHTML = '';

            const today = new Date();
            const upcomingEvents = eventosAcademicos
                .filter(evento => {
                    const eventDate = new Date(evento.data);
                    return eventDate >= today && shouldShowEvent(evento);
                })
                .sort((a, b) => new Date(a.data) - new Date(b.data))
                .slice(0, 10);

            upcomingEvents.forEach(evento => {
                const eventDiv = document.createElement('div');
                eventDiv.className = `event-item ${evento.tipo}`;
                
                const eventDate = new Date(evento.data);
                const dateStr = eventDate.toLocaleDateString('pt-BR', {
                    day: 'numeric',
                    month: 'long',
                    weekday: 'long'
                });

                eventDiv.innerHTML = `
                    <div class="event-date">
                        <span class="material-symbols-outlined">schedule</span>
                        ${dateStr}${evento.horario ? `, ${evento.horario}` : ''}
                    </div>
                    <div class="event-title">${evento.titulo}</div>
                    <div class="event-details">${evento.descricao}</div>
                    <div class="event-meta">
                        <span class="event-type-badge">${getEventTypeLabel(evento.tipo)}</span>
                        ${evento.local ? `<span>${evento.local}</span>` : ''}
                    </div>
                `;
                
                eventDiv.onclick = () => showEventDetails(evento);
                container.appendChild(eventDiv);
            });

            if (upcomingEvents.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">Nenhum evento encontrado</p>';
            }
        }

        function getEventTypeLabel(tipo) {
            const labels = {
                'aula': 'Aula',
                'avaliacao': 'Avaliação',
                'reuniao': 'Reunião',
                'prazo': 'Prazo',
                'feriado': 'Feriado',
                'evento-escolar': 'Evento',
                'formacao': 'Formação'
            };
            return labels[tipo] || tipo;
        }

        // Funções de navegação
        function changeMonth(direction) {
            currentDate.setMonth(currentDate.getMonth() + direction);
            updateCalendarHeader();
            renderCalendar();
        }

        function changeView(view) {
            currentView = view;
            document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Implementar diferentes visualizações
            if (view === 'week') {
                alert('Visualização semanal será implementada');
            } else if (view === 'day') {
                alert('Visualização diária será implementada');
            }
        }

        function filterEvents(tipo) {
            currentFilter = tipo;
            document.querySelectorAll('.quick-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            renderCalendar();
            renderUpcomingEvents();
        }

        function filterSidebarEvents() {
            const select = document.getElementById('event-filter');
            currentFilter = select.value;
            renderUpcomingEvents();
        }

        // Funções do modal
        function openEventModal(evento = null) {
            const modal = document.getElementById('eventModal');
            const form = document.getElementById('eventForm');
            
            if (evento) {
                document.getElementById('modalTitle').textContent = 'Editar Evento';
                document.getElementById('eventTitle').value = evento.titulo;
                document.getElementById('eventType').value = evento.tipo;
                document.getElementById('eventDate').value = evento.data;
                document.getElementById('eventTime').value = evento.horario;
                document.getElementById('eventLocation').value = evento.local;
                document.getElementById('eventDescription').value = evento.descricao;
            } else {
                document.getElementById('modalTitle').textContent = 'Novo Evento';
                form.reset();
            }
            
            modal.style.display = 'block';
        }

        function closeEventModal() {
            document.getElementById('eventModal').style.display = 'none';
        }

        function showEventDetails(evento) {
            alert(`Evento: ${evento.titulo}\nTipo: ${getEventTypeLabel(evento.tipo)}\nData: ${new Date(evento.data).toLocaleDateString('pt-BR')}\nDescrição: ${evento.descricao}`);
        }

        function openDayView(date) {
            const events = getEventsForDate(date);
            const dateStr = date.toLocaleDateString('pt-BR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                weekday: 'long'
            });
            
            let message = `Eventos para ${dateStr}:\n\n`;
            if (events.length === 0) {
                message += 'Nenhum evento agendado para este dia.';
            } else {
                events.forEach(evento => {
                    message += `• ${evento.titulo} (${getEventTypeLabel(evento.tipo)})`;
                    if (evento.horario) message += ` - ${evento.horario}`;
                    message += '\n';
                });
            }
            
            alert(message);
        }

        // Event listeners
        document.getElementById('eventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const novoEvento = {
                id: Date.now(),
                titulo: document.getElementById('eventTitle').value,
                tipo: document.getElementById('eventType').value,
                data: document.getElementById('eventDate').value,
                horario: document.getElementById('eventTime').value,
                turma: document.getElementById('eventClass').value,
                local: document.getElementById('eventLocation').value,
                descricao: document.getElementById('eventDescription').value
            };
            
            eventosAcademicos.push(novoEvento);
            renderCalendar();
            renderUpcomingEvents();
            closeEventModal();
            
            alert('Evento criado com sucesso!');
        });

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('eventModal');
            if (event.target == modal) {
                closeEventModal();
            }
        }

        // Toggle sidebar on mobile
        document.getElementById('menuToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            initCalendar();
        });
    </script>
</body>
</html>