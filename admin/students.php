<?php 
  session_start();

  
// Configurações para evitar cache e voltar após logout
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Script para impedir voltar após logout
echo '<script type="text/javascript">
        history.pushState(null, null, location.href);
        window.onpopstate = function() {
            history.go(1);
        };
      </script>';

if (isset($_SESSION['id']) && isset($_SESSION['role'])) {
    include('dbconnection.php');

    // Buscar turmas
    $sqlTurmas = "SELECT id, class_name, class_grade FROM turma ORDER BY class_grade, class_name";
    $stmtTurmas = $conn->query($sqlTurmas);
    $turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);

    // Buscar encarregados
    $sqlEncarregados = "SELECT id, fname, lname FROM encarregados ORDER BY fname";
    $stmtEncarregados = $conn->query($sqlEncarregados);
    $encarregados = $stmtEncarregados->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        // Coleta e sanitização dos dados
        $fname = htmlspecialchars(trim($_POST['fname']));
        $lname = htmlspecialchars(trim($_POST['lname']));
        $genero = $_POST['genero'] ?? '';
        $data_nascimento = $_POST['dataa'];
        $numbi = $_POST['numbi'];
        $endereco = htmlspecialchars(trim($_POST['endereco']));
        $telefone = $_POST['telefone'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $encarregado_id = $_POST['encarregado'] ?? null;
        $area = $_POST['area'] ?? null;
        $password_raw = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $status = $_POST['status'] ?? 'ativo';  // Novo campo status

        // Validação simples do status
        if (!in_array($status, ['ativo', 'inativo'])) {
            $status = 'ativo';
        }

        // Validações
        if (empty($fname) || empty($lname) || empty($genero) || empty($data_nascimento) || empty($numbi) || empty($endereco) || empty($telefone) || empty($email) || empty($encarregado_id) || empty($area)) {
            $_SESSION['error'] = "Todos os campos obrigatórios devem ser preenchidos.";
            header("Location: students.php");
            exit;
        }

        if (empty($password_raw)) {
            $_SESSION['error'] = "A senha não pode estar vazia.";
            header("Location: students.php");
            exit;
        }

        if ($password_raw !== $confirm_password) {
            $_SESSION['error'] = "As senhas não coincidem.";
            header("Location: students.php");
            exit;
        }

        if (!preg_match('/^\d{7}[A-Za-z]{2}\d{3}$/', $numbi)) {
            $_SESSION['error'] = "Formato do BI inválido. Use o formato: 0000000LA000";
            header("Location: students.php");
            exit;
        }

        if (!preg_match('/^\d{9}$/', $telefone)) {
            $_SESSION['error'] = "Número de telefone inválido. Use 9 dígitos.";
            header("Location: students.php");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Email inválido.";
            header("Location: students.php");
            exit;
        }

        // Upload de arquivos
        if (!isset($_FILES['foto_bi1'], $_FILES['foto_bi2'], $_FILES['fotoperfil'])) {
            $_SESSION['error'] = "Faltam arquivos para upload.";
            header("Location: students.php");
            exit;
        }

        function uploadFile($file, $targetDir = "uploads/", $allowedTypes = ['jpg', 'jpeg', 'png']) {
            if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
                return false;
            }

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedTypes)) {
                return false;
            }

            $uniqueName = uniqid('file_', true) . '.' . $fileExtension;
            $destination = $targetDir . $uniqueName;

            return move_uploaded_file($file['tmp_name'], $destination) ? $destination : false;
        }

        $uploadDir = "uploads/students/";
        $foto_bi1 = uploadFile($_FILES['foto_bi1'], $uploadDir);
        $foto_bi2 = uploadFile($_FILES['foto_bi2'], $uploadDir);
        $fotoperfil = uploadFile($_FILES['fotoperfil'], $uploadDir);

        if (!$foto_bi1 || !$foto_bi2 || !$fotoperfil) {
            $_SESSION['error'] = "Erro ao carregar as imagens. Use formatos JPG, JPEG ou PNG.";
            header("Location: students.php");
            exit;
        }

        // Hash da senha
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        // Inserção no banco de dados com o campo status incluído
        $sql = "INSERT INTO estudantes (
            fname, lname, genero, data_nascimento, num_bi, foto_bi1, foto_bi2, endereco,
            fotoperfil, telefone, email, encarregado_id, area, password, status
        ) VALUES (
            :fname, :lname, :genero, :data_nascimento, :numbi, :foto_bi1, :foto_bi2, :endereco,
            :fotoperfil, :telefone, :email, :encarregado_id, :area, :password, :status
        )";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':data_nascimento', $data_nascimento);
        $stmt->bindParam(':numbi', $numbi);
        $stmt->bindParam(':foto_bi1', $foto_bi1);
        $stmt->bindParam(':foto_bi2', $foto_bi2);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':fotoperfil', $fotoperfil);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':encarregado_id', $encarregado_id);
        $stmt->bindParam(':area', $area);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Aluno adicionado com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao adicionar aluno.";
        }
        
        header("Location: students.php");
        exit;
    }
     // --- BUSCAR LISTAGEM DE PROFESSORES ---
    try {
        $listStmt = $conn->prepare("SELECT id, fname, lname, genero, telefone, email, especializacao, nivel_academico FROM professores ORDER BY fname ASC");
        $listStmt->execute();
        $teachers = $listStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $teachers = [];
        $_SESSION['error'] = "Erro ao buscar professores: " . $e->getMessage();
    }

    // --- BUSCAR LISTAGEM DE ALUNOS COM STATUS ---
    try {
        $alunosStmt = $conn->prepare("
            SELECT 
                e.id, e.fname, e.lname, e.genero, e.data_nascimento, e.num_bi, e.endereco, 
                e.telefone, e.email, e.status, t.class_name, t.class_grade,
                en.fname AS encarregado_fname, en.lname AS encarregado_lname
            FROM estudantes e
            LEFT JOIN turma t ON e.area = t.id
            LEFT JOIN encarregados en ON e.encarregado_id = en.id
            ORDER BY e.lname, e.fname
        ");
        $alunosStmt->execute();
        $alunos = $alunosStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $alunos = [];
        $_SESSION['error'] = "Erro ao buscar alunos: " . $e->getMessage();
    }

?>



<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estudantes - Sistema Pitruca Camama</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>

          /* Estilo para o botão de logout fixo */
        .fixed-logout-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 999;
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;       /* Reduzido de 60px */
    height: 50px;      /* Reduzido de 60px */
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Sombra mais suave */
    transition: all 0.3s;
    text-decoration: none;
}

