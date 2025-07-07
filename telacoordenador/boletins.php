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
    <title>Impressão de Boletins - Dashboard de Professores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="boletins.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<<<<<<< HEAD
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .boletim-document, .boletim-document * {
                visibility: visible;
            }
            .boletim-document {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
                transform: none !important;
            }
        }
        
        .grades-table table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .grades-table th, .grades-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            background-color: #f9f9ff;
        }

        .grades-table thead th {
            background-color: #f0f4ff;
            font-weight: bold;
        }

        .grades-table th[rowspan] {
            vertical-align: middle;
        }

        .grades-table tbody td:first-child {
            text-align: left;
            font-weight: 500;
        }

        .grades-table tbody tr:hover {
            background-color: #f1f7ff;
        }
        
        /* Estilos específicos para o boletim */
        .boletim-document {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            transform-origin: top center;
            transition: transform 0.2s;
        }
        
        .boletim-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .school-logo img {
            height: 80px;
            margin-right: 20px;
        }
        
        .school-info h2 {
            margin: 0;
            font-size: 22px;
            color: #333;
        }
        
        .school-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
        
        .boletim-title {
            text-align: center;
            margin: 20px 0;
        }
        
        .boletim-title h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
            text-transform: uppercase;
        }
        
        .boletim-title h3 {
            margin: 5px 0 0;
            font-size: 18px;
            color: #555;
        }
        
        .student-info {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .info-group {
            flex: 1;
            margin-right: 20px;
        }
        
        .info-group label {
            font-weight: bold;
            margin-right: 5px;
        }
        
        .teacher-comments {
            margin-top: 30px;
        }
        
        .teacher-comments h4 {
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .comment-box {
            border: 1px solid #ddd;
            padding: 15px;
            min-height: 80px;
            background: #f9f9f9;
        }
        
        .boletim-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #333;
        }
        
        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .signature {
            width: 200px;
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            height: 1px;
            margin-bottom: 5px;
        }
        
        .signature p {
            margin: 0;
            font-size: 12px;
        }
        
        .date-area {
            text-align: right;
            font-style: italic;
        }
        
        .preview-content {
            width: 100%;
            overflow: auto;
            display: flex;
            justify-content: center;
            padding: 20px 0;
            background: #f5f5f5;
        }
    </style>
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
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
                    <h3><?php echo htmlspecialchars($coordinator['fname'] . ' ' . $coordinator['lname']); ?></h3>
                    <p>Coordenador/a</p>
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
<<<<<<< HEAD
                    <li >
                        <a href="professores.php">
                            <span class="material-symbols-outlined">group</span>
                            <span class="menu-text">Professores</span>
                        </a>
                    </li>
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
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
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar alunos...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Impressão de Boletins</h1>
                    <button class="btn-primary" id="printButton">
                        <span class="material-symbols-outlined">print</span>
                        Imprimir Boletins
                    </button>
                </div>
                
                <div class="boletins-container">
                    <div class="boletins-config">
                        <div class="config-card">
                            <h3>Configurações do Boletim</h3>
                            <div class="filter-container">
                                <div class="filter-group">
                                    <label for="ano-letivo">Ano Letivo:</label>
                                    <select id="ano-letivo" class="filter-select">
                                        <option value="2025" selected data-dynamic="school-year">2025</option>
                                        <option value="2024">2024</option>
                                        <option value="2023">2023</option>
                                    </select>
                                </div>
                                <div class="filter-group">
                                    <label for="periodo">Período:</label>
                                    <select id="periodo" class="filter-select">
                                        <option value="1" selected data-dynamic="current-period">1º trimestre</option>
                                        <option value="2">1º trimestre</option>
                                        <option value="3">2º trimestre</option>
                                        <option value="4">3º trimestre</option>
                                        <option value="anual">Anual</option>
                                    </select>
                                </div>
                                <div class="filter-group">
                                    <label for="turma">Turma:</label>
                                    <select id="turma" class="filter-select">
                                        <option value="todos">Todas as Turmas</option>
                                        <option value="9A" selected>9º Ano A</option>
                                        <option value="8B">8º Ano B</option>
                                        <option value="10C">10º Ano C</option>
                                        <option value="7D">7º Ano D</option>
                                    </select>
                                </div>
                            </div>

                            <h4>Opções de Impressão</h4>
                            <div class="print-options">
                                <div class="option-group">
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="incluir-notas" class="toggle-input" checked>
                                        <label for="incluir-notas" class="toggle-label"></label>
                                        <span>Incluir notas</span>
                                    </div>
                                </div>
                                <div class="option-group">
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="incluir-frequencia" class="toggle-input" checked>
                                        <label for="incluir-frequencia" class="toggle-label"></label>
                                        <span>Incluir frequência</span>
                                    </div>
                                </div>
                                <div class="option-group">
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="incluir-comentarios" class="toggle-input" checked>
                                        <label for="incluir-comentarios" class="toggle-label"></label>
                                        <span>Incluir comentários dos professores</span>
                                    </div>
                                </div>
                                <div class="option-group">
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="incluir-media-turma" class="toggle-input">
                                        <label for="incluir-media-turma" class="toggle-label"></label>
                                        <span>Incluir média da turma</span>
                                    </div>
                                </div>
                                <div class="option-group">
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="incluir-grafico" class="toggle-input" checked>
                                        <label for="incluir-grafico" class="toggle-label"></label>
                                        <span>Incluir gráfico de desempenho</span>
                                    </div>
                                </div>
                            </div>

                            <h4>Alunos</h4>
                            <div class="alunos-selection">
                                <div class="selection-header">
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="selecionar-todos" class="toggle-input" checked>
                                        <label for="selecionar-todos" class="toggle-label"></label>
<<<<<<< HEAD
                                        <span>Selecionar todos</span>
                                    </div>
                                    <button class="btn-text" id="limparSelecao">Limpar seleção</button>
=======
                                        <span>Selecionar todos</span> </label>
                                        
                                    </div>
                                    <button class="btn-text">Limpar seleção</button>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                                </div>
                                <div class="alunos-list" id="alunos-list">
                                    <!-- Alunos serão carregados dinamicamente -->
                                </div>
                            </div>

                            <div class="config-actions">
                                <button class="btn-outline" id="visualizarBtn">
                                    <span class="material-symbols-outlined">visibility</span>
                                    Visualizar
                                </button>
                                <button class="btn-outline" id="exportarBtn">
                                    <span class="material-symbols-outlined">download</span>
                                    Exportar PDF
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="boletim-preview">
                        <div class="preview-header">
                            <h3>Visualização do Boletim</h3>
                            <div class="preview-actions">
                                <button class="btn-text" id="zoomInBtn">
                                    <span class="material-symbols-outlined">zoom_in</span>
                                </button>
                                <button class="btn-text" id="zoomOutBtn">
                                    <span class="material-symbols-outlined">zoom_out</span>
                                </button>
                                <button class="btn-text" id="fullscreenBtn">
                                    <span class="material-symbols-outlined">fullscreen</span>
                                </button>
                            </div>
                        </div>
                        <div class="preview-content">
                            <div class="boletim-document" id="boletim-document">
                                <div class="boletim-header">
                                    <div class="school-logo">
                                        <img src="https://via.placeholder.com/80" alt="Logo da Escola">
                                    </div>
                                    <div class="school-info">
                                        <h2>Colégio pitruca</h2>
                                        <p>CAMAMA</p>
                                        <p>Tel: (244) 912345678</p>
                                    </div>
                                </div>
                                <div class="boletim-title">
                                    <h1>BOLETIM ESCOLAR</h1>
                                    <h3>1º trimestre 2025</h3>
                                </div>
                                <div class="student-info">
                                    <div class="info-row">
                                        <div class="info-group">
                                            <label>Aluno(a):</label>
                                            <span data-dynamic="report-student-name">Ana Silva</span>
                                        </div>
                                        <div class="info-group">
                                            <label>Matrícula:</label>
                                            <span>2023001</span>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-group">
                                            <label>Turma:</label>
                                            <span data-dynamic="report-student-class">9º Ano A</span>
                                        </div>
                                        <div class="info-group">
                                            <label>Período:</label>
                                            <span>Manha</span>
                                        </div>
                                        <div class="info-group">
                                            <label>Ano Letivo:</label>
                                            <span data-dynamic="report-year">2025</span>
                                        </div>
                                    </div>
                                </div>
<<<<<<< HEAD
                                <div class="grades-table">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th rowspan="2">Disciplina</th>
                                                <th colspan="3">Avaliações</th>
                                                <th rowspan="2">Média</th>
                                                <th rowspan="2">Frequência</th>
                                                <th rowspan="2">Situação</th>
                                            </tr>
                                            <tr>
                                                <th>AV1</th>
                                                <th>AV2</th>
                                                <th>AV3</th>
                                            </tr>
                                        </thead>
                                        <tbody id="grades-table-body">
                                            <tr>
                                                <td>Matemática</td>
                                                <td>15</td>
                                                <td>14</td>
                                                <td>17</td>
                                                <td>15.3</td>
                                                <td>96%</td>
                                                <td>Aprovado</td>
                                            </tr>
                                            <tr>
                                                <td>Português</td>
                                                <td>13</td>
                                                <td>12</td>
                                                <td>14</td>
                                                <td>13.0</td>
                                                <td>92%</td>
                                                <td>Aprovado</td>
                                            </tr>
                                            <tr>
                                                <td>História</td>
                                                <td>10</td>
                                                <td>11</td>
                                                <td>9</td>
                                                <td>10.0</td>
                                                <td>85%</td>
                                                <td>Recuperação</td>
                                            </tr>
                                            <tr>
                                                <td>Inglês</td>
                                                <td>8</td>
                                                <td>7</td>
                                                <td>6</td>
                                                <td>7.0</td>
                                                <td>78%</td>
                                                <td>Reprovado</td>
                                            </tr>
                                            <tr>
                                                <td>Geografia</td>
                                                <td>16</td>
                                                <td>15</td>
                                                <td>14</td>
                                                <td>15.0</td>
                                                <td>99%</td>
                                                <td>Aprovado</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
=======
                                  <style>
    .grades-table table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 14px;
    }

    .grades-table th, .grades-table td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: center;
        background-color: #f9f9ff;
    }

    .grades-table thead th {
        background-color: #f0f4ff;
        font-weight: bold;
    }

    .grades-table th[rowspan] {
        vertical-align: middle;
    }

    .grades-table tbody td:first-child {
        text-align: left;
        font-weight: 500;
    }

    .grades-table tbody tr:hover {
        background-color: #f1f7ff;
    }
