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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        $fname = htmlspecialchars(trim($_POST['fname']));
        $lname = htmlspecialchars(trim($_POST['lname']));
        $genero = $_POST['genero'];
        $dataa = $_POST['dataa'];
        $numbi = $_POST['numbi'];
        $endereco = htmlspecialchars(trim($_POST['endereco']));
        $telefone = $_POST['telefone'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $especializacao = $_POST['especializacao'];
        $nivel_academico = $_POST['nivel_academico'];
        $password_raw = $_POST['password'];

        if (empty($password_raw)) {
            $_SESSION['error'] = "A senha não pode estar vazia.";
            header("Location: teacher.php");
            exit;
        }

        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        if (!preg_match('/^\d{7}[A-Za-z]{2}\d{3}$/', $numbi)) {
            $_SESSION['error'] = "Formato do BI inválido. Use o formato: 0000000LA000";
            header("Location: teacher.php");
            exit;
        }

        if (!preg_match('/^\d{9}$/', $telefone)) {
            $_SESSION['error'] = "Número de telefone inválido. Use 9 dígitos.";
            header("Location: teacher.php");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Email inválido.";
            header("Location: teacher.php");
            exit;
        }

        try {
            $checkTelStmt = $conn->prepare("SELECT id FROM professores WHERE telefone = ?");
            $checkTelStmt->execute([$telefone]);
            if ($checkTelStmt->rowCount() > 0) {
                $_SESSION['error'] = "Este telefone já está em uso por outro professor.";
                header("Location: teacher.php");
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erro ao verificar telefone: " . $e->getMessage();
            header("Location: teacher.php");
            exit;
        }

        try {
            $checkBISql = $conn->prepare("SELECT id FROM professores WHERE num_bi = ?");
            $checkBISql->execute([$numbi]);
            if ($checkBISql->rowCount() > 0) {
                $_SESSION['error'] = "Este número do BI já está cadastrado para outro professor.";
                header("Location: teacher.php");
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erro ao verificar número do BI: " . $e->getMessage();
            header("Location: teacher.php");
            exit;
        }

        function uploadFile($file, $targetDir = "uploads/teacher/", $allowedTypes = ['jpg', 'jpeg', 'png']) {
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

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                return $destination;
            }

            return false;
        }

        $foto_bi1 = uploadFile($_FILES['foto_bi1']);
        $foto_bi2 = uploadFile($_FILES['foto_bi2']);
        $fotoperfil = uploadFile($_FILES['fotoperfil']);

        if (!$foto_bi1 || !$foto_bi2 || !$fotoperfil) {
            $_SESSION['error'] = "Erro no upload das imagens.";
            header("Location: teacher.php");
            exit;
        }

        // GERAR NOME DE UTILIZADOR AUTOMÁTICO
        $base_username = strtolower($fname . '.' . $lname);
        $checkUsername = $conn->prepare("SELECT COUNT(*) FROM professores WHERE username LIKE :username");
        $suffix = '';
        do {
            $username_try = $base_username . $suffix;
            $checkUsername->execute([':username' => $username_try]);
            $count = $checkUsername->fetchColumn();
            $suffix = $count > 0 ? rand(100, 999) : '';
        } while ($count > 0);
        $username = $username_try;

        try {
            $sql = "INSERT INTO professores (
                fname, lname, genero, data_nascimento, num_bi, foto_bi1, foto_bi2, endereco,
                fotoperfil, telefone, email, especializacao, nivel_academico, password, username
            ) VALUES (
                :fname, :lname, :genero, :dataa, :numbi, :foto_bi1, :foto_bi2, :endereco,
                :fotoperfil, :telefone, :email, :especializacao, :nivel_academico, :password, :username
            )";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':fname', $fname);
            $stmt->bindParam(':lname', $lname);
            $stmt->bindParam(':genero', $genero);
            $stmt->bindParam(':dataa', $dataa);
            $stmt->bindParam(':numbi', $numbi);
            $stmt->bindParam(':foto_bi1', $foto_bi1);
            $stmt->bindParam(':foto_bi2', $foto_bi2);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':fotoperfil', $fotoperfil);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':especializacao', $especializacao);
            $stmt->bindParam(':nivel_academico', $nivel_academico);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':username', $username);

            $stmt->execute();
            $_SESSION['success'] = "Professor adicionado com sucesso! Nome de utilizador gerado: <strong>$username</strong>";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erro ao adicionar professor: " . $e->getMessage();
        }

        header("Location: teacher.php");
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


