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
    <title>Comunicados - Dashboard Encarregados</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<<<<<<< HEAD
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
            position: fixed;
            height: 100vh;
            z-index: 100;
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

        /* Filtros */
        .filter-container {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-group label {
            font-size: 0.9rem;
            color: var(--text-light);
            white-space: nowrap;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.9rem;
            background-color: var(--white);
            color: var(--text-color);
            cursor: pointer;
            min-width: 150px;
        }

        /* Communications List */
        .communications-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .communication-item {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
            overflow: hidden;
            word-wrap: break-word;
        }

        .communication-item:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .communication-item.unread {
            border-left-color: var(--primary-color);
            background-color: rgba(74, 111, 220, 0.02);
        }

        .communication-item.important {
            border-left-color: #ff9800;
        }

        .communication-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .communication-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .communication-category {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .communication-category.academic {
            background-color: rgba(33, 150, 243, 0.1);
            color: #2196f3;
        }

        .communication-category.event {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .communication-category.administrative {
            background-color: rgba(255, 152, 0, 0.1);
            color: #ff9800;
        }

        .communication-category.general {
            background-color: rgba(156, 39, 176, 0.1);
            color: #9c27b0;
        }

        .communication-date {
            font-size: 0.85rem;
            color: var(--text-light);
            white-space: nowrap;
        }

        .communication-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .important-icon {
            color: #ff9800;
        }

        .communication-content h3 {
            margin-bottom: 10px;
            font-size: 1.1rem;
            color: var(--text-color);
            word-break: break-word;
        }

        .communication-content p {
            line-height: 1.6;
            color: var(--text-light);
            margin-bottom: 15px;
            word-break: break-word;
        }

        .communication-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sender {
            font-size: 0.85rem;
            color: var(--text-light);
            font-weight: 500;
        }

        .communication-tags {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .tag {
            padding: 2px 8px;
            background-color: var(--secondary-color);
            border-radius: 12px;
            font-size: 0.75rem;
            color: var(--text-light);
            white-space: nowrap;
        }

        .load-more-container {
            text-align: center;
            margin-top: 20px;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .filter-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .filter-select {
                flex-grow: 1;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }

            .sidebar-header h2, 
            .profile-info, 
            .menu-text, 
            .sidebar-footer .menu-text {
                display: none;
            }

            .profile {
                justify-content: center;
                padding: 15px 0;
            }

            .profile-avatar {
                margin-right: 0;
            }

            .menu li a {
                justify-content: center;
                padding: 15px 0;
            }

            .menu li a .material-symbols-outlined {
                margin-right: 0;
                font-size: 1.4rem;
            }

            .sidebar-footer {
                padding: 10px 0;
            }

            .sidebar-footer a {
                justify-content: center;
                padding: 10px 0;
            }

            .sidebar-footer a .material-symbols-outlined {
                margin-right: 0;
                font-size: 1.4rem;
            }

            .content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }

            .top-bar {
                padding: 12px 15px;
            }

            .search-container {
                width: 200px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .communication-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .communication-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 576px) {
            .search-container {
                width: 150px;
            }
            
            .communication-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu -->
        <aside class="sidebar">
<<<<<<< HEAD
             <div class="sidebar-header">
                <h2>Pítruca Camama</h2>
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
=======
            <div class="sidebar-header">
                <h2>Pitruca Camama</h2>
            </div>
            <div class="profile">
                <div class="profile-info">
                    <h3><span><?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?></span></h1></h3>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
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
                    <li>
                        <a href="calendario.php">
                            <span class="material-symbols-outlined">calendar_month</span>
                            <span class="menu-text">Calendário</span>
                        </a>
                    </li>
                    <li class="active">
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
<<<<<<< HEAD
                </ul>
            </nav>
            <div class="sidebar-footer">
                <!-- 
=======
                   
                </ul>
            </nav>
            <div class="sidebar-footer">
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                <a href="configuracoes.php">
                    <span class="material-symbols-outlined">settings</span>
                    <span class="menu-text">Configurações</span>
                </a>
<<<<<<< HEAD
    -->
=======
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
                    <input type="text" placeholder="Pesquisar comunicados...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Comunicados</h1>
                    <div class="header-actions">
                        <button class="btn-outline">
                            <span class="material-symbols-outlined">mark_email_read</span>
                            Marcar Todos como Lidos
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-container">
                    <div class="filter-group">
                        <label>Categoria:</label>
                        <select class="filter-select">
                            <option>Todas</option>
                            <option>Geral</option>
                            <option>Acadêmico</option>
                            <option>Eventos</option>
                            <option>Administrativo</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Status:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>Não Lidos</option>
                            <option>Lidos</option>
                            <option>Importantes</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Período:</label>
                        <select class="filter-select">
                            <option>Últimos 30 dias</option>
                            <option>Última semana</option>
                            <option>Hoje</option>
                            <option>Este mês</option>
                        </select>
                    </div>
                </div>

                <!-- Communications List -->
                <div class="communications-container">
                    <div class="communication-item unread important">
                        <div class="communication-header">
                            <div class="communication-meta">
                                <span class="communication-category academic">Acadêmico</span>
                                <span class="communication-date">15/04/2025 - 14:30</span>
                            </div>
                            <div class="communication-actions">
                                <span class="material-symbols-outlined important-icon">star</span>
                                <span class="material-symbols-outlined">more_vert</span>
                            </div>
                        </div>
                        <div class="communication-content">
                            <h3>Reunião de Pais - 9º Ano A</h3>
                            <p>Prezados pais e responsáveis do 9º Ano A, convocamos para reunião no dia 18/04/2025 às 19:00 no auditório da escola. Assuntos: apresentação do projeto de formatura, cronograma de atividades do 3º trimestre e orientações sobre o ensino médio.</p>
                            <div class="communication-footer">
                                <span class="sender">Coordenação Pedagógica</span>
                                <div class="communication-tags">
                                    <span class="tag">Reunião</span>
                                    <span class="tag">9º Ano</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="communication-item unread">
                        <div class="communication-header">
                            <div class="communication-meta">
                                <span class="communication-category event">Eventos</span>
                                <span class="communication-date">14/04/2025 - 16:45</span>
                            </div>
                            <div class="communication-actions">
                                <span class="material-symbols-outlined">more_vert</span>
                            </div>
                        </div>
                        <div class="communication-content">
                            <h3>Festa Junina 2025 - Inscrições Abertas</h3>
                            <p>Estão abertas as inscrições para a tradicional Festa Junina da escola! O evento acontecerá no dia 25 de junho. Precisamos de voluntários para organização e doações para as barracas. Formulário de inscrição disponível na secretaria.</p>
                            <div class="communication-footer">
                                <span class="sender">Direção</span>
                                <div class="communication-tags">
                                    <span class="tag">Festa Junina</span>
                                    <span class="tag">Voluntários</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="communication-item read">
                        <div class="communication-header">
                            <div class="communication-meta">
                                <span class="communication-category administrative">Administrativo</span>
                                <span class="communication-date">13/04/2025 - 10:20</span>
                            </div>
                            <div class="communication-actions">
                                <span class="material-symbols-outlined">more_vert</span>
                            </div>
                        </div>
                        <div class="communication-content">
                            <h3>Calendário de Provas - 2º Trimestre</h3>
                            <p>Segue o calendário atualizado das avaliações do 2º trimestre. As provas começam no dia 21/04 e seguem até 28/04. Orientamos que os alunos se organizem para os estudos. Cronograma detalhado em anexo.</p>
                            <div class="communication-footer">
                                <span class="sender">Secretaria Acadêmica</span>
                                <div class="communication-tags">
                                    <span class="tag">Provas</span>
                                    <span class="tag">Cronograma</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="communication-item read">
                        <div class="communication-header">
                            <div class="communication-meta">
                                <span class="communication-category general">Geral</span>
                                <span class="communication-date">12/04/2025 - 08:15</span>
                            </div>
                            <div class="communication-actions">
                                <span class="material-symbols-outlined">more_vert</span>
                            </div>
                        </div>
                        <div class="communication-content">
                            <h3>Novo Sistema de Comunicação</h3>
                            <p>Informamos que a partir desta semana estamos utilizando um novo sistema de comunicação digital. Todas as informações importantes serão enviadas através desta plataforma. Mantenham seus dados atualizados.</p>
                            <div class="communication-footer">
                                <span class="sender">Tecnologia da Informação</span>
                                <div class="communication-tags">
                                    <span class="tag">Sistema</span>
                                    <span class="tag">Digital</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="communication-item read">
                        <div class="communication-header">
                            <div class="communication-meta">
                                <span class="communication-category academic">Acadêmico</span>
                                <span class="communication-date">10/04/2025 - 15:30</span>
                            </div>
                            <div class="communication-actions">
                                <span class="material-symbols-outlined">more_vert</span>
                            </div>
                        </div>
                        <div class="communication-content">
                            <h3>Projeto de Leitura - Abril</h3>
                            <p>Iniciamos o projeto de leitura do mês de abril. Cada turma receberá uma lista de livros recomendados. O objetivo é incentivar o hábito da leitura e desenvolver o senso crítico dos alunos. Participação da família é fundamental.</p>
                            <div class="communication-footer">
                                <span class="sender">Biblioteca</span>
                                <div class="communication-tags">
                                    <span class="tag">Leitura</span>
                                    <span class="tag">Projeto</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Load More -->
                <div class="load-more-container">
                    <button class="btn-outline">
                        <span class="material-symbols-outlined">expand_more</span>
                        Carregar Mais Comunicados
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script>
<<<<<<< HEAD
=======
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        // Mark as read functionality
        document.querySelectorAll('.communication-item').forEach(item => {
            item.addEventListener('click', function() {
                if (this.classList.contains('unread')) {
                    this.classList.remove('unread');
                    this.classList.add('read');
                }
            });
        });
    </script>
<<<<<<< HEAD
=======

    <style>
        .communications-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .communication-item {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .communication-item:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .communication-item.unread {
            border-left-color: var(--primary-color);
            background-color: rgba(74, 111, 220, 0.02);
        }

        .communication-item.important {
            border-left-color: #ff9800;
        }

        .communication-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .communication-meta {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .communication-category {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .communication-category.academic {
            background-color: rgba(33, 150, 243, 0.1);
            color: #2196f3;
        }

        .communication-category.event {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .communication-category.administrative {
            background-color: rgba(255, 152, 0, 0.1);
            color: #ff9800;
        }

        .communication-category.general {
            background-color: rgba(156, 39, 176, 0.1);
            color: #9c27b0;
        }

        .communication-date {
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .communication-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .important-icon {
            color: #ff9800;
        }

        .communication-content h3 {
            margin-bottom: 10px;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .communication-content p {
            line-height: 1.6;
            color: var(--text-light);
            margin-bottom: 15px;
        }

        .communication-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sender {
            font-size: 0.85rem;
            color: var(--text-light);
            font-weight: 500;
        }

        .communication-tags {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .tag {
            padding: 2px 8px;
            background-color: var(--secondary-color);
            border-radius: 12px;
            font-size: 0.75rem;
            color: var(--text-light);
        }

        .load-more-container {
            text-align: center;
        }

        @media (max-width: 768px) {
            .communication-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .communication-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
</body>
</html>