</style>

<div class="grades-table">
    <table>
        <thead>
            <tr>
                <th rowspan="2">Disciplina</th>
                <th colspan="3">Avaliações</th>
                <th rowspan="2">Média</th>
                <th rowspan="2">Frequência</th>
                <th rowspan="2">Situação</th>
            </tr>
            <tr>
                <th>AV1</th>
                <th>AV2</th>
                <th>AV3</th>
            </tr>
        </thead>
        <tbody id="grades-table-body">
            <tr>
                <td>Matemática</td>
                <td>15</td>
                <td>14</td>
                <td>17</td>
                <td>15.3</td>
                <td>96%</td>
                <td>Aprovado</td>
            </tr>
            <tr>
                <td>Português</td>
                <td>13</td>
                <td>12</td>
                <td>14</td>
                <td>13.0</td>
                <td>92%</td>
                <td>Aprovado</td>
            </tr>
            <tr>
                <td>História</td>
                <td>10</td>
                <td>11</td>
                <td>9</td>
                <td>10.0</td>
                <td>85%</td>
                <td>Recuperação</td>
            </tr>
            <tr>
                <td>Inglês</td>
                <td>8</td>
                <td>7</td>
                <td>6</td>
                <td>7.0</td>
                <td>78%</td>
                <td>Reprovado</td>
            </tr>
            <tr>
                <td>Geografia</td>
                <td>16</td>
                <td>15</td>
                <td>14</td>
                <td>15.0</td>
                <td>99%</td>
                <td>Aprovado</td>
            </tr>
        </tbody>
    </table>
