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
    <title>Frequência - Dashboard Encarregados</title>
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
                    <li class="active">
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
                    <h1>Frequência</h1>
                    <div class="header-actions">
                        <button class="btn-outline">
                            <span class="material-symbols-outlined">download</span>
                            Relatório de Frequência
                        </button>
                    </div>
                </div>

                <!-- Attendance Summary -->
                <div class="attendance-summary">
                    <div class="attendance-stat">
                        <div class="attendance-icon present">
                            <span class="material-symbols-outlined">check_circle</span>
                        </div>
                        <div class="attendance-count">
                            <h3>186</h3>
                            <p>Presenças</p>
                        </div>
                    </div>
                    <div class="attendance-stat">
                        <div class="attendance-icon absent">
                            <span class="material-symbols-outlined">cancel</span>
                        </div>
                        <div class="attendance-count">
                            <h3>8</h3>
                            <p>Faltas</p>
                        </div>
                    </div>
                    <div class="attendance-stat">
                        <div class="attendance-icon late">
                            <span class="material-symbols-outlined">schedule</span>
                        </div>
                        <div class="attendance-count">
                            <h3>3</h3>
                            <p>Atrasos</p>
                        </div>
                    </div>
                    <div class="attendance-stat">
                        <div class="attendance-icon justified">
                            <span class="material-symbols-outlined">verified</span>
                        </div>
                        <div class="attendance-count">
                            <h3>5</h3>
                            <p>Faltas Justificadas</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-container">
                    <div class="filter-group">
                        <label>Filho:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>Steeve Salvador</option>
                            <option>Kelton Gonçalves</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Período:</label>
                        <select class="filter-select">
                            <option>Abril 2025</option>
                            <option>Março 2025</option>
                            <option>Fevereiro 2025</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Disciplina:</label>
                        <select class="filter-select">
                            <option>Todas</option>
                            <option>Matemática</option>
                            <option>Português</option>
                            <option>História</option>
                        </select>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Aluno</th>
                                <th>Disciplina</th>
                                <th>Professor</th>
                                <th>Horário</th>
                                <th>Status</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>15/04/2025</td>
                                <td>
                                    <div class="student-name">
                                        <div>
                                            <p>Steeve Salvador</p>
                                            <span class="text-muted">9º Ano A</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Matemática</td>
                                <td>Prof. Carlos Silva</td>
                                <td>07:30 - 08:20</td>
                                <td><span class="attendance-label present">Presente</span></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>15/04/2025</td>
                                <td>
                                    <div class="student-name">
                                        <div>
                                            <p>Kelton Gonçalves</p>
                                            <span class="text-muted">6º Ano B</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Português</td>
                                <td>Prof. Fernanda Silva</td>
                                <td>08:20 - 09:10</td>
                                <td><span class="attendance-label late">Atraso</span></td>
                                <td>Chegou 10 min atrasada</td>
                            </tr>
                            <tr>
                                <td>14/04/2025</td>
                                <td>
                                    <div class="student-name">
                                        <div>
                                            <p>Steeve Salvador</p>
                                            <span class="text-muted">9º Ano A</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Física</td>
                                <td>Prof. Roberto Lima</td>
                                <td>09:10 - 10:00</td>
                                <td><span class="attendance-label absent">Falta</span></td>
                                <td>Consulta médica</td>
                            </tr>
                            <tr>
                                <td>14/04/2025</td>
                                <td>
                                    <div class="student-name">
                                        <div>
                                            <p>Kelton Gonçalves</p>
                                            <span class="text-muted">6º Ano B</span>
                                        </div>
                                    </div>
                                </td>
                                <td>História</td>
                                <td>Prof. Lucia Santos</td>
                                <td>10:20 - 11:10</td>
                                <td><span class="attendance-label present">Presente</span></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>13/04/2025</td>
                                <td>
                                    <div class="student-name">
                                        <div>
                                            <p>Steeve Salvador</p>
                                            <span class="text-muted">9º Ano A</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Inglês</td>
                                <td>Prof. Michael Johnson</td>
                                <td>11:10 - 12:00</td>
                                <td><span class="attendance-label justified">Justificada</span></td>
                                <td>Atestado médico apresentado</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Monthly Calendar -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>Calendário de Frequência - Abril 2025</h2>
                        <div class="calendar-navigation">
                            <button class="calendar-nav-btn">
                                <span class="material-symbols-outlined">chevron_left</span>
                            </button>
                            <span class="current-month">Abril 2025</span>
                            <button class="calendar-nav-btn">
                                <span class="material-symbols-outlined">chevron_right</span>
                            </button>
                        </div>
                    </div>
                    <div class="attendance-calendar">
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
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">2</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">3</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">4</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day weekend">5</div>
                            <div class="calendar-day weekend">6</div>
                            <div class="calendar-day">
                                <span class="day-number">7</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">8</span>
                                <div class="attendance-indicator absent"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">9</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">10</span>
                                <div class="attendance-indicator late"></div>
                            </div>
                            <div class="calendar-day">
                                <span class="day-number">11</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                            <div class="calendar-day weekend">12</div>
                            <div class="calendar-day weekend">13</div>
                            <div class="calendar-day">
                                <span class="day-number">14</span>
                                <div class="attendance-indicator justified"></div>
                            </div>
                            <div class="calendar-day current-day">
                                <span class="day-number">15</span>
                                <div class="attendance-indicator present"></div>
                            </div>
                        </div>
                    </div>
                    <div class="calendar-legend">
                        <div class="legend-item">
                            <div class="legend-color present"></div>
                            <span>Presente</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color absent"></div>
                            <span>Falta</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color late"></div>
                            <span>Atraso</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color justified"></div>
                            <span>Justificada</span>
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