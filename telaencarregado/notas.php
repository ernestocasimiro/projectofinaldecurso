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
    <title>Notas - Dashboard Encarregados</title>
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
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar disciplina...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Notas</h1>
                    <div class="header-actions">
                        <button class="btn-outline">
                            <span class="material-symbols-outlined">download</span>
                            Exportar Boletim
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-container">
                    <div class="filter-group">
                        <label>Filho:</label>
                        <select class="filter-select" id="studentFilter">
                            <option value="all">Todos</option>
                            <option value="joao">Steeve Salvador</option>
                            <option value="ana">Kelton Gonçalves</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Período:</label>
                        <select class="filter-select">
                            <option>2º Trimestre 2025</option>
                            <option>1º Trimestre 2025</option>
                            <option>3º Trimestre 2024</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Disciplina:</label>
                        <select class="filter-select">
                            <option>Todas</option>
                            <option>Matemática</option>
                            <option>Português</option>
                            <option>História</option>
                            <option>Geografia</option>
                        </select>
                    </div>
                </div>

                <!-- Steeve Salvador Grades -->
                <div class="student-grades" id="joao-grades">
                    <div class="student-header">
                        <div class="student-info">
                            <div>
                                <h2>Steeve Salvador</h2>
                                <p>9º Ano A - Ensino Fundamental</p>
                                <span class="average-badge good">Média Geral: 8.7</span>
                            </div>
                        </div>
                    </div>

                    <div class="grades-table-container">
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Disciplina</th>
                                    <th>Professor</th>
                                    <th>1ª Avaliação</th>
                                    <th>2ª Avaliação</th>
                                    <th>Trabalhos</th>
                                    <th>Média</th>
                                    <th>Situação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">calculate</span>
                                            <span>Matemática</span>
                                        </div>
                                    </td>
                                    <td>Prof. Carlos Silva</td>
                                    <td><span class="grade excellent">9.0</span></td>
                                    <td><span class="grade good">8.5</span></td>
                                    <td><span class="grade excellent">9.2</span></td>
                                    <td><span class="grade excellent">8.9</span></td>
                                    <td><span class="status-badge active">Aprovado</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">menu_book</span>
                                            <span>Português</span>
                                        </div>
                                    </td>
                                    <td>Prof. Ana Maria</td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade excellent">9.0</span></td>
                                    <td><span class="grade good">8.5</span></td>
                                    <td><span class="grade good">8.5</span></td>
                                    <td><span class="status-badge active">Aprovado</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">science</span>
                                            <span>Física</span>
                                        </div>
                                    </td>
                                    <td>Prof. Roberto Lima</td>
                                    <td><span class="grade average">7.0</span></td>
                                    <td><span class="grade average">7.5</span></td>
                                    <td><span class="grade average">7.0</span></td>
                                    <td><span class="grade average">7.2</span></td>
                                    <td><span class="status-badge warning">Recuperação</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">history_edu</span>
                                            <span>História</span>
                                        </div>
                                    </td>
                                    <td>Prof. Lucia Santos</td>
                                    <td><span class="grade good">8.5</span></td>
                                    <td><span class="grade excellent">9.0</span></td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade good">8.5</span></td>
                                    <td><span class="status-badge active">Aprovado</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Kelton Gonçalves Grades -->
                <div class="student-grades" id="ana-grades">
                    <div class="student-header">
                        <div class="student-info">
                            <div>
                                <h2>Kelton Gonçalves</h2>
                                <p>6º Ano B - Ensino Fundamental</p>
                                <span class="average-badge good">Média Geral: 8.3</span>
                            </div>
                        </div>
                    </div>

                    <div class="grades-table-container">
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Disciplina</th>
                                    <th>Professor</th>
                                    <th>1ª Avaliação</th>
                                    <th>2ª Avaliação</th>
                                    <th>Trabalhos</th>
                                    <th>Média</th>
                                    <th>Situação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">calculate</span>
                                            <span>Matemática</span>
                                        </div>
                                    </td>
                                    <td>Prof. Pedro Costa</td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade average">7.5</span></td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade average">7.8</span></td>
                                    <td><span class="status-badge active">Aprovado</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">menu_book</span>
                                            <span>Português</span>
                                        </div>
                                    </td>
                                    <td>Prof. Fernanda Silva</td>
                                    <td><span class="grade excellent">9.0</span></td>
                                    <td><span class="grade excellent">9.5</span></td>
                                    <td><span class="grade excellent">9.0</span></td>
                                    <td><span class="grade excellent">9.2</span></td>
                                    <td><span class="status-badge active">Aprovado</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">language</span>
                                            <span>Inglês</span>
                                        </div>
                                    </td>
                                    <td>Prof. Michael Johnson</td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="status-badge active">Aprovado</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Performance Chart -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>Evolução das Notas</h2>
                        <div class="chart-filters">
                            <button class="btn-outline active">Trimestre</button>
                            <button class="btn-outline">Ano</button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div class="chart-placeholder">
                            <span class="material-symbols-outlined">trending_up</span>
                            <p>Gráfico de evolução das notas por trimestre</p>
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

        // Filter functionality
        document.getElementById('studentFilter').addEventListener('change', function() {
            const value = this.value;
            const joaoGrades = document.getElementById('joao-grades');
            const anaGrades = document.getElementById('ana-grades');
            
            if (value === 'all') {
                joaoGrades.style.display = 'block';
                anaGrades.style.display = 'block';
            } else if (value === 'joao') {
                joaoGrades.style.display = 'block';
                anaGrades.style.display = 'none';
            } else if (value === 'ana') {
                joaoGrades.style.display = 'none';
                anaGrades.style.display = 'block';
            }
        });
    </script>
</body>
</html>