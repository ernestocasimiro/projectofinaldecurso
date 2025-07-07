<?php
session_start();

$sName = "localhost";
$uName = "root";
$pass = "";
$db_name = "escolabd";

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

$idTeacher = $_SESSION['id'] ?? null;

if (!$idTeacher) {
    die("Professor não identificado.");
}

try {
    $stmt = $conn->prepare("SELECT fname, lname FROM professores WHERE id = :id");
    $stmt->bindParam(':id', $idTeacher, PDO::PARAM_INT);
    $stmt->execute();

    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$teacher) {
        die("Professor não encontrado.");
    }
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit;
}

$dataAtual = '15 de Abril de 2025';
$trimestre = '2º trimestre';
$anoLetivo = '2025';

// Verifica se foi solicitado o detalhe de uma turma
$turmaId = $_GET['turma_id'] ?? null;
$turmaDetalhes = null;
$disciplinas = [];
$alunos = [];

if ($turmaId) {
    // Busca detalhes da turma
    try {
        $stmt = $conn->prepare("SELECT * FROM turmas WHERE id = :id");
        $stmt->bindParam(':id', $turmaId, PDO::PARAM_INT);
        $stmt->execute();
        $turmaDetalhes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($turmaDetalhes) {
            // Busca disciplinas da turma
            $stmt = $conn->prepare("SELECT d.* FROM disciplinas d 
                                  INNER JOIN turma_disciplina td ON d.id = td.disciplina_id 
                                  WHERE td.turma_id = :turma_id");
            $stmt->bindParam(':turma_id', $turmaId, PDO::PARAM_INT);
            $stmt->execute();
            $disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Busca alunos da turma
            $stmt = $conn->prepare("SELECT a.* FROM alunos a 
                                  INNER JOIN turma_aluno ta ON a.id = ta.aluno_id 
                                  WHERE ta.turma_id = :turma_id");
            $stmt->bindParam(':turma_id', $turmaId, PDO::PARAM_INT);
            $stmt->execute();
            $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        echo "Erro na consulta: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turmas - Dashboard de Professores</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Estilos para as turmas fictícias */
        .fictitious-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }
        
        .fictitious-section h2 {
            color: #5d5d5d;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .fictitious-section h2 .material-symbols-outlined {
            font-size: 1.8rem;
            color: #3a5bb9;
        }
        
        .fictitious-section .description {
            color: #666;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        
        .fictitious-classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .fictitious-class-card {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s ease;
            border-left: 4px solid #3a5bb9;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            cursor: pointer;
        }
        
        .fictitious-class-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        
        .fictitious-class-card h3 {
            color: #444;
            margin-bottom: 12px;
            font-size: 1.2rem;
        }
        
        .fictitious-class-card .class-info {
            color: #666;
            margin: 8px 0;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .fictitious-class-card .class-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #e0e0e0;
        }
        
        .fictitious-class-card .class-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            color: #777;
        }

        /* Modal de detalhes da turma */
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
            width: 80%;
            max-width: 900px;
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

        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .modal-section {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
        }

        .modal-section h3 {
            color: #555;
            margin-top: 0;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .disciplinas-list, .alunos-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .disciplina-card, .aluno-card {
            background-color: #fff;
            border-radius: 6px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .disciplina-card h4, .aluno-card h4 {
            margin: 0 0 5px 0;
            color: #444;
        }

        .disciplina-card p, .aluno-card p {
            margin: 5px 0;
            color: #666;
            font-size: 0.9rem;
        }

        .turma-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-item strong {
            display: block;
            color: #555;
            margin-bottom: 3px;
        }

        .info-item span {
            color: #666;
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
                    <li class="active">
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
                    <li>
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
                    <input type="text" placeholder="Pesquisar turmas...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Minhas Turmas</h1>
                    <div class="view-options">
                        <button class="view-btn active">
                            <span class="material-symbols-outlined">grid_view</span>
                        </button>
                        <button class="view-btn">
                            <span class="material-symbols-outlined">view_list</span>
                        </button>
                    </div>
                </div>
                
                <div class="filter-container">
                    <div class="filter-group">
                        <label for="ano-filter">Ano Letivo:</label>
                        <select id="ano-filter" class="filter-select">
                            <option value="2025" selected data-dynamic="school-year">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="periodo-filter">Período:</label>
                        <select id="periodo-filter" class="filter-select">
                            <option value="todos">Todos</option>
                            <option value="matutino">Matutino</option>
                            <option value="vespertino">Vespertino</option>
                            <option value="noturno">Noturno</option>
                        </select>
                    </div>
                </div>

                <div class="classes-grid" data-dynamic="classes-container">
                    <!-- Turmas serão carregadas dinamicamente -->
                </div>
                
                <!-- Seção de Turmas Fictícias -->
                <div class="fictitious-section">
                    <h2>
                        <span class="material-symbols-outlined">auto_awesome</span>
                        Todas As Turmas
                    </h2>
                    <p class="description">Aqui estão todas as suas turmas atribuídas. Use este espaço para acompanhar o progresso, organizar conteúdos e interagir com seus alunos.</p>
                    
                    <div class="fictitious-classes-grid">
                        <!-- Turma 1 -->
                        <div class="fictitious-class-card" onclick="openTurmaModal('10matA', '10ª Classe - Matemática A', 'Matutino', 'B-12', 'Segunda e Quarta, 8:00-9:30', '32', '5')">
                            <h3>10ª Classe - Matemática A</h3>
                            <p class="class-info"><strong>Período:</strong> Matutino</p>
                            <p class="class-info"><strong>Sala:</strong> B-12</p>
                            <p class="class-info"><strong>Horário:</strong> Segunda e Quarta, 8:00-9:30</p>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    32 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">book</span>
                                    5 materiais
                                </span>
                            </div>
                        </div>
                        
                        <!-- Turma 2 -->
                        <div class="fictitious-class-card" onclick="openTurmaModal('11fis', '11ª Classe - Física', 'Vespertino', 'A-07', 'Terça e Quinta, 14:00-15:30', '28', '7')">
                            <h3>11ª Classe - Física</h3>
                            <p class="class-info"><strong>Período:</strong> Vespertino</p>
                            <p class="class-info"><strong>Sala:</strong> A-07</p>
                            <p class="class-info"><strong>Horário:</strong> Terça e Quinta, 14:00-15:30</p>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    28 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">book</span>
                                    7 materiais
                                </span>
                            </div>
                        </div>
                        
                        <!-- Turma 3 -->
                        <div class="fictitious-class-card" onclick="openTurmaModal('12quim', '12ª Classe - Química', 'Noturno', 'Lab-03', 'Segunda e Quarta, 19:00-20:30', '25', '4')">
                            <h3>12ª Classe - Química</h3>
                            <p class="class-info"><strong>Período:</strong> Noturno</p>
                            <p class="class-info"><strong>Sala:</strong> Lab-03</p>
                            <p class="class-info"><strong>Horário:</strong> Segunda e Quarta, 19:00-20:30</p>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    25 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">book</span>
                                    4 materiais
                                </span>
                            </div>
                        </div>
                        
                        <!-- Turma 4 -->
                        <div class="fictitious-class-card" onclick="openTurmaModal('9matB', '9ª Classe - Matemática B', 'Matutino', 'C-05', 'Sexta, 10:00-12:00', '35', '6')">
                            <h3>9ª Classe - Matemática B</h3>
                            <p class="class-info"><strong>Período:</strong> Matutino</p>
                            <p class="class-info"><strong>Sala:</strong> C-05</p>
                            <p class="class-info"><strong>Horário:</strong> Sexta, 10:00-12:00</p>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    35 alunos
                                </span>
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">book</span>
                                    6 materiais
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de Detalhes da Turma -->
    <div id="turmaModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTurmaNome"></h2>
                <button class="close-modal" onclick="closeTurmaModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-section">
                    <h3><span class="material-symbols-outlined">info</span> Informações da Turma</h3>
                    <div class="turma-info-grid">
                        <div class="info-item">
                            <strong>Período:</strong>
                            <span id="modalTurmaPeriodo"></span>
                        </div>
                        <div class="info-item">
                            <strong>Sala:</strong>
                            <span id="modalTurmaSala"></span>
                        </div>
                        <div class="info-item">
                            <strong>Horário:</strong>
                            <span id="modalTurmaHorario"></span>
                        </div>
                        <div class="info-item">
                            <strong>Nº de Alunos:</strong>
                            <span id="modalTurmaAlunos"></span>
                        </div>
                        <div class="info-item">
                            <strong>Materiais:</strong>
                            <span id="modalTurmaMateriais"></span>
                        </div>
                    </div>
                </div>
                
                <div class="modal-section">
                    <h3><span class="material-symbols-outlined">menu_book</span> Disciplinas</h3>
                    <div class="disciplinas-list" id="modalTurmaDisciplinas">
                        <!-- Disciplinas serão preenchidas via JavaScript -->
                    </div>
                </div>
                
                <div class="modal-section">
                    <h3><span class="material-symbols-outlined">group</span> Alunos</h3>
                    <div class="alunos-list" id="modalTurmaAlunosLista">
                        <!-- Alunos serão preenchidos via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="dashboard-data.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

        // Adicionar funcionalidade aos botões de visualização
        document.querySelectorAll('.view-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.view-btn').forEach(function(b) {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                
                // Alternar entre visualização de grade e lista
                const classesGrid = document.querySelector('.classes-grid');
                if (this.querySelector('.material-symbols-outlined').textContent === 'view_list') {
                    classesGrid.classList.add('list-view');
                } else {
                    classesGrid.classList.remove('list-view');
                }
            });
        });

        // Filtrar turmas por período
        document.getElementById('periodo-filter').addEventListener('change', function() {
            const periodo = this.value;
            window.dashboardData.loadClassesData(periodo);
        });

        // Funções para o modal de turma
        function openTurmaModal(id, nome, periodo, sala, horario, alunos, materiais) {
            const modal = document.getElementById('turmaModal');
            document.getElementById('modalTurmaNome').textContent = nome;
            document.getElementById('modalTurmaPeriodo').textContent = periodo;
            document.getElementById('modalTurmaSala').textContent = sala;
            document.getElementById('modalTurmaHorario').textContent = horario;
            document.getElementById('modalTurmaAlunos').textContent = alunos;
            document.getElementById('modalTurmaMateriais').textContent = materiais;
            
            // Preencher disciplinas (dados fictícios)
            const disciplinasContainer = document.getElementById('modalTurmaDisciplinas');
            disciplinasContainer.innerHTML = '';
            
            const disciplinas = [
                { nome: 'Matemática', carga: '5 aulas/semana', professor: 'Prof. Silva' },
                { nome: 'Português', carga: '4 aulas/semana', professor: 'Prof. Oliveira' },
                { nome: 'Física', carga: '3 aulas/semana', professor: 'Prof. Costa' },
                { nome: 'Química', carga: '3 aulas/semana', professor: 'Prof. Santos' }
            ];
            
            disciplinas.forEach(disciplina => {
                const card = document.createElement('div');
                card.className = 'disciplina-card';
                card.innerHTML = `
                    <h4>${disciplina.nome}</h4>
                    <p><strong>Carga horária:</strong> ${disciplina.carga}</p>
                    <p><strong>Professor:</strong> ${disciplina.professor}</p>
                `;
                disciplinasContainer.appendChild(card);
            });
            
            // Preencher alunos (dados fictícios)
            const alunosContainer = document.getElementById('modalTurmaAlunosLista');
            alunosContainer.innerHTML = '';
            
            for (let i = 1; i <= parseInt(alunos); i++) {
                const card = document.createElement('div');
                card.className = 'aluno-card';
                card.innerHTML = `
                    <h4>Aluno ${i}</h4>
                    <p><strong>Nº Matrícula:</strong> 2023${i.toString().padStart(3, '0')}</p>
                    <p><strong>Status:</strong> Ativo</p>
                `;
                alunosContainer.appendChild(card);
            }
            
            modal.style.display = 'block';
        }

        function closeTurmaModal() {
            document.getElementById('turmaModal').style.display = 'none';
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('turmaModal');
            if (event.target == modal) {
                closeTurmaModal();
            }
        }
    </script>
</body>
</html>