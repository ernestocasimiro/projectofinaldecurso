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

    require_once 'dbconnection.php';

    // Exibir mensagens de sucesso ou erro
    if (isset($_SESSION['success'])) {
        echo '<div style="padding: 12px 20px; background-color: #d4edda; color: #155724; border-left: 5px solid #28a745; border-radius: 5px; margin-bottom: 20px; font-weight: bold;">
                ✅ ' . $_SESSION['success'] . '
              </div>';
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo '<div style="padding: 12px 20px; background-color: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; border-radius: 5px; margin-bottom: 20px; font-weight: bold;">
                ❌ ' . $_SESSION['error'] . '
              </div>';
        unset($_SESSION['error']);
    }

    // Função para buscar professores (diretores/subdiretores)
    function buscarProfessores() {
        global $conn;
        $sql = "SELECT id, fname, lname FROM professores ORDER BY fname ASC";
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $professores = buscarProfessores();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Pega os dados do formulário
        $class_name = $_POST['class_name'];
        $class_grade = $_POST['class_grade'];
        $class_course = $_POST['class_course'];
        $class_capacity = $_POST['class_capacity'];
        $class_room = $_POST['class_room'];
        $class_director_id = $_POST['class_director_id'];
        $class_period = $_POST['class_period'];
        $class_year = $_POST['class_year'];
        $class_description = $_POST['class_description'];
        $class_observations = $_POST['class_observations'];

        // Prepara e executa o INSERT
        $sql = "INSERT INTO turma 
            (class_name, class_grade, class_course, class_capacity, class_room, class_director_id, class_period, class_year, class_description, class_observations) 
            VALUES 
            (:class_name, :class_grade, :class_course, :class_capacity, :class_room, :class_director_id, :class_period, :class_year, :class_description, :class_observations)";

        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([
            ':class_name' => $class_name,
            ':class_grade' => $class_grade,
            ':class_course' => $class_course,
            ':class_capacity' => $class_capacity,
            ':class_room' => $class_room,
            ':class_director_id' => $class_director_id,
            ':class_period' => $class_period,
            ':class_year' => $class_year,
            ':class_description' => $class_description,
            ':class_observations' => $class_observations,
        ]);

        if ($success) {
            $_SESSION['success'] = "Turma salva com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao salvar turma.";
        }

        header("Location: classes.php");
        exit();
    }

    // Buscar turmas cadastradas
    $sqlTurmas = "SELECT 
                    t.*, 
                    p.fname AS diretor_fname, 
                    p.lname AS diretor_lname 
                  FROM turma t
                  LEFT JOIN professores p ON t.class_director_id = p.id
                  ORDER BY t.id DESC";
    $stmtTurmas = $conn->query($sqlTurmas);
    $turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão de Turmas - Sistema Pitruca Camama</title>
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
        .view-btn {
      text-decoration: none;
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
    
    .submenu.show {
      max-height: 500px;
    }
    
    .nav-links li.open .submenu {
      max-height: 500px;
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
    
    .nav-links li.active > a,
    .nav-links li.active > .menu-item {
      background-color: rgba(52, 152, 219, 0.2);
      border-left: 4px solid #3498db;
    }
    
    .submenu li.active > a {
      background-color: rgba(52, 152, 219, 0.1);
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
    
    /* Estilos para o conteúdo da página de turmas */
    .page-content {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      padding: 20px;
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
    
    .action-btn {
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
    
    .action-btn:hover {
      background-color: #2980b9;
    }
    
    /* Cards para estatísticas */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .stat-card {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      padding: 20px;
      display: flex;
      flex-direction: column;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 15px;
      font-size: 1.5rem;
    }
    
    .stat-card .stat-icon.blue {
      background-color: rgba(52, 152, 219, 0.1);
      color: #3498db;
    }
    
    .stat-card .stat-icon.green {
      background-color: rgba(46, 204, 113, 0.1);
      color: #2ecc71;
    }
    
    .stat-card .stat-icon.orange {
      background-color: rgba(230, 126, 34, 0.1);
      color: #e67e22;
    }
    
    .stat-card .stat-icon.red {
      background-color: rgba(231, 76, 60, 0.1);
      color: #e74c3c;
    }
    
    .stat-card .stat-value {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 5px;
      color: #2c3e50;
    }
    
    .stat-card .stat-label {
      color: #7f8c8d;
      font-size: 0.9rem;
    }
    
    /* Filtros para a tabela */
    .filters-container {
      background-color: #f8f9fa;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
    }
    
    .filters-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 15px;
    }
    
    .filter-group {
      display: flex;
      flex-direction: column;
    }
    
    .filter-group label {
      font-size: 0.9rem;
      font-weight: 500;
      margin-bottom: 5px;
      color: #495057;
    }
    
    .filter-group select,
    .filter-group input {
      padding: 8px 10px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      font-size: 0.9rem;
      color: #495057;
    }
    
    .filter-group select:focus,
    .filter-group input:focus {
      border-color: #3498db;
      outline: none;
    }
    
    .filter-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 15px;
    }
    
    .filter-btn {
      padding: 8px 15px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 5px;
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
    
    /* Tabela de turmas */
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
      position: sticky;
      top: 0;
      z-index: 10;
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
    
    .class-name {
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .class-icon {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background-color: #3498db;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.8rem;
      font-weight: 600;
    }
    
    .capacity-indicator {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .capacity-bar {
      width: 60px;
      height: 6px;
      background-color: #e9ecef;
      border-radius: 3px;
      overflow: hidden;
    }
    
    .capacity-fill {
      height: 100%;
      border-radius: 3px;
      transition: width 0.3s;
    }
    
    .capacity-fill.low {
      background-color: #2ecc71;
    }
    
    .capacity-fill.medium {
      background-color: #f39c12;
    }
    
    .capacity-fill.high {
      background-color: #e74c3c;
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
    
    .form-group small {
      color: #6c757d;
      font-size: 0.8rem;
      margin-top: 5px;
      display: block;
    }
    
    /* Botões do formulário */
    .form-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
    }
    
    .btn-cancel {
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
    
    .btn-cancel:hover {
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
    
    /* Lista de alunos na turma */
    .students-list {
      max-height: 300px;
      overflow-y: auto;
      border: 1px solid #e9ecef;
      border-radius: 4px;
      padding: 10px;
    }
    
    .student-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px;
      border-bottom: 1px solid #f8f9fa;
    }
    
    .student-item:last-child {
      border-bottom: none;
    }
    
    .student-avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      object-fit: cover;
    }
    
    .student-info {
      flex: 1;
    }
    
    .student-name {
      font-weight: 500;
      color: #2c3e50;
    }
    
    .student-id {
      font-size: 0.8rem;
      color: #6c757d;
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
      
      .search-bar {
        width: 200px;
      }
      
      .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
      
      .action-btn {
        align-self: flex-start;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
      }
      
      .filters-grid {
        grid-template-columns: 1fr;
      }
      
      .form-row {
        flex-direction: column;
        gap: 15px;
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
            
            
            <!-- Configurações 
            <li data-tab="settings" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'current' : ''; ?>">
                    <i class="fas fa-cog"></i> 
                    <span>Configurações</span>
                </a>-->
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
                <input type="text" placeholder="Pesquisar turmas..." id="search-classes">
            </div>
            <div class="user-info">
                <div class="user user-dropdown">
                    <img src="login/images/semft-removebg-preview.png" alt="" class="userOptions__avatar img-circle" width="42" height="42">
                    <span><?php echo $_SESSION['fname']; ?></span>
                </div>
            </div>
        </header>
        
        <div class="page-content">
            <div class="section-header">
                <h2>Gestão de Turmas</h2>
                <button class="action-btn" id="add-class-btn">
                    <i class="fas fa-plus"></i> Adicionar Turma
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
            
            <!-- Cards de estatísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value">5</div>
                    <div class="stat-label">Total de Turmas</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-value">100</div>
                    <div class="stat-label">Total de Alunos</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-value">5</div>
                    <div class="stat-label">Diretores de Turma</div>
                </div>
            </div>
            
            <!-- Filtros para a tabela -->
            <div class="filters-container">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="filter-grade">Filtrar por Ano</label>
                        <select id="filter-grade">
                            <option value="">Todos os Anos</option>
                            <option value="10">10º Ano</option>
                            <option value="11">11º Ano</option>
                            <option value="12">12º Ano</option>
                            <option value="13">13º Ano</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-course">Filtrar por Curso</label>
                        <select id="filter-course">
                            <option value="">Todos os Cursos</option>
                            <option value="informatica">Informática</option>
                            <option value="gestao">Gestão</option>
                            <option value="contabilidade">Contabilidade</option>
                            <option value="turismo">Turismo</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-director">Filtrar por Diretor</label>
                        <select id="filter-director">
                            <option value="">Todos os Diretores</option>
                            <option value="maria">Maria Santos</option>
                            <option value="joao">João Oliveira</option>
                            <option value="ana">Ana Costa</option>
                            <option value="carlos">Carlos Ferreira</option>
                            <option value="pedro">Pedro Silva</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-capacity">Filtrar por Ocupação</label>
                        <select id="filter-capacity">
                            <option value="">Todas</option>
                            <option value="low">Baixa (< 70%)</option>
                            <option value="medium">Média (70-90%)</option>
                            <option value="high">Alta (> 90%)</option>
                        </select>
                    </div>
                </div>
                
                <div class="filter-buttons">
                    <button class="filter-btn reset-filter" id="reset-filters">
                        <i class="fas fa-undo"></i> Limpar Filtros
                    </button>
                    <button class="filter-btn apply-filter" id="apply-filters">
                        <i class="fas fa-filter"></i> Aplicar Filtros
                    </button>
                </div>
            </div>
            
         <!-- Tabela de turmas -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome da Turma</th>
                <th>Ano</th>
                <th>Diretor de Turma</th>
                <th>Ocupação</th>
                <th>Capacidade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="class-table-body">
            <?php
            require_once 'dbconnection.php';

            // Consulta turmas com nome do diretor
            $sql = "SELECT 
                        t.id, 
                        t.class_name, 
                        t.class_grade, 
                        t.class_capacity,
                        t.class_room,
                        t.class_director_id,
                        t.class_period,
                        t.class_year,
                        t.class_description,
                        t.class_observations,
                        CONCAT(p.fname, ' ', p.lname) AS director_name,
                        (
                            SELECT COUNT(*) 
                            FROM estudantes a 
                            WHERE a.area = t.id
                        ) AS total_alunos
                    FROM turma t
                    LEFT JOIN professores p ON t.class_director_id = p.id
                    ORDER BY t.class_grade, t.class_name";

            $stmt = $conn->query($sql);
            $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($turmas as $turma):
                $ocupacao = intval($turma['total_alunos']);
                $capacidade = intval($turma['class_capacity']);
                $percentual = $capacidade > 0 ? ($ocupacao / $capacidade) * 100 : 0;

                // Define a classe visual com base no percentual
                if ($percentual >= 90) {
                    $fillClass = 'high';
                } elseif ($percentual >= 70) {
                    $fillClass = 'medium';
                } else {
                    $fillClass = 'low';
                }
            ?>
                <tr>
                    <td><?= htmlspecialchars($turma['id']) ?></td>
                    <td>
                        <div class="class-name">
                            <div class="class-icon"><?= htmlspecialchars($turma['class_name']) ?></div>
                            <?= "Turma {$turma['class_grade']}ª {$turma['class_name']}" ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($turma['class_grade']) ?>º</td>
                    <td><?= htmlspecialchars($turma['director_name']) ?></td>
                    <td>
                        <div class="capacity-indicator">
                            <span><?= $ocupacao ?>/<?= $capacidade ?></span>
                            <div class="capacity-bar">
                                <div class="capacity-fill <?= $fillClass ?>" style="width: <?= round($percentual) ?>%;"></div>
                            </div>
                        </div>
                    </td>
                    <td><?= $capacidade ?></td>
                    <td>
                        <div class="action-buttons">
                          <a class="view-btn" href="view_class.php?id=<?= $turma['id'] ?>" title="Visualizar"><i class="fas fa-eye"></i></a>
                          <a class="edit-btn" href="edit_class.php?id=<?= $turma['id'] ?>" title="Editar"><i class="fas fa-edit"></i></a>
                          <a class="delete-btn" href="delete_class.php?id=<?= $turma['id'] ?>" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta turma?')"><i class="fas fa-trash"></i></a>
                        </div>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

            <!-- Paginação -->
            <div class="pagination">
                <button class="pagination-btn" data-page="prev"><i class="fas fa-chevron-left"></i></button>
                <button class="pagination-btn active" data-page="1">1</button>
                <button class="pagination-btn" data-page="2">2</button>
                <button class="pagination-btn" data-page="3">3</button>
                <button class="pagination-btn" data-page="next"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </main>
</div>

<!-- Modal para adicionar/editar turma -->
<div class="modal" id="class-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="class-modal-title">Adicionar Nova Turma</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-container">
               <form id="class-form" action="classes.php" method="POST" enctype="multipart/form-data">
                    <div class="form-section">
                        <h3>Informações Básicas</h3>
                        
                        <div class="form-group">
                            <label for="class-name">Nome da Turma*</label>
                            <input type="text" id="class-name" name="class_name" required placeholder="Ex: Turma 10ª A Informática">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="class-grade">Ano/Classe*</label>
                                <select id="class-grade" name="class_grade" required>
                                    <option value="">Selecionar Ano</option>
                                    <option value="1">1º Ano</option>
                                    <option value="2">2º Ano</option>
                                    <option value="3">3º Ano</option>
                                    <option value="4">4º Ano</option>
                                    <option value="5">5º Ano</option>
                                    <option value="6">6º Ano</option>
                                    <option value="7">7º Ano</option>
                                    <option value="8">8º Ano</option>
                                    <option value="9">9º Ano</option>
                                    <option value="10">10º Ano</option>
                                    <option value="11">11º Ano</option>
                                    <option value="12">12º Ano</option>
                                    <option value="13">13º Ano</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="class-course">Turma*</label>
                                <select id="class-course" name="class_course" required>
                                    <option value="">Selecionar Turma</option>
                                    <option value="pciclo">I Ciclo</option>
                                    <option value="sciclo">II Ciclo</option>
                                    <option value="puniv">Curso PUNIV</option>
                                    <option value="tecnico">Curso Técnico</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="class-capacity">Capacidade Máxima*</label>
                                <input type="number" id="class-capacity" name="class_capacity" min="1" max="30" value="25" required>
                                <small>Máximo recomendado: 25 alunos</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="class-room">Sala de Aula</label>
                                <input type="text" id="class-room" name="class_room" placeholder="Ex: Sala 101">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Direção e Coordenação</h3>
                        
                       <div class="form-group">
                            <label for="class-director_id">Diretor de Turma*</label>
                            <select id="class-director_id" name="class_director_id" required>
                                <option value="">Selecionar Diretor de Turma</option>
                                <?php foreach ($professores as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['fname'] . ' ' . $p['lname']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                    
                    <div class="form-section">
                        <h3>Horários e Períodos</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="class-period">Período*</label>
                                <select id="class-period" name="class_period" required>
                                    <option value="">Selecionar Período</option>
                                    <option value="morning">Manhã (08:00 - 12:30)</option>
                                    <option value="afternoon">Tarde (13:00 - 18:00)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="class-year">Ano Letivo*</label>
                                <select id="class-year" name="class_year" required>
                                    <option value="">Selecionar Ano Letivo</option>
                                    <option value="2023">2024/2025</option>
                                    <option value="2024" selected>2025/2026</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Informações Adicionais</h3>
                        
                        <div class="form-group">
                            <label for="class-description">Descrição da Turma</label>
                            <textarea id="class-description" name="class_description" rows="3" placeholder="Descrição opcional da turma, objetivos, características especiais..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="class-observations">Observações</label>
                            <textarea id="class-observations" name="class_observations" rows="2" placeholder="Observações importantes sobre a turma..."></textarea>
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn-cancel">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Salvar Turma
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar turma -->
<div class="modal" id="view-class-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalhes da Turma</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div id="class-details">
                <!-- Detalhes da turma serão carregados via JavaScript -->
            </div>
            <div class="form-buttons">
                <button class="btn-cancel close-view-btn">
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
            if (confirm("Tem certeza que deseja deletar esta turma?")) {
                window.location.href = `delete_class.php?id=${id}`;
            }
        });
    });
});


document.addEventListener('DOMContentLoaded', function () {
    fetch('classes.php') // ajuste o caminho se necessário
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('class-director_id');
            select.innerHTML = '<option value="">Selecionar Professor</option>'; // limpar opções anteriores

            data.forEach(prof => {
                const option = document.createElement('option');
                option.value = prof.id;
                option.textContent = `${prof.fname} ${prof.lname}`;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erro ao carregar professores:', error);
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
                        const submenuEl = submenu.querySelector('.submenu');
                        if (submenuEl) {
                            submenuEl.classList.remove('show');
                        }
                    }
                });
            }
            
            // Alterna o estado do submenu atual
            parent.classList.toggle('open');
            const submenuEl = parent.querySelector('.submenu');
            if (submenuEl) {
                submenuEl.classList.toggle('show');
            }
        });
    });
    
    // Garantir que os submenus da página atual estejam abertos
    document.querySelectorAll('.nav-links .has-submenu.active').forEach(item => {
        item.classList.add('open');
        const submenu = item.querySelector('.submenu');
        if (submenu) {
            submenu.classList.add('show');
        }
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
    
    // Controle do modal de turma
    const classModal = document.getElementById('class-modal');
    const viewClassModal = document.getElementById('view-class-modal');
    const addClassBtn = document.getElementById('add-class-btn');
    const closeModalBtns = document.querySelectorAll('.close-modal, .close-view-btn');
    
    // Abrir modal de adicionar turma
    if (addClassBtn) {
        addClassBtn.addEventListener('click', function() {
            document.getElementById('class-modal-title').textContent = 'Adicionar Nova Turma';
            document.getElementById('class-form').reset();
            classModal.style.display = 'block';
        });
    }
    
    // Fechar modais
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            classModal.style.display = 'none';
            viewClassModal.style.display = 'none';
        });
    });
    
    // Fechar modal ao clicar fora
    window.addEventListener('click', function(e) {
        if (e.target === classModal) {
            classModal.style.display = 'none';
        }
        if (e.target === viewClassModal) {
            viewClassModal.style.display = 'none';
        }
    });
    
  // Validação do formulário + envio via AJAX