.fixed-logout-btn i {
    font-size: 1.2rem; /* Ícone um pouco menor (era 1.5rem) */
}

    /* Reset e estilos básicos */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f7fa;
      color: #333;
      line-height: 1.6;
    }
    
    .container {
      display: flex;
      width: 100%;
      min-height: 100vh;
    }
    
    /* Estilos para a barra lateral */
    .sidebar {
      width: 260px;
      height: 100vh;
      background-color: #2c3e50;
      color: #ecf0f1;
      position: fixed;
      left: 0;
      top: 0;
      overflow-y: auto;
      transition: all 0.3s ease;
      z-index: 1000;
    }
    
    .sidebar .logo {
      padding: 20px 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      text-align: center;
    }
    
    .sidebar .logo h2 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 600;
    }
    
    .nav-links {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .nav-links li {
      position: relative;
      transition: all 0.3s ease;
    }
    
    .nav-links li a,
    .nav-links li .menu-item {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      color: #ecf0f1;
      text-decoration: none;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    
    .nav-links li a i,
    .nav-links li .menu-item i {
      min-width: 25px;
      font-size: 1.1rem;
      text-align: center;
      margin-right: 10px;
    }
    
    .nav-links li a span,
    .nav-links li .menu-item span {
      flex: 1;
    }
    
    .nav-links li:hover > a,
    .nav-links li:hover > .menu-item,
    .nav-links li.active > a,
    .nav-links li.active > .menu-item {
      background-color: rgba(255, 255, 255, 0.1);
      color: #fff;
    }
    
    .nav-links li.active > a,
    .nav-links li.active > .menu-item {
      border-left: 4px solid #3498db;
    }
    
    .arrow {
      transition: transform 0.3s ease;
    }
    
    .nav-links li.open .arrow {
      transform: rotate(180deg);
    }
    
    .submenu {
      list-style: none;
      padding-left: 0;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
      background-color: rgba(0, 0, 0, 0.1);
    }
    
    .nav-links li.open .submenu {
      max-height: 500px; /* Valor alto o suficiente para acomodar todos os itens */
    }
    
    .submenu li a {
      padding: 10px 15px 10px 50px;
      font-size: 0.9rem;
    }
    
    .submenu li a i {
      font-size: 0.9rem;
    }
    
    .submenu li:hover > a,
    .submenu li.active > a {
      background-color: rgba(255, 255, 255, 0.05);
    }
    
    /* Indicador de página atual */
    .nav-links li a.current,
    .submenu li a.current {
      background-color: rgba(52, 152, 219, 0.2);
      border-left: 4px solid #3498db;
    }
    
    /* Conteúdo principal */
    .content {
      flex: 1;
      margin-left: 260px;
      padding: 20px;
      transition: margin-left 0.3s ease;
    }
    
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      margin-bottom: 20px;
    }
    
    .search-bar {
      display: flex;
      align-items: center;
      background-color: #f5f7fa;
      border-radius: 20px;
      padding: 8px 15px;
      width: 300px;
    }
    
    .search-bar i {
      color: #7f8c8d;
      margin-right: 10px;
    }
    
    .search-bar input {
      border: none;
      background: transparent;
      outline: none;
      width: 100%;
      color: #333;
    }
    
    .user-info {
      display: flex;
      align-items: center;
    }
    
    .user {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .user img {
      border-radius: 50%;
      object-fit: cover;
    }
    
    .user span {
      font-weight: 500;
    }
    
    .user-dropdown {
      position: relative;
      cursor: pointer;
    }
    
    .dropdown-menu {
      display: none;
      position: absolute;
      right: 0;
      top: 100%;
      background-color: white;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 4px;
      padding: 8px 0;
      z-index: 1000;
      min-width: 150px;
    }
    
    .dropdown-menu.show {
      display: block;
    }
    
    .dropdown-item {
      padding: 8px 16px;
      display: flex;
      align-items: center;
      gap: 8px;
      color: #333;
      text-decoration: none;
    }
    
    .dropdown-item:hover {
      background-color: #f5f5f5;
    }
    
    /* Estilos para o painel de estudantes */
    .tab-content {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      padding: 20px;
    }
    
    .tab-pane {
      display: none;
    }
    
    .tab-pane.active {
      display: block;
    }
    
    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .section-header h2 {
      color: #2c3e50;
      font-weight: 600;
    }
    
    .add-btn {
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 10px 15px;
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .add-btn:hover {
      background-color: #2980b9;
    }
    
    /* Tabela de dados */
    .table-container {
      overflow-x: auto;
      margin-bottom: 20px;
    }
    
    .data-table {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 0;
    }
    
    .data-table thead th {
      background-color: #f8f9fa;
      color: #495057;
      font-weight: 600;
      text-align: left;
      padding: 12px 15px;
      border-bottom: 2px solid #e9ecef;
    }
    
    .data-table tbody tr {
      transition: background-color 0.3s;
    }
    
    .data-table tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    .data-table tbody td {
      padding: 12px 15px;
      border-bottom: 1px solid #e9ecef;
      color: #495057;
    }
    
    /* Botões de ação na tabela */
    .action-buttons {
      display: flex;
      gap: 5px;
    }
    
    .view-btn, .edit-btn, .delete-btn {
      padding: 6px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.85rem;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: opacity 0.3s;
    }
    
    .view-btn {
      background-color: #17a2b8;
      color: white;
    }
    
    .edit-btn {
      background-color: #ffc107;
      color: #212529;
    }
    
    .delete-btn {
      background-color: #dc3545;
      color: white;
    }
    
    .view-btn:hover, .edit-btn:hover, .delete-btn:hover {
      opacity: 0.85;
    }
    
    /* Paginação */
    .pagination {
      display: flex;
      justify-content: center;
      gap: 5px;
      margin-top: 20px;
    }
    
    .pagination-btn {
      padding: 8px 12px;
      border: 1px solid #dee2e6;
      background-color: white;
      color: #495057;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .pagination-btn.active {
      background-color: #3498db;
      color: white;
      border-color: #3498db;
    }
    
    .pagination-btn:hover:not(.active) {
      background-color: #f8f9fa;
    }
    
    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1050;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
    }
    
    .modal-content {
      background-color: #fff;
      margin: 30px auto;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      width: 90%;
      max-width: 800px;
      max-height: 90vh;
      overflow-y: auto;
      animation: modalFadeIn 0.3s;
    }
    
    @keyframes modalFadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      border-bottom: 1px solid #e9ecef;
    }
    
    .modal-header h3 {
      margin: 0;
      color: #2c3e50;
      font-weight: 600;
    }
    
    .close-modal {
      font-size: 1.5rem;
      font-weight: 700;
      color: #adb5bd;
      cursor: pointer;
      transition: color 0.3s;
    }
    
    .close-modal:hover {
      color: #495057;
    }
    
    .modal-body {
      padding: 20px;
    }
    
    /* Formulário */
    .form-container {
      width: 100%;
    }
    
    .form-section {
      margin-bottom: 25px;
      padding-bottom: 20px;
      border-bottom: 1px solid #e9ecef;
    }
    
    .form-section:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }
    
    .form-section h3 {
      color: #3498db;
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 15px;
    }
    
    .form-group {
      margin-bottom: 15px;
    }
    
    .form-row {
      display: flex;
      gap: 15px;
      margin-bottom: 15px;
    }
    
    .form-row .form-group {
      flex: 1;
      margin-bottom: 0;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
      color: #495057;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      font-size: 0.95rem;
      color: #495057;
      transition: border-color 0.3s;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: #3498db;
      outline: none;
    }
    
    .form-group textarea {
      resize: vertical;
      min-height: 80px;
    }
    
    /* Botões do formulário */
    .form-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
    }
    
    .btn-reset {
      padding: 10px 20px;
      background-color: #6c757d;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .btn-submit {
      padding: 10px 20px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .btn-reset:hover {
      background-color: #5a6268;
    }
    
    .btn-submit:hover {
      background-color: #218838;
    }
    
    /* Alertas */
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border: 1px solid transparent;
      border-radius: 4px;
    }
    
    .alert-success {
      color: #155724;
      background-color: #d4edda;
      border-color: #c3e6cb;
    }
    
    .alert-error {
      color: #721c24;
      background-color: #f8d7da;
      border-color: #f5c6cb;
    }
    
    /* Estilos para preview de imagens */
    .file-preview {
      margin-top: 10px;
      max-width: 200px;
      max-height: 200px;
      overflow: hidden;
      border: 1px solid #ced4da;
      border-radius: 4px;
      padding: 5px;
    }
    
    .file-preview img {
      max-width: 100%;
      max-height: 100%;
      border-radius: 4px;
    }
    
    /* Estilos para campos de upload de arquivo */
    .file-input-container {
      position: relative;
      margin-bottom: 15px;
    }
    
    .file-input-container label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
      color: #495057;
    }
    
    .file-input-container input[type="file"] {
      width: 100%;
      padding: 8px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      font-size: 0.95rem;
      color: #495057;
    }
    
    /* Estilos para filtros e pesquisa na tabela */
    .table-filters {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-bottom: 20px;
      padding: 15px;
      background-color: #f8f9fa;
      border-radius: 8px;
    }
    
    .filter-group {
      flex: 1;
      min-width: 200px;
    }
    
    .filter-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
      color: #495057;
    }
    
    .filter-group select,
    .filter-group input {
      width: 100%;
      padding: 8px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      font-size: 0.95rem;
      color: #495057;
    }
    
    .filter-buttons {
      display: flex;
      align-items: flex-end;
      gap: 10px;
    }
    
    .filter-btn {
      padding: 8px 15px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .apply-filter {
      background-color: #3498db;
      color: white;
    }
    
    .reset-filter {
      background-color: #6c757d;
      color: white;
    }
    
    .apply-filter:hover {
      background-color: #2980b9;
    }
    
    .reset-filter:hover {
      background-color: #5a6268;
    }
    
    /* Estilos para status de aluno */
    .status-badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-align: center;
    }
    
    .status-active {
      background-color: #d4edda;
      color: #155724;
    }
    
    .status-inactive {
      background-color: #f8d7da;
      color: #721c24;
    }
    
    .status-pending {
      background-color: #fff3cd;
      color: #856404;
    }
    
    /* Responsividade */
    @media (max-width: 768px) {
      .sidebar {
        width: 70px;
        transform: translateX(0);
      }
      
      .sidebar.expanded {
        width: 260px;
      }
      
      .nav-links li a span,
      .nav-links li .menu-item span,
      .arrow {
        display: none;
      }
      
      .sidebar.expanded .nav-links li a span,
      .sidebar.expanded .nav-links li .menu-item span,
      .sidebar.expanded .arrow {
        display: inline-block;
      }
      
      .submenu li a {
        padding-left: 25px;
      }
      
      .sidebar.expanded .submenu li a {
        padding-left: 50px;
      }
      
      .content {
        margin-left: 70px;
      }
      
      .sidebar.expanded + .content {
        margin-left: 260px;
      }
      
      .form-row {
        flex-direction: column;
        gap: 15px;
      }
      
      .search-bar {
        width: 200px;
      }
      
      .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
      
      .add-btn {
        align-self: flex-start;
      }
    }
    
    /* Botão de toggle para dispositivos móveis */
    .sidebar-toggle {
      display: none;
      position: fixed;
      top: 15px;
      left: 15px;
      z-index: 1001;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 8px;
      cursor: pointer;
    }
    
    @media (max-width: 768px) {
      .sidebar-toggle {
        display: block;
      }
    }
  </style>
