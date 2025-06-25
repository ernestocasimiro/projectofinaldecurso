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
    <title>Materiais - Dashboard de Professores</title>
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
                    <li class="active">
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
                    <input type="text" placeholder="Pesquisar materiais...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Materiais Didáticos</h1>
                    <button class="btn-primary">
                        <span class="material-symbols-outlined">add</span>
                        Novo Material
                    </button>
                </div>
                
                <div class="filter-container">
                    <div class="filter-group">
                        <label for="turma-materiais-filter">Turma:</label>
                        <select id="turma-materiais-filter" class="filter-select">
                            <option value="todas">Todas as Turmas</option>
                            <option value="9A">9º Ano A</option>
                            <option value="8B">8º Ano B</option>
                            <option value="10C">10º Ano C</option>
                            <option value="7D">7º Ano D</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="tipo-material-filter">Tipo:</label>
                        <select id="tipo-material-filter" class="filter-select">
                            <option value="todos">Todos os Tipos</option>
                            <option value="documento">Documentos</option>
                            <option value="apresentacao">Apresentações</option>
                            <option value="video">Vídeos</option>
                            <option value="exercicio">Exercícios</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="ordenar-por">Ordenar por:</label>
                        <select id="ordenar-por" class="filter-select">
                            <option value="recentes">Mais Recentes</option>
                            <option value="antigos">Mais Antigos</option>
                            <option value="nome-asc">Nome (A-Z)</option>
                            <option value="nome-desc">Nome (Z-A)</option>
                        </select>
                    </div>
                </div>

                <div class="tabs">
                    <button class="tab-btn active">Meus Materiais</button>
                    <button class="tab-btn">Compartilhados Comigo</button>
                    <button class="tab-btn">Biblioteca da Escola</button>
                </div>

                <div class="materials-grid">
                    <div class="material-card">
                        <div class="material-icon pdf">
                            <span class="material-symbols-outlined">description</span>
                        </div>
                        <div class="material-info">
                            <h3>Apostila de Álgebra</h3>
                            <p class="material-meta">PDF • 2.5 MB • Atualizado em 10/05/2023</p>
                            <div class="material-tags">
                                <span class="material-tag">9º Ano A</span>
                                <span class="material-tag">Álgebra</span>
                            </div>
                        </div>
                        <div class="material-actions">
                            <button class="action-btn" title="Visualizar">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                            <button class="action-btn" title="Editar">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="action-btn" title="Compartilhar">
                                <span class="material-symbols-outlined">share</span>
                            </button>
                            <button class="action-btn" title="Mais Opções">
                                <span class="material-symbols-outlined">more_vert</span>
                            </button>
                        </div>
                    </div>

                    <div class="material-card">
                        <div class="material-icon ppt">
                            <span class="material-symbols-outlined">slideshow</span>
                        </div>
                        <div class="material-info">
                            <h3>Apresentação sobre Geometria</h3>
                            <p class="material-meta">PPT • 5.8 MB • Atualizado em 05/05/2023</p>
                            <div class="material-tags">
                                <span class="material-tag">8º Ano B</span>
                                <span class="material-tag">Geometria</span>
                            </div>
                        </div>
                        <div class="material-actions">
                            <button class="action-btn" title="Visualizar">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                            <button class="action-btn" title="Editar">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="action-btn" title="Compartilhar">
                                <span class="material-symbols-outlined">share</span>
                            </button>
                            <button class="action-btn" title="Mais Opções">
                                <span class="material-symbols-outlined">more_vert</span>
                            </button>
                        </div>
                    </div>

                    <div class="material-card">
                        <div class="material-icon video">
                            <span class="material-symbols-outlined">play_circle</span>
                        </div>
                        <div class="material-info">
                            <h3>Vídeo Explicativo: Estatística Básica</h3>
                            <p class="material-meta">MP4 • 45 MB • Atualizado em 12/05/2023</p>
                            <div class="material-tags">
                                <span class="material-tag">10º Ano C</span>
                                <span class="material-tag">Estatística</span>
                            </div>
                        </div>
                        <div class="material-actions">
                            <button class="action-btn" title="Visualizar">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                            <button class="action-btn" title="Editar">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="action-btn" title="Compartilhar">
                                <span class="material-symbols-outlined">share</span>
                            </button>
                            <button class="action-btn" title="Mais Opções">
                                <span class="material-symbols-outlined">more_vert</span>
                            </button>
                        </div>
                    </div>

                    <div class="material-card">
                        <div class="material-icon doc">
                            <span class="material-symbols-outlined">article</span>
                        </div>
                        <div class="material-info">
                            <h3>Lista de Exercícios: Equações</h3>
                            <p class="material-meta">DOCX • 1.2 MB • Atualizado em 08/05/2023</p>
                            <div class="material-tags">
                                <span class="material-tag">9º Ano A</span>
                                <span class="material-tag">Álgebra</span>
                            </div>
                        </div>
                        <div class="material-actions">
                            <button class="action-btn" title="Visualizar">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                            <button class="action-btn" title="Editar">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="action-btn" title="Compartilhar">
                                <span class="material-symbols-outlined">share</span>
                            </button>
                            <button class="action-btn" title="Mais Opções">
                                <span class="material-symbols-outlined">more_vert</span>
                            </button>
                        </div>
                    </div>

                    <div class="material-card">
                        <div class="material-icon xls">
                            <span class="material-symbols-outlined">table_chart</span>
                        </div>
                        <div class="material-info">
                            <h3>Planilha de Dados Estatísticos</h3>
                            <p class="material-meta">XLSX • 0.8 MB • Atualizado em 15/05/2023</p>
                            <div class="material-tags">
                                <span class="material-tag">10º Ano C</span>
                                <span class="material-tag">Estatística</span>
                            </div>
                        </div>
                        <div class="material-actions">
                            <button class="action-btn" title="Visualizar">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                            <button class="action-btn" title="Editar">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="action-btn" title="Compartilhar">
                                <span class="material-symbols-outlined">share</span>
                            </button>
                            <button class="action-btn" title="Mais Opções">
                                <span class="material-symbols-outlined">more_vert</span>
                            </button>
                        </div>
                    </div>

                    <div class="material-card">
                        <div class="material-icon zip">
                            <span class="material-symbols-outlined">folder_zip</span>
                        </div>
                        <div class="material-info">
                            <h3>Recursos para Aula de Aritmética</h3>
                            <p class="material-meta">ZIP • 15.3 MB • Atualizado em 03/05/2023</p>
                            <div class="material-tags">
                                <span class="material-tag">7º Ano D</span>
                                <span class="material-tag">Aritmética</span>
                            </div>
                        </div>
                        <div class="material-actions">
                            <button class="action-btn" title="Visualizar">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                            <button class="action-btn" title="Editar">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="action-btn" title="Compartilhar">
                                <span class="material-symbols-outlined">share</span>
                            </button>
                            <button class="action-btn" title="Mais Opções">
                                <span class="material-symbols-outlined">more_vert</span>
                            </button>
                        </div>
                    </div>

                    <div class="material-card upload-card">
                        <div class="upload-content">
                            <span class="material-symbols-outlined">cloud_upload</span>
                            <p>Arraste arquivos aqui ou clique para fazer upload</p>
                        </div>
                    </div>
                </div>

                <div class="storage-info">
                    <div class="storage-usage">
                        <div class="storage-bar">
                            <div class="storage-used" style="width: 65%;"></div>
                        </div>
                        <p>6.5 GB de 10 GB utilizados</p>
                    </div>
                    <button class="btn-outline">
                        <span class="material-symbols-outlined">upgrade</span>
                        Aumentar Armazenamento
                    </button>
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
    </script>
    
</body>
</html>