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
  <title>Controle de Presença - Sistema Pitruca Camama</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
      max-height: 500px; /* Valor alto o suficiente para acomodar todos os itens */
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
    
    /* Estilos para o conteúdo da página de presença */
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
    
    /* Filtros para a tabela de presença */
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
    
    /* Calendário de presença */
    .calendar-container {
      margin-bottom: 30px;
    }
    
    .calendar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .calendar-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: #2c3e50;
    }
    
    .calendar-nav {
      display: flex;
      gap: 10px;
    }
    
    .calendar-nav-btn {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 4px;
      padding: 5px 10px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 5px;
      transition: all 0.3s;
    }
    
    .calendar-nav-btn:hover {
      background-color: #e9ecef;
    }
    
    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 5px;
    }
    
    .calendar-day-header {
      text-align: center;
      font-weight: 600;
      padding: 10px;
      background-color: #f8f9fa;
      border-radius: 4px;
    }
    
    .calendar-day {
      text-align: center;
      padding: 10px;
      border: 1px solid #dee2e6;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .calendar-day:hover {
      background-color: #f8f9fa;
    }
    
    .calendar-day.today {
      background-color: rgba(52, 152, 219, 0.1);
      border-color: #3498db;
      font-weight: 600;
    }
    
    .calendar-day.selected {
      background-color: #3498db;
      color: white;
      border-color: #3498db;
    }
    
    .calendar-day.has-absences {
      background-color: rgba(231, 76, 60, 0.1);
      border-color: #e74c3c;
    }
    
    .calendar-day.weekend {
      background-color: #f8f9fa;
      color: #adb5bd;
    }
    
    .calendar-day.other-month {
      color: #adb5bd;
      background-color: #f8f9fa;
      opacity: 0.5;
    }
    
    /* Tabela de presença */
    .attendance-table-container {
      overflow-x: auto;
      margin-bottom: 20px;
    }
    
    .attendance-table {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 0;
    }
    
    .attendance-table thead th {
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
    
    .attendance-table tbody tr {
      transition: background-color 0.3s;
    }
    
    .attendance-table tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    .attendance-table tbody td {
      padding: 12px 15px;
      border-bottom: 1px solid #e9ecef;
      color: #495057;
    }
    
    .attendance-table .student-name {
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .attendance-table .student-avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      object-fit: cover;
    }
    
    .attendance-status {
      display: flex;
      gap: 10px;
    }
    
    .status-radio {
      display: none;
    }
    
    .status-label {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      cursor: pointer;
      transition: all 0.3s;
      border: 2px solid transparent;
    }
    
    .status-label.present {
      background-color: rgba(46, 204, 113, 0.1);
      color: #2ecc71;
    }
    
    .status-label.absent {
      background-color: rgba(231, 76, 60, 0.1);
      color: #e74c3c;
    }
    
    .status-label.late {
      background-color: rgba(230, 126, 34, 0.1);
      color: #e67e22;
    }
    
    .status-label.justified {
      background-color: rgba(52, 152, 219, 0.1);
      color: #3498db;
    }
    
    .status-radio:checked + .status-label {
      border-color: currentColor;
      background-color: currentColor;
      color: white;
    }
    
    .status-radio:checked + .status-label i {
      color: white;
    }
    
    .attendance-actions {
      display: flex;
      gap: 5px;
    }
    
    .attendance-action-btn {
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
    
    .justify-btn {
      background-color: #17a2b8;
      color: white;
    }
    
    .note-btn {
      background-color: #ffc107;
      color: #212529;
    }
    
    .attendance-action-btn:hover {
      opacity: 0.85;
    }
    
    /* Botões de ação em massa */
    .mass-actions {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }
    
    .mass-action-btn {
      padding: 8px 15px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 5px;
      transition: background-color 0.3s;
    }
    
    .mark-all-present {
      background-color: #2ecc71;
      color: white;
    }
    
    .mark-all-absent {
      background-color: #e74c3c;
      color: white;
    }
    
    .save-attendance {
      background-color: #3498db;
      color: white;
      margin-left: auto;
    }
    
    .mass-action-btn:hover {
      opacity: 0.9;
    }
    
    /* Modal para justificar falta */
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
    
    .form-group {
      margin-bottom: 15px;
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
      min-height: 100px;
    }
    
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
      
      .calendar-grid {
        grid-template-columns: repeat(7, 1fr);
        font-size: 0.8rem;
      }
      
      .calendar-day {
        padding: 5px;
      }
      
      .mass-actions {
        flex-wrap: wrap;
      }
      
      .save-attendance {
        margin-left: 0;
        width: 100%;
        justify-content: center;
        margin-top: 10px;
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
                <input type="text" placeholder="Pesquisar aluno..." id="search-student">
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
                <h2>Controle de Presença</h2>
                <button class="action-btn" id="export-attendance">
                    <i class="fas fa-file-export"></i> Exportar Relatório
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
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value">95%</div>
                    <div class="stat-label">Taxa de Presença</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-value">5%</div>
                    <div class="stat-label">Taxa de Ausência</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">2%</div>
                    <div class="stat-label">Taxa de Atrasos</div>
                </div>
            </div>
            
            <!-- Filtros para a tabela de presença -->
            <div class="filters-container">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="filter-class">Turma</label>
                        <select id="filter-class">
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
                        <label for="filter-date">Data</label>
                        <input type="date" id="filter-date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-period">Período</label>
                        <select id="filter-period">
                            <option value="morning">Manhã</option>
                            <option value="afternoon">Tarde</option>
                            <option value="all">Dia Completo</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-subject">Disciplina</label>
                        <select id="filter-subject">
                            <option value="">Todas as Disciplinas</option>
                            <option value="math">Matemática</option>
                            <option value="portuguese">Português</option>
                            <option value="science">Ciências</option>
                            <option value="history">História</option>
                            <option value="geography">Geografia</option>
                            <option value="english">Inglês</option>
                            <option value="arts">Artes</option>
                            <option value="pe">Educação Física</option>
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
            
            <!-- Calendário de presença -->
            <div class="calendar-container">
                <div class="calendar-header">
                    <div class="calendar-title">Maio 2023</div>
                    <div class="calendar-nav">
                        <button class="calendar-nav-btn" id="prev-month">
                            <i class="fas fa-chevron-left"></i> Mês Anterior
                        </button>
                        <button class="calendar-nav-btn" id="today">
                            <i class="fas fa-calendar-day"></i> Hoje
                        </button>
                        <button class="calendar-nav-btn" id="next-month">
                            Próximo Mês <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                <div class="calendar-grid">
                    <div class="calendar-day-header">Dom</div>
                    <div class="calendar-day-header">Seg</div>
                    <div class="calendar-day-header">Ter</div>
                    <div class="calendar-day-header">Qua</div>
                    <div class="calendar-day-header">Qui</div>
                    <div class="calendar-day-header">Sex</div>
                    <div class="calendar-day-header">Sáb</div>
                    
                    <!-- Dias do mês anterior -->
                    <div class="calendar-day other-month weekend">30</div>
                    
                    <!-- Dias do mês atual -->
                    <div class="calendar-day">1</div>
                    <div class="calendar-day">2</div>
                    <div class="calendar-day">3</div>
                    <div class="calendar-day">4</div>
                    <div class="calendar-day">5</div>
                    <div class="calendar-day weekend">6</div>
                    <div class="calendar-day weekend">7</div>
                    <div class="calendar-day">8</div>
                    <div class="calendar-day">9</div>
                    <div class="calendar-day">10</div>
                    <div class="calendar-day has-absences">11</div>
                    <div class="calendar-day">12</div>
                    <div class="calendar-day weekend">13</div>
                    <div class="calendar-day weekend">14</div>
                    <div class="calendar-day">15</div>
                    <div class="calendar-day">16</div>
                    <div class="calendar-day">17</div>
                    <div class="calendar-day has-absences">18</div>
                    <div class="calendar-day">19</div>
                    <div class="calendar-day weekend">20</div>
                    <div class="calendar-day weekend">21</div>
                    <div class="calendar-day today selected">22</div>
                    <div class="calendar-day">23</div>
                    <div class="calendar-day">24</div>
                    <div class="calendar-day">25</div>
                    <div class="calendar-day">26</div>
                    <div class="calendar-day weekend">27</div>
                    <div class="calendar-day weekend">28</div>
                    <div class="calendar-day">29</div>
                    <div class="calendar-day">30</div>
                    <div class="calendar-day">31</div>
                    
                    <!-- Dias do próximo mês -->
                    <div class="calendar-day other-month">1</div>
                    <div class="calendar-day other-month">2</div>
                    <div class="calendar-day other-month weekend">3</div>
                </div>
            </div>
            
            <!-- Botões de ação em massa -->
            <div class="mass-actions">
                <button class="mass-action-btn mark-all-present" id="mark-all-present">
                    <i class="fas fa-check-circle"></i> Marcar Todos Presentes
                </button>
                <button class="mass-action-btn mark-all-absent" id="mark-all-absent">
                    <i class="fas fa-times-circle"></i> Marcar Todos Ausentes
                </button>
                <button class="mass-action-btn save-attendance" id="save-attendance">
                    <i class="fas fa-save"></i> Salvar Registro de Presença
                </button>
            </div>
            
            <!-- Tabela de presença -->
            <div class="attendance-table-container">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Aluno</th>
                            <th style="width: 180px;">Status</th>
                            <th style="width: 120px;">Horário</th>
                            <th style="width: 150px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <div class="student-name">
                                    <img src="uploads/students/default-avatar.jpg" alt="Foto do Aluno" class="student-avatar">
                                    João Silva
                                </div>
                            </td>
                            <td>
                                <div class="attendance-status">
                                    <input type="radio" name="status-1" id="present-1" class="status-radio" checked>
                                    <label for="present-1" class="status-label present" title="Presente">
                                        <i class="fas fa-check"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-1" id="absent-1" class="status-radio">
                                    <label for="absent-1" class="status-label absent" title="Ausente">
                                        <i class="fas fa-times"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-1" id="late-1" class="status-radio">
                                    <label for="late-1" class="status-label late" title="Atrasado">
                                        <i class="fas fa-clock"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-1" id="justified-1" class="status-radio">
                                    <label for="justified-1" class="status-label justified" title="Falta Justificada">
                                        <i class="fas fa-file-medical"></i>
                                    </label>
                                </div>
                            </td>
                            <td>08:00 - 12:00</td>
                            <td>
                                <div class="attendance-actions">
                                    <button class="attendance-action-btn justify-btn" data-id="1" title="Justificar Falta">
                                        <i class="fas fa-file-medical"></i>
                                    </button>
                                    <button class="attendance-action-btn note-btn" data-id="1" title="Adicionar Observação">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>
                                <div class="student-name">
                                    <img src="uploads/students/default-avatar.jpg" alt="Foto do Aluno" class="student-avatar">
                                    Ana Oliveira
                                </div>
                            </td>
                            <td>
                                <div class="attendance-status">
                                    <input type="radio" name="status-2" id="present-2" class="status-radio">
                                    <label for="present-2" class="status-label present" title="Presente">
                                        <i class="fas fa-check"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-2" id="absent-2" class="status-radio" checked>
                                    <label for="absent-2" class="status-label absent" title="Ausente">
                                        <i class="fas fa-times"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-2" id="late-2" class="status-radio">
                                    <label for="late-2" class="status-label late" title="Atrasado">
                                        <i class="fas fa-clock"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-2" id="justified-2" class="status-radio">
                                    <label for="justified-2" class="status-label justified" title="Falta Justificada">
                                        <i class="fas fa-file-medical"></i>
                                    </label>
                                </div>
                            </td>
                            <td>08:00 - 12:00</td>
                            <td>
                                <div class="attendance-actions">
                                    <button class="attendance-action-btn justify-btn" data-id="2" title="Justificar Falta">
                                        <i class="fas fa-file-medical"></i>
                                    </button>
                                    <button class="attendance-action-btn note-btn" data-id="2" title="Adicionar Observação">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>
                                <div class="student-name">
                                    <img src="uploads/students/default-avatar.jpg" alt="Foto do Aluno" class="student-avatar">
                                    Pedro Santos
                                </div>
                            </td>
                            <td>
                                <div class="attendance-status">
                                    <input type="radio" name="status-3" id="present-3" class="status-radio">
                                    <label for="present-3" class="status-label present" title="Presente">
                                        <i class="fas fa-check"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-3" id="absent-3" class="status-radio">
                                    <label for="absent-3" class="status-label absent" title="Ausente">
                                        <i class="fas fa-times"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-3" id="late-3" class="status-radio" checked>
                                    <label for="late-3" class="status-label late" title="Atrasado">
                                        <i class="fas fa-clock"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-3" id="justified-3" class="status-radio">
                                    <label for="justified-3" class="status-label justified" title="Falta Justificada">
                                        <i class="fas fa-file-medical"></i>
                                    </label>
                                </div>
                            </td>
                            <td>08:15 - 12:00</td>
                            <td>
                                <div class="attendance-actions">
                                    <button class="attendance-action-btn justify-btn" data-id="3" title="Justificar Falta">
                                        <i class="fas fa-file-medical"></i>
                                    </button>
                                    <button class="attendance-action-btn note-btn" data-id="3" title="Adicionar Observação">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>
                                <div class="student-name">
                                    <img src="uploads/students/default-avatar.jpg" alt="Foto do Aluno" class="student-avatar">
                                    Maria Costa
                                </div>
                            </td>
                            <td>
                                <div class="attendance-status">
                                    <input type="radio" name="status-4" id="present-4" class="status-radio">
                                    <label for="present-4" class="status-label present" title="Presente">
                                        <i class="fas fa-check"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-4" id="absent-4" class="status-radio">
                                    <label for="absent-4" class="status-label absent" title="Ausente">
                                        <i class="fas fa-times"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-4" id="late-4" class="status-radio">
                                    <label for="late-4" class="status-label late" title="Atrasado">
                                        <i class="fas fa-clock"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-4" id="justified-4" class="status-radio" checked>
                                    <label for="justified-4" class="status-label justified" title="Falta Justificada">
                                        <i class="fas fa-file-medical"></i>
                                    </label>
                                </div>
                            </td>
                            <td>08:00 - 12:00</td>
                            <td>
                                <div class="attendance-actions">
                                    <button class="attendance-action-btn justify-btn" data-id="4" title="Justificar Falta">
                                        <i class="fas fa-file-medical"></i>
                                    </button>
                                    <button class="attendance-action-btn note-btn" data-id="4" title="Adicionar Observação">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>
                                <div class="student-name">
                                    <img src="uploads/students/default-avatar.jpg" alt="Foto do Aluno" class="student-avatar">
                                    Carlos Ferreira
                                </div>
                            </td>
                            <td>
                                <div class="attendance-status">
                                    <input type="radio" name="status-5" id="present-5" class="status-radio" checked>
                                    <label for="present-5" class="status-label present" title="Presente">
                                        <i class="fas fa-check"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-5" id="absent-5" class="status-radio">
                                    <label for="absent-5" class="status-label absent" title="Ausente">
                                        <i class="fas fa-times"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-5" id="late-5" class="status-radio">
                                    <label for="late-5" class="status-label late" title="Atrasado">
                                        <i class="fas fa-clock"></i>
                                    </label>
                                    
                                    <input type="radio" name="status-5" id="justified-5" class="status-radio">
                                    <label for="justified-5" class="status-label justified" title="Falta Justificada">
                                        <i class="fas fa-file-medical"></i>
                                    </label>
                                </div>
                            </td>
                            <td>08:00 - 12:00</td>
                            <td>
                                <div class="attendance-actions">
                                    <button class="attendance-action-btn justify-btn" data-id="5" title="Justificar Falta">
                                        <i class="fas fa-file-medical"></i>
                                    </button>
                                    <button class="attendance-action-btn note-btn" data-id="5" title="Adicionar Observação">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Modal para justificar falta -->
<div class="modal" id="justify-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Justificar Falta</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="justify-form">
                <input type="hidden" id="student-id" name="student_id">
                <input type="hidden" id="attendance-date" name="attendance_date">
                
                <div class="form-group">
                    <label for="justification-type">Tipo de Justificação</label>
                    <select id="justification-type" name="justification_type" required>
                        <option value="">Selecione o tipo</option>
                        <option value="medical">Atestado Médico</option>
                        <option value="family">Motivo Familiar</option>
                        <option value="transportation">Problema de Transporte</option>
                        <option value="other">Outro</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="justification-date">Data da Justificação</label>
                    <input type="date" id="justification-date" name="justification_date" required>
                </div>
                
                <div class="form-group">
                    <label for="justification-document">Documento Comprobatório (opcional)</label>
                    <input type="file" id="justification-document" name="justification_document">
                </div>
                
                <div class="form-group">
                    <label for="justification-description">Descrição</label>
                    <textarea id="justification-description" name="justification_description" required></textarea>
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="btn-cancel" id="cancel-justify">Cancelar</button>
                    <button type="submit" class="btn-submit">Salvar Justificação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para adicionar observação -->
<div class="modal" id="note-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Adicionar Observação</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="note-form">
                <input type="hidden" id="note-student-id" name="student_id">
                <input type="hidden" id="note-attendance-date" name="attendance_date">
                
                <div class="form-group">
                    <label for="note-text">Observação</label>
                    <textarea id="note-text" name="note_text" required></textarea>
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="btn-cancel" id="cancel-note">Cancelar</button>
                    <button type="submit" class="btn-submit">Salvar Observação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
    
    // Inicializar o seletor de data
    flatpickr("#filter-date", {
        dateFormat: "Y-m-d",
        defaultDate: new Date()
    });
    
    flatpickr("#justification-date", {
        dateFormat: "Y-m-d",
        defaultDate: new Date()
    });
    
    // Calendário de presença
    const calendarDays = document.querySelectorAll('.calendar-day:not(.other-month)');
    
    calendarDays.forEach(day => {
        day.addEventListener('click', function() {
            // Remover a classe selected de todos os dias
            calendarDays.forEach(d => d.classList.remove('selected'));
            
            // Adicionar a classe selected ao dia clicado
            this.classList.add('selected');
            
            // Aqui você carregaria os dados de presença para o dia selecionado
            const dayNumber = this.textContent;
            console.log(`Carregando dados de presença para o dia ${dayNumber}`);
        });
    });
    
    // Navegação do calendário
    const prevMonthBtn = document.getElementById('prev-month');
    const nextMonthBtn = document.getElementById('next-month');
    const todayBtn = document.getElementById('today');
    
    prevMonthBtn.addEventListener('click', function() {
        console.log('Mês anterior');
        // Implementar lógica para navegar para o mês anterior
    });
    
    nextMonthBtn.addEventListener('click', function() {
        console.log('Próximo mês');
        // Implementar lógica para navegar para o próximo mês
    });
    
    todayBtn.addEventListener('click', function() {
        console.log('Hoje');
        // Implementar lógica para navegar para o dia atual
    });
    
    // Botões de ação em massa
    const markAllPresentBtn = document.getElementById('mark-all-present');
    const markAllAbsentBtn = document.getElementById('mark-all-absent');
    const saveAttendanceBtn = document.getElementById('save-attendance');
    
    markAllPresentBtn.addEventListener('click', function() {
        // Marcar todos os alunos como presentes
        document.querySelectorAll('input[id^="present-"]').forEach(radio => {
            radio.checked = true;
        });
    });
    
    markAllAbsentBtn.addEventListener('click', function() {
        // Marcar todos os alunos como ausentes
        document.querySelectorAll('input[id^="absent-"]').forEach(radio => {
            radio.checked = true;
        });
    });
    
    saveAttendanceBtn.addEventListener('click', function() {
        // Salvar o registro de presença
        alert('Registro de presença salvo com sucesso!');
    });
    
    // Filtros
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    
    applyFiltersBtn.addEventListener('click', function() {
        const classFilter = document.getElementById('filter-class').value;
        const dateFilter = document.getElementById('filter-date').value;
        const periodFilter = document.getElementById('filter-period').value;
        const subjectFilter = document.getElementById('filter-subject').value;
        
        console.log('Filtros aplicados:', {
            class: classFilter,
            date: dateFilter,
            period: periodFilter,
            subject: subjectFilter
        });
        
        // Implementar lógica para filtrar os dados
    });
    
    resetFiltersBtn.addEventListener('click', function() {
        // Limpar todos os filtros
        document.getElementById('filter-class').value = '';
        document.getElementById('filter-date').value = new Date().toISOString().split('T')[0];
        document.getElementById('filter-period').value = 'morning';
        document.getElementById('filter-subject').value = '';
    });
    
    // Pesquisa de aluno
    const searchInput = document.getElementById('search-student');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        // Filtrar a tabela de presença
        const rows = document.querySelectorAll('.attendance-table tbody tr');
        
        rows.forEach(row => {
            const studentName = row.querySelector('.student-name').textContent.toLowerCase();
            
            if (studentName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Exportar relatório
    const exportBtn = document.getElementById('export-attendance');
    
    exportBtn.addEventListener('click', function() {
        alert('Relatório de presença exportado com sucesso!');
        // Implementar lógica para exportar o relatório
    });
    
    // Modal de justificação
    const justifyModal = document.getElementById('justify-modal');
    const justifyBtns = document.querySelectorAll('.justify-btn');
    const closeJustifyModal = justifyModal.querySelector('.close-modal');
    const cancelJustifyBtn = document.getElementById('cancel-justify');
    const justifyForm = document.getElementById('justify-form');
    
    justifyBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.getAttribute('data-id');
            document.getElementById('student-id').value = studentId;
            document.getElementById('attendance-date').value = document.getElementById('filter-date').value;
            
            justifyModal.style.display = 'block';
        });
    });
    
    closeJustifyModal.addEventListener('click', function() {
        justifyModal.style.display = 'none';
    });
    
    cancelJustifyBtn.addEventListener('click', function() {
        justifyModal.style.display = 'none';
    });
    
    justifyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Processar o formulário de justificação
        const studentId = document.getElementById('student-id').value;
        const justificationType = document.getElementById('justification-type').value;
        const justificationDate = document.getElementById('justification-date').value;
        const justificationDescription = document.getElementById('justification-description').value;
        
        console.log('Justificação enviada:', {
            studentId,
            justificationType,
            justificationDate,
            justificationDescription
        });
        
        // Marcar o aluno como justificado
        document.getElementById(`justified-${studentId}`).checked = true;
        
        // Fechar o modal
        justifyModal.style.display = 'none';
        
        // Exibir mensagem de sucesso
        alert('Falta justificada com sucesso!');
    });
    
    // Modal de observação
    const noteModal = document.getElementById('note-modal');
    const noteBtns = document.querySelectorAll('.note-btn');
    const closeNoteModal = noteModal.querySelector('.close-modal');
    const cancelNoteBtn = document.getElementById('cancel-note');
    const noteForm = document.getElementById('note-form');
    
    noteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.getAttribute('data-id');
            document.getElementById('note-student-id').value = studentId;
            document.getElementById('note-attendance-date').value = document.getElementById('filter-date').value;
            
            noteModal.style.display = 'block';
        });
    });
    
    closeNoteModal.addEventListener('click', function() {
        noteModal.style.display = 'none';
    });
    
    cancelNoteBtn.addEventListener('click', function() {
        noteModal.style.display = 'none';
    });
    
    noteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Processar o formulário de observação
        const studentId = document.getElementById('note-student-id').value;
        const noteText = document.getElementById('note-text').value;
        
        console.log('Observação enviada:', {
            studentId,
            noteText
        });
        
        // Fechar o modal
        noteModal.style.display = 'none';
        
        // Exibir mensagem de sucesso
        alert('Observação adicionada com sucesso!');
    });
    
    // Fechar modais ao clicar fora
    window.addEventListener('click', function(e) {
        if (e.target === justifyModal) {
            justifyModal.style.display = 'none';
        }
        
        if (e.target === noteModal) {
            noteModal.style.display = 'none';
        }
    });
});
</script>
</body>
</html>

<?php }else{
    header("Location: ../login.php");
    exit;
} ?>
