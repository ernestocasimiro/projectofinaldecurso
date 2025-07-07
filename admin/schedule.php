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
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão de Horários - Sistema Pitruca Camama</title>
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
    
    /* Estilos para o conteúdo da página de horários */
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
    
    /* Filtros para horários */
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
    
    /* Tabela de horários */
    .schedule-container {
      overflow-x: auto;
      margin-bottom: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .schedule-table {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 0;
    }
    
    .schedule-table thead th {
      background-color: #f8f9fa;
      color: #495057;
      font-weight: 600;
      text-align: center;
      padding: 12px 15px;
      border-bottom: 2px solid #e9ecef;
      position: sticky;
      top: 0;
      z-index: 10;
    }
    
    .schedule-table thead th:first-child {
      text-align: left;
      width: 120px;
    }
    
    .schedule-table tbody tr {
      transition: background-color 0.3s;
    }
    
    .schedule-table tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    .schedule-table tbody td {
      padding: 10px;
      border: 1px solid #e9ecef;
      vertical-align: top;
      height: 100px;
    }
    
    .schedule-table tbody td:first-child {
      background-color: #f8f9fa;
      font-weight: 500;
      color: #495057;
      text-align: center;
      vertical-align: middle;
      width: 120px;
    }
    
    .schedule-cell {
      position: relative;
      min-height: 100px;
    }
    
    .subject-card {
      background-color: #f8f9fa;
      border-radius: 6px;
      padding: 10px;
      height: 100%;
      transition: transform 0.3s, box-shadow 0.3s;
      cursor: pointer;
      position: relative;
    }
    
    .subject-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .subject-card h4 {
      margin: 0 0 5px 0;
      font-size: 0.95rem;
      font-weight: 600;
    }
    
    .subject-card p {
      margin: 0;
      font-size: 0.85rem;
      color: #6c757d;
    }
    
    .subject-card.math {
      background-color: rgba(52, 152, 219, 0.1);
      border-left: 4px solid #3498db;
    }
    
    .subject-card.language {
      background-color: rgba(46, 204, 113, 0.1);
      border-left: 4px solid #2ecc71;
    }
    
    .subject-card.science {
      background-color: rgba(155, 89, 182, 0.1);
      border-left: 4px solid #9b59b6;
    }
    
    .subject-card.tech {
      background-color: rgba(230, 126, 34, 0.1);
      border-left: 4px solid #e67e22;
    }
    
    .subject-card.arts {
      background-color: rgba(231, 76, 60, 0.1);
      border-left: 4px solid #e74c3c;
    }
    
    .subject-card.sports {
      background-color: rgba(52, 73, 94, 0.1);
      border-left: 4px solid #34495e;
    }
    
    .subject-card .actions {
      position: absolute;
      top: 5px;
      right: 5px;
      display: none;
    }
    
    .subject-card:hover .actions {
      display: flex;
      gap: 5px;
    }
    
    .subject-card .action-icon {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      background-color: rgba(255, 255, 255, 0.8);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 0.8rem;
      transition: background-color 0.3s;
    }
    
    .subject-card .action-icon:hover {
      background-color: #fff;
    }
    
    .subject-card .edit-icon {
      color: #ffc107;
    }
    
    .subject-card .delete-icon {
      color: #dc3545;
    }
    
    /* Visualização semanal/diária */
    .view-toggle {
      display: flex;
      gap: 10px;
      margin-bottom: 15px;
    }
    
    .view-btn {
      padding: 8px 15px;
      border: 1px solid #ced4da;
      background-color: #f8f9fa;
      color: #495057;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s;
      font-size: 0.9rem;
    }
    
    .view-btn.active {
      background-color: #3498db;
      color: white;
      border-color: #3498db;
    }
    
    /* Legenda de cores */
    .schedule-legend {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-top: 20px;
      padding: 15px;
      background-color: #f8f9fa;
      border-radius: 8px;
    }
    
    .legend-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
    }
    
    .legend-color {
      width: 16px;
      height: 16px;
      border-radius: 4px;
    }
    
    .legend-color.math {
      background-color: #3498db;
    }
    
    .legend-color.language {
      background-color: #2ecc71;
    }
    
    .legend-color.science {
      background-color: #9b59b6;
    }
    
    .legend-color.tech {
      background-color: #e67e22;
    }
    
    .legend-color.arts {
      background-color: #e74c3c;
    }
    
    .legend-color.sports {
      background-color: #34495e;
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
      max-width: 600px;
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
      
      .schedule-table {
        min-width: 800px;
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
                <input type="text" placeholder="Pesquisar horários..." id="search-schedules">
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
                <h2>Gestão de Horários</h2>
                <button class="action-btn" id="add-schedule-btn">
                    <i class="fas fa-plus"></i> Adicionar Horário
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
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-value">5</div>
                    <div class="stat-label">Turmas com Horários</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-value">8</div>
                    <div class="stat-label">Disciplinas</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">45</div>
                    <div class="stat-label">Aulas Semanais</div>
                </div>
            </div>
            
            <!-- Filtros para horários -->
            <div class="filters-container">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="filter-class">Filtrar por Turma</label>
                        <select id="filter-class">
                            <option value="">Todas as Turmas</option>
                            <option value="10A">Turma 10ª A Informática</option>
                            <option value="10B">Turma 10ª B Informática</option>
                            <option value="11">Turma 11ª Informática</option>
                            <option value="12">Turma 12ª Informática</option>
                            <option value="13">Turma 13ª Informática</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-teacher">Filtrar por Professor</label>
                        <select id="filter-teacher">
                            <option value="">Todos os Professores</option>
                            <option value="maria">Maria Santos</option>
                            <option value="joao">João Oliveira</option>
                            <option value="ana">Ana Costa</option>
                            <option value="carlos">Carlos Ferreira</option>
                            <option value="pedro">Pedro Silva</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-subject">Filtrar por Disciplina</label>
                        <select id="filter-subject">
                            <option value="">Todas as Disciplinas</option>
                            <option value="math">Matemática</option>
                            <option value="portuguese">Português</option>
                            <option value="english">Inglês</option>
                            <option value="physics">Física</option>
                            <option value="chemistry">Química</option>
                            <option value="informatics">Informática</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-day">Filtrar por Dia</label>
                        <select id="filter-day">
                            <option value="">Todos os Dias</option>
                            <option value="monday">Segunda-feira</option>
                            <option value="tuesday">Terça-feira</option>
                            <option value="wednesday">Quarta-feira</option>
                            <option value="thursday">Quinta-feira</option>
                            <option value="friday">Sexta-feira</option>
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
            
            <!-- Alternar entre visualização semanal e diária -->
            <div class="view-toggle">
                <button class="view-btn active" data-view="week">
                    <i class="fas fa-calendar-week"></i> Visualização Semanal
                </button>
                <button class="view-btn" data-view="day">
                    <i class="fas fa-calendar-day"></i> Visualização Diária
                </button>
            </div>
            
            <!-- Tabela de horários - Visualização Semanal -->
            <div class="schedule-container" id="week-view">
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Segunda-feira</th>
                            <th>Terça-feira</th>
                            <th>Quarta-feira</th>
                            <th>Quinta-feira</th>
                            <th>Sexta-feira</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>08:00 - 09:30</td>
                            <td class="schedule-cell">
                                <div class="subject-card math">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH001"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH001"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Matemática</h4>
                                    <p>Prof. Ana Costa</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card science">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH002"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH002"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Física</h4>
                                    <p>Prof. Carlos Ferreira</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card language">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH003"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH003"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Português</h4>
                                    <p>Prof. Maria Santos</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card math">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH004"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH004"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Matemática</h4>
                                    <p>Prof. Ana Costa</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card tech">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH005"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH005"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Informática</h4>
                                    <p>Prof. Pedro Silva</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>09:45 - 11:15</td>
                            <td class="schedule-cell">
                                <div class="subject-card language">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH006"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH006"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Inglês</h4>
                                    <p>Prof. João Oliveira</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card math">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH007"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH007"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Matemática</h4>
                                    <p>Prof. Ana Costa</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card science">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH008"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH008"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Química</h4>
                                    <p>Prof. Carlos Ferreira</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card language">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH009"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH009"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Português</h4>
                                    <p>Prof. Maria Santos</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card language">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH010"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH010"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Inglês</h4>
                                    <p>Prof. João Oliveira</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>11:30 - 13:00</td>
                            <td class="schedule-cell">
                                <div class="subject-card tech">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH011"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH011"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Informática</h4>
                                    <p>Prof. Pedro Silva</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card language">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH012"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH012"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Português</h4>
                                    <p>Prof. Maria Santos</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card tech">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH013"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH013"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Informática</h4>
                                    <p>Prof. Pedro Silva</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card science">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH014"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH014"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Física</h4>
                                    <p>Prof. Carlos Ferreira</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card science">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH015"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH015"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Química</h4>
                                    <p>Prof. Carlos Ferreira</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>14:00 - 15:30</td>
                            <td class="schedule-cell"></td>
                            <td class="schedule-cell">
                                <div class="subject-card arts">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH016"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH016"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Educação Visual</h4>
                                    <p>Prof. Ana Costa</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell"></td>
                            <td class="schedule-cell">
                                <div class="subject-card sports">
                                    <div class="actions">
                                        <span class="action-icon edit-icon" data-id="SCH017"><i class="fas fa-edit"></i></span>
                                        <span class="action-icon delete-icon" data-id="SCH017"><i class="fas fa-trash"></i></span>
                                    </div>
                                    <h4>Educação Física</h4>
                                    <p>Prof. João Oliveira</p>
                                    <small>Turma 10ª A</small>
                                </div>
                            </td>
                            <td class="schedule-cell"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Tabela de horários - Visualização Diária (inicialmente oculta) -->
            <div class="schedule-container" id="day-view" style="display: none;">
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Turma 10ª A</th>
                            <th>Turma 10ª B</th>
                            <th>Turma 11ª</th>
                            <th>Turma 12ª</th>
                            <th>Turma 13ª</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>08:00 - 09:30</td>
                            <td class="schedule-cell">
                                <div class="subject-card math">
                                    <h4>Matemática</h4>
                                    <p>Prof. Ana Costa</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card language">
                                    <h4>Português</h4>
                                    <p>Prof. Maria Santos</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card science">
                                    <h4>Física</h4>
                                    <p>Prof. Carlos Ferreira</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card tech">
                                    <h4>Informática</h4>
                                    <p>Prof. Pedro Silva</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card language">
                                    <h4>Inglês</h4>
                                    <p>Prof. João Oliveira</p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>09:45 - 11:15</td>
                            <td class="schedule-cell">
                                <div class="subject-card language">
                                    <h4>Inglês</h4>
                                    <p>Prof. João Oliveira</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card math">
                                    <h4>Matemática</h4>
                                    <p>Prof. Ana Costa</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card language">
                                    <h4>Português</h4>
                                    <p>Prof. Maria Santos</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card science">
                                    <h4>Química</h4>
                                    <p>Prof. Carlos Ferreira</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card tech">
                                    <h4>Informática</h4>
                                    <p>Prof. Pedro Silva</p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>11:30 - 13:00</td>
                            <td class="schedule-cell">
                                <div class="subject-card tech">
                                    <h4>Informática</h4>
                                    <p>Prof. Pedro Silva</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card science">
                                    <h4>Física</h4>
                                    <p>Prof. Carlos Ferreira</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card math">
                                    <h4>Matemática</h4>
                                    <p>Prof. Ana Costa</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card language">
                                    <h4>Português</h4>
                                    <p>Prof. Maria Santos</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card science">
                                    <h4>Química</h4>
                                    <p>Prof. Carlos Ferreira</p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>14:00 - 15:30</td>
                            <td class="schedule-cell"></td>
                            <td class="schedule-cell">
                                <div class="subject-card arts">
                                    <h4>Educação Visual</h4>
                                    <p>Prof. Ana Costa</p>
                                </div>
                            </td>
                            <td class="schedule-cell">
                                <div class="subject-card sports">
                                    <h4>Educação Física</h4>
                                    <p>Prof. João Oliveira</p>
                                </div>
                            </td>
                            <td class="schedule-cell"></td>
                            <td class="schedule-cell">
                                <div class="subject-card tech">
                                    <h4>Informática</h4>
                                    <p>Prof. Pedro Silva</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Legenda de cores -->
            <div class="schedule-legend">
                <div class="legend-item">
                    <div class="legend-color math"></div>
                    <span>Matemática</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color language"></div>
                    <span>Línguas</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color science"></div>
                    <span>Ciências</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color tech"></div>
                    <span>Informática</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color arts"></div>
                    <span>Artes</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color sports"></div>
                    <span>Educação Física</span>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal para adicionar/editar horário -->
<div class="modal" id="schedule-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="schedule-modal-title">Adicionar Novo Horário</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-container">
                <form id="schedule-form">
                    <div class="form-section">
                        <h3>Informações Básicas</h3>
                        
                        <div class="form-group">
                            <label for="schedule-class">Turma*</label>
                            <select id="schedule-class" name="schedule_class" required>
                                <option value="">Selecionar Turma</option>
                                <option value="CLS001">Turma 10ª A Informática</option>
                                <option value="CLS002">Turma 10ª B Informática</option>
                                <option value="CLS003">Turma 11ª Informática</option>
                                <option value="CLS004">Turma 12ª Informática</option>
                                <option value="CLS005">Turma 13ª Informática</option>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="schedule-day">Dia da Semana*</label>
                                <select id="schedule-day" name="schedule_day" required>
                                    <option value="">Selecionar Dia</option>
                                    <option value="monday">Segunda-feira</option>
                                    <option value="tuesday">Terça-feira</option>
                                    <option value="wednesday">Quarta-feira</option>
                                    <option value="thursday">Quinta-feira</option>
                                    <option value="friday">Sexta-feira</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="schedule-time">Horário*</label>
                                <select id="schedule-time" name="schedule_time" required>
                                    <option value="">Selecionar Horário</option>
                                    <option value="1">08:00 - 09:30</option>
                                    <option value="2">09:45 - 11:15</option>
                                    <option value="3">11:30 - 13:00</option>
                                    <option value="4">14:00 - 15:30</option>
                                    <option value="5">15:45 - 17:15</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Disciplina e Professor</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="schedule-subject">Disciplina*</label>
                                <select id="schedule-subject" name="schedule_subject" required>
                                    <option value="">Selecionar Disciplina</option>
                                    <option value="math">Matemática</option>
                                    <option value="portuguese">Português</option>
                                    <option value="english">Inglês</option>
                                    <option value="physics">Física</option>
                                    <option value="chemistry">Química</option>
                                    <option value="informatics">Informática</option>
                                    <option value="arts">Educação Visual</option>
                                    <option value="sports">Educação Física</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="schedule-teacher">Professor*</label>
                                <select id="schedule-teacher" name="schedule_teacher" required>
                                    <option value="">Selecionar Professor</option>
                                    <option value="1">Maria Santos</option>
                                    <option value="2">João Oliveira</option>
                                    <option value="3">Ana Costa</option>
                                    <option value="4">Carlos Ferreira</option>
                                    <option value="5">Pedro Silva</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="schedule-room">Sala de Aula</label>
                            <input type="text" id="schedule-room" name="schedule_room" placeholder="Ex: Sala 101">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Informações Adicionais</h3>
                        
                        <div class="form-group">
                            <label for="schedule-notes">Observações</label>
                            <textarea id="schedule-notes" name="schedule_notes" rows="3" placeholder="Observações ou notas adicionais sobre esta aula..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="schedule-recurring">Recorrência</label>
                            <select id="schedule-recurring" name="schedule_recurring">
                                <option value="weekly">Semanal (Padrão)</option>
                                <option value="biweekly">Quinzenal</option>
                                <option value="monthly">Mensal</option>
                                <option value="once">Apenas uma vez</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn-cancel">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Salvar Horário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
    
    // Controle do modal de horário
    const scheduleModal = document.getElementById('schedule-modal');
    const addScheduleBtn = document.getElementById('add-schedule-btn');
    const closeModalBtns = document.querySelectorAll('.close-modal, .btn-cancel');
    
    // Abrir modal de adicionar horário
    if (addScheduleBtn) {
        addScheduleBtn.addEventListener('click', function() {
            document.getElementById('schedule-modal-title').textContent = 'Adicionar Novo Horário';
            document.getElementById('schedule-form').reset();
            scheduleModal.style.display = 'block';
        });
    }
    
    // Fechar modal
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            scheduleModal.style.display = 'none';
        });
    });
    
    // Fechar modal ao clicar fora
    window.addEventListener('click', function(e) {
        if (e.target === scheduleModal) {
            scheduleModal.style.display = 'none';
        }
    });
    
    // Validação do formulário
    const scheduleForm = document.getElementById('schedule-form');
    
    if (scheduleForm) {
        scheduleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const scheduleClass = document.getElementById('schedule-class').value;
            const scheduleDay = document.getElementById('schedule-day').value;
            const scheduleTime = document.getElementById('schedule-time').value;
            const scheduleSubject = document.getElementById('schedule-subject').value;
            const scheduleTeacher = document.getElementById('schedule-teacher').value;
            
            if (!scheduleClass || !scheduleDay || !scheduleTime || !scheduleSubject || !scheduleTeacher) {
                alert('Por favor, preencha todos os campos obrigatórios!');
                return false;
            }
            
            // Simular salvamento
            alert('Horário salvo com sucesso!');
            scheduleModal.style.display = 'none';
            
            // Aqui você implementaria a lógica para salvar no banco de dados
            console.log('Dados do horário:', {
                scheduleClass,
                scheduleDay,
                scheduleTime,
                scheduleSubject,
                scheduleTeacher
            });
        });
    }
    
    // Alternar entre visualizações
    const viewBtns = document.querySelectorAll('.view-btn');
    const weekView = document.getElementById('week-view');
    const dayView = document.getElementById('day-view');
    
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.getAttribute('data-view');
            
            // Remover classe active de todos os botões
            viewBtns.forEach(b => b.classList.remove('active'));
            
            // Adicionar classe active ao botão clicado
            this.classList.add('active');
            
            // Mostrar a visualização correspondente
            if (view === 'week') {
                weekView.style.display = 'block';
                dayView.style.display = 'none';
            } else if (view === 'day') {
                weekView.style.display = 'none';
                dayView.style.display = 'block';
            }
        });
    });
    
    // Botões de ação nos cards de disciplina
    const editButtons = document.querySelectorAll('.edit-icon');
    const deleteButtons = document.querySelectorAll('.delete-icon');
    
    // Editar horário
    editButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation(); // Impede a propagação do evento para o card
            const scheduleId = this.getAttribute('data-id');
            editSchedule(scheduleId);
        });
    });
    
    // Excluir horário
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation(); // Impede a propagação do evento para o card
            const scheduleId = this.getAttribute('data-id');
            deleteSchedule(scheduleId);
        });
    });
    
    // Função para editar horário
    function editSchedule(id) {
        // Aqui você faria uma requisição AJAX para obter os dados do horário
        // Por enquanto, vamos simular preenchendo o formulário com dados estáticos
        
        document.getElementById('schedule-modal-title').textContent = 'Editar Horário';
        
        // Preencher o formulário com dados simulados
        document.getElementById('schedule-class').value = 'CLS001';
        document.getElementById('schedule-day').value = 'monday';
        document.getElementById('schedule-time').value = '1';
        document.getElementById('schedule-subject').value = 'math';
        document.getElementById('schedule-teacher').value = '3';
        document.getElementById('schedule-room').value = 'Sala 101';
        document.getElementById('schedule-notes').value = 'Aula de revisão para a prova.';
        
        // Exibir o modal
        scheduleModal.style.display = 'block';
    }
    
    // Função para excluir horário
    function deleteSchedule(id) {
        if (confirm(`Tem certeza que deseja excluir o horário ${id}? Esta ação não pode ser desfeita.`)) {
            // Aqui você faria uma requisição AJAX para excluir o horário
            alert(`Horário ${id} excluído com sucesso!`);
            // Recarregar a tabela após excluir
            // loadSchedules();
        }
    }
    
    // Filtros e pesquisa
    const searchInput = document.getElementById('search-schedules');
    const filterClass = document.getElementById('filter-class');
    const filterTeacher = document.getElementById('filter-teacher');
    const filterSubject = document.getElementById('filter-subject');
    const filterDay = document.getElementById('filter-day');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            const classFilter = filterClass ? filterClass.value : '';
            const teacherFilter = filterTeacher ? filterTeacher.value : '';
            const subjectFilter = filterSubject ? filterSubject.value : '';
            const dayFilter = filterDay ? filterDay.value : '';
            
            // Implementar lógica de filtro
            filterSchedules(searchTerm, classFilter, teacherFilter, subjectFilter, dayFilter);
        });
    }
    
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (filterClass) filterClass.value = '';
            if (filterTeacher) filterTeacher.value = '';
            if (filterSubject) filterSubject.value = '';
            if (filterDay) filterDay.value = '';
            
            // Limpar filtros
            filterSchedules('', '', '', '', '');
        });
    }
    
    // Função para filtrar horários
    function filterSchedules(search, classFilter, teacherFilter, subjectFilter, dayFilter) {
        // Em um ambiente real, você faria uma requisição AJAX para obter os dados filtrados
        // Por enquanto, vamos simular com um alerta
        alert(`Filtros aplicados: Pesquisa="${search}", Turma="${classFilter}", Professor="${teacherFilter}", Disciplina="${subjectFilter}", Dia="${dayFilter}"`);
    }
    
    // Inicializar a página
    function initPage() {
        // Carregar dados iniciais
        console.log('Página de horários inicializada');
    }
    
    // Inicializar a página
    initPage();
});
</script>
</body>
</html>

<?php }else{
    header("Location: ../login.php");
    exit;
} ?>