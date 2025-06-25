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
    <title>Configurações - Dashboard Encarregados</title>
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
                <div class="avatar">
                    <img src="https://via.placeholder.com/50" alt="Foto do Encarregado">
                </div>
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
                    <div class="header-actions">
                        <button class="btn-primary" id="saveAllBtn">
                            <span class="material-symbols-outlined">save</span>
                            Salvar Alterações
                        </button>
                    </div>
                </div>

                <div class="settings-layout">
                    <!-- Settings Navigation -->
                    <div class="settings-nav">
                        <div class="nav-item active" data-section="profile">
                            <span class="material-symbols-outlined">person</span>
                            <span>Perfil</span>
                        </div>
                        <div class="nav-item" data-section="notifications">
                            <span class="material-symbols-outlined">notifications</span>
                            <span>Notificações</span>
                        </div>
                        <div class="nav-item" data-section="privacy">
                            <span class="material-symbols-outlined">security</span>
                            <span>Privacidade</span>
                        </div>
                        <div class="nav-item" data-section="preferences">
                            <span class="material-symbols-outlined">tune</span>
                            <span>Preferências</span>
                        </div>
                        <div class="nav-item" data-section="account">
                            <span class="material-symbols-outlined">manage_accounts</span>
                            <span>Conta</span>
                        </div>
                    </div>

                    <!-- Settings Content -->
                    <div class="settings-content">
                        <!-- Profile Section -->
                        <div class="settings-section active" id="profile">
                            <div class="section-header">
                                <h2>Informações do Perfil</h2>
                                <p>Gerencie suas informações pessoais e de contato</p>
                            </div>

                            <div class="settings-card">
                                <div class="profile-photo-section">
                                    <div class="current-photo">
                                        <img src="https://via.placeholder.com/100" alt="Foto do perfil" id="profileImage">
                                    </div>
                                    <div class="photo-actions">
                                        <button class="btn-outline">
                                            <span class="material-symbols-outlined">photo_camera</span>
                                            Alterar Foto
                                        </button>
                                        <button class="btn-outline text-danger">
                                            <span class="material-symbols-outlined">delete</span>
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-card">
                                <h3>Informações Pessoais</h3>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Nome Completo</label>
                                        <input type="text" class="form-control" value="Maria Santos" placeholder="Digite seu nome completo">
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" value="maria.santos@email.com" placeholder="Digite seu email">
                                    </div>
                                    <div class="form-group">
                                        <label>Telefone</label>
                                        <input type="tel" class="form-control" value="(11) 99999-9999" placeholder="Digite seu telefone">
                                    </div>
                                    <div class="form-group">
                                        <label>CPF</label>
                                        <input type="text" class="form-control" value="123.456.789-00" placeholder="Digite seu CPF" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Data de Nascimento</label>
                                        <input type="date" class="form-control" value="1985-03-15">
                                    </div>
                                    <div class="form-group">
                                        <label>Profissão</label>
                                        <input type="text" class="form-control" value="Enfermeira" placeholder="Digite sua profissão">
                                    </div>
                                </div>
                            </div>

                            <div class="settings-card">
                                <h3>Endereço</h3>
                                <div class="form-grid">
                                    <div class="form-group full-width">
                                        <label>Endereço</label>
                                        <input type="text" class="form-control" value="Rua das Flores, 123" placeholder="Digite seu endereço">
                                    </div>
                                    <div class="form-group">
                                        <label>Cidade</label>
                                        <input type="text" class="form-control" value="São Paulo" placeholder="Digite sua cidade">
                                    </div>
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select class="form-control">
                                            <option value="SP" selected>São Paulo</option>
                                            <option value="RJ">Rio de Janeiro</option>
                                            <option value="MG">Minas Gerais</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>CEP</label>
                                        <input type="text" class="form-control" value="01234-567" placeholder="Digite seu CEP">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications Section -->
                        <div class="settings-section" id="notifications">
                            <div class="section-header">
                                <h2>Configurações de Notificação</h2>
                                <p>Escolha como e quando você quer receber notificações</p>
                            </div>

                            <div class="settings-card">
                                <h3>Notificações por Email</h3>
                                <div class="notification-options">
                                    <div class="notification-item">
                                        <div class="notification-info">
                                            <h4>Novas Notas</h4>
                                            <p>Receba um email quando novas notas forem lançadas</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="notification-item">
                                        <div class="notification-info">
                                            <h4>Comunicados da Escola</h4>
                                            <p>Receba comunicados importantes da escola</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="notification-item">
                                        <div class="notification-info">
                                            <h4>Eventos e Reuniões</h4>
                                            <p>Seja notificado sobre eventos e reuniões escolares</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="notification-item">
                                        <div class="notification-info">
                                            <h4>Faltas e Atrasos</h4>
                                            <p>Receba alertas sobre faltas e atrasos dos seus filhos</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-card">
                                <h3>Notificações Push</h3>
                                <div class="notification-options">
                                    <div class="notification-item">
                                        <div class="notification-info">
                                            <h4>Mensagens dos Professores</h4>
                                            <p>Receba notificações instantâneas de mensagens</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="notification-item">
                                        <div class="notification-info">
                                            <h4>Lembretes de Eventos</h4>
                                            <p>Receba lembretes antes de eventos importantes</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox">
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-card">
                                <h3>Frequência de Notificações</h3>
                                <div class="form-group">
                                    <label>Resumo Semanal</label>
                                    <select class="form-control">
                                        <option value="monday">Segunda-feira</option>
                                        <option value="friday" selected>Sexta-feira</option>
                                        <option value="sunday">Domingo</option>
                                        <option value="disabled">Desabilitado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Privacy Section -->
                        <div class="settings-section" id="privacy">
                            <div class="section-header">
                                <h2>Privacidade e Segurança</h2>
                                <p>Gerencie suas configurações de privacidade e segurança</p>
                            </div>

                            <div class="settings-card">
                                <h3>Alterar Senha</h3>
                                <div class="form-grid">
                                    <div class="form-group full-width">
                                        <label>Senha Atual</label>
                                        <input type="password" class="form-control" placeholder="Digite sua senha atual">
                                    </div>
                                    <div class="form-group">
                                        <label>Nova Senha</label>
                                        <input type="password" class="form-control" placeholder="Digite a nova senha">
                                    </div>
                                    <div class="form-group">
                                        <label>Confirmar Nova Senha</label>
                                        <input type="password" class="form-control" placeholder="Confirme a nova senha">
                                    </div>
                                </div>
                                <button class="btn-primary">
                                    <span class="material-symbols-outlined">lock</span>
                                    Alterar Senha
                                </button>
                            </div>

                            <div class="settings-card">
                                <h3>Autenticação de Dois Fatores</h3>
                                <div class="two-factor-section">
                                    <div class="two-factor-info">
                                        <p>Adicione uma camada extra de segurança à sua conta</p>
                                        <div class="two-factor-status">
                                            <span class="status-indicator disabled"></span>
                                            <span>Desabilitado</span>
                                        </div>
                                    </div>
                                    <button class="btn-outline">
                                        <span class="material-symbols-outlined">security</span>
                                        Configurar 2FA
                                    </button>
                                </div>
                            </div>

                            <div class="settings-card">
                                <h3>Sessões Ativas</h3>
                                <div class="sessions-list">
                                    <div class="session-item">
                                        <div class="session-info">
                                            <div class="session-device">
                                                <span class="material-symbols-outlined">computer</span>
                                                <div>
                                                    <h4>Windows - Chrome</h4>
                                                    <p>São Paulo, Brasil • Ativo agora</p>
                                                </div>
                                            </div>
                                            <span class="current-session">Sessão atual</span>
                                        </div>
                                    </div>
                                    <div class="session-item">
                                        <div class="session-info">
                                            <div class="session-device">
                                                <span class="material-symbols-outlined">smartphone</span>
                                                <div>
                                                    <h4>iPhone - Safari</h4>
                                                    <p>São Paulo, Brasil • Há 2 horas</p>
                                                </div>
                                            </div>
                                            <button class="btn-outline btn-sm">Encerrar</button>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn-outline text-danger">
                                    <span class="material-symbols-outlined">logout</span>
                                    Encerrar Todas as Sessões
                                </button>
                            </div>
                        </div>

                        <!-- Preferences Section -->
                        <div class="settings-section" id="preferences">
                            <div class="section-header">
                                <h2>Preferências do Sistema</h2>
                                <p>Personalize sua experiência no sistema</p>
                            </div>

                            <div class="settings-card">
                                <h3>Aparência</h3>
                                <div class="preference-options">
                                    <div class="preference-item">
                                        <div class="preference-info">
                                            <h4>Tema</h4>
                                            <p>Escolha entre tema claro ou escuro</p>
                                        </div>
                                        <select class="form-control">
                                            <option value="light" selected>Claro</option>
                                            <option value="dark">Escuro</option>
                                            <option value="auto">Automático</option>
                                        </select>
                                    </div>
                                    <div class="preference-item">
                                        <div class="preference-info">
                                            <h4>Idioma</h4>
                                            <p>Selecione o idioma da interface</p>
                                        </div>
                                        <select class="form-control">
                                            <option value="pt-br" selected>Português (Brasil)</option>
                                            <option value="en">English</option>
                                            <option value="es">Español</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-card">
                                <h3>Dashboard</h3>
                                <div class="preference-options">
                                    <div class="preference-item">
                                        <div class="preference-info">
                                            <h4>Página Inicial</h4>
                                            <p>Defina qual página abrir ao fazer login</p>
                                        </div>
                                        <select class="form-control">
                                            <option value="dashboard" selected>Dashboard</option>
                                            <option value="grades">Notas</option>
                                            <option value="attendance">Frequência</option>
                                            <option value="messages">Mensagens</option>
                                        </select>
                                    </div>
                                    <div class="preference-item">
                                        <div class="preference-info">
                                            <h4>Itens por Página</h4>
                                            <p>Número de itens exibidos em listas</p>
                                        </div>
                                        <select class="form-control">
                                            <option value="10">10</option>
                                            <option value="20" selected>20</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Section -->
                        <div class="settings-section" id="account">
                            <div class="section-header">
                                <h2>Configurações da Conta</h2>
                                <p>Gerencie sua conta e dados</p>
                            </div>

                            <div class="settings-card">
                                <h3>Informações da Conta</h3>
                                <div class="account-info">
                                    <div class="info-item">
                                        <span class="label">ID da Conta:</span>
                                        <span class="value">ENC-2025-001234</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label">Membro desde:</span>
                                        <span class="value">Janeiro 2023</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label">Último acesso:</span>
                                        <span class="value">Hoje às 14:30</span>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-card">
                                <h3>Exportar Dados</h3>
                                <p>Baixe uma cópia dos seus dados pessoais e dos seus filhos</p>
                                <div class="export-options">
                                    <button class="btn-outline">
                                        <span class="material-symbols-outlined">download</span>
                                        Exportar Dados Pessoais
                                    </button>
                                    <button class="btn-outline">
                                        <span class="material-symbols-outlined">download</span>
                                        Exportar Histórico Escolar
                                    </button>
                                </div>
                            </div>

                            <div class="settings-card danger-zone">
                                <h3>Zona de Perigo</h3>
                                <div class="danger-actions">
                                    <div class="danger-item">
                                        <div class="danger-info">
                                            <h4>Desativar Conta</h4>
                                            <p>Desative temporariamente sua conta. Você pode reativá-la a qualquer momento.</p>
                                        </div>
                                        <button class="btn-outline text-danger">Desativar</button>
                                    </div>
                                    <div class="danger-item">
                                        <div class="danger-info">
                                            <h4>Excluir Conta</h4>
                                            <p>Exclua permanentemente sua conta e todos os dados associados. Esta ação não pode ser desfeita.</p>
                                        </div>
                                        <button class="btn-outline text-danger">Excluir Conta</button>
                                    </div>
                                </div>
                            </div>
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

        // Settings navigation
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                const section = this.dataset.section;
                
                // Update navigation
                document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
                
                // Update content
                document.querySelectorAll('.settings-section').forEach(sec => sec.classList.remove('active'));
                document.getElementById(section).classList.add('active');
            });
        });

        // Save all settings
        document.getElementById('saveAllBtn').addEventListener('click', function() {
            // Simulate saving
            this.innerHTML = '<span class="material-symbols-outlined">check</span>Salvo!';
            this.style.backgroundColor = '#4caf50';
            
            setTimeout(() => {
                this.innerHTML = '<span class="material-symbols-outlined">save</span>Salvar Alterações';
                this.style.backgroundColor = '';
            }, 2000);
        });
    </script>

    <style>
        .settings-layout {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 20px;
        }

        .settings-nav {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px 0;
            height: fit-content;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background-color: var(--secondary-color);
        }

        .nav-item.active {
            background-color: var(--secondary-color);
            border-left-color: var(--primary-color);
            color: var(--primary-color);
        }

        .nav-item .material-symbols-outlined {
            margin-right: 15px;
        }

        .settings-content {
            position: relative;
        }

        .settings-section {
            display: none;
        }

        .settings-section.active {
            display: block;
        }

        .section-header {
            margin-bottom: 30px;
        }

        .section-header h2 {
            margin-bottom: 5px;
            font-size: 1.5rem;
        }

        .section-header p {
            color: var(--text-light);
        }

        .settings-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            margin-bottom: 20px;
        }

        .settings-card h3 {
            margin-bottom: 20px;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 111, 220, 0.1);
        }

        .profile-photo-section {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .current-photo img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--border-color);
        }

        .photo-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .notification-options {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .notification-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-info h4 {
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .notification-info p {
            color: var(--text-light);
            font-size: 0.9rem;
            margin: 0;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--primary-color);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .two-factor-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .two-factor-status {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .status-indicator.disabled {
            background-color: #f44336;
        }

        .status-indicator.enabled {
            background-color: #4caf50;
        }

        .sessions-list {
            margin-bottom: 20px;
        }

        .session-item {
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .session-item:last-child {
            border-bottom: none;
        }

        .session-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .session-device {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .session-device .material-symbols-outlined {
            color: var(--text-light);
            font-size: 24px;
        }

        .session-device h4 {
            margin-bottom: 3px;
            font-size: 0.95rem;
        }

        .session-device p {
            color: var(--text-light);
            font-size: 0.85rem;
            margin: 0;
        }

        .current-session {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .preference-options {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .preference-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .preference-info {
            flex: 1;
            margin-right: 20px;
        }

        .preference-info h4 {
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .preference-info p {
            color: var(--text-light);
            font-size: 0.9rem;
            margin: 0;
        }

        .preference-item .form-control {
            width: 200px;
        }

        .account-info {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item .label {
            color: var(--text-light);
        }

        .info-item .value {
            font-weight: 500;
        }

        .export-options {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .danger-zone {
            border: 1px solid #f44336;
            background-color: rgba(244, 67, 54, 0.02);
        }

        .danger-zone h3 {
            color: #f44336;
        }

        .danger-actions {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .danger-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(244, 67, 54, 0.2);
        }

        .danger-item:last-child {
            border-bottom: none;
        }

        .danger-info h4 {
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .danger-info p {
            color: var(--text-light);
            font-size: 0.9rem;
            margin: 0;
        }

        .text-danger {
            color: #f44336 !important;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .settings-layout {
                grid-template-columns: 1fr;
            }

            .settings-nav {
                display: flex;
                overflow-x: auto;
                padding: 10px;
            }

            .nav-item {
                white-space: nowrap;
                border-left: none;
                border-bottom: 3px solid transparent;
            }

            .nav-item.active {
                border-left: none;
                border-bottom-color: var(--primary-color);
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .profile-photo-section {
                flex-direction: column;
                text-align: center;
            }

            .preference-item,
            .danger-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .preference-item .form-control {
                width: 100%;
            }
        }
    </style>
</body>
</html>