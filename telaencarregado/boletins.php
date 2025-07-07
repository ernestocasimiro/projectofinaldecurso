<?php
<<<<<<< HEAD
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
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit;
}

$dataAtual = '15 de Abril de 2025';
$trimestre = '2º trimestre';
$anoLetivo = '2025';

?>
=======
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

>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boletins - Dashboard Encarregados</title>
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

        /* Filters */
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

        /* Bulletins Grid */
=======
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Pitruca Camama</h2>
                <span class="material-symbols-outlined menu-toggle" id="menuToggle">menu</span>
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
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar boletins...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Boletins Escolares</h1>
                    <div class="header-actions">
                        <button class="btn-outline">
                            <span class="material-symbols-outlined">download</span>
                            Baixar Todos
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-container">
                    <div class="filter-group">
                        <label>Aluno:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>João Santos</option>
                            <option>Ana Santos</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Ano Letivo:</label>
                        <select class="filter-select">
                            <option>2025</option>
                            <option>2024</option>
                            <option>2023</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Período:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>1º Trimestre</option>
                            <option>2º Trimestre</option>
                            <option>3º Trimestre</option>
                            <option>Final</option>
                        </select>
                    </div>
                </div>

                <!-- Bulletins Grid -->
                <div class="bulletins-grid">
                    <div class="bulletin-card" data-student="joao" data-period="2025-2">
                        <div class="bulletin-header">
                            <div class="bulletin-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="bulletin-status available">
                                <span class="material-symbols-outlined">check_circle</span>
                                Disponível
                            </div>
                        </div>
                        <div class="bulletin-info">
                            <h3>João Santos - 2º Trimestre 2025</h3>
                            <p>9º Ano A - Ensino Fundamental</p>
                            <div class="bulletin-stats">
                                <div class="stat">
                                    <span class="label">Média Geral:</span>
                                    <span class="value good">8.7</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Frequência:</span>
                                    <span class="value excellent">96%</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Situação:</span>
                                    <span class="value approved">Aprovado</span>
                                </div>
                            </div>
                        </div>
                        <div class="bulletin-actions">
                            <button class="btn-primary view-bulletin">
                                <span class="material-symbols-outlined">visibility</span>
                                Visualizar
                            </button>
                            <button class="btn-outline download-bulletin">
                                <span class="material-symbols-outlined">download</span>
                                Baixar PDF
                            </button>
                        </div>
                        <div class="bulletin-date">
                            Gerado em: 15/04/2025
                        </div>
                    </div>

                    <div class="bulletin-card" data-student="ana" data-period="2025-2">
                        <div class="bulletin-header">
                            <div class="bulletin-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="bulletin-status available">
                                <span class="material-symbols-outlined">check_circle</span>
                                Disponível
                            </div>
                        </div>
                        <div class="bulletin-info">
                            <h3>Ana Santos - 2º Trimestre 2025</h3>
                            <p>6º Ano B - Ensino Fundamental</p>
                            <div class="bulletin-stats">
                                <div class="stat">
                                    <span class="label">Média Geral:</span>
                                    <span class="value good">8.3</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Frequência:</span>
                                    <span class="value good">94%</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Situação:</span>
                                    <span class="value approved">Aprovado</span>
                                </div>
                            </div>
                        </div>
                        <div class="bulletin-actions">
                            <button class="btn-primary view-bulletin">
                                <span class="material-symbols-outlined">visibility</span>
                                Visualizar
                            </button>
                            <button class="btn-outline download-bulletin">
                                <span class="material-symbols-outlined">download</span>
                                Baixar PDF
                            </button>
                        </div>
                        <div class="bulletin-date">
                            Gerado em: 15/04/2025
                        </div>
                    </div>

                    <div class="bulletin-card" data-student="joao" data-period="2025-1">
                        <div class="bulletin-header">
                            <div class="bulletin-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="bulletin-status available">
                                <span class="material-symbols-outlined">check_circle</span>
                                Disponível
                            </div>
                        </div>
                        <div class="bulletin-info">
                            <h3>João Santos - 1º Trimestre 2025</h3>
                            <p>9º Ano A - Ensino Fundamental</p>
                            <div class="bulletin-stats">
                                <div class="stat">
                                    <span class="label">Média Geral:</span>
                                    <span class="value good">8.2</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Frequência:</span>
                                    <span class="value excellent">98%</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Situação:</span>
                                    <span class="value approved">Aprovado</span>
                                </div>
                            </div>
                        </div>
                        <div class="bulletin-actions">
                            <button class="btn-primary view-bulletin">
                                <span class="material-symbols-outlined">visibility</span>
                                Visualizar
                            </button>
                            <button class="btn-outline download-bulletin">
                                <span class="material-symbols-outlined">download</span>
                                Baixar PDF
                            </button>
                        </div>
                        <div class="bulletin-date">
                            Gerado em: 15/03/2025
                        </div>
                    </div>

                    <div class="bulletin-card" data-student="ana" data-period="2025-1">
                        <div class="bulletin-header">
                            <div class="bulletin-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="bulletin-status available">
                                <span class="material-symbols-outlined">check_circle</span>
                                Disponível
                            </div>
                        </div>
                        <div class="bulletin-info">
                            <h3>Ana Santos - 1º Trimestre 2025</h3>
                            <p>6º Ano B - Ensino Fundamental</p>
                            <div class="bulletin-stats">
                                <div class="stat">
                                    <span class="label">Média Geral:</span>
                                    <span class="value good">8.5</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Frequência:</span>
                                    <span class="value good">92%</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Situação:</span>
                                    <span class="value approved">Aprovado</span>
                                </div>
                            </div>
                        </div>
                        <div class="bulletin-actions">
                            <button class="btn-primary view-bulletin">
                                <span class="material-symbols-outlined">visibility</span>
                                Visualizar
                            </button>
                            <button class="btn-outline download-bulletin">
                                <span class="material-symbols-outlined">download</span>
                                Baixar PDF
                            </button>
                        </div>
                        <div class="bulletin-date">
                            Gerado em: 15/03/2025
                        </div>
                    </div>
                </div>

                <!-- Historical Performance -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>Histórico de Desempenho</h2>
                        <div class="chart-filters">
                            <button class="btn-outline active">João Santos</button>
                            <button class="btn-outline">Ana Santos</button>
                        </div>
                    </div>
                    <div class="performance-chart">
                        <div class="chart-placeholder">
                            <span class="material-symbols-outlined">trending_up</span>
                            <p>Gráfico de evolução das médias por trimestre</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de Visualização do Boletim -->
    <div class="modal" id="bulletinModal">
        <div class="modal-content bulletin-modal">
            <div class="modal-header">
                <h3>Boletim Escolar</h3>
                <span class="modal-close" id="closeBulletinModal">&times;</span>
            </div>
            <div class="modal-body">
                <div class="bulletin-document">
                    <!-- Cabeçalho do Boletim -->
                    <div class="bulletin-doc-header">
                        <div class="school-info">
                            <h2>Escola Pitruca</h2>
                            <p>Rua da Educação, 123 camama, luanda</p>
                            <p>Tel: (244) 912345678| email@pitruca.com</p>
                        </div>
                        <div class="bulletin-title">
                            <h1>BOLETIM ESCOLAR</h1>
                            <p id="bulletinPeriod">2º Trimestre - 2025</p>
                        </div>
                    </div>

                    <!-- Informações do Aluno -->
                    <div class="student-info-section">
                        <div class="info-row">
                            <div class="info-item">
                                <strong>Nome:</strong> <span id="studentName">João Santos</span>
                            </div>
                            <div class="info-item">
                                <strong>Matrícula:</strong> <span id="studentId">2025001234</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <strong>Turma:</strong> <span id="studentClass">9º Ano A</span>
                            </div>
                            <div class="info-item">
                                <strong>Ano Letivo:</strong> <span id="schoolYear">2025</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Notas -->
                    <div class="grades-section">
                        <h3>Desempenho Acadêmico</h3>
                        <table class="bulletin-table">
                            <thead>
                                <tr>
                                    <th>Disciplina</th>
                                    <th>Professor</th>
                                    <th>1ª Aval.</th>
                                    <th>2ª Aval.</th>
                                    <th>Trabalhos</th>
                                    <th>Média</th>
                                    <th>Faltas</th>
                                    <th>Situação</th>
                                </tr>
                            </thead>
                            <tbody id="gradesTableBody">
                                <!-- Dados serão inseridos dinamicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Resumo -->
                    <div class="bulletin-summary">
                        <div class="summary-item">
                            <strong>Média Geral:</strong> <span id="overallAverage">8.7</span>
                        </div>
                        <div class="summary-item">
                            <strong>Total de Faltas:</strong> <span id="totalAbsences">3</span>
                        </div>
                        <div class="summary-item">
                            <strong>Frequência:</strong> <span id="attendanceRate">96%</span>
                        </div>
                        <div class="summary-item">
                            <strong>Situação Final:</strong> <span id="finalStatus" class="status-approved">APROVADO</span>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="observations-section">
                        <h3>Observações</h3>
                        <div class="observation-item">
                            <strong>Coordenação Pedagógica:</strong>
                            <p>O aluno demonstra excelente desempenho acadêmico e participação ativa nas atividades escolares. Continue assim!</p>
                        </div>
                    </div>

                    <!-- Assinaturas -->
                    <div class="signatures-section">
                        <div class="signature-item">
                            <div class="signature-line"></div>
                            <p>Coordenação Pedagógica</p>
                        </div>
                        <div class="signature-item">
                            <div class="signature-line"></div>
                            <p>Direção</p>
                        </div>
                    </div>

                    <!-- Data de Emissão -->
                    <div class="issue-date">
                        <p>luanda, <span id="issueDate">15 de Abril de 2025</span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-primary" id="downloadBulletinBtn">
                    <span class="material-symbols-outlined">download</span>
                    Baixar PDF
                </button>
                <button class="btn-outline" id="printBulletinBtn">
                    <span class="material-symbols-outlined">print</span>
                    Imprimir
                </button>
                <button class="btn-outline" id="closeBulletinBtn">Fechar</button>
            </div>
        </div>
    </div>

    <!-- Modal de Download -->
    <div class="modal" id="downloadModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Download do Boletim</h3>
                <span class="modal-close" id="closeDownloadModal">&times;</span>
            </div>
            <div class="modal-body">
                <div class="download-options">
                    <div class="download-option" data-type="pdf">
                        <div class="option-icon">
                            <span class="material-symbols-outlined">picture_as_pdf</span>
                        </div>
                        <div class="option-info">
                            <h4>PDF Completo</h4>
                            <p>Boletim completo com todas as informações e formatação oficial</p>
                        </div>
                        <button class="btn-primary">
                            <span class="material-symbols-outlined">download</span>
                            Baixar
                        </button>
                    </div>
                    <div class="download-option" data-type="excel">
                        <div class="option-icon">
                            <span class="material-symbols-outlined">table_chart</span>
                        </div>
                        <div class="option-info">
                            <h4>Planilha Excel</h4>
                            <p>Dados das notas em formato de planilha para análise</p>
                        </div>
                        <button class="btn-outline">
                            <span class="material-symbols-outlined">download</span>
                            Baixar
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-outline" id="cancelDownloadBtn">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Progress Modal -->
    <div class="modal" id="progressModal">
        <div class="modal-content progress-modal">
            <div class="modal-body">
                <div class="progress-content">
                    <div class="progress-icon">
                        <span class="material-symbols-outlined rotating">download</span>
                    </div>
                    <h3 id="progressTitle">Preparando download...</h3>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <p id="progressText">0%</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

        // Dados dos boletins
        const bulletinData = {
            'joao-2025-2': {
                student: {
                    name: 'João Santos',
                    id: '2025001234',
                    class: '9º Ano A - Ensino Fundamental',
                    year: '2025'
                },
                period: '2º Trimestre - 2025',
                grades: [
                    { subject: 'Matemática', teacher: 'Prof. Carlos Silva', eval1: 9.0, eval2: 8.5, work: 9.2, average: 8.9, absences: 0, status: 'Aprovado' },
                    { subject: 'Português', teacher: 'Prof. Ana Maria', eval1: 8.0, eval2: 9.0, work: 8.5, average: 8.5, absences: 1, status: 'Aprovado' },
                    { subject: 'Física', teacher: 'Prof. Roberto Lima', eval1: 7.0, eval2: 7.5, work: 7.0, average: 7.2, absences: 2, status: 'Recuperação' },
                    { subject: 'História', teacher: 'Prof. Lucia Santos', eval1: 8.5, eval2: 9.0, work: 8.0, average: 8.5, absences: 0, status: 'Aprovado' }
                ],
                summary: {
                    overallAverage: 8.7,
                    totalAbsences: 3,
                    attendanceRate: '96%',
                    finalStatus: 'APROVADO'
                },
                issueDate: '15 de Abril de 2025'
            },
            'ana-2025-2': {
                student: {
                    name: 'Ana Santos',
                    id: '2025001235',
                    class: '6º Ano B - Ensino Fundamental',
                    year: '2025'
                },
                period: '2º Trimestre - 2025',
                grades: [
                    { subject: 'Matemática', teacher: 'Prof. Pedro Costa', eval1: 8.0, eval2: 7.5, work: 8.0, average: 7.8, absences: 1, status: 'Aprovado' },
                    { subject: 'Português', teacher: 'Prof. Fernanda Silva', eval1: 9.0, eval2: 9.5, work: 9.0, average: 9.2, absences: 0, status: 'Aprovado' },
                    { subject: 'Inglês', teacher: 'Prof. Michael Johnson', eval1: 8.0, eval2: 8.0, work: 8.0, average: 8.0, absences: 0, status: 'Aprovado' }
                ],
                summary: {
                    overallAverage: 8.3,
                    totalAbsences: 2,
                    attendanceRate: '94%',
                    finalStatus: 'APROVADO'
                },
                issueDate: '15 de Abril de 2025'
            }
        };

        // Função para visualizar boletim
        function viewBulletin(student, period) {
            const bulletinKey = `${student}-${period}`;
            const data = bulletinData[bulletinKey];
            
            if (!data) {
                alert('Boletim não encontrado!');
                return;
            }
            
            // Preencher dados do estudante
            document.getElementById('studentName').textContent = data.student.name;
            document.getElementById('studentId').textContent = data.student.id;
            document.getElementById('studentClass').textContent = data.student.class;
            document.getElementById('schoolYear').textContent = data.student.year;
            document.getElementById('bulletinPeriod').textContent = data.period;
            
            // Preencher tabela de notas
            const tbody = document.getElementById('gradesTableBody');
            tbody.innerHTML = '';
            
            data.grades.forEach(grade => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${grade.subject}</td>
                    <td>${grade.teacher}</td>
                    <td>${grade.eval1.toFixed(1)}</td>
                    <td>${grade.eval2.toFixed(1)}</td>
                    <td>${grade.work.toFixed(1)}</td>
                    <td class="grade-average ${getGradeClass(grade.average)}">${grade.average.toFixed(1)}</td>
                    <td>${grade.absences}</td>
                    <td><span class="status-badge ${grade.status === 'Aprovado' ? 'approved' : 'recovery'}">${grade.status}</span></td>
                `;
                tbody.appendChild(row);
            });
            
            // Preencher resumo
            document.getElementById('overallAverage').textContent = data.summary.overallAverage.toFixed(1);
            document.getElementById('totalAbsences').textContent = data.summary.totalAbsences;
            document.getElementById('attendanceRate').textContent = data.summary.attendanceRate;
            document.getElementById('finalStatus').textContent = data.summary.finalStatus;
            document.getElementById('issueDate').textContent = data.issueDate;
            
            // Mostrar modal
            document.getElementById('bulletinModal').style.display = 'flex';
        }

        // Função para obter classe CSS da nota
        function getGradeClass(grade) {
            if (grade >= 9) return 'excellent';
            if (grade >= 8) return 'good';
            if (grade >= 7) return 'average';
            return 'poor';
        }

        // Função para simular download
        function simulateDownload(fileName, fileType) {
            document.getElementById('downloadModal').style.display = 'none';
            document.getElementById('progressModal').style.display = 'flex';
            
            const progressFill = document.getElementById('progressFill');
            const progressText = document.getElementById('progressText');
            const progressTitle = document.getElementById('progressTitle');
            
            progressTitle.textContent = `Gerando arquivo ${fileType}...`;
            
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 100) progress = 100;
                
                progressFill.style.width = progress + '%';
                progressText.textContent = Math.round(progress) + '%';
                
                if (progress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        document.getElementById('progressModal').style.display = 'none';
                        showDownloadSuccess(fileName);
                        createDownloadLink(fileName);
                    }, 500);
                }
            }, 200);
        }

        // Função para mostrar sucesso do download
        function showDownloadSuccess(fileName) {
            const notification = document.createElement('div');
            notification.className = 'download-notification';
            notification.innerHTML = `
                <div class="notification-content">
                    <span class="material-symbols-outlined">check_circle</span>
                    <div>
                        <h4>Download concluído!</h4>
                        <p>${fileName} foi baixado com sucesso</p>
                    </div>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Função para criar link de download
        function createDownloadLink(fileName) {
            const content = `Conteúdo do arquivo ${fileName}`;
            const blob = new Blob([content], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            
            const a = document.createElement('a');
            a.href = url;
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Botões de visualizar
            document.querySelectorAll('.view-bulletin').forEach(button => {
                button.addEventListener('click', function() {
                    const card = this.closest('.bulletin-card');
                    const student = card.dataset.student;
                    const period = card.dataset.period;
                    viewBulletin(student, period);
                });
            });

            // Botões de download direto
            document.querySelectorAll('.download-bulletin').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('downloadModal').style.display = 'flex';
                });
            });

            // Fechar modais
            document.getElementById('closeBulletinModal').addEventListener('click', function() {
                document.getElementById('bulletinModal').style.display = 'none';
            });

            document.getElementById('closeBulletinBtn').addEventListener('click', function() {
                document.getElementById('bulletinModal').style.display = 'none';
            });

            document.getElementById('closeDownloadModal').addEventListener('click', function() {
                document.getElementById('downloadModal').style.display = 'none';
            });

            document.getElementById('cancelDownloadBtn').addEventListener('click', function() {
                document.getElementById('downloadModal').style.display = 'none';
            });

            // Download do modal do boletim
            document.getElementById('downloadBulletinBtn').addEventListener('click', function() {
                document.getElementById('downloadModal').style.display = 'flex';
            });

            // Imprimir boletim
            document.getElementById('printBulletinBtn').addEventListener('click', function() {
                window.print();
            });

            // Opções de download
            document.querySelectorAll('.download-option button').forEach(button => {
                button.addEventListener('click', function() {
                    const option = this.closest('.download-option');
                    const type = option.dataset.type;
                    
                    if (type === 'pdf') {
                        simulateDownload('boletim-completo.pdf', 'PDF');
                    } else if (type === 'excel') {
                        simulateDownload('notas.xlsx', 'Excel');
                    }
                });
            });

            // Chart filters
            document.querySelectorAll('.chart-filters button').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.chart-filters button').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>

    <style>
        /* Estilos específicos para boletins */
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        .bulletins-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .bulletin-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            transition: all 0.2s ease;
<<<<<<< HEAD
            overflow: hidden;
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        .bulletin-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .bulletin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .bulletin-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bulletin-icon .material-symbols-outlined {
            color: var(--primary-color);
            font-size: 28px;
        }

        .bulletin-status {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .bulletin-status.available {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }

        .bulletin-info h3 {
            margin-bottom: 5px;
            font-size: 1.1rem;
<<<<<<< HEAD
            word-break: break-word;
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        .bulletin-info p {
            color: var(--text-light);
            margin-bottom: 15px;
<<<<<<< HEAD
            word-break: break-word;
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        .bulletin-stats {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .bulletin-stats .stat {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bulletin-stats .label {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .bulletin-stats .value {
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        .bulletin-stats .value.excellent {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }

        .bulletin-stats .value.good {
            background-color: rgba(33, 150, 243, 0.1);
<<<<<<< HEAD
            color: var(--primary-color);
=======
            color: var(--info-color);
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        .bulletin-stats .value.approved {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }

        .bulletin-actions {
            display: flex;
            gap: 10px;
            margin: 20px 0 15px 0;
        }

        .bulletin-actions button {
            flex: 1;
        }

        .bulletin-date {
            font-size: 0.8rem;
            color: var(--text-light);
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }

<<<<<<< HEAD
        /* Performance Chart */
        .dashboard-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .chart-filters {
            display: flex;
            gap: 10px;
        }

        .chart-filters button {
            padding: 8px 12px;
            font-size: 0.85rem;
        }

        .chart-filters button.active {
            background-color: var(--primary-light);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        .performance-chart {
            padding: 20px;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
<<<<<<< HEAD
            background-color: var(--gray-100);
            border-radius: 8px;
        }

        .chart-placeholder {
            text-align: center;
            color: var(--text-light);
        }

        .chart-placeholder .material-symbols-outlined {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 10px;
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 10px;
            width: 90%;
<<<<<<< HEAD
            max-width: 800px;
=======
            max-width: 500px;
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            max-height: 90vh;
            overflow-y: auto;
        }

<<<<<<< HEAD
=======
        .bulletin-modal {
            max-width: 900px;
            width: 95%;
        }

>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }

<<<<<<< HEAD
        .modal-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
        }

=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        .modal-close {
            font-size: 24px;
            cursor: pointer;
            color: var(--text-light);
<<<<<<< HEAD
            transition: all 0.2s;
        }

        .modal-close:hover {
            color: var(--danger-color);
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px;
            border-top: 1px solid var(--border-color);
        }

        /* Bulletin document styles */
        .bulletin-document {
            background-color: white;
            padding: 30px;
            font-family: 'Times New Roman', serif;
        }

        .bulletin-doc-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .school-info h2 {
            margin: 0;
            color: #333;
            font-size: 1.5rem;
        }

        .school-info p {
            margin: 5px 0;
            color: #666;
<<<<<<< HEAD
            font-size: 0.9rem;
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        .bulletin-title h1 {
            margin: 20px 0 10px 0;
            color: var(--primary-color);
            font-size: 1.8rem;
        }

<<<<<<< HEAD
        #bulletinPeriod {
            font-weight: bold;
            color: #333;
        }

=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        .student-info-section {
            margin: 20px 0;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }

        .info-item {
            flex: 1;
        }

<<<<<<< HEAD
        .info-item strong {
            color: #333;
        }

=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        .grades-section {
            margin: 30px 0;
        }

        .grades-section h3 {
            margin-bottom: 15px;
            color: #333;
<<<<<<< HEAD
            font-size: 1.1rem;
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        .bulletin-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 0.9rem;
        }

        .bulletin-table th,
        .bulletin-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .bulletin-table th {
            background-color: var(--secondary-color);
            font-weight: bold;
        }

<<<<<<< HEAD
        .grade-average {
            font-weight: bold;
        }

        .grade-average.excellent {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
=======
        .grade-average.excellent {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            font-weight: bold;
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        .grade-average.good {
            background-color: rgba(33, 150, 243, 0.1);
<<<<<<< HEAD
            color: var(--primary-color);
=======
            color: var(--info-color);
            font-weight: bold;
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        .grade-average.average {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning-color);
<<<<<<< HEAD
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
=======
            font-weight: bold;
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        .status-badge.approved {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
<<<<<<< HEAD
=======
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        .status-badge.recovery {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning-color);
<<<<<<< HEAD
=======
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        }

        .bulletin-summary {
            margin: 30px 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-approved {
            color: var(--success-color);
            font-weight: bold;
        }

        .observations-section {
            margin: 30px 0;
        }

        .observation-item {
            margin: 15px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

<<<<<<< HEAD
        .observation-item strong {
            color: #333;
        }

        .observation-item p {
            margin: 5px 0 0 0;
        }

=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        .signatures-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
        }

        .signature-item {
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            width: 200px;
            margin-bottom: 10px;
        }

        .issue-date {
            text-align: right;
            margin-top: 30px;
            font-style: italic;
        }

        /* Download options */
        .download-options {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .download-option {
            display: flex;
            align-items: center;
            padding: 20px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            transition: all 0.2s;
        }

        .download-option:hover {
            border-color: var(--primary-color);
            background-color: rgba(74, 111, 220, 0.02);
        }

        .option-icon {
            margin-right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .option-icon .material-symbols-outlined {
            color: var(--primary-color);
            font-size: 28px;
        }

        .option-info {
            flex: 1;
            margin-right: 20px;
        }

        .option-info h4 {
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .option-info p {
            color: var(--text-light);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Progress modal */
        .progress-modal {
            max-width: 400px;
        }

        .progress-content {
            text-align: center;
            padding: 20px;
        }

        .progress-icon {
            margin-bottom: 20px;
        }

        .progress-icon .material-symbols-outlined {
            font-size: 48px;
            color: var(--primary-color);
        }

        .rotating {
            animation: rotate 2s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: var(--border-color);
            border-radius: 4px;
            overflow: hidden;
            margin: 20px 0;
        }

        .progress-fill {
            height: 100%;
            background-color: var(--primary-color);
            transition: width 0.3s ease;
            width: 0%;
        }

        /* Download notification */
        .download-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 20px;
            z-index: 1001;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            border-left: 4px solid var(--success-color);
        }

        .download-notification.show {
            transform: translateX(0);
        }

        .notification-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .notification-content .material-symbols-outlined {
            color: var(--success-color);
            font-size: 24px;
        }

        .notification-content h4 {
            margin-bottom: 3px;
            font-size: 0.95rem;
        }

        .notification-content p {
            color: var(--text-light);
            font-size: 0.85rem;
            margin: 0;
        }

<<<<<<< HEAD
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

=======
        @media (max-width: 768px) {
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            .bulletins-grid {
                grid-template-columns: 1fr;
            }

            .bulletin-actions {
                flex-direction: column;
            }

<<<<<<< HEAD
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .chart-filters {
                width: 100%;
                justify-content: space-between;
            }

            .bulletin-summary {
                grid-template-columns: 1fr;
            }

            .signatures-section {
                flex-direction: column;
                gap: 30px;
            }

            .download-option {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .option-info {
                margin-right: 0;
            }
        }

        @media (max-width: 576px) {
            .search-container {
                width: 150px;
            }

=======
            .bulletin-modal {
                width: 98%;
                max-height: 95vh;
            }
            
            .bulletin-document {
                padding: 15px;
            }
            
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            .bulletin-table {
                font-size: 0.8rem;
            }
            
            .bulletin-table th,
            .bulletin-table td {
                padding: 6px 4px;
            }
            
            .info-row {
                flex-direction: column;
                gap: 10px;
            }
<<<<<<< HEAD
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu -->
        <aside class="sidebar">
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
                    <li class="active">
                        <a href="boletins.php">
                            <span class="material-symbols-outlined">description</span>
                            <span class="menu-text">Boletins</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
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
                    <input type="text" placeholder="Pesquisar boletins...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Boletins Escolares</h1>
                    <div class="header-actions">
                        <button class="btn-outline">
                            <span class="material-symbols-outlined">download</span>
                            Baixar Todos
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-container">
                    <div class="filter-group">
                        <label>Aluno:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>Kelton Gonçalves</option>
                            <option>Steeve Salvador</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Ano Letivo:</label>
                        <select class="filter-select">
                            <option>2025</option>
                            <option>2024</option>
                            <option>2023</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Período:</label>
                        <select class="filter-select">
                            <option>Todos</option>
                            <option>1º Trimestre</option>
                            <option>2º Trimestre</option>
                            <option>3º Trimestre</option>
                            <option>Final</option>
                        </select>
                    </div>
                </div>

                <!-- Bulletins Grid -->
                <div class="bulletins-grid">
                    <div class="bulletin-card" data-student="joao" data-period="2025-2">
                        <div class="bulletin-header">
                            <div class="bulletin-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="bulletin-status available">
                                <span class="material-symbols-outlined">check_circle</span>
                                Disponível
                            </div>
                        </div>
                        <div class="bulletin-info">
                            <h3>Joao Francisco Francisco - 2º Trimestre 2025</h3>
                            <p>Ensino Fundamental</p>
                            <div class="bulletin-stats">
                                <div class="stat">
                                    <span class="label">Média Geral:</span>
                                    <span class="value good">8.7</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Frequência:</span>
                                    <span class="value excellent">96%</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Situação:</span>
                                    <span class="value approved">Aprovado</span>
                                </div>
                            </div>
                        </div>
                        <div class="bulletin-actions">
                            <button class="btn-primary view-bulletin">
                                <span class="material-symbols-outlined">visibility</span>
                                Visualizar
                            </button>
                            <button class="btn-outline download-bulletin">
                                <span class="material-symbols-outlined">download</span>
                                Baixar PDF
                            </button>
                        </div>
                        <div class="bulletin-date">
                            Gerado em: 15/04/2025
                        </div>
                    </div>
                    <div class="bulletin-card" data-student="ana" data-period="2025-2">
                        <div class="bulletin-header">
                            <div class="bulletin-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="bulletin-status available">
                                <span class="material-symbols-outlined">check_circle</span>
                                Disponível
                            </div>
                        </div>
                        <div class="bulletin-info">
                            <h3>Joana Francisco - 2º Trimestre 2025</h3>
                            <p>6º Ano B - Ensino Fundamental</p>
                            <div class="bulletin-stats">
                                <div class="stat">
                                    <span class="label">Média Geral:</span>
                                    <span class="value good">8.3</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Frequência:</span>
                                    <span class="value good">94%</span>
                                </div>
                               
                                <div class="stat">
                                    <span class="label">Situação:</span>
                                    <span class="value approved">Aprovado</span>
                                </div>
                            </div>
                        </div>
                        <div class="bulletin-actions">
                            <button class="btn-primary view-bulletin">
                                <span class="material-symbols-outlined">visibility</span>
                                Visualizar
                            </button>
                            <button class="btn-outline download-bulletin">
                                <span class="material-symbols-outlined">download</span>
                                Baixar PDF
                            </button>
                        </div>
                        <div class="bulletin-date">
                            Gerado em: 15/04/2025
                        </div>
                    </div>
                    <!--
                    <div class="bulletin-card" data-student="joao" data-period="2025-1">
                        <div class="bulletin-header">
                            <div class="bulletin-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="bulletin-status available">
                                <span class="material-symbols-outlined">check_circle</span>
                                Disponível
                            </div>
                        </div>
                        <div class="bulletin-info">
                            <h3>Kelton Gonçalves - 1º Trimestre 2025</h3>
                            <p>9º Steeve Salvador- Ensino Fundamental</p>
                            <div class="bulletin-stats">
                                <div class="stat">
                                    <span class="label">Média Geral:</span>
                                    <span class="value good">8.2</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Frequência:</span>
                                    <span class="value excellent">98%</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Situação:</span>
                                    <span class="value approved">Aprovado</span>
                                </div>
                            </div>
                        </div>
                        <div class="bulletin-actions">
                            <button class="btn-primary view-bulletin">
                                <span class="material-symbols-outlined">visibility</span>
                                Visualizar
                            </button>
                            <button class="btn-outline download-bulletin">
                                <span class="material-symbols-outlined">download</span>
                                Baixar PDF
                            </button>
                        </div>
                        <div class="bulletin-date">
                            Gerado em: 15/03/2025
                        </div>
                    </div>
    -->
                    <!--
                    <div class="bulletin-card" data-student="ana" data-period="2025-1">
                        <div class="bulletin-header">
                            <div class="bulletin-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="bulletin-status available">
                                <span class="material-symbols-outlined">check_circle</span>
                                Disponível
                            </div>
                        </div>
                        <div class="bulletin-info">
                            <h3>Steeve Salvador - 1º Trimestre 2025</h3>
                            <p>6º Ano B - Ensino Fundamental</p>
                            <div class="bulletin-stats">
                                <div class="stat">
                                    <span class="label">Média Geral:</span>
                                    <span class="value good">8.5</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Frequência:</span>
                                    <span class="value good">92%</span>
                                </div>


                                                              <div class="stat">
                                    <span class="label">Situação:</span>
                                    <span class="value approved">Aprovado</span>
                                </div>
                            </div>
                        </div>
                        <div class="bulletin-actions">
                            <button class="btn-primary view-bulletin">
                                <span class="material-symbols-outlined">visibility</span>
                                Visualizar
                            </button>
                            <button class="btn-outline download-bulletin">
                                <span class="material-symbols-outlined">download</span>
                                Baixar PDF
                            </button>
                        </div>
                        <div class="bulletin-date">
                            Gerado em: 15/04/2025
                        </div>
                    </div>
                </div>
    -->
                <!-- Performance Chart 
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>Desempenho dos Alunos</h2>
                        <div class="chart-filters">
                            <button class="btn-outline active">Geral</button>
                            <button class="btn-outline">Português</button>
                            <button class="btn-outline">Matemática</button>
                            <button class="btn-outline">História</button>
                        </div>
                    </div>
                    <div class="performance-chart">
                        <div class="chart-placeholder">
                            <span class="material-symbols-outlined">bar_chart</span>
                            <p>Gráfico de desempenho dos alunos</p>
                        </div>
                    </div>
                </div>
                -->
            </div>
        </main>
    </div>

    <!-- Bulletin Modal -->
<div class="modal" id="bulletinModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Boletim Escolar</h3>
            <span class="modal-close">&times;</span>
        </div>

        <div class="modal-body">
            <div class="bulletin-document">
                <div class="bulletin-doc-header">
                    <div class="school-info">
                        <h2>ESCOLA PITRUCA CAMAMA</h2>
                        <p>Bairro BPC - Luanda, Angola</p>
                        <p>Telefone: 222 333 444 | Email: info@pitruca.edu.ao</p>
                    </div>
                    <div class="bulletin-title">
                        <h1>BOLETIM ESCOLAR</h1>
                        <p id="bulletinPeriod"><?php echo $trimestre . ' - ' . $anoLetivo; ?></p>
                    </div>
                </div>

                <div class="student-info-section">
                    <div class="info-row">
                        <div class="info-item">
                            <strong>Nome do Aluno:</strong> Joao Francisco Francisco
                        </div>
                        <div class="info-item">
                            <strong>Nº de Matrícula:</strong> 20250015
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-item">
                            <strong>Turma:</strong> 9º Ano A
                        </div>
                        <div class="info-item">
                            <strong>Turno:</strong> Manhã
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-item">
                            <strong>Encarregado:</strong> <?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?>
                        </div>
                        <div class="info-item">
                            <strong>Contacto:</strong> 923 456 789
                        </div>
                    </div>
                </div>

                <div class="grades-section">
                    <h3>Resultados Escolares</h3>
                    <table class="bulletin-table">
                        <thead>
                            <tr>
                                <th rowspan="2">Disciplina</th>
                                <th colspan="3">Avaliações</th>
                                <th rowspan="2">Média</th>
                                <th rowspan="2">Faltas</th>
                                <th rowspan="2">Situação</th>
                            </tr>
                            <tr>
                                <th>1ª</th>
                                <th>2ª</th>
                                <th>3ª</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Português</td>
                                <td>8.5</td>
                                <td>9.0</td>
                                <td>8.0</td>
                                <td class="grade-average good">8.5</td>
                                <td>2</td>
                                <td><span class="status-badge approved">Aprovado</span></td>
                            </tr>
                            <tr>
                                <td>Matemática</td>
                                <td>9.0</td>
                                <td>8.5</td>
                                <td>9.5</td>
                                <td class="grade-average excellent">9.0</td>
                                <td>1</td>
                                <td><span class="status-badge approved">Aprovado</span></td>
                            </tr>
                            <tr>
                                <td>História</td>
                                <td>7.5</td>
                                <td>8.0</td>
                                <td>7.0</td>
                                <td class="grade-average good">7.5</td>
                                <td>0</td>
                                <td><span class="status-badge approved">Aprovado</span></td>
                            </tr>
                            <tr>
                                <td>Geografia</td>
                                <td>8.0</td>
                                <td>8.5</td>
                                <td>9.0</td>
                                <td class="grade-average good">8.5</td>
                                <td>1</td>
                                <td><span class="status-badge approved">Aprovado</span></td>
                            </tr>
                            <tr>
                                <td>Ciências</td>
                                <td>9.5</td>
                                <td>9.0</td>
                                <td>8.5</td>
                                <td class="grade-average excellent">9.0</td>
                                <td>0</td>
                                <td><span class="status-badge approved">Aprovado</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bulletin-summary">
                    <div class="summary-item">
                        <span>Média Geral:</span>
                        <strong>8.7</strong>
                    </div>
                    <div class="summary-item">
                        <span>Frequência:</span>
                        <strong>96%</strong>
                    </div>
                    <div class="summary-item">
                        <span>Situação Final:</span>
                        <strong class="status-approved">Aprovado</strong>
                    </div>
                    <div class="summary-item">
                        <span>Posição na Turma:</span>
                        <strong>5º de 30 alunos</strong>
                    </div>
                </div>

                <div class="observations-section">
                    <h3>Observações</h3>
                    <div class="observation-item">
                        <strong>Comportamento:</strong>
                        <p>O aluno demonstra excelente comportamento em sala de aula, sendo respeitoso com colegas e professores.</p>
                    </div>
                    <div class="observation-item">
                        <strong>Desempenho:</strong>
                        <p>Bom desempenho geral, com destaque para Matemática e Ciências. Pode melhorar em História.</p>
                    </div>
                </div>

                <div class="signatures-section">
                    <div class="signature-item">
                        <div class="signature-line"></div>
                        <p>Professor Responsável</p>
                    </div>
                    <div class="signature-item">
                        <div class="signature-line"></div>
                        <p>Director de Turma</p>
                    </div>
                </div>

                <div class="issue-date">
                    Luanda, <?php echo $dataAtual; ?>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-outline" id="printBulletin">
                <span class="material-symbols-outlined">print</span>
                Imprimir
            </button>
            <button class="btn-primary" id="downloadModalBulletin">
                <span class="material-symbols-outlined">download</span>
                Baixar PDF
            </button>
        </div>
    </div>
</div>


    <!-- Download Progress Modal -->
    <div class="modal" id="downloadProgressModal">
        <div class="modal-content progress-modal">
            <div class="progress-content">
                <div class="progress-icon">
                    <span class="material-symbols-outlined rotating">download</span>
                </div>
                <h3>Preparando o boletim para download</h3>
                <p>Por favor aguarde enquanto geramos o PDF...</p>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Notification -->
    <div class="download-notification" id="downloadNotification">
        <div class="notification-content">
            <span class="material-symbols-outlined">check_circle</span>
            <div>
                <h4>Download concluído com sucesso!</h4>
                <p>O boletim foi baixado para a sua pasta de downloads.</p>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById('bulletinModal');
        const closeModal = document.querySelector('.modal-close');
        const viewButtons = document.querySelectorAll('.view-bulletin');
        const downloadButtons = document.querySelectorAll('.download-bulletin');
        const downloadModalBulletin = document.getElementById('downloadModalBulletin');
        const printBulletin = document.getElementById('printBulletin');
        const downloadProgressModal = document.getElementById('downloadProgressModal');
        const downloadNotification = document.getElementById('downloadNotification');
        const progressFill = document.getElementById('progressFill');

        // Open modal when view button is clicked
        viewButtons.forEach(button => {
            button.addEventListener('click', () => {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        });

        // Close modal
        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

        // Print bulletin
        printBulletin.addEventListener('click', () => {
            window.print();
        });

        // Download bulletin - both from card and modal
        function downloadBulletin() {
            // Show progress modal
            downloadProgressModal.style.display = 'flex';
            
            // Simulate progress
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                progressFill.style.width = `${progress}%`;
                
                if (progress >= 100) {
                    clearInterval(interval);
                    downloadProgressModal.style.display = 'none';
                    progressFill.style.width = '0%';
                    
                    // Show download notification
                    downloadNotification.classList.add('show');
                    setTimeout(() => {
                        downloadNotification.classList.remove('show');
                    }, 3000);
                    
                    // In a real application, this would trigger the actual PDF download
                    // For demo purposes, we'll simulate it
                    console.log('Downloading bulletin PDF...');
                    
                    // In a real implementation, you would:
                    // 1. Generate the PDF on the server
                    // 2. Return the PDF file for download
                    // Example:
                    // window.location.href = 'generate_pdf.php?student=joao&period=2025-2';
                }
            }, 200);
        }

        // Attach download function to all download buttons
        downloadButtons.forEach(button => {
            button.addEventListener('click', downloadBulletin);
        });

        downloadModalBulletin.addEventListener('click', downloadBulletin);
    </script>
</body>
</html>
=======
            
            .bulletin-summary {
                grid-template-columns: 1fr;
            }
            
            .signatures-section {
                flex-direction: column;
                gap: 30px;
            }
            
            .download-option {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .option-info {
                margin-right: 0;
            }
        }
    </style>
</body>
</html>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
