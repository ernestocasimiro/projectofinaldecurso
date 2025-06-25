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
        $idTeacher = $_SESSION['id'] ?? null;

        if (!$idTeacher) {
            die("professor não identificado.");
        }

        try {
            $stmt = $conn->prepare("SELECT fname, lname FROM professores WHERE id = :id");
            $stmt->bindParam(':id', $idTeacher, PDO::PARAM_INT);
            $stmt->execute();

            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$teacher) {
                die("professor não encontrado.");
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
    <title>Configurações - Dashboard de Professores</title>
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
                    
                    <li>
                        <a href="minipauta.php">
                            <span class="material-symbols-outlined">summarize</span>
                            <span class="menu-text">Minipautas</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="configuracoes.php" class="active">
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
                    <input type="text" placeholder="Pesquisar configurações...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Configurações</h1>
                    <button class="btn-primary">
                        <span class="material-symbols-outlined">save</span>
                        Salvar Alterações
                    </button>
                </div>
                
                <div class="settings-container">
                    <div class="settings-sidebar">
                        <div class="settings-nav">
                            <a href="#perfil" class="settings-nav-item active">
                                <span class="material-symbols-outlined">person</span>
                                Perfil
                            </a>
                            <a href="#conta" class="settings-nav-item">
                                <span class="material-symbols-outlined">manage_accounts</span>
                                Conta
                            </a>
                            <a href="#notificacoes" class="settings-nav-item">
                                <span class="material-symbols-outlined">notifications</span>
                                Notificações
                            </a>
                            <a href="#aparencia" class="settings-nav-item">
                                <span class="material-symbols-outlined">palette</span>
                                Aparência
                            </a>
                            <a href="#privacidade" class="settings-nav-item">
                                <span class="material-symbols-outlined">security</span>
                                Privacidade
                            </a>
                            <a href="#integracao" class="settings-nav-item">
                                <span class="material-symbols-outlined">integration_instructions</span>
                                Integrações
                            </a>
                        </div>
                    </div>
                    <div class="settings-content">
                        <section id="perfil" class="settings-section">
                            <h2>Configurações de Perfil</h2>
                            <div class="profile-settings">
                                <div class="profile-picture-settings">
                                    <div class="profile-picture">
                                        <img src="https://via.placeholder.com/150" alt="Foto do Perfil">
                                        <div class="profile-picture-overlay">
                                            <span class="material-symbols-outlined">photo_camera</span>
                                        </div>
                                    </div>
                                    <div class="profile-picture-actions">
                                        <button class="btn-outline">Alterar Foto</button>
                                        <button class="btn-text">Remover</button>
                                    </div>
                                </div>
                                <div class="profile-form">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="nome">Nome Completo</label>
                                            <input type="text" id="nome" value="Carlos Silva" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="apelido">Nome de Exibição</label>
                                            <input type="text" id="apelido" value="Prof. Silva" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" id="email" value="carlos.silva@pitruca.com" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="telefone">Telefone</label>
                                            <input type="tel" id="telefone" value="(11) 98765-4321" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="disciplina">Disciplina</label>
                                        <input type="text" id="disciplina" value="Matemática" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="bio">Biografia</label>
                                        <textarea id="bio" class="form-control" rows="4">Professor de Matemática com 10 anos de experiência. Especialista em Álgebra e Geometria. Mestre em Educação Matemática pela Universidade Federal.</textarea>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section id="conta" class="settings-section">
                            <h2>Configurações de Conta</h2>
                            <div class="account-settings">
                                <div class="settings-card">
                                    <h3>Alterar Senha</h3>
                                    <div class="form-group">
                                        <label for="senha-atual">Senha Atual</label>
                                        <input type="password" id="senha-atual" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="nova-senha">Nova Senha</label>
                                        <input type="password" id="nova-senha" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="confirmar-senha">Confirmar Nova Senha</label>
                                        <input type="password" id="confirmar-senha" class="form-control">
                                    </div>
                                    <button class="btn-primary">Alterar Senha</button>
                                </div>

                                <div class="settings-card">
                                    <h3>Verificação em Duas Etapas</h3>
                                    <p>Adicione uma camada extra de segurança à sua conta.</p>
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="two-factor" class="toggle-input">
                                        <label for="two-factor" class="toggle-label"></label>
                                        <span>Ativar verificação em duas etapas</span>
                                    </div>
                                    <button class="btn-outline">Configurar</button>
                                </div>

                                <div class="settings-card danger-zone">
                                    <h3>Zona de Perigo</h3>
                                    <p>Ações que podem afetar permanentemente sua conta.</p>
                                    <button class="btn-danger">Desativar Conta</button>
                                </div>
                            </div>
                        </section>

                        <section id="notificacoes" class="settings-section">
                            <h2>Configurações de Notificações</h2>
                            <div class="notification-settings">
                                <div class="settings-card">
                                    <h3>Notificações por Email</h3>
                                    <div class="notification-option">
                                        <div class="toggle-switch">
                                            <input type="checkbox" id="email-mensagens" class="toggle-input" checked>
                                            <label for="email-mensagens" class="toggle-label"></label>
                                            <span>Novas mensagens</span>
                                        </div>
                                    </div>
                                    <div class="notification-option">
                                        <div class="toggle-switch">
                                            <input type="checkbox" id="email-notas" class="toggle-input" checked>
                                            <label for="email-notas" class="toggle-label"></label>
                                            <span>Atualizações de notas</span>
                                        </div>
                                    </div>
                                    <div class="notification-option">
                                        <div class="toggle-switch">
                                            <input type="checkbox" id="email-eventos" class="toggle-input" checked>
                                            <label for="email-eventos" class="toggle-label"></label>
                                            <span>Eventos do calendário</span>
                                        </div>
                                    </div>
                                    <div class="notification-option">
                                        <div class="toggle-switch">
                                            <input type="checkbox" id="email-anuncios" class="toggle-input">
                                            <label for="email-anuncios" class="toggle-label"></label>
                                            <span>Anúncios da escola</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="settings-card">
                                    <h3>Notificações no Sistema</h3>
                                    <div class="notification-option">
                                        <div class="toggle-switch">
                                            <input type="checkbox" id="sistema-mensagens" class="toggle-input" checked>
                                            <label for="sistema-mensagens" class="toggle-label"></label>
                                            <span>Novas mensagens</span>
                                        </div>
                                    </div>
                                    <div class="notification-option">
                                        <div class="toggle-switch">
                                            <input type="checkbox" id="sistema-comentarios" class="toggle-input" checked>
                                            <label for="sistema-comentarios" class="toggle-label"></label>
                                            <span>Comentários em materiais</span>
                                        </div>
                                    </div>
                                    <div class="notification-option">
                                        <div class="toggle-switch">
                                            <input type="checkbox" id="sistema-lembretes" class="toggle-input" checked>
                                            <label for="sistema-lembretes" class="toggle-label"></label>
                                            <span>Lembretes de eventos</span>
                                        </div>
                                    </div>
                                    <div class="notification-option">
                                        <div class="toggle-switch">
                                            <input type="checkbox" id="sistema-atualizacoes" class="toggle-input" checked>
                                            <label for="sistema-atualizacoes" class="toggle-label"></label>
                                            <span>Atualizações do sistema</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section id="aparencia" class="settings-section">
                            <h2>Configurações de Aparência</h2>
                            <div class="appearance-settings">
                                <div class="settings-card">
                                    <h3>Tema</h3>
                                    <div class="theme-options">
                                        <div class="theme-option">
                                            <input type="radio" name="theme" id="theme-light" checked>
                                            <label for="theme-light" class="theme-label light">
                                                <div class="theme-preview"></div>
                                                <span>Claro</span>
                                            </label>
                                        </div>
                                        <div class="theme-option">
                                            <input type="radio" name="theme" id="theme-dark">
                                            <label for="theme-dark" class="theme-label dark">
                                                <div class="theme-preview"></div>
                                                <span>Escuro</span>
                                            </label>
                                        </div>
                                        <div class="theme-option">
                                            <input type="radio" name="theme" id="theme-system">
                                            <label for="theme-system" class="theme-label system">
                                                <div class="theme-preview"></div>
                                                <span>Sistema</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="settings-card">
                                    <h3>Cor de Destaque</h3>
                                    <div class="color-options">
                                        <div class="color-option">
                                            <input type="radio" name="color" id="color-blue" checked>
                                            <label for="color-blue" class="color-label blue"></label>
                                        </div>
                                        <div class="color-option">
                                            <input type="radio" name="color" id="color-purple">
                                            <label for="color-purple" class="color-label purple"></label>
                                        </div>
                                        <div class="color-option">
                                            <input type="radio" name="color" id="color-green">
                                            <label for="color-green" class="color-label green"></label>
                                        </div>
                                        <div class="color-option">
                                            <input type="radio" name="color" id="color-orange">
                                            <label for="color-orange" class="color-label orange"></label>
                                        </div>
                                        <div class="color-option">
                                            <input type="radio" name="color" id="color-red">
                                            <label for="color-red" class="color-label red"></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="settings-card">
                                    <h3>Fonte</h3>
                                    <div class="form-group">
                                        <label for="font-family">Família da Fonte</label>
                                        <select id="font-family" class="form-control">
                                            <option value="segoe-ui" selected>Segoe UI</option>
                                            <option value="roboto">Roboto</option>
                                            <option value="open-sans">Open Sans</option>
                                            <option value="arial">Arial</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="font-size">Tamanho da Fonte</label>
                                        <select id="font-size" class="form-control">
                                            <option value="small">Pequeno</option>
                                            <option value="medium" selected>Médio</option>
                                            <option value="large">Grande</option>
                                            <option value="x-large">Extra Grande</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section id="privacidade" class="settings-section">
                            <h2>Configurações de Privacidade</h2>
                            <div class="privacy-settings">
                                <div class="settings-card">
                                    <h3>Visibilidade do Perfil</h3>
                                    <div class="form-group">
                                        <label for="profile-visibility">Quem pode ver meu perfil</label>
                                        <select id="profile-visibility" class="form-control">
                                            <option value="all">Todos na escola</option>
                                            <option value="staff" selected>Apenas funcionários</option>
                                            <option value="teachers">Apenas professores</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact-visibility">Quem pode ver minhas informações de contato</label>
                                        <select id="contact-visibility" class="form-control">
                                            <option value="all">Todos na escola</option>
                                            <option value="staff" selected>Apenas funcionários</option>
                                            <option value="none">Ninguém</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="settings-card">
                                    <h3>Dados e Cookies</h3>
                                    <p>Gerencie como seus dados são coletados e utilizados.</p>
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="analytics" class="toggle-input" checked>
                                        <label for="analytics" class="toggle-label"></label>
                                        <span>Permitir análise de uso para melhorar o sistema</span>
                                    </div>
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="cookies" class="toggle-input" checked>
                                        <label for="cookies" class="toggle-label"></label>
                                        <span>Aceitar cookies não essenciais</span>
                                    </div>
                                    <button class="btn-outline">Gerenciar Cookies</button>
                                    <button class="btn-outline">Exportar Meus Dados</button>
                                </div>
                            </div>
                        </section>

                        <section id="integracao" class="settings-section">
                            <h2>Integrações</h2>
                            <div class="integration-settings">
                                <div class="settings-card">
                                    <h3>Aplicativos Conectados</h3>
                                    <div class="connected-app">
                                        <div class="app-info">
                                            <img src="https://via.placeholder.com/40" alt="Google">
                                            <div>
                                                <h4>Google Workspace</h4>
                                                <p>Conectado em 10/03/2025</p>
                                            </div>
                                        </div>
                                        <button class="btn-outline">Desconectar</button>
                                    </div>
                                    <div class="connected-app">
                                        <div class="app-info">
                                            <img src="https://via.placeholder.com/40" alt="Microsoft">
                                            <div>
                                                <h4>Microsoft 365</h4>
                                                <p>Conectado em 15/04/2025</p>
                                            </div>
                                        </div>
                                        <button class="btn-outline">Desconectar</button>
                                    </div>
                                    <button class="btn-primary">Adicionar Nova Integração</button>
                                </div>

                                <div class="settings-card">
                                    <h3>Exportação de Dados</h3>
                                    <p>Configure a exportação automática de dados para outros sistemas.</p>
                                    <div class="form-group">
                                        <label for="export-frequency">Frequência de Exportação</label>
                                        <select id="export-frequency" class="form-control">
                                            <option value="never">Nunca</option>
                                            <option value="daily">Diariamente</option>
                                            <option value="weekly" selected>Semanalmente</option>
                                            <option value="monthly">Mensalmente</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="export-format">Formato de Exportação</label>
                                        <select id="export-format" class="form-control">
                                            <option value="csv" selected>CSV</option>
                                            <option value="excel">Excel</option>
                                            <option value="json">JSON</option>
                                        </select>
                                    </div>
                                    <button class="btn-outline">Configurar Exportação</button>
                                </div>
                            </div>
                        </section>
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

        // Settings navigation
        document.querySelectorAll('.settings-nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all items
                document.querySelectorAll('.settings-nav-item').forEach(navItem => {
                    navItem.classList.remove('active');
                });
                
                // Add active class to clicked item
                this.classList.add('active');
                
                // Show corresponding section
                const targetId = this.getAttribute('href').substring(1);
                document.querySelectorAll('.settings-section').forEach(section => {
                    section.style.display = 'none';
                });
                document.getElementById(targetId).style.display = 'block';
            });
        });
    </script>
    
</body>
</html>