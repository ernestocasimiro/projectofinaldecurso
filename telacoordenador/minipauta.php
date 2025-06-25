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

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minipautas - Dashboard de Professores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        .minipauta-container {
            display: flex;
            gap: 1.5rem;
        }
        
        .minipauta-config {
            width: 300px;
            flex-shrink: 0;
        }
        
        .minipauta-preview {
            flex: 1;
        }
        
        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .preview-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .preview-content {
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            min-height: 600px;
        }
        
        .minipauta-document {
            width: 100%;
            padding: 1rem;
            transition: transform 0.3s;
        }
        
        .minipauta-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .school-logo {
            width: 80px;
            height: 80px;
        }
        
        .school-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .school-info {
            text-align: center;
            flex: 1;
        }
        
        .school-info h2 {
            margin: 0 0 0.5rem;
            font-size: 1.5rem;
        }
        
        .school-info p {
            margin: 0.25rem 0;
            font-size: 0.9rem;
        }
        
        .minipauta-title {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .minipauta-title h1 {
            margin: 0 0 0.5rem;
            font-size: 1.5rem;
            text-transform: uppercase;
        }
        
        .minipauta-title h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .class-info {
            margin-bottom: 1.5rem;
        }
        
        .info-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .info-group {
            display: flex;
            gap: 0.5rem;
        }
        
        .info-group label {
            font-weight: 600;
        }
        
        .minipauta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .minipauta-table th, .minipauta-table td {
            border: 1px solid var(--border-color);
            padding: 0.5rem;
            text-align: center;
        }
        
        .minipauta-table th {
            background-color: #f5f5f5;
            font-weight: 600;
        }
        
        .minipauta-table .student-name {
            text-align: left;
            white-space: nowrap;
        }
        
        .minipauta-table .grade-cell {
            width: 50px;
        }
        
        .minipauta-table .attendance-cell {
            width: 60px;
        }
        
        .minipauta-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            font-size: 0.9rem;
        }
        
        .signature-area {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 3rem;
        }
        
        .signature {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-bottom: 0.5rem;
        }
        
        .date-area {
            text-align: right;
            margin-top: 1rem;
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            
            .minipauta-document, .minipauta-document * {
                visibility: visible;
            }
            
            .minipauta-document {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                padding: 0;
                margin: 0;
            }
        }
        
        @media (max-width: 992px) {
            .minipauta-container {
                flex-direction: column;
            }
            
            .minipauta-config {
                width: 100%;
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
                    <li>
                        <a href="boletins.php">
                            <span class="material-symbols-outlined">description</span>
                            <span class="menu-text">Boletins</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="minipauta.php">
                            <span class="material-symbols-outlined">summarize</span>
                            <span class="menu-text">Minipautas</span>
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
                    <h1>Minipautas</h1>
                    <button class="btn-primary" id="printButton">
                        <span class="material-symbols-outlined">print</span>
                        Imprimir Minipauta
                    </button>
                </div>
                
                 <div class="minipauta-container">
                    <div class="minipauta-config">
                        <div class="card">
                            <div class="card-header">
                                <h3>Configurações da Minipauta</h3>
                            </div>
                            <div class="card-content">
                                <div class="filter-group">
                                    <label for="ano-letivo">Ano Letivo:</label>
                                    <select id="ano-letivo" class="filter-select">
                                        <option value="2025" selected data-dynamic="school-year">2025</option>
                                        <option value="2024">2024</option>
                                        <option value="2023">2023</option>
                                    </select>
                                </div><br>
                                <div class="filter-group">
                                    <label for="periodo">Período:</label>
                                    <select id="periodo" class="filter-select">
                                        <option value="1" selected data-dynamic="current-period">1º trimestre</option>
                                        <option value="2">2º trimestre</option>
                                        <option value="3">3º trimestre</option>
                                        <option value="anual">Anual</option>
                                    </select>
                                </div><br>
                                <div class="filter-group">
                                    <label for="turma">Turma:</label>
                                    <select id="turma" class="filter-select">
                                        <option value="9A" selected>9º Ano A</option>
                                        <option value="8B">8º Ano B</option>
                                        <option value="10C">10º Ano C</option>
                                        <option value="7D">7º Ano D</option>
                                    </select>
                                </div><br>
                                <div class="filter-group">
                                    <label for="disciplina">Disciplina:</label>
                                    <select id="disciplina" class="filter-select">
                                        <option value="math" selected>Matemática</option>
                                        <option value="portuguese">Português</option>
                                        <option value="history">História</option>
                                        <option value="geography">Geografia</option>
                                        <option value="science">Ciências</option>
                                        <option value="pe">Educação Física</option>
                                        <option value="english">Inglês</option>
                                    </select>
                                </div><br>

                                <h4>Opções de Exibição</h4><br>
                                <div class="option-group">
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="incluir-notas" class="toggle-input" checked>
                                        <label for="incluir-notas" class="toggle-label"></label>
                                        <span>Incluir notas</span>
                                    </div>
                                </div><br>
                                <div class="option-group">
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="incluir-frequencia" class="toggle-input" checked>
                                        <label for="incluir-frequencia" class="toggle-label"></label>
                                        <span>Incluir frequência</span>
                                    </div>
                                </div><br>
                                <div class="option-group">
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="incluir-media" class="toggle-input" checked>
                                        <label for="incluir-media" class="toggle-label"></label>
                                        <span>Incluir média</span>
                                    </div>
                                </div><br>
                                <div class="option-group">
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="incluir-situacao" class="toggle-input" checked>
                                        <label for="incluir-situacao" class="toggle-label"></label>
                                        <span>Incluir situação</span>
                                    </div>
                                </div><br>

                                <div class="config-actions">
                                    <button class="btn-outline" id="visualizarBtn">
                                        <span class="material-symbols-outlined">visibility</span>
                                        Visualizar
                                    </button><br>
                                    <button class="btn-outline" id="exportarBtn">
                                        <span class="material-symbols-outlined">download</span>
                                        Exportar PDF
                                    </button><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="minipauta-preview">
                        <div class="preview-header">
                            <h3>Visualização da Minipauta</h3>
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
                            <div class="minipauta-document" id="minipauta-document">
                                <div class="minipauta-header">
                                    <div class="school-logo">
                                        <img src="img/logo.png" alt="Logo da Escola">
                                    </div>
                                    <div class="school-info">
                                        <h2>Colégio pitruca</h2>
                                        <p>CAMAMA</p>
                                        <p>Tel: (244) 912345678</p>
                                    </div>
                                </div>
                                <div class="minipauta-title">
                                    <h1>MINIPAUTA</h1>
                                    <h3><span id="disciplina-title">Matemática</span> - <span data-dynamic="current-period">1º trimestre</span> - <span data-dynamic="school-year">2025</span></h3>
                                </div>
                                <div class="class-info">
                                    <div class="info-row">
                                        <div class="info-group">
                                            <label>Turma:</label>
                                            <span id="turma-nome">9º Ano A</span>
                                        </div>
                                        <div class="info-group">
                                            <label>Professor(a):</label>
                                            <span data-dynamic="user-name">Prof. Silva</span>
                                        </div>
                                        <div class="info-group">
                                            <label>Total de Alunos:</label>
                                            <span id="total-alunos">28</span>
                                        </div>
                                    </div>
                                </div>
                                <table class="minipauta-table" id="minipauta-table">
                                    
                                <style>
                                    table {
                                        width: 100%;
                                        border-collapse: collapse;
                                        margin-top: 10px;
                                        font-family: Arial, sans-serif;
                                        font-size: 14px;
                                    }

                                    th, td {
                                        border: 1px solid #ccc;
                                        padding: 8px;
                                        text-align: center;
                                        vertical-align: middle;
                                    }

                                    th {
                                        background-color: #f2f2f2;
                                        font-weight: bold;
                                    }

                                    .aluno-nome {
                                        text-align: left;
                                    }
                                </style>

                                <!-- Tabela completa -->
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Nº</th>
                                            <th>Nome do Aluno</th>
                                            <th>AV1</th>
                                            <th>AV2</th>
                                            <th>AV3</th>
                                            <th>Média</th>
                                            <th>Freq. (%)</th>
                                            <th>Situação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td class="aluno-nome">João Pedro</td>
                                            <td>15</td>
                                            <td>14</td>
                                            <td>17</td>
                                            <td>15.3</td>
                                            <td>96%</td>
                                            <td>Aprovado</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td class="aluno-nome">Maria Silva</td>
                                            <td>18</td>
                                            <td>17</td>
                                            <td>19</td>
                                            <td>18.0</td>
                                            <td>100%</td>
                                            <td>Aprovada</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td class="aluno-nome">Carlos António</td>
                                            <td>12</td>
                                            <td>13</td>
                                            <td>14</td>
                                            <td>13.0</td>
                                            <td>89%</td>
                                            <td>Recuperação</td>
                                        </tr>
                                    </tbody>
                                </table>


                                <div class="minipauta-footer">
                                    <div class="signature-area">
                                        <div class="signature">
                                            <div class="signature-line"></div>
                                            <p>Assinatura do(a) Professor(a)</p>
                                        </div>
                                        <div class="signature">
                                            <div class="signature-line"></div>
                                            <p>Assinatura do(a) Coordenador(a)</p>
                                        </div>
                                    </div>
                                    <div class="date-area">
                                        <p>Data: <span data-dynamic="current-date" data-format="short">15/04/2025</span></p>
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
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

        // Carregar dados da minipauta
        function loadMinipautaData() {
            const turmaId = document.getElementById('turma').value;
            const disciplinaId = document.getElementById('disciplina').value;
            
            // Atualizar títulos
            document.getElementById('turma-nome').textContent = window.dashboardData.classes.find(c => c.id === turmaId)?.name || turmaId;
            document.getElementById('disciplina-title').textContent = document.getElementById('disciplina').options[document.getElementById('disciplina').selectedIndex].text;
            document.getElementById('total-alunos').textContent = window.dashboardData.classes.find(c => c.id === turmaId)?.students || 0;
            
            // Filtrar alunos pela turma selecionada
            const filteredStudents = window.dashboardData.students.filter(student => student.class === turmaId);
            
            // Limpar tabela
            const minipautaTableBody = document.getElementById('minipauta-table-body');
            minipautaTableBody.innerHTML = '';
            
            // Adicionar cada aluno
            filteredStudents.forEach((student, index) => {
                const row = document.createElement('tr');
                
                // Obter notas da disciplina selecionada
                const grades = student.grades[disciplinaId];
                
                // Determinar situação com base na média
                let situacao = 'Aprovado';
                let situacaoClass = 'approved';
                
                if (grades.average < 6.0) {
                    situacao = 'Reprovado';
                    situacaoClass = 'failed';
                } else if (grades.average < 7.0) {
                    situacao = 'Recuperação';
                    situacaoClass = 'recovery';
                }
                
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td class="student-name">${student.name}</td>
                    <td class="grade-cell">${grades.av1.toFixed(1)}</td>
                    <td class="grade-cell">${grades.av2.toFixed(1)}</td>
                    <td class="grade-cell">${grades.av3.toFixed(1)}</td>
                    <td class="grade-cell">${grades.average.toFixed(1)}</td>
                    <td class="attendance-cell">${student.attendance}%</td>
                    <td class="grade-status ${situacaoClass}">${situacao}</td>
                `;
                
                minipautaTableBody.appendChild(row);
            });
        }

        // Atualizar minipauta ao mudar turma ou disciplina
        document.getElementById('turma').addEventListener('change', loadMinipautaData);
        document.getElementById('disciplina').addEventListener('change', loadMinipautaData);
        document.getElementById('visualizarBtn').addEventListener('click', loadMinipautaData);

        // Botão de impressão
        document.getElementById('printButton').addEventListener('click', function() {
            window.print();
        });

        // Zoom in/out
        let currentZoom = 100;
        document.getElementById('zoomInBtn').addEventListener('click', function() {
            if (currentZoom < 150) {
                currentZoom += 10;
                document.getElementById('minipauta-document').style.transform = `scale(${currentZoom / 100})`;
                document.getElementById('minipauta-document').style.transformOrigin = 'top center';
            }
        });

        document.getElementById('zoomOutBtn').addEventListener('click', function() {
            if (currentZoom > 50) {
                currentZoom -= 10;
                document.getElementById('minipauta-document').style.transform = `scale(${currentZoom / 100})`;
                document.getElementById('minipauta-document').style.transformOrigin = 'top center';
            }
        });

        // Tela cheia
        document.getElementById('fullscreenBtn').addEventListener('click', function() {
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
            window.dashboardData.updateDynamicDates();
            window.dashboardData.loadCurrentUserData();
            
            // Carregar dados da minipauta
            loadMinipautaData();
        });
    </script>
    
</body>
</html>