const classForm = document.getElementById('class-form');

if (classForm) {
    classForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const className = document.getElementById('class-name').value.trim();
        const classGrade = document.getElementById('class-grade').value.trim();
        const classCourse = document.getElementById('class-course').value.trim();
        const classCapacity = Number(document.getElementById('class-capacity').value);
        const classDirector = document.getElementById('class-director_id').value.trim();
        const classPeriod = document.getElementById('class-period').value.trim();
        const classYear = document.getElementById('class-year').value.trim();

        // Validação simples
        if (!className || !classGrade || !classCourse || !classCapacity || !classDirector || !classPeriod || !classYear) {
            alert('Por favor, preencha todos os campos obrigatórios!');
            return false;
        }

        if (classCapacity < 1 || classCapacity > 30) {
            alert('A capacidade deve estar entre 1 e 30 alunos!');
            return false;
        }

        // Criar FormData para envio
        const formData = new FormData(classForm);

        fetch('classes.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text()) // ou response.json() se quiser JSON
        .then(data => {
            // Supondo que seu PHP retorne uma mensagem ou redirecione
            alert('Turma salva com sucesso!');
            
            // Fechar modal (se existir)
            const classModal = document.getElementById('class-modal');
            if(classModal) {
                classModal.style.display = 'none';
            }
            
            // Opcional: resetar formulário
            classForm.reset();

            console.log('Resposta do servidor:', data);
        })
        .catch(error => {
            alert('Erro ao salvar turma: ' + error);
            console.error('Erro ao enviar formulário:', error);
        });
    });
}

    
    // Botões de ação na tabela
    function setupActionButtons() {
        // Botões de visualizar
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const classId = this.getAttribute('data-id');
                viewClass(classId);
            });
        });
        
        // Botões de editar
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const classId = this.getAttribute('data-id');
                editClass(classId);
            });
        });
        
        // Botões de excluir
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const classId = this.getAttribute('data-id');
                deleteClass(classId);
            });
        });
    }
    
    // Função para visualizar turma
    function viewClass(id) {
        // Aqui você faria uma requisição AJAX para obter os detalhes da turma
        // Por enquanto, vamos simular com dados estáticos
        
        const classDetails = `
            <div class="class-profile">
                <div class="class-header">
                    <div class="class-info">
                        <h2>Turma 10ª A Informática</h2>
                        <p><strong>ID:</strong> ${id}</p>
                        <p><strong>Ano:</strong> 10º</p>
                        <p><strong>Curso:</strong> Informática</p>
                    </div>
                </div>
                
                <div class="class-details-grid">
                    <div class="detail-section">
                        <h3>Informações Gerais</h3>
                        <p><strong>Capacidade:</strong> 25 alunos</p>
                        <p><strong>Alunos Matriculados:</strong> 22</p>
                        <p><strong>Vagas Disponíveis:</strong> 3</p>
                        <p><strong>Sala:</strong> Sala 101</p>
                    </div>
                    
                    <div class="detail-section">
                        <h3>Direção</h3>
                        <p><strong>Diretor de Turma:</strong> Maria Santos</p>
                        <p><strong>Disciplina:</strong> Matemática</p>
                        <p><strong>Contato:</strong> maria.santos@escola.com</p>
                    </div>
                    
                    <div class="detail-section">
                        <h3>Horários</h3>
                        <p><strong>Período:</strong> Manhã</p>
                        <p><strong>Horário:</strong> 08:00 - 12:00</p>
                        <p><strong>Ano Letivo:</strong> 2024</p>
                    </div>
                    
                    <div class="detail-section">
                        <h3>Alunos da Turma</h3>
                        <div class="students-list">
                            <div class="student-item">
                                <img src="uploads/students/default-avatar.jpg" alt="Aluno" class="student-avatar">
                                <div class="student-info">
                                    <div class="student-name">João Silva</div>
                                    <div class="student-id">ID: STD001</div>
                                </div>
                            </div>
                            <div class="student-item">
                                <img src="uploads/students/default-avatar.jpg" alt="Aluno" class="student-avatar">
                                <div class="student-info">
                                    <div class="student-name">Ana Oliveira</div>
                                    <div class="student-id">ID: STD002</div>
                                </div>
                            </div>
                            <div class="student-item">
                                <img src="uploads/students/default-avatar.jpg" alt="Aluno" class="student-avatar">
                                <div class="student-info">
                                    <div class="student-name">Pedro Santos</div>
                                    <div class="student-id">ID: STD003</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('class-details').innerHTML = classDetails;
        viewClassModal.style.display = 'block';
    }
    
    // Função para editar turma
    function editClass(id) {
        // Aqui você faria uma requisição AJAX para obter os dados da turma
        // Por enquanto, vamos simular preenchendo o formulário com dados estáticos
        
        document.getElementById('class-modal-title').textContent = 'Editar Turma';
        
        // Preencher o formulário com dados simulados
        document.getElementById('class-name').value = 'Turma 10ª A Informática';
        document.getElementById('class-grade').value = '10';
        document.getElementById('class-course').value = 'informatica';
        document.getElementById('class-capacity').value = '25';
        document.getElementById('class-room').value = 'Sala 101';
        document.getElementById('class-director_id').value = '1';
        document.getElementById('class-period').value = 'morning';
        document.getElementById('class-year').value = '2024';
        document.getElementById('class-description').value = 'Turma de informática do 10º ano, focada em programação e desenvolvimento web.';
        
        // Exibir o modal
        classModal.style.display = 'block';
    }
    
    // Função para excluir turma
    function deleteClass(id) {
        if (confirm(`Tem certeza que deseja excluir a turma ${id}? Esta ação não pode ser desfeita.`)) {
            // Aqui você faria uma requisição AJAX para excluir a turma
            alert(`Turma ${id} excluída com sucesso!`);
            // Recarregar a tabela após excluir
            // loadClasses();
        }
    }
    
    // Inicializar os botões de ação
    setupActionButtons();
    
    // Filtros e pesquisa
    const searchInput = document.getElementById('search-classes');
    const filterGrade = document.getElementById('filter-grade');
    const filterCourse = document.getElementById('filter-course');
    const filterDirector = document.getElementById('filter-director');
    const filterCapacity = document.getElementById('filter-capacity');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            // Implementar pesquisa em tempo real
            const searchTerm = this.value.toLowerCase();
            filterTable(searchTerm, filterGrade.value, filterCourse.value, filterDirector.value, filterCapacity.value);
        });
    }
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const searchTerm = searchInput.value.toLowerCase();
            filterTable(searchTerm, filterGrade.value, filterCourse.value, filterDirector.value, filterCapacity.value);
        });
    }
    
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterGrade.value = '';
            filterCourse.value = '';
            filterDirector.value = '';
            filterCapacity.value = '';
            filterTable('', '', '', '', '');
        });
    }
    
    function filterTable(searchTerm, grade, course, director, capacity) {
        const rows = document.querySelectorAll('#class-table-body tr');
        
        rows.forEach(row => {
            const className = row.cells[1].textContent.toLowerCase();
            const classGrade = row.cells[2].textContent;
            const classDirector = row.cells[3].textContent.toLowerCase();
            
            const matchSearch = className.includes(searchTerm);
            const matchGrade = grade === '' || classGrade.includes(grade);
            const matchCourse = course === '' || className.includes(course);
            const matchDirector = director === '' || classDirector.includes(director);
            
            // Lógica para filtro de capacidade
            let matchCapacity = true;
            if (capacity !== '') {
                const capacityText = row.cells[4].textContent;
                const currentOccupancy = parseInt(capacityText.split('/')[0]);
                const maxCapacity = parseInt(capacityText.split('/')[1]);
                const occupancyRate = (currentOccupancy / maxCapacity) * 100;
                
                switch (capacity) {
                    case 'low':
                        matchCapacity = occupancyRate < 70;
                        break;
                    case 'medium':
                        matchCapacity = occupancyRate >= 70 && occupancyRate <= 90;
                        break;
                    case 'high':
                        matchCapacity = occupancyRate > 90;
                        break;
                }
            }
            
            if (matchSearch && matchGrade && matchCourse && matchDirector && matchCapacity) {
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
            // loadClassesPage(page);
        });
    });
    
    // Função para carregar turmas (simulação)
    function loadClasses() {
        // Em um ambiente real, você faria uma requisição AJAX para obter os dados
        console.log("Carregando turmas...");
        // A tabela já está preenchida com dados de exemplo
    }
    
    // Carregar turmas ao iniciar a página
    loadClasses();
});
</script>
</body>
</html>

<?php }else{
    header("Location: ../login.php");
    exit;
} ?>
