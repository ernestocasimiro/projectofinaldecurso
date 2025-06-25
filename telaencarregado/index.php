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
    <title>Dashboard - Encarregados de Educação</title>
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
                    <h3><?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?></h3>
                    <p>Encarregado/a de Educação</p>
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
                    <li>
                      
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
                <div class="welcome-section">
                  <div class="welcome-text">
                        <h1>Bem-vindo/a, <span><?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?></span>!</h1>
                        <p>Hoje é <span><?php echo $dataAtual; ?></span> | <span><?php echo $trimestre; ?></span> | Ano Letivo <span><?php echo $anoLetivo; ?></span></p>
                    </div>

                    <div class="welcome-actions">
                        <button class="btn-primary">
                            <span class="material-symbols-outlined">message</span>
                            Nova Mensagem
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span class="material-symbols-outlined">family_restroom</span>
                        </div>
                        <div class="stat-info">
                            <h3>2</h3>
                            <p>Filhos Matriculados</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span class="material-symbols-outlined">grade</span>
                        </div>
                        <div class="stat-info">
                            <h3>8.5</h3>
                            <p>Média Geral</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span class="material-symbols-outlined">check_circle</span>
                        </div>
                        <div class="stat-info">
                            <h3>95%</h3>
                            <p>Frequência</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <div class="stat-info">
                            <h3>3</h3>
                            <p>Mensagens Não Lidas</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Meus Filhos</h2>
                            <a href="filhos.php" class="view-all">Ver todos</a>
                        </div>
                        <div class="card-content">
                            <div class="student-item">
                                <div class="student-info">
                                    <h4>Steeve Salvador</h4>
                                    <p>9º Ano A - Ensino Fundamental</p>
                                    <div class="student-stats">
                                        <span class="stat">Média: 8.7</span>
                                        <span class="stat">Frequência: 96%</span>
                                    </div>
                                </div>
                                <div class="student-status">
                                    <span class="status-badge active">Ativo</span>
                                </div>
                            </div>
                            <div class="student-item">
                                <div class="student-info">
                                    <h4>Kelton Gonçalves</h4>
                                    <p>6º Ano B - Ensino Fundamental</p>
                                    <div class="student-stats">
                                        <span class="stat">Média: 8.3</span>
                                        <span class="stat">Frequência: 94%</span>
                                    </div>
                                </div>
                                <div class="student-status">
                                    <span class="status-badge active">Ativo</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Próximos Eventos</h2>
                            <a href="calendario.php" class="view-all">Ver todos</a>
                        </div>
                        <div class="events-list">
                            <div class="event">
                                <div class="event-date">
                                    <span class="day">18</span>
                                    <span class="month">Abr</span>
                                </div>
                                <div class="event-details">
                                    <h3>Reunião de Pais - 9º Ano</h3>
                                    <p>19:00 - 21:00 | Auditório</p>
                                </div>
                            </div>
                            <div class="event">
                                <div class="event-date">
                                    <span class="day">20</span>
                                    <span class="month">Abr</span>
                                </div>
                                <div class="event-details">
                                    <h3>Prova de Matemática - João</h3>
                                    <p>08:00 - 10:00 | Sala 105</p>
                                </div>
                            </div>
                            <div class="event">
                                <div class="event-date">
                                    <span class="day">22</span>
                                    <span class="month">Abr</span>
                                </div>
                                <div class="event-details">
                                    <h3>Entrega de Boletins</h3>
                                    <p>14:00 - 17:00 | Secretaria</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Notas Recentes</h2>
                            <a href="notas.php" class="view-all">Ver todas</a>
                        </div>
                        <div class="grades-container">
                            <div class="grade-item">
                                <div class="grade-subject">
                                    <h4>Matemática</h4>
                                    <p>Steeve Salvador - Prova Bimestral</p>
                                </div>
                                <div class="grade-value">
                                    <span class="grade good">8.5</span>
                                </div>
                            </div>
                            <div class="grade-item">
                                <div class="grade-subject">
                                    <h4>Português</h4>
                                    <p>Kelton Gonçalves - Trabalho</p>
                                </div>
                                <div class="grade-value">
                                    <span class="grade excellent">9.2</span>
                                </div>
                            </div>
                            <div class="grade-item">
                                <div class="grade-subject">
                                    <h4>História</h4>
                                    <p>Steeve Salvador - Seminário</p>
                                </div>
                                <div class="grade-value">
                                    <span class="grade good">8.0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Comunicados Recentes</h2>
                            <a href="comunicados.php" class="view-all">Ver todos</a>
                        </div>
                        <div class="announcements-list">
                            <div class="announcement-item">
                                <div class="announcement-icon">
                                    <span class="material-symbols-outlined">campaign</span>
                                </div>
                                <div class="announcement-content">
                                    <h4>Reunião de Pais - 9º Ano</h4>
                                    <p>Convocamos todos os pais do 9º ano para reunião sobre o projeto de formatura.</p>
                                    <span class="announcement-date">Há 2 horas</span>
                                </div>
                            </div>
                            <div class="announcement-item">
                                <div class="announcement-icon">
                                    <span class="material-symbols-outlined">event</span>
                                </div>
                                <div class="announcement-content">
                                    <h4>Festa Junina 2025</h4>
                                    <p>Venha participar da nossa tradicional festa junina no dia 25 de junho.</p>
                                    <span class="announcement-date">Ontem</span>
                                </div>
                            </div>
                            <div class="announcement-item">
                                <div class="announcement-icon">
                                    <span class="material-symbols-outlined">school</span>
                                </div>
                                <div class="announcement-content">
                                    <h4>Calendário de Provas</h4>
                                    <p>Confira o calendário atualizado das provas do 2º trimestre.</p>
                                    <span class="announcement-date">2 dias atrás</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

        // Add notification badge animation
        document.querySelector('.notification').addEventListener('click', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'bell-ring 0.5s ease-in-out';
            }, 10);
        });
    </script>
</body>
</html>