</div>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31

                                <div class="teacher-comments">
                                    <h4>Comentários dos Professores</h4>
                                    <div class="comment-box">
                                        <p data-dynamic="report-comments">A aluna Ana Silva demonstra excelente desempenho em Matemática e Ciências. Recomenda-se atenção especial à disciplina de Inglês, onde apresenta dificuldades. No geral, é uma aluna dedicada e participativa.</p>
                                    </div>
                                </div>
                                <div class="boletim-footer">
                                    <div class="signature-area">
                                        <div class="signature">
                                            <div class="signature-line"></div>
                                            <p>Assinatura do(a) Diretor(a)</p>
                                        </div>
                                        <div class="signature">
                                            <div class="signature-line"></div>
                                            <p>Assinatura do(a) Responsável</p>
                                        </div>
                                    </div>
                                    <div class="date-area">
                                        <p>luanda, <span data-dynamic="report-date" data-format="medium">15 de Abril de 2025</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="dashboard-data.js"></script>
    <script>
        // Toggle sidebar on mobile
<<<<<<< HEAD
        document.getElementById('menuToggle')?.addEventListener('click', function() {
=======
        document.getElementById('menuToggle').addEventListener('click', function() {
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

        // Carregar lista de alunos dinamicamente
        function loadStudentsList() {
            const alunosList = document.getElementById('alunos-list');
            if (!alunosList) return;
            
            // Limpar a lista
            alunosList.innerHTML = '';
            
            // Obter a turma selecionada
            const turmaSelect = document.getElementById('turma');
            const turmaId = turmaSelect.value;
            
            // Filtrar alunos pela turma selecionada
<<<<<<< HEAD
            let filteredStudents = window.dashboardData?.students || [];
=======
            let filteredStudents = window.dashboardData.students;
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            if (turmaId !== 'todos') {
                filteredStudents = filteredStudents.filter(student => student.class === turmaId);
            }
            
            // Adicionar cada aluno à lista
            filteredStudents.forEach((student, index) => {
                const alunoItem = document.createElement('div');
                alunoItem.className = 'aluno-item';
                alunoItem.innerHTML = `
                    <div class="toggle-switch">
                        <input type="checkbox" id="aluno-${index}" class="toggle-input" checked data-student-id="${student.id}">
                        <label for="aluno-${index}" class="toggle-label"></label>
                        <div class="aluno-info">
<<<<<<< HEAD
                            <img src="${student.avatar || 'https://via.placeholder.com/40'}" alt="${student.name}">
                            <div>
                                <p>${student.name}</p>
                                <span class="text-muted">${(window.dashboardData?.classes?.find(c => c.id === student.class)?.name) || ''}</span>
=======
                            <img src="${student.avatar}" alt="${student.name}">
                            <div>
                                <p>${student.name}</p>
                                <span class="text-muted">${window.dashboardData.classes.find(c => c.id === student.class)?.name || ''}</span>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                            </div>
                        </div>
                    </div>
                `;
                alunosList.appendChild(alunoItem);
            });
        }

        // Carregar dados do boletim para um aluno específico
        function loadStudentReportCard(studentId) {
<<<<<<< HEAD
            const student = window.dashboardData?.students?.find(s => s.id === studentId);
=======
            const student = window.dashboardData.students.find(s => s.id === studentId);
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            if (!student) return;
            
            // Atualizar informações do aluno no boletim
            const studentNameElement = document.querySelector('[data-dynamic="report-student-name"]');
            if (studentNameElement) studentNameElement.textContent = student.name;
            
            const studentClassElement = document.querySelector('[data-dynamic="report-student-class"]');
            if (studentClassElement) {
<<<<<<< HEAD
                const classInfo = window.dashboardData?.classes?.find(c => c.id === student.class);
=======
                const classInfo = window.dashboardData.classes.find(c => c.id === student.class);
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                studentClassElement.textContent = classInfo ? classInfo.name : '';
            }
            
            // Atualizar comentários no boletim
            const commentsElement = document.querySelector('[data-dynamic="report-comments"]');
<<<<<<< HEAD
            if (commentsElement) commentsElement.textContent = student.comments || 'Sem comentários disponíveis.';
            
            // Atualizar tabela de notas (se a função existir)
            if (window.dashboardData?.updateGradesTable) {
                window.dashboardData.updateGradesTable(student);
            }
            
            // Atualizar gráfico de desempenho (se a função existir)
            if (window.dashboardData?.updatePerformanceChart) {
                window.dashboardData.updatePerformanceChart(student);
            }
        }

        // Selecionar todos os alunos
        document.getElementById('selecionar-todos')?.addEventListener('change', function() {
=======
            if (commentsElement) commentsElement.textContent = student.comments;
            
            // Atualizar tabela de notas
            window.dashboardData.updateGradesTable(student);
            
            // Atualizar gráfico de desempenho
            window.dashboardData.updatePerformanceChart(student);
        }

        // Selecionar todos os alunos
        document.getElementById('selecionar-todos').addEventListener('change', function() {
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            const checkboxes = document.querySelectorAll('.alunos-list .toggle-input');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

<<<<<<< HEAD
        // Botão de impressão - corrigido para imprimir apenas o boletim
        document.getElementById('printButton')?.addEventListener('click', function() {
            // Criar um clone do boletim
            const boletimOriginal = document.getElementById('boletim-document');
            const boletimClone = boletimOriginal.cloneNode(true);
            
            // Criar uma janela de impressão
            const printWindow = window.open('', '_blank');
            printWindow.document.open();
            
            // Adicionar estilos específicos para impressão
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Boletim Escolar</title>
                    <style>
                        @page { 
                            size: A4; 
                            margin: 15mm 15mm 15mm 15mm;
                        }
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 0;
                            padding: 0;
                        }
                        .boletim-document { 
                            width: 100%; 
                            margin: 0; 
                            padding: 0; 
                        }
                        .boletim-header {
                            display: flex;
                            align-items: center;
                            margin-bottom: 20px;
                            border-bottom: 2px solid #333;
                            padding-bottom: 15px;
                        }
                        .school-logo img {
                            height: 80px;
                            margin-right: 20px;
                        }
                        .school-info h2 {
                            margin: 0;
                            font-size: 22px;
                            color: #333;
                        }
                        .school-info p {
                            margin: 5px 0;
                            font-size: 14px;
                            color: #555;
                        }
                        .boletim-title {
                            text-align: center;
                            margin: 20px 0;
                        }
                        .boletim-title h1 {
                            margin: 0;
                            font-size: 24px;
                            color: #333;
                            text-transform: uppercase;
                        }
                        .boletim-title h3 {
                            margin: 5px 0 0;
                            font-size: 18px;
                            color: #555;
                        }
                        .student-info {
                            margin-bottom: 20px;
                        }
                        .info-row {
                            display: flex;
                            margin-bottom: 10px;
                        }
                        .info-group {
                            flex: 1;
                            margin-right: 20px;
                        }
                        .info-group label {
                            font-weight: bold;
                            margin-right: 5px;
                        }
                        .grades-table {
                            width: 100%;
                            margin: 20px 0;
                        }
                        .grades-table table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        .grades-table th, .grades-table td {
                            border: 1px solid #000;
                            padding: 8px;
                            text-align: center;
                        }
                        .grades-table thead th {
                            background-color: #f0f0f0;
                            font-weight: bold;
                        }
                        .grades-table tbody td:first-child {
                            text-align: left;
                            font-weight: 500;
                        }
                        .teacher-comments {
                            margin-top: 30px;
                        }
                        .teacher-comments h4 {
                            margin-bottom: 10px;
                            font-size: 16px;
                        }
                        .comment-box {
                            border: 1px solid #ddd;
                            padding: 15px;
                            min-height: 80px;
                            background: #f9f9f9;
                        }
                        .boletim-footer {
                            margin-top: 40px;
                            padding-top: 20px;
                            border-top: 2px solid #333;
                        }
                        .signature-area {
                            display: flex;
                            justify-content: space-between;
                            margin-bottom: 30px;
                        }
                        .signature {
                            width: 200px;
                            text-align: center;
                        }
                        .signature-line {
                            border-bottom: 1px solid #333;
                            height: 1px;
                            margin-bottom: 5px;
                        }
                        .signature p {
                            margin: 0;
                            font-size: 12px;
                        }
                        .date-area {
                            text-align: right;
                            font-style: italic;
                        }
                    </style>
                </head>
                <body>
            `);
            
            // Adicionar o conteúdo do boletim
            printWindow.document.write(boletimClone.outerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            
            // Esperar o conteúdo carregar e então imprimir
            printWindow.onload = function() {
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            };
        });

        // Limpar seleção de alunos
        document.getElementById('limparSelecao')?.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.alunos-list .toggle-input');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('selecionar-todos').checked = false;
        });

        // Atualizar turma e recarregar lista de alunos
        document.getElementById('turma')?.addEventListener('change', function() {
=======
        // Botão de impressão
        document.getElementById('printButton').addEventListener('click', function() {
            window.print();
        });

        // Atualizar turma e recarregar lista de alunos
        document.getElementById('turma').addEventListener('change', function() {
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            loadStudentsList();
        });

        // Botão de visualização
<<<<<<< HEAD
        document.getElementById('visualizarBtn')?.addEventListener('click', function() {
=======
        document.getElementById('visualizarBtn').addEventListener('click', function() {
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            // Obter o primeiro aluno selecionado
            const selectedCheckbox = document.querySelector('.alunos-list .toggle-input:checked');
            if (selectedCheckbox) {
                const studentId = selectedCheckbox.getAttribute('data-student-id');
                loadStudentReportCard(studentId);
<<<<<<< HEAD
            } else {
                alert('Por favor, selecione pelo menos um aluno para visualizar.');
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            }
        });

        // Zoom in/out
        let currentZoom = 100;
<<<<<<< HEAD
        document.getElementById('zoomInBtn')?.addEventListener('click', function() {
=======
        document.getElementById('zoomInBtn').addEventListener('click', function() {
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            if (currentZoom < 150) {
                currentZoom += 10;
                document.getElementById('boletim-document').style.transform = `scale(${currentZoom / 100})`;
                document.getElementById('boletim-document').style.transformOrigin = 'top center';
            }
        });

<<<<<<< HEAD
        document.getElementById('zoomOutBtn')?.addEventListener('click', function() {
=======
        document.getElementById('zoomOutBtn').addEventListener('click', function() {
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            if (currentZoom > 50) {
                currentZoom -= 10;
                document.getElementById('boletim-document').style.transform = `scale(${currentZoom / 100})`;
                document.getElementById('boletim-document').style.transformOrigin = 'top center';
            }
        });

        // Tela cheia
<<<<<<< HEAD
        document.getElementById('fullscreenBtn')?.addEventListener('click', function() {
=======
        document.getElementById('fullscreenBtn').addEventListener('click', function() {
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            const previewContent = document.querySelector('.preview-content');
            if (previewContent.requestFullscreen) {
                previewContent.requestFullscreen();
            } else if (previewContent.webkitRequestFullscreen) {
                previewContent.webkitRequestFullscreen();
            } else if (previewContent.msRequestFullscreen) {
                previewContent.msRequestFullscreen();
            }
        });

        // Inicializar a página
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar dados dinâmicos
<<<<<<< HEAD
            if (window.dashboardData?.updateDynamicDates) {
                window.dashboardData.updateDynamicDates();
            }
            
            if (window.dashboardData?.loadCurrentUserData) {
                window.dashboardData.loadCurrentUserData();
            }
=======
            window.dashboardData.updateDynamicDates();
            window.dashboardData.loadCurrentUserData();
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            
            // Carregar lista de alunos
            loadStudentsList();
            
<<<<<<< HEAD
            // Carregar boletim do primeiro aluno (se existir)
            if (window.dashboardData?.students?.length > 0) {
=======
            // Carregar boletim do primeiro aluno
            if (window.dashboardData.students.length > 0) {
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                loadStudentReportCard(window.dashboardData.students[0].id);
            }
        });
    </script>
    
</body>
</html>