</head>
<body>
<div class="container">
    <!-- Botão de toggle para dispositivos móveis -->
    <button class="sidebar-toggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <nav class="sidebar">
        <div class="logo">
            <h2>Pitruca Camama</h2>
        </div>
        <ul class="nav-links">
            <!-- Painel (Visão Geral) -->
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" data-tab="dashboard">
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'current' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> 
                    <span>Painel</span>
                </a>
            </li>
            
            <!-- Gestão De Alunos -->
            <li class="has-submenu <?php echo in_array(basename($_SERVER['PHP_SELF']), ['students.php', 'attendance.php']) ? 'open active' : ''; ?>" data-tab="student-management">
                <div class="menu-item">
                    <i class="fas fa-user-graduate"></i> 
                    <span>Gestão De Alunos</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu" style="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['students.php', 'attendance.php']) ? 'max-height: 500px;' : ''; ?>">
                    <li data-tab="register-students" class="<?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : ''; ?>">
                        <a href="students.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'current' : ''; ?>">
                            <i class="fas fa-user-plus"></i> 
                            <span>Estudantes</span>
                        </a>
                    </li>
                    <li data-tab="attendance" class="<?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'active' : ''; ?>">
                        <a href="attendance.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'current' : ''; ?>">
                            <i class="fas fa-calendar-check"></i> 
                            <span>Presença</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Gestão Pedagógica -->
            <li class="has-submenu <?php echo in_array(basename($_SERVER['PHP_SELF']), ['classes.php', 'schedule.php', 'tests.php', 'bulletins.php']) ? 'open active' : ''; ?>" data-tab="pedagogical-management">
                <div class="menu-item">
                    <i class="fas fa-chalkboard"></i> 
                    <span>Gestão Pedagógica</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu" style="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['classes.php', 'schedule.php', 'tests.php', 'bulletins.php']) ? 'max-height: 500px;' : ''; ?>">
                    <li data-tab="classes" class="<?php echo basename($_SERVER['PHP_SELF']) == 'classes.php' ? 'active' : ''; ?>">
                        <a href="classes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'classes.php' ? 'current' : ''; ?>">
                            <i class="fas fa-users"></i> 
                            <span>Turmas</span>
                        </a>
                    </li>
                    <li data-tab="schedule" class="<?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'active' : ''; ?>">
                        <a href="schedule.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'current' : ''; ?>">
                            <i class="fas fa-calendar-alt"></i> 
                            <span>Horários</span>
                        </a>
                    </li>
                    <li data-tab="tests" class="<?php echo basename($_SERVER['PHP_SELF']) == 'tests.php' ? 'active' : ''; ?>">
                        <a href="tests.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'tests.php' ? 'current' : ''; ?>">
                            <i class="fas fa-file-alt"></i> 
                            <span>Provas</span>
                        </a>
                    </li>
                    <li data-tab="bulletins" class="<?php echo basename($_SERVER['PHP_SELF']) == 'bulletins.php' ? 'active' : ''; ?>">
                        <a href="bulletins.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'bulletins.php' ? 'current' : ''; ?>">
                            <i class="fas fa-file-invoice"></i> 
                            <span>Boletins</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Gestão de Funcionários -->
            <li class="has-submenu <?php echo in_array(basename($_SERVER['PHP_SELF']), ['teacher.php', 'coordinator.php']) ? 'open active' : ''; ?>" data-tab="staff-management">
                <div class="menu-item">
                    <i class="fas fa-chalkboard-teacher"></i> 
                    <span>Gestão de Funcionários</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu" style="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['teacher.php', 'coordinator.php']) ? 'max-height: 500px;' : ''; ?>">
                    <li data-tab="register-teacher" class="<?php echo basename($_SERVER['PHP_SELF']) == 'teacher.php' ? 'active' : ''; ?>">
                        <a href="teacher.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'teacher.php' ? 'current' : ''; ?>">
                            <i class="fas fa-user-plus"></i> 
                            <span>Professores</span>
                        </a>
                    </li>
                    <li data-tab="register-coordinator" class="<?php echo basename($_SERVER['PHP_SELF']) == 'coordinator.php' ? 'active' : ''; ?>">
                        <a href="coordinator.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'coordinator.php' ? 'current' : ''; ?>">
                            <i class="fas fa-user-plus"></i> 
                            <span>Coordenador</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Utilizadores -->
            <li class="has-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'guardians.php' ? 'open active' : ''; ?>" data-tab="users">
                <div class="menu-item">
                    <i class="fas fa-users"></i> 
                    <span>Utilizadores</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu" style="<?php echo basename($_SERVER['PHP_SELF']) == 'guardians.php' ? 'max-height: 500px;' : ''; ?>">
                    <li data-tab="guardians" class="<?php echo basename($_SERVER['PHP_SELF']) == 'guardians.php' ? 'active' : ''; ?>">
                        <a href="guardians.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'guardians.php' ? 'current' : ''; ?>">
                            <i class="fas fa-user-friends"></i> 
                            <span>Encarregados</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            
