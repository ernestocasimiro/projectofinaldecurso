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
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

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
</body>
</html>