?>


<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Professores - Sistema Pitruca Camama</title>
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
            
            <!-- Configurações -->
            <li data-tab="settings" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'current' : ''; ?>">
                    <i class="fas fa-cog"></i> 
                    <span>Configurações</span>
                </a>
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
                <input type="text" placeholder="Pesquisar professores..." id="search-teacher">
            </div>
            <div class="user-info">
                <div class="user user-dropdown">
                    <img src="login/images/semft-removebg-preview.png" alt="" class="userOptions__avatar img-circle" width="42" height="42">
                    <span><?php echo $_SESSION['fname']; ?></span>
                </div>
            </div>
        </header>
        
        <div class="tab-content">
           <!-- Gestão De Professores -->
<div class="tab-pane active" id="register-teacher">
    <div class="section-header">
        <h2>Gestão de Professores</h2>
        <button class="add-btn" id="add-teacher-btn">
            <i class="fas fa-plus"></i> Adicionar Professor
        </button>
    </div>

    <!-- Alertas -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="table-filters">
        <div class="filter-group">
            <label for="filter-especializacao">Filtrar por Especialização</label>
            <select id="filter-especializacao">
                <option value="">Todas</option>
                <option value="Matemática">Matemática</option>
                <option value="Português">Português</option>
                <option value="Ciências">Ciências</option>
                <option value="História">História</option>
                <option value="Geografia">Geografia</option>
            </select>
        </div>
        <div class="filter-group">
            <label for="filter-status">Filtrar por Estado</label>
            <select id="filter-status">
                <option value="">Todos</option>
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
            </select>
        </div>
        <div class="filter-buttons">
            <button class="filter-btn apply-filter" id="apply-filters"><i class="fas fa-filter"></i> Aplicar Filtros</button>
            <button class="filter-btn reset-filter" id="reset-filters"><i class="fas fa-undo"></i> Limpar</button>
        </div>
    </div>

  
       <!-- Tabela de professores -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Especialização</th>
                <th>Nível Acadêmico</th>
                <th>Contato</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="guardian-table-body">
            <?php if (!empty($teachers)): ?>
                <?php foreach ($teachers as $g): ?>
                    <tr>
                        <td><?= htmlspecialchars($g['id']); ?></td>
                        <td><?= htmlspecialchars($g['fname'] . ' ' . $g['lname']); ?></td>
                        <td><?= htmlspecialchars($g['especializacao'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($g['nivel_academico'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($g['telefone'] ?? ''); ?></td>
                        <td>
                            <?php 
                            $status = strtolower($g['estado'] ?? 'active'); 
                            $statusClass = ($status === 'active') ? 'status-active' : 'status-inactive';
                            $statusText = ($status === 'active') ? 'Ativo' : 'Inativo';
                            ?>
                            <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                        <td>
                        <div class="action-buttons">
                          <button class="view-btn" data-id="<?= $g['id']; ?>" onclick="window.location.href='view_teacher.php?id=<?= $g['id']; ?>'">
                            <i class="fas fa-eye"></i>
                          </button>

                          <button class="edit-btn" onclick="window.location.href='edit_teacher.php?id=<?= $g['id']; ?>'">
                            <i class="fas fa-edit"></i>
                          </button>

                          <button class="delete-btn" data-id="<?= $g['id']; ?>"><i class="fas fa-trash"></i></button>

                        </div>
                      </td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">Nenhum professor cadastrado ainda.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    <!-- Paginação (manual, opcional implementar dinâmica depois) -->
    <div class="pagination" id="teacher-paginacao">
        <button class="pagination-btn active" data-page="1">1</button>
        <button class="pagination-btn" data-page="2">2</button>
        <button class="pagination-btn" data-page="3">3</button>
        <button class="pagination-btn" data-page="next"><i class="fas fa-chevron-right"></i></button>
    </div>
</div>

        </div>
    </main>
</div>

<!-- Modal para adicionar/editar professor -->
<div class="modal" id="teacher-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="teacher-modal-title">Adicionar Novo Professor</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-container">
                <form id="formProfessor" action="teacher.php" method="POST" enctype="multipart/form-data">
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
                            <label for="numbi">Número de BI/Documento de Identidade*</label>
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
                    
                    <div class="form-section">
                        <h3>Contato e Endereço</h3>
                        
                        <div class="form-group">
                            <label for="endereco">Endereço Completo*</label>
                            <input type="text" id="endereco" name="endereco" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefone">Telefone*</label>
                                <input type="tel" id="telefone" name="telefone" required placeholder="9 dígitos">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">E-mail*</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Informações Profissionais</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="especializacao">Especialização*</label>
                                <select id="especializacao" name="especializacao" required>
                                    <option value="">Selecione</option>
                                    <option value="Matemática">Matemática</option>
                                    <option value="Português">Português</option>
                                    <option value="Ciências">Ciências</option>
                                    <option value="História">História</option>
                                    <option value="Geografia">Geografia</option>
                                    <option value="Inglês">Inglês</option>
                                    <option value="Educação Física">Educação Física</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="nivel_academico">Nível Acadêmico*</label>
                                <select id="nivel_academico" name="nivel_academico" required>
                                    <option value="">Selecione</option>
                                    <option value="Bacharelato">Bacharelato</option>
                                    <option value="Licenciatura">Licenciatura</option>
                                    <option value="Mestrado">Mestrado</option>
                                    <option value="Doutorado">Doutorado</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="file-input-container">
                            <label for="fotoperfil">Foto do Professor*</label>
                            <input type="file" id="fotoperfil" name="fotoperfil" accept="image/*" required>
                            <div class="file-preview" id="fotoperfil-preview"></div>
                        </div>
                    </div>
                    
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

<!-- Modal para visualizar professor -->
<div class="modal" id="view-teacher-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalhes do Professor</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div id="teacher-details">
                <!-- Detalhes do professor serão carregados via JavaScript -->
            </div>
            <div class="form-buttons">
                <button class="btn-reset close-view-btn">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<script>

  
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            if (confirm("Tem certeza que deseja deletar este professor?")) {
                window.location.href = `delete_teacher.php?id=${id}`;
            }
        });
    });
});

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
    
    // Controle do modal de professor
    const teacherModal = document.getElementById('teacher-modal');
    const viewTeacherModal = document.getElementById('view-teacher-modal');
    const addTeacherBtn = document.getElementById('add-teacher-btn');
    const closeModalBtns = document.querySelectorAll('.close-modal, .close-view-btn');
    
    // Abrir modal de adicionar professor
    if (addTeacherBtn) {
        addTeacherBtn.addEventListener('click', function() {
            document.getElementById('teacher-modal-title').textContent = 'Adicionar Novo Professor';
            document.getElementById('formProfessor').reset();
            clearImagePreviews();
            teacherModal.style.display = 'block';
        });
    }
    
    // Fechar modais
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            teacherModal.style.display = 'none';
            viewTeacherModal.style.display = 'none';
        });
    });
    
    // Fechar modal ao clicar fora
    window.addEventListener('click', function(e) {
        if (e.target === teacherModal) {
            teacherModal.style.display = 'none';
        }
        if (e.target === viewTeacherModal) {
            viewTeacherModal.style.display = 'none';
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
    const teacherForm = document.getElementById('formProfessor');
    
    if (teacherForm) {
        teacherForm.addEventListener('submit', function(e) {
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
            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
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
                const teacherId = this.getAttribute('data-id');
                viewTeacher(teacherId);
            });
        });
        
        // Botões de editar
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const teacherId = this.getAttribute('data-id');
                editTeacher(teacherId);
            });
        });
        
        // Botões de excluir
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const teacherId = this.getAttribute('data-id');
                deleteTeacher(teacherId);
            });
        });
    }
    
    // Função para visualizar professor
    function viewTeacher(id) {
        // Aqui você faria uma requisição AJAX para obter os detalhes do professor
        // Por enquanto, vamos simular com dados estáticos
        
        const teacherDetails = `
            <div class="teacher-profile">
                <div class="teacher-header">
                    <div class="teacher-avatar">
                        <img src="uploads/teacher/default-avatar.jpg" alt="Foto do Professor">
                    </div>
                    <div class="teacher-basic-info">
                        <h2>João Silva</h2>
                        <p><strong>ID:</strong> ${id}</p>
                        <p><strong>Especialização:</strong> Matemática</p>
                        <p><strong>Status:</strong> <span class="status-badge status-active">Ativo</span></p>
                    </div>
                </div>
                
                <div class="teacher-details-grid">
                    <div class="detail-section">
                        <h3>Informações Pessoais</h3>
                        <p><strong>Data de Nascimento:</strong> 15/05/1980</p>
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
                        <h3>Informações Profissionais</h3>
                        <p><strong>Nível Acadêmico:</strong> Licenciatura</p>
                        <p><strong>Anos de Experiência:</strong> 10</p>
                        <p><strong>Disciplinas:</strong> Matemática, Física</p>
                    </div>
                    
                    <div class="detail-section">
                        <h3>Informações Adicionais</h3>
                        <p><strong>Data de Contratação:</strong> 10/01/2015</p>
                        <p><strong>Observações:</strong> Professor dedicado com excelente desempenho.</p>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('teacher-details').innerHTML = teacherDetails;
        viewTeacherModal.style.display = 'block';
    }
    
    // Função para editar professor
    function editTeacher(id) {
        // Aqui você faria uma requisição AJAX para obter os dados do professor
        // Por enquanto, vamos simular preenchendo o formulário com dados estáticos
        
        document.getElementById('teacher-modal-title').textContent = 'Editar Professor';
        
        // Preencher o formulário com dados simulados
        document.getElementById('fname').value = 'João';
        document.getElementById('lname').value = 'Silva';
        document.getElementById('dataa').value = '1980-05-15';
        document.getElementById('genero').value = 'M';
        document.getElementById('numbi').value = '1234567LA123';
        document.getElementById('endereco').value = 'Rua Principal, 123, Camama';
        document.getElementById('telefone').value = '912345678';
        document.getElementById('email').value = 'joao.silva@email.com';
        document.getElementById('especializacao').value = 'Matemática';
        document.getElementById('nivel_academico').value = 'Licenciatura';
        
        // Exibir o modal
        teacherModal.style.display = 'block';
    }
    
    // Função para excluir professor
    function deleteTeacher(id) {
        if (confirm(`Tem certeza que deseja excluir o professor com ID ${id}?`)) {
            // Aqui você faria uma requisição AJAX para excluir o professor
            alert(`Professor com ID ${id} excluído com sucesso!`);
            // Recarregar a tabela após excluir
            // loadteacher();
        }
    }
    
    // Inicializar os botões de ação
    setupActionButtons();
    
    // Filtros e pesquisa
    const searchInput = document.getElementById('search-teacher');
    const filterEspecializacao = document.getElementById('filter-especializacao');
    const filterStatus = document.getElementById('filter-status');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            // Implementar pesquisa em tempo real
            const searchTerm = this.value.toLowerCase();
            filterTeacherTable(searchTerm, filterEspecializacao.value, filterStatus.value);
        });
    }
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const searchTerm = searchInput.value.toLowerCase();
            filterTeacherTable(searchTerm, filterEspecializacao.value, filterStatus.value);
        });
    }
    
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterEspecializacao.value = '';
            filterStatus.value = '';
            filterTeacherTable('', '', '');
        });
    }
    
    function filterTeacherTable(searchTerm, especializacao, status) {
        const rows = document.querySelectorAll('#teacher-table-body tr');
        
        rows.forEach(row => {
            const nome = row.cells[1].textContent.toLowerCase();
            const especializacaoCel = row.cells[2].textContent;
            const statusCel = row.cells[5].textContent.toLowerCase();
            
            const matchSearch = nome.includes(searchTerm);
            const matchEspecializacao = especializacao === '' || especializacaoCel.includes(especializacao);
            const matchStatus = status === '' || statusCel.includes(status);
            
            if (matchSearch && matchEspecializacao && matchStatus) {
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
            // loadteacherPage(page);
        });
    });
    
    // Função para carregar professores (simulação)
    function loadteacher() {
        // Em um ambiente real, você faria uma requisição AJAX para obter os dados
        console.log("Carregando professores...");
        // A tabela já está preenchida com dados de exemplo
    }
    
    // Carregar professores ao iniciar a página
    loadteacher();
});
</script>
</body>
</html>

<?php }else{
    header("Location: ../login.php");
    exit;
} ?>