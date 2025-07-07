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

    /* Buscar mensagens
    $mensagens = [];
    try {
        $stmt = $conn->prepare("
            SELECT 
                m.id,
                m.remetente_id,
                m.destinatario_id,
                m.assunto,
                m.conteudo,
                m.data_envio,
                m.lida,
                m.tipo_mensagem,
                m.prioridade,
                u.nome as remetente_nome,
                u.tipo as remetente_tipo
            FROM mensagens m 
            LEFT JOIN usuarios u ON m.remetente_id = u.id
            WHERE m.destinatario_id = :professor_id OR m.remetente_id = :professor_id
            ORDER BY m.data_envio DESC
        ");
        $stmt->bindParam(':professor_id', $idTeacher, PDO::PARAM_INT);
        $stmt->execute();
        $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao buscar mensagens: " . $e->getMessage();
    }
*/
    $dataAtual = '15 de Abril de 2025';
    $trimestre = '2º trimestre';
    $anoLetivo = '2025';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mensagens - Dashboard de Professores</title>
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<style>
    /* Estilos para o sistema de mensagens */
    .messages-container {
        display: flex;
        height: calc(100vh - 140px);
        gap: 0;
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .messages-sidebar {
        width: 350px;
        flex-shrink: 0;
        border-right: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
        background-color: #f9f9f9;
    }
    
    .messages-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: #fff;
    }
    
    .messages-header {
        padding: 20px;
        border-bottom: 1px solid #e0e0e0;
        background-color: #fff;
    }
    
    .messages-header h2 {
        color: #444;
        margin: 0 0 15px 0;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .messages-header .material-symbols-outlined {
        color: #3a5bb9;
        font-size: 1.5rem;
    }
    
    .messages-search {
        position: relative;
        margin-bottom: 15px;
    }
    
    .messages-search input {
        width: 100%;
        padding: 10px 40px 10px 15px;
        border: 1px solid #ddd;
        border-radius: 25px;
        font-size: 0.9rem;
        background-color: #f5f5f5;
    }
    
    .messages-search .material-symbols-outlined {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        font-size: 1.2rem;
    }
    
    .messages-filters {
        display: flex;
        gap: 5px;
        margin-bottom: 10px;
    }
    
    .filter-btn {
        padding: 6px 12px;
        border: 1px solid #ddd;
        background-color: #fff;
        color: #666;
        cursor: pointer;
        font-size: 0.8rem;
        border-radius: 15px;
        transition: all 0.2s ease;
    }
    
    .filter-btn:hover {
        background-color: #f0f0f0;
    }
    
    .filter-btn.active {
        background-color: #3a5bb9;
        color: white;
        border-color: #3a5bb9;
    }
    
    .conversations-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px 0;
    }
    
    .conversation-item {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    
    .conversation-item:hover {
        background-color: #f5f5f5;
    }
    
    .conversation-item.active {
        background-color: #e3f2fd;
        border-right: 3px solid #3a5bb9;
    }
    
    .conversation-item.unread {
        background-color: #fff3e0;
        border-left: 3px solid #ff9800;
    }
    
    .conversation-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 5px;
    }
    
    .conversation-name {
        font-weight: 600;
        color: #444;
        font-size: 0.95rem;
    }
    
    .conversation-time {
        font-size: 0.75rem;
        color: #666;
    }
    
    .conversation-preview {
        color: #666;
        font-size: 0.85rem;
        line-height: 1.3;
        margin-bottom: 5px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .conversation-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .conversation-type {
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
    }
    
    .type-aluno { background-color: #2196f3; }
    .type-responsavel { background-color: #4caf50; }
    .type-professor { background-color: #ff9800; }
    .type-coordenacao { background-color: #9c27b0; }
    .type-sistema { background-color: #607d8b; }
    
    .unread-badge {
        background-color: #f44336;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }
    
    .priority-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    
    .priority-high { background-color: #f44336; }
    .priority-medium { background-color: #ff9800; }
    .priority-low { background-color: #4caf50; }
    
    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .chat-header {
        padding: 20px;
        border-bottom: 1px solid #e0e0e0;
        background-color: #f9f9f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .chat-contact-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .contact-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .contact-details h3 {
        margin: 0;
        color: #444;
        font-size: 1.1rem;
    }
    
    .contact-details p {
        margin: 2px 0 0 0;
        color: #666;
        font-size: 0.85rem;
    }
    
    .chat-actions {
        display: flex;
        gap: 10px;
    }
    
    .chat-btn {
        padding: 8px 12px;
        border: 1px solid #ddd;
        background-color: #fff;
        color: #444;
        cursor: pointer;
        border-radius: 4px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .chat-btn:hover {
        background-color: #f5f5f5;
    }
    
    .messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background-color: #fafafa;
    }
    
    .message-bubble {
        max-width: 70%;
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
    }
    
    .message-bubble.sent {
        align-self: flex-end;
        align-items: flex-end;
    }
    
    .message-bubble.received {
        align-self: flex-start;
        align-items: flex-start;
    }
    
    .message-content {
        padding: 12px 16px;
        border-radius: 18px;
        font-size: 0.9rem;
        line-height: 1.4;
        word-wrap: break-word;
    }
    
    .message-bubble.sent .message-content {
        background-color: #3a5bb9;
        color: white;
        border-bottom-right-radius: 4px;
    }
    
    .message-bubble.received .message-content {
        background-color: #fff;
        color: #444;
        border: 1px solid #e0e0e0;
        border-bottom-left-radius: 4px;
    }
    
    .message-time {
        font-size: 0.7rem;
        color: #666;
        margin-top: 4px;
        padding: 0 5px;
    }
    
    .message-status {
        font-size: 0.7rem;
        color: #666;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 3px;
    }
    
    .compose-area {
        padding: 20px;
        border-top: 1px solid #e0e0e0;
        background-color: #fff;
    }
    
    .compose-form {
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }
    
    .compose-input {
        flex: 1;
        min-height: 40px;
        max-height: 120px;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 20px;
        resize: none;
        font-family: inherit;
        font-size: 0.9rem;
        line-height: 1.4;
    }
    
    .compose-input:focus {
        outline: none;
        border-color: #3a5bb9;
    }
    
    .compose-actions {
        display: flex;
        gap: 5px;
    }
    
    .compose-btn {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    
    .attach-btn {
        background-color: #f5f5f5;
        color: #666;
    }
    
    .attach-btn:hover {
        background-color: #e0e0e0;
    }
    
    .send-btn {
        background-color: #3a5bb9;
        color: white;
    }
    
    .send-btn:hover {
        background-color: #2d4494;
    }
    
    .send-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
    
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #666;
        text-align: center;
    }
    
    .empty-state .material-symbols-outlined {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        margin-bottom: 10px;
        color: #444;
    }
    
    .compose-modal {
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
    
    .compose-modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 30px;
        border-radius: 10px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .compose-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .compose-modal-header h2 {
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
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #444;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.9rem;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #3a5bb9;
    }
    
    .form-control.textarea {
        min-height: 120px;
        resize: vertical;
        font-family: inherit;
    }
    
    .recipient-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 5px;
    }
    
    .recipient-tag {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .recipient-tag .remove-tag {
        cursor: pointer;
        font-weight: bold;
    }
    
    .btn-group {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }
    
    .btn {
        padding: 10px 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background-color: #3a5bb9;
        color: white;
        border-color: #3a5bb9;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
        border-color: #6c757d;
    }
    
    .quick-replies {
        display: flex;
        gap: 5px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    
    .quick-reply {
        padding: 4px 8px;
        background-color: #f0f0f0;
        border: 1px solid #ddd;
        border-radius: 12px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .quick-reply:hover {
        background-color: #e0e0e0;
    }
    
    @media (max-width: 768px) {
        .messages-container {
            flex-direction: column;
            height: auto;
        }
        
        .messages-sidebar {
            width: 100%;
            max-height: 300px;
        }
        
        .message-bubble {
            max-width: 85%;
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
                <li class="active">
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
                <input type="text" placeholder="Pesquisar mensagens...">
            </div>
            <div class="top-bar-actions">
                <span class="material-symbols-outlined notification">notifications</span>
                <span class="material-symbols-outlined">help</span>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="page-header">
                <h1>Central de Mensagens</h1>
                <button class="btn-primary" onclick="openComposeModal()">
                    <span class="material-symbols-outlined">edit</span>
                    Nova Mensagem
                </button>
            </div>
            
            <div class="messages-container">
                <!-- Sidebar de Conversas -->
                <div class="messages-sidebar">
                    <div class="messages-header">
                        <h2>
                            <span class="material-symbols-outlined">forum</span>
                            Conversas
                        </h2>
                        <div class="messages-search">
                            <input type="text" placeholder="Buscar conversas..." id="searchConversations">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <div class="messages-filters">
                            <button class="filter-btn active" onclick="filterMessages('todas')">Todas</button>
                            <button class="filter-btn" onclick="filterMessages('nao-lidas')">Não lidas</button>
                            <button class="filter-btn" onclick="filterMessages('importantes')">Importantes</button>
                        </div>
                    </div>
                    
                    <div class="conversations-list" id="conversationsList">
                        <!-- Conversas serão carregadas dinamicamente -->
                    </div>
                </div>
                
                <!-- Área Principal de Chat -->
                <div class="messages-main">
                    <div class="empty-state" id="emptyState">
                        <span class="material-symbols-outlined">chat_bubble_outline</span>
                        <h3>Selecione uma conversa</h3>
                        <p>Escolha uma conversa da lista para começar a trocar mensagens</p>
                    </div>
                    
                    <div class="chat-area" id="chatArea" style="display: none;">
                        <div class="chat-header">
                            <div class="chat-contact-info">
                                <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face" alt="Contato" class="contact-avatar" id="contactAvatar">
                                <div class="contact-details">
                                    <h3 id="contactName">Nome do Contato</h3>
                                    <p id="contactType">Tipo de Contato</p>
                                </div>
                            </div>
                            <div class="chat-actions">
                                <button class="chat-btn" onclick="toggleChatInfo()">
                                    <span class="material-symbols-outlined">info</span>
                                    Info
                                </button>
                                <button class="chat-btn" onclick="archiveConversation()">
                                    <span class="material-symbols-outlined">archive</span>
                                    Arquivar
                                </button>
                            </div>
                        </div>
                        
                        <div class="messages-area" id="messagesArea">
                            <!-- Mensagens serão carregadas dinamicamente -->
                        </div>
                        
                        <div class="compose-area">
                            <div class="quick-replies">
                                <span class="quick-reply" onclick="insertQuickReply('Obrigado pela mensagem!')">Obrigado</span>
                                <span class="quick-reply" onclick="insertQuickReply('Vou verificar e retorno em breve.')">Vou verificar</span>
                                <span class="quick-reply" onclick="insertQuickReply('Podemos agendar uma reunião?')">Agendar reunião</span>
                                <span class="quick-reply" onclick="insertQuickReply('Preciso de mais informações.')">Mais info</span>
                            </div>
                            <form class="compose-form" onsubmit="sendMessage(event)">
                                <textarea class="compose-input" id="messageInput" placeholder="Digite sua mensagem..." rows="1"></textarea>
                                <div class="compose-actions">
                                    <button type="button" class="compose-btn attach-btn" onclick="attachFile()">
                                        <span class="material-symbols-outlined">attach_file</span>
                                    </button>
                                    <button type="submit" class="compose-btn send-btn" id="sendBtn" disabled>
                                        <span class="material-symbols-outlined">send</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal de Nova Mensagem -->
<div id="composeModal" class="compose-modal">
    <div class="compose-modal-content">
        <div class="compose-modal-header">
            <h2>Nova Mensagem</h2>
            <button class="close-modal" onclick="closeComposeModal()">&times;</button>
        </div>
        <div class="compose-modal-body">
            <form id="composeForm" onsubmit="sendNewMessage(event)">
                <div class="form-group">
                    <label for="recipientType">Tipo de Destinatário</label>
                    <select id="recipientType" class="form-control" onchange="loadRecipients()">
                        <option value="">Selecione o tipo</option>
                        <option value="aluno">Aluno</option>
                        <option value="responsavel">Responsável</option>
                        <option value="professor">Professor</option>
                        <option value="coordenacao">Coordenação</option>
                        <option value="turma">Turma Completa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="recipients">Destinatários</label>
                    <select id="recipients" class="form-control" multiple>
                        <!-- Opções serão carregadas dinamicamente -->
                    </select>
                    <div class="recipient-tags" id="recipientTags">
                        <!-- Tags dos destinatários selecionados -->
                    </div>
                </div>
                <div class="form-group">
                    <label for="messageSubject">Assunto</label>
                    <input type="text" id="messageSubject" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="messagePriority">Prioridade</label>
                    <select id="messagePriority" class="form-control">
                        <option value="baixa">Baixa</option>
                        <option value="media" selected>Média</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="messageContent">Mensagem</label>
                    <textarea id="messageContent" class="form-control textarea" required placeholder="Digite sua mensagem..."></textarea>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" onclick="closeComposeModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="dashboard-data.js"></script>
<script>
    // Dados simulados para mensagens
    const conversasSimuladas = [
        {
            id: 1,
            nome: "Maria Silva Santos",
            tipo: "responsavel",
            avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face",
            ultimaMensagem: "Gostaria de saber sobre o desempenho da Ana em Matemática.",
            horario: "14:30",
            naoLida: true,
            prioridade: "media",
            mensagens: [
                {
                    id: 1,
                    conteudo: "Boa tarde, professor! Gostaria de saber sobre o desempenho da Ana em Matemática.",
                    enviada: false,
                    horario: "14:30",
                    data: "2025-04-15"
                }
            ]
        },
        {
            id: 2,
            nome: "João Pedro Costa",
            tipo: "aluno",
            avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face",
            ultimaMensagem: "Professor, não consegui entender o exercício 5 da lista.",
            horario: "13:45",
            naoLida: false,
            prioridade: "baixa",
            mensagens: [
                {
                    id: 1,
                    conteudo: "Professor, não consegui entender o exercício 5 da lista.",
                    enviada: false,
                    horario: "13:45",
                    data: "2025-04-15"
                },
                {
                    id: 2,
                    conteudo: "Olá João! Vou explicar passo a passo. O exercício 5 trata de equações do segundo grau...",
                    enviada: true,
                    horario: "14:10",
                    data: "2025-04-15"
                }
            ]
        },
        {
            id: 3,
            nome: "Coordenação Pedagógica",
            tipo: "coordenacao",
            avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face",
            ultimaMensagem: "Reunião de planejamento agendada para quinta-feira às 15h.",
            horario: "12:20",
            naoLida: true,
            prioridade: "alta",
            mensagens: [
                {
                    id: 1,
                    conteudo: "Reunião de planejamento agendada para quinta-feira às 15h. Favor confirmar presença.",
                    enviada: false,
                    horario: "12:20",
                    data: "2025-04-15"
                }
            ]
        },
        {
            id: 4,
            nome: "Prof. Carlos Mendes",
            tipo: "professor",
            avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face",
            ultimaMensagem: "Podemos trocar algumas aulas na próxima semana?",
            horario: "11:15",
            naoLida: false,
            prioridade: "media",
            mensagens: [
                {
                    id: 1,
                    conteudo: "Oi! Podemos trocar algumas aulas na próxima semana? Preciso resolver uma questão pessoal.",
                    enviada: false,
                    horario: "11:15",
                    data: "2025-04-15"
                },
                {
                    id: 2,
                    conteudo: "Claro, Carlos! Qual dia você precisa? Podemos ajustar o cronograma.",
                    enviada: true,
                    horario: "11:30",
                    data: "2025-04-15"
                }
            ]
        },
        {
            id: 5,
            nome: "Turma 9º Ano A",
            tipo: "turma",
            avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&h=150&fit=crop&crop=face",
            ultimaMensagem: "Lembrete: Prova de Matemática na próxima sexta-feira.",
            horario: "10:00",
            naoLida: false,
            prioridade: "media",
            mensagens: [
                {
                    id: 1,
                    conteudo: "Bom dia, turma! Lembrete: Prova de Matemática na próxima sexta-feira. Estudem os capítulos 5 e 6.",
                    enviada: true,
                    horario: "10:00",
                    data: "2025-04-15"
                }
            ]
        }
    ];

    let conversaAtiva = null;
    let filtroAtivo = 'todas';

    // Inicializar sistema de mensagens
    function initMessages() {
        renderConversations();
        setupEventListeners();
    }

    function renderConversations() {
        const container = document.getElementById('conversationsList');
        container.innerHTML = '';

        const conversasFiltradas = conversasSimuladas.filter(conversa => {
            if (filtroAtivo === 'nao-lidas') return conversa.naoLida;
            if (filtroAtivo === 'importantes') return conversa.prioridade === 'alta';
            return true;
        });

        conversasFiltradas.forEach(conversa => {
            const conversaElement = document.createElement('div');
            conversaElement.className = `conversation-item ${conversa.naoLida ? 'unread' : ''} ${conversaAtiva?.id === conversa.id ? 'active' : ''}`;
            
            conversaElement.innerHTML = `
                ${conversa.prioridade === 'alta' ? '<div class="priority-indicator priority-high"></div>' : ''}
                ${conversa.prioridade === 'media' ? '<div class="priority-indicator priority-medium"></div>' : ''}
                <div class="conversation-header">
                    <span class="conversation-name">${conversa.nome}</span>
                    <span class="conversation-time">${conversa.horario}</span>
                </div>
                <div class="conversation-preview">${conversa.ultimaMensagem}</div>
                <div class="conversation-meta">
                    <span class="conversation-type type-${conversa.tipo}">${getTipoLabel(conversa.tipo)}</span>
                    ${conversa.naoLida ? '<span class="unread-badge">1</span>' : ''}
                </div>
            `;
            
            conversaElement.onclick = () => openConversation(conversa);
            container.appendChild(conversaElement);
        });
    }

    function getTipoLabel(tipo) {
        const labels = {
            'aluno': 'Aluno',
            'responsavel': 'Responsável',
            'professor': 'Professor',
            'coordenacao': 'Coordenação',
            'turma': 'Turma',
            'sistema': 'Sistema'
        };
        return labels[tipo] || tipo;
    }

    function openConversation(conversa) {
        conversaAtiva = conversa;
        conversa.naoLida = false; // Marcar como lida
        
        // Atualizar interface
        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('chatArea').style.display = 'flex';
        
        // Atualizar cabeçalho do chat
        document.getElementById('contactAvatar').src = conversa.avatar;
        document.getElementById('contactName').textContent = conversa.nome;
        document.getElementById('contactType').textContent = getTipoLabel(conversa.tipo);
        
        // Renderizar mensagens
        renderMessages(conversa.mensagens);
        
        // Atualizar lista de conversas
        renderConversations();
    }

    function renderMessages(mensagens) {
        const container = document.getElementById('messagesArea');
        container.innerHTML = '';
        
        mensagens.forEach(mensagem => {
            const messageElement = document.createElement('div');
            messageElement.className = `message-bubble ${mensagem.enviada ? 'sent' : 'received'}`;
            
            messageElement.innerHTML = `
                <div class="message-content">${mensagem.conteudo}</div>
                <div class="message-time">${mensagem.horario}</div>
                ${mensagem.enviada ? '<div class="message-status"><span class="material-symbols-outlined">done_all</span> Entregue</div>' : ''}
            `;
            
            container.appendChild(messageElement);
        });
        
        // Scroll para a última mensagem
        container.scrollTop = container.scrollHeight;
    }

    function sendMessage(event) {
        event.preventDefault();
        
        const input = document.getElementById('messageInput');
        const conteudo = input.value.trim();
        
        if (!conteudo || !conversaAtiva) return;
        
        // Criar nova mensagem
        const novaMensagem = {
            id: Date.now(),
            conteudo: conteudo,
            enviada: true,
            horario: new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }),
            data: new Date().toISOString().split('T')[0]
        };
        
        // Adicionar à conversa ativa
        conversaAtiva.mensagens.push(novaMensagem);
        conversaAtiva.ultimaMensagem = conteudo;
        conversaAtiva.horario = novaMensagem.horario;
        
        // Atualizar interface
        renderMessages(conversaAtiva.mensagens);
        renderConversations();
        
        // Limpar input
        input.value = '';
        updateSendButton();
    }

    function filterMessages(filtro) {
        filtroAtivo = filtro;
        
        // Atualizar botões de filtro
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        // Renderizar conversas filtradas
        renderConversations();
    }

    function setupEventListeners() {
        const messageInput = document.getElementById('messageInput');
        
        // Auto-resize do textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            updateSendButton();
        });
        
        // Enviar com Enter (Shift+Enter para nova linha)
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim()) {
                    document.querySelector('.compose-form').dispatchEvent(new Event('submit'));
                }
            }
        });
        
        // Busca de conversas
        document.getElementById('searchConversations').addEventListener('input', function() {
            const termo = this.value.toLowerCase();
            const conversas = document.querySelectorAll('.conversation-item');
            
            conversas.forEach(conversa => {
                const nome = conversa.querySelector('.conversation-name').textContent.toLowerCase();
                const preview = conversa.querySelector('.conversation-preview').textContent.toLowerCase();
                
                if (nome.includes(termo) || preview.includes(termo)) {
                    conversa.style.display = 'block';
                } else {
                    conversa.style.display = 'none';
                }
            });
        });
    }

    function updateSendButton() {
        const input = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        
        if (input.value.trim()) {
            sendBtn.disabled = false;
        } else {
            sendBtn.disabled = true;
        }
    }

    function insertQuickReply(texto) {
        const input = document.getElementById('messageInput');
        input.value = texto;
        input.focus();
        updateSendButton();
    }

    function attachFile() {
        alert('Funcionalidade de anexar arquivo será implementada');
    }

    function toggleChatInfo() {
        alert('Informações do contato serão exibidas');
    }

    function archiveConversation() {
        if (confirm('Deseja arquivar esta conversa?')) {
            alert('Conversa arquivada com sucesso!');
        }
    }

    // Funções do modal de nova mensagem
    function openComposeModal() {
        document.getElementById('composeModal').style.display = 'block';
    }

    function closeComposeModal() {
        document.getElementById('composeModal').style.display = 'none';
        document.getElementById('composeForm').reset();
    }

    function loadRecipients() {
        const tipo = document.getElementById('recipientType').value;
        const select = document.getElementById('recipients');
        
        select.innerHTML = '';
        
        const opcoes = {
            'aluno': [
                { value: '1', text: 'Ana Beatriz Silva - 9º Ano A' },
                { value: '2', text: 'Bruno Santos Costa - 9º Ano A' },
                { value: '3', text: 'Carla Oliveira Mendes - 10º Ano B' }
            ],
            'responsavel': [
                { value: '1', text: 'Maria Silva Santos (Mãe da Ana)' },
                { value: '2', text: 'José Costa Lima (Pai do Bruno)' },
                { value: '3', text: 'Carmen Mendes (Mãe da Carla)' }
            ],
            'professor': [
                { value: '1', text: 'Prof. Carlos Mendes - Física' },
                { value: '2', text: 'Profa. Elena Rodriguez - Química' },
                { value: '3', text: 'Prof. Diego Ferreira - História' }
            ],
            'coordenacao': [
                { value: '1', text: 'Coordenação Pedagógica' },
                { value: '2', text: 'Direção Escolar' }
            ],
            'turma': [
                { value: '1', text: '9º Ano A - Matemática' },
                { value: '2', text: '10º Ano B - Física' },
                { value: '3', text: '11º Ano C - Química' }
            ]
        };
        
        if (opcoes[tipo]) {
            opcoes[tipo].forEach(opcao => {
                const option = document.createElement('option');
                option.value = opcao.value;
                option.textContent = opcao.text;
                select.appendChild(option);
            });
        }
    }

    function sendNewMessage(event) {
        event.preventDefault();
        
        const assunto = document.getElementById('messageSubject').value;
        const conteudo = document.getElementById('messageContent').value;
        const prioridade = document.getElementById('messagePriority').value;
        
        // Simular envio
        alert(`Mensagem enviada com sucesso!\nAssunto: ${assunto}\nPrioridade: ${prioridade}`);
        
        closeComposeModal();
    }

    // Fechar modal ao clicar fora
    window.onclick = function(event) {
        const modal = document.getElementById('composeModal');
        if (event.target == modal) {
            closeComposeModal();
        }
    }

    // Toggle sidebar on mobile
    document.getElementById('menuToggle')?.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('collapsed');
        document.querySelector('.content').classList.toggle('expanded');
    });

    // Inicializar quando a página carregar
    document.addEventListener('DOMContentLoaded', function() {
        initMessages();
    });
</script>
</body>
</html>