<<<<<<< HEAD
            <!-- Configurações 
=======
            <!-- Configurações -->
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            <li data-tab="settings" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'current' : ''; ?>">
                    <i class="fas fa-cog"></i> 
                    <span>Configurações</span>
<<<<<<< HEAD
                </a>-->
=======
                </a>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            </li>

             <a href="/dashboardpitruca/login/logout.php" class="fixed-logout-btn" title="Sair do Sistema">
              <i class="fas fa-sign-out-alt"></i>
            </a>
        </ul>
    </nav>
    
    <main class="content">
        <header>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Pesquisar estudantes..." id="search-students">
            </div>
            <div class="user-info">
                <div class="user user-dropdown">
                    <img src="login/images/semft-removebg-preview.png" alt="" class="userOptions__avatar img-circle" width="42" height="42">
                    <span><?php echo $_SESSION['fname']; ?></span>
                </div>
            </div>
        </header>
        
        <div class="tab-content">
            <!-- Gestão De Alunos - Cadastrar Estudantes -->
            <div class="tab-pane active" id="register-students">
                <div class="section-header">
                    <h2>Gestão de Estudantes</h2>
                    <button class="add-btn" id="add-student-btn">
                        <i class="fas fa-plus"></i> Adicionar Aluno
                    </button>
                </div>
                
                <!-- Alertas para feedback -->
                <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
                <?php endif; ?>
                
                <!-- Filtros para a tabela -->
                <div class="table-filters">
                    <div class="filter-group">
                        <label for="filter-turma">Filtrar por Turma</label>
                        <select id="filter-turma">
                            <option value="">Todas as Turmas</option>
                            <option value="1A">1º Ano - Turma A</option>
                            <option value="1B">1º Ano - Turma B</option>
                            <option value="2A">2º Ano - Turma A</option>
                            <option value="2B">2º Ano - Turma B</option>
                            <option value="3A">3º Ano - Turma A</option>
                            <option value="3B">3º Ano - Turma B</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-status">Filtrar por Estado</label>
                        <select id="filter-status">
                            <option value="">Todos os Estado</option>
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                            <option value="pending">Pendente</option>
                        </select>
                    </div>
                    <div class="filter-buttons">
                        <button class="filter-btn apply-filter" id="apply-filters">
                            <i class="fas fa-filter"></i> Aplicar Filtros
                        </button>
                        <button class="filter-btn reset-filter" id="reset-filters">
                            <i class="fas fa-undo"></i> Limpar
                        </button>
                    </div>
                </div>
                
               
<!-- Tabela de estudantes -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Turma</th>
                <th>Contato</th>
                <th>Encarregado</th>
<<<<<<< HEAD
=======
                <th>Estado</th>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="student-table-body">
            <?php if (!empty($alunos)): ?>
                <?php foreach ($alunos as $aluno): ?>
                    <tr>
                        <td><?= htmlspecialchars($aluno['id']) ?></td>
                        <td><?= htmlspecialchars($aluno['fname'] . ' ' . $aluno['lname']) ?></td>
                        <td><?= htmlspecialchars($aluno['class_name']) ?></td>
                        <td><?= htmlspecialchars($aluno['telefone']) ?></td>
                        <td><?= htmlspecialchars($aluno['encarregado_fname'] . ' ' . $aluno['encarregado_lname']) ?></td>
<<<<<<< HEAD
                       
=======
                        <td>
                            <?php
                                $statusClass = $aluno['status'] === 'ativo' ? 'status-active' : 'status-inactive';
                                $statusText = ucfirst($aluno['status']);
                            ?>
                            <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                        <td>
                             <div class="action-buttons">
                          <button class="view-btn" data-id="<?= $g['id']; ?>" onclick="window.location.href='view_student.php?id=<?= $g['id']; ?>'">
                            <i class="fas fa-eye"></i>
                          </button>

                          <button class="edit-btn" onclick="window.location.href='edit_student.php?id=<?= $g['id']; ?>'">
                            <i class="fas fa-edit"></i>
                          </button>

                          <button class="delete-btn" data-id="<?= $g['id']; ?>"><i class="fas fa-trash"></i></button>

                        </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">Nenhum estudante cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
                
                <!-- Paginação -->
                <div class="pagination" id="estudantes-paginacao">
                    <button class="pagination-btn active" data-page="1">1</button>
                    <button class="pagination-btn" data-page="2">2</button>
                    <button class="pagination-btn" data-page="3">3</button>
                    <button class="pagination-btn" data-page="next"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal para adicionar/editar estudante -->
<div class="modal" id="student-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="student-modal-title">Adicionar Novo Aluno</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-container">
                <form id="formEstudante" action="students.php" method="POST" enctype="multipart/form-data">
                    
                    <!-- Informações Pessoais -->
                    <div class="form-section">
                        <h3>Informações Pessoais</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fname">Nome*</label>
                                <input type="text" id="fname" name="fname" required>
                            </div>
                            <div class="form-group">
                                <label for="lname">Sobrenome*</label>
                                <input type="text" id="lname" name="lname" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="dataa">Data de Nascimento*</label>
                                <input type="date" id="dataa" name="dataa" required>
                            </div>
                            <div class="form-group">
                                <label for="genero">Gênero*</label>
                                <select id="genero" name="genero" required>
                                    <option value="">Selecione</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Feminino</option>
                                    <option value="O">Outro</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="numbi">Número de BI*</label>
                            <input type="text" id="numbi" name="numbi" required placeholder="Formato: 0000000LA000">
                        </div>

                        <div class="form-row">
                            <div class="file-input-container">
                                <label for="foto_bi1">Foto do BI (Frente)*</label>
                                <input type="file" id="foto_bi1" name="foto_bi1" accept="image/*" required>
                                <div class="file-preview" id="foto_bi1-preview"></div>
                            </div>
                            <div class="file-input-container">
                                <label for="foto_bi2">Foto do BI (Verso)*</label>
                                <input type="file" id="foto_bi2" name="foto_bi2" accept="image/*" required>
                                <div class="file-preview" id="foto_bi2-preview"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Contato e Endereço -->
                    <div class="form-section">
                        <h3>Contato e Endereço</h3>
                        <div class="form-group">
                            <label for="endereco">Endereço Completo*</label>
                            <input type="text" id="endereco" name="endereco" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefone">Telefone*</label>
                                <input type="tel" id="telefone" name="telefone" required pattern="\d{9}" placeholder="9 dígitos">
                            </div>
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" id="email" name="email">
                            </div>
                        </div>
                    </div>

                    <!-- Informações Acadêmicas -->
                    <div class="form-section">
                        <h3>Informações Acadêmicas</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="turma">Turma*</label>
                                <select id="turma" name="turma" required>
                                    <option value="">Selecione uma turma</option>
                                    <?php foreach ($turmas as $turma): ?>
                                        <option value="<?= htmlspecialchars($turma['id']) ?>">
                                            <?= htmlspecialchars($turma['class_grade']) ?>º Ano - <?= htmlspecialchars($turma['class_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
<<<<<<< HEAD
                                <label for="area">Área*</label>
                                <select id="area" name="area" required>
                                    <option value="">Selecione uma área</option>
                                    <option value="T_Click">T_Click</option>
                                    <option value="i|_Click">i|_Click</option>
                                    <option value="Curso_PUNY">Curso_PUNY</option>
                                    <option value="Curso__">Curso__</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                                <label for="status">Estado*</label>
                                <select id="status" name="status" required>
                                    <option value="active">Ativo</option>
                                    <option value="inactive">Inativo</option>
<<<<<<< HEAD
                                    <option value="pending" selected>Pendente</option>
=======
                                    <option value="pending">Pendente</option>
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                                </select>
                            </div>
                        </div>

                        <div class="file-input-container">
                            <label for="fotoperfil">Foto do Aluno*</label>
                            <input type="file" id="fotoperfil" name="fotoperfil" accept="image/*" required>
                            <div class="file-preview" id="fotoperfil-preview"></div>
                        </div>
                    </div>

                    <!-- Encarregado -->
                    <div class="form-section">
                        <h3>Encarregado de Educação</h3>
                        <div class="form-group">
                            <label for="encarregado">Encarregado*</label>
                            <select id="encarregado" name="encarregado" required>
                                <option value="">Selecione um encarregado</option>
                                <?php if (!empty($encarregados)) : ?>
                                    <?php foreach ($encarregados as $enc) : ?>
                                        <option value="<?= htmlspecialchars($enc['id']) ?>">
                                            <?= htmlspecialchars($enc['fname']) . ' ' . htmlspecialchars($enc['lname']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <option value="">Nenhum encarregado encontrado</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Acesso -->
                    <div class="form-section">
                        <h3>Acesso ao Sistema</h3>
                        <div class="form-group">
                            <label for="password">Senha*</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirmar Senha*</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="form-buttons">
                        <button type="reset" class="btn-reset">
                            <i class="fas fa-undo"></i> Limpar
                        </button>
                        <button type="submit" name="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal para visualizar estudante -->
<div class="modal" id="view-student-modal">
    <div class="modal-content">
<<<<<<< HEAD
        
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        <div class="modal-header">
            <h3>Detalhes do Aluno</h3>
            <span class="close-modal">&times;</span>
        </div>
<<<<<<< HEAD
        
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
        <div class="modal-body">
            <div id="student-details">
                <!-- Detalhes do aluno serão carregados via JavaScript -->
            </div>
<<<<<<< HEAD
            
=======
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
            <div class="form-buttons">
                <button class="btn-reset close-view-btn">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        </div>
<<<<<<< HEAD
        
    </div>
</div>


=======
    </div>
</div>

>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown do usuário
    const userDropdown = document.querySelector('.user-dropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (userDropdown && dropdownMenu) {
        userDropdown.addEventListener('click', function() {
            dropdownMenu.classList.toggle('show');
        });
        
        document.addEventListener('click', function(event) {
            if (!userDropdown.contains(event.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }
    
    // Navegação da barra lateral
    const menuItems = document.querySelectorAll('.nav-links .has-submenu .menu-item');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            const parent = this.parentElement;
            
            // Verifica se o item já está aberto
            const isOpen = parent.classList.contains('open');
            
            // Fecha todos os submenus
            if (!isOpen) {
                document.querySelectorAll('.nav-links .has-submenu').forEach(submenu => {
                    // Não fecha o submenu atual se estiver na página atual
                    if (!submenu.classList.contains('active') || submenu === parent) {
                        submenu.classList.remove('open');
                    }
                });
            }
            
            // Alterna o estado do submenu atual
            parent.classList.toggle('open');
        });
    });
    
    // Toggle da barra lateral para dispositivos móveis
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.content');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('expanded');
            if (content) {
                content.style.marginLeft = sidebar.classList.contains('expanded') ? '260px' : '70px';
            }
        });
    }
    
    // Detecta o tamanho da tela e ajusta a barra lateral
    function checkScreenSize() {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('expanded');
            if (content) {
                content.style.marginLeft = '70px';
            }
        } else {
            sidebar.classList.remove('expanded'); // Remove a classe expanded
            if (content) {
                content.style.marginLeft = '260px'; // Retorna ao tamanho normal
            }
        }
    }
    
    // Verifica o tamanho da tela ao carregar e ao redimensionar
    window.addEventListener('resize', checkScreenSize);
    checkScreenSize();
    
    // Controle do modal de estudante
    const studentModal = document.getElementById('student-modal');
    const viewStudentModal = document.getElementById('view-student-modal');
    const addStudentBtn = document.getElementById('add-student-btn');
    const closeModalBtns = document.querySelectorAll('.close-modal, .close-view-btn');
    
    // Abrir modal de adicionar estudante
    if (addStudentBtn) {
        addStudentBtn.addEventListener('click', function() {
            document.getElementById('student-modal-title').textContent = 'Adicionar Novo Aluno';
            document.getElementById('formEstudante').reset();
            clearImagePreviews();
            studentModal.style.display = 'block';
        });
    }
    
    // Fechar modais
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            studentModal.style.display = 'none';
            viewStudentModal.style.display = 'none';
        });
    });
    
    // Fechar modal ao clicar fora
    window.addEventListener('click', function(e) {
        if (e.target === studentModal) {
            studentModal.style.display = 'none';
        }
        if (e.target === viewStudentModal) {
            viewStudentModal.style.display = 'none';
        }
    });
    
    // Preview de imagens
    function setupImagePreviews() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                const previewId = this.id + '-preview';
                const preview = document.getElementById(previewId);
                
                if (preview && this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    }
    
    function clearImagePreviews() {
        const previews = document.querySelectorAll('.file-preview');
        previews.forEach(preview => {
            preview.innerHTML = '';
        });
    }
    
    setupImagePreviews();
    
    // Validação do formulário
    const studentForm = document.getElementById('formEstudante');
    
    if (studentForm) {
        studentForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('As senhas não coincidem!');
                return false;
            }
            
            const numbi = document.getElementById('numbi').value;
            if (!numbi.match(/^\d{7}[A-Za-z]{2}\d{3}$/)) {
                e.preventDefault();
                alert('Formato do BI inválido. Use o formato: 0000000LA000');
                return false;
            }
            
            const telefone = document.getElementById('telefone').value;
            if (!telefone.match(/^\d{9}$/)) {
                e.preventDefault();
                alert('Número de telefone inválido. Use 9 dígitos.');
                return false;
            }
            
            const email = document.getElementById('email').value;
            if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                e.preventDefault();
                alert('Email inválido.');
                return false;
            }
        });
    }
    
    // Botões de ação na tabela
    function setupActionButtons() {
        // Botões de visualizar
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const studentId = this.getAttribute('data-id');
                viewStudent(studentId);
            });
        });
        
        // Botões de editar
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const studentId = this.getAttribute('data-id');
                editStudent(studentId);
            });
        });
        
        // Botões de excluir
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const studentId = this.getAttribute('data-id');
                deleteStudent(studentId);
            });
        });
    }
    
<<<<<<< HEAD
   // Função para visualizar detalhes do estudante
function viewStudent(id) {
    // Exemplo de resposta simulada (mock) - substituir futuramente por chamada AJAX
    const studentDetails = `
        <div class="student-profile">

            <!-- Cabeçalho com avatar e informações básicas -->
            <div class="student-header">
               
                <div class="student-basic-info">
                    <h2>João Silva</h2>
                   
                    <p><strong>Turma:</strong> 1º Ano - Turma A</p>
                    <p><strong>Status:</strong> <span class="status-badge status-active">Ativo</span></p>
                </div>
            </div>

            <!-- Grade de informações divididas em seções -->
            <div class="student-details-grid">

                <!-- Seção: Informações Pessoais -->
                <div class="detail-section">
                    <h3>Informações Pessoais</h3>
                    <p><strong>Data de Nascimento:</strong> 15/05/2010</p>
                    <p><strong>Gênero:</strong> Masculino</p>
                    <p><strong>BI:</strong> 1234567LA123</p>
                </div>

                <!-- Seção: Contato -->
                <div class="detail-section">
                    <h3>Contato</h3>
                    <p><strong>Endereço:</strong> Rua Principal, 123, Camama</p>
                    <p><strong>Telefone:</strong> 912345678</p>
                    <p><strong>Email:</strong> joao.silva@email.com</p>
                </div>

                <!-- Seção: Encarregado -->
                <div class="detail-section">
                    <h3>Encarregado</h3>
                    <p><strong>Nome:</strong> Maria Silva</p>
                    <p><strong>Telefone:</strong> 923456789</p>
                    <p><strong>Parentesco:</strong> Mãe</p>
                </div>

                <!-- Seção: Acadêmico -->
                <div class="detail-section">
                    <h3>Informações Acadêmicas</h3>
                    <p><strong>Matrícula:</strong> 2023001</p>
                    <p><strong>Ano Letivo:</strong> 2023</p>
                    <p><strong>Observações:</strong> Aluno exemplar, participativo em todas as atividades.</p>
                </div>

            </div>
        </div>
    `;

    // Exibe os dados no modal e abre o modal
    document.getElementById('student-details').innerHTML = studentDetails;
    document.getElementById('view-student-modal').style.display = 'flex';
}

=======
    // Função para visualizar estudante
    function viewStudent(id) {
        // Aqui você faria uma requisição AJAX para obter os detalhes do estudante
        // Por enquanto, vamos simular com dados estáticos
        
        const studentDetails = `
            <div class="student-profile">
                <div class="student-header">
                    <div class="student-avatar">
                        <img src="uploads/students/default-avatar.jpg" alt="Foto do Aluno">
                    </div>
                    <div class="student-basic-info">
                        <h2>João Silva</h2>
                        <p><strong>ID:</strong> ${id}</p>
                        <p><strong>Turma:</strong> 1º Ano - Turma A</p>
                        <p><strong>Status:</strong> <span class="status-badge status-active">Ativo</span></p>
                    </div>
                </div>
                
                <div class="student-details-grid">
                    <div class="detail-section">
                        <h3>Informações Pessoais</h3>
                        <p><strong>Data de Nascimento:</strong> 15/05/2010</p>
                        <p><strong>Gênero:</strong> Masculino</p>
                        <p><strong>BI:</strong> 1234567LA123</p>
                    </div>
                    
                    <div class="detail-section">
                        <h3>Contato</h3>
                        <p><strong>Endereço:</strong> Rua Principal, 123, Camama</p>
                        <p><strong>Telefone:</strong> 912345678</p>
                        <p><strong>Email:</strong> joao.silva@email.com</p>
                    </div>
                    
                    <div class="detail-section">
                        <h3>Encarregado</h3>
                        <p><strong>Nome:</strong> Maria Silva</p>
                        <p><strong>Telefone:</strong> 923456789</p>
                        <p><strong>Parentesco:</strong> Mãe</p>
                    </div>
                    
                    <div class="detail-section">
                        <h3>Informações Acadêmicas</h3>
                        <p><strong>Matrícula:</strong> 2023001</p>
                        <p><strong>Ano Letivo:</strong> 2023</p>
                        <p><strong>Observações:</strong> Aluno exemplar, participativo em todas as atividades.</p>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('student-details').innerHTML = studentDetails;
        viewStudentModal.style.display = 'block';
    }
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
    
    // Função para editar estudante
    function editStudent(id) {
        // Aqui você faria uma requisição AJAX para obter os dados do estudante
        // Por enquanto, vamos simular preenchendo o formulário com dados estáticos
        
        document.getElementById('student-modal-title').textContent = 'Editar Aluno';
        
        // Preencher o formulário com dados simulados
        document.getElementById('fname').value = 'João';
        document.getElementById('lname').value = 'Silva';
        document.getElementById('dataa').value = '2010-05-15';
        document.getElementById('genero').value = 'M';
        document.getElementById('numbi').value = '1234567LA123';
        document.getElementById('endereco').value = 'Rua Principal, 123, Camama';
        document.getElementById('telefone').value = '912345678';
        document.getElementById('email').value = 'joao.silva@email.com';
        document.getElementById('turma').value = '1A';
        document.getElementById('status').value = 'active';
        
        // Exibir o modal
        studentModal.style.display = 'block';
    }
    
    // Função para excluir estudante
    function deleteStudent(id) {
        if (confirm(`Tem certeza que deseja excluir o aluno com ID ${id}?`)) {
            // Aqui você faria uma requisição AJAX para excluir o estudante
            alert(`Aluno com ID ${id} excluído com sucesso!`);
            // Recarregar a tabela após excluir
            // loadStudents();
        }
    }
    
    // Inicializar os botões de ação
    setupActionButtons();
    
    // Filtros e pesquisa
    const searchInput = document.getElementById('search-students');
    const filterTurma = document.getElementById('filter-turma');
    const filterStatus = document.getElementById('filter-status');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            // Implementar pesquisa em tempo real
            const searchTerm = this.value.toLowerCase();
            filterTable(searchTerm, filterTurma.value, filterStatus.value);
        });
    }
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const searchTerm = searchInput.value.toLowerCase();
            filterTable(searchTerm, filterTurma.value, filterStatus.value);
        });
    }
    
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterTurma.value = '';
            filterStatus.value = '';
            filterTable('', '', '');
        });
    }
    
    function filterTable(searchTerm, turma, status) {
        const rows = document.querySelectorAll('#student-table-body tr');
        
        rows.forEach(row => {
            const nome = row.cells[1].textContent.toLowerCase();
            const turmaCel = row.cells[2].textContent;
            const statusCel = row.cells[5].textContent.toLowerCase();
            
            const matchSearch = nome.includes(searchTerm);
            const matchTurma = turma === '' || turmaCel.includes(turma);
            const matchStatus = status === '' || statusCel.includes(status);
            
            if (matchSearch && matchTurma && matchStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Paginação
    const paginationBtns = document.querySelectorAll('.pagination-btn');
    
    paginationBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const page = this.getAttribute('data-page');
            
            // Remover classe active de todos os botões
            paginationBtns.forEach(b => b.classList.remove('active'));
            
            // Adicionar classe active ao botão clicado
            if (page !== 'prev' && page !== 'next') {
                this.classList.add('active');
            }
            
            // Aqui você implementaria a lógica para carregar a página correspondente
            // loadStudentsPage(page);
        });
    });
    
    // Adicionar novo encarregado
    const addNewGuardianBtn = document.getElementById('add-new-guardian');
    
    if (addNewGuardianBtn) {
        addNewGuardianBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Funcionalidade para adicionar novo encarregado será implementada aqui.');
            // Aqui você abriria um modal para adicionar novo encarregado
        });
    }
    
    // Ca
    
    // Carregar encarregados quando o modal é aberto
    if (addStudentBtn) {
        addStudentBtn.addEventListener('click', loadGuardiansForSelect);
    }
    
    // Função para carregar estudantes (simulação)
    function loadStudents() {
        // Em um ambiente real, você faria uma requisição AJAX para obter os dados
        console.log("Carregando estudantes...");
        // A tabela já está preenchida com dados de exemplo
    }
    
    // Carregar estudantes ao iniciar a página
    loadStudents();
});
</script>
</body>
</html>

<?php }else{
<<<<<<< HEAD
    header("Location: ..login/login.php");
=======
    header("Location: ../login.php");
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
    exit;
} ?>