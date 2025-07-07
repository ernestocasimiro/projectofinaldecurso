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
  <title>Gestão de Provas - Sistema Pitruca Camama</title>
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
    
    /* Estilos para o conteúdo da página de provas */
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
    
    /* Filtros para provas */
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
    
    /* Tabela de provas */
    .table-container {
      overflow-x: auto;
      margin-bottom: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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
      vertical-align: middle;
    }
    
    .data-table tbody tr:last-child td {
      border-bottom: none;
    }
    
    /* Status badges */
    .status-badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 500;
      text-align: center;
      min-width: 80px;
    }
    
    .status-badge.scheduled {
      background-color: rgba(52, 152, 219, 0.1);
      color: #3498db;
    }
    
    .status-badge.in-progress {
      background-color: rgba(230, 126, 34, 0.1);
      color: #e67e22;
    }
    
    .status-badge.completed {
      background-color: rgba(46, 204, 113, 0.1);
      color: #2ecc71;
    }
    
    .status-badge.cancelled {
      background-color: rgba(231, 76, 60, 0.1);
      color: #e74c3c;
    }
    
    /* Botões de ação na tabela */
    .action-buttons {
      display: flex;
      gap: 5px;
      justify-content: center;
    }
    
    .table-action-btn {
      width: 30px;
      height: 30px;
      border-radius: 4px;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .table-action-btn.edit {
      background-color: rgba(52, 152, 219, 0.1);
      color: #3498db;
    }
    
    .table-action-btn.view {
      background-color: rgba(46, 204, 113, 0.1);
      color: #2ecc71;
    }
    
    .table-action-btn.delete {
      background-color: rgba(231, 76, 60, 0.1);
      color: #e74c3c;
    }
    
    .table-action-btn.grade {
      background-color: rgba(155, 89, 182, 0.1);
      color: #9b59b6;
    }
    
    .table-action-btn:hover {
      opacity: 0.8;
    }
    
    /* Paginação */
    .pagination {
      display: flex;
      justify-content: center;
      gap: 5px;
      margin-top: 20px;
    }
    
    .pagination-btn {
      min-width: 35px;
      height: 35px;
      border: 1px solid #ced4da;
      background-color: #fff;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s;
      color: #495057;
    }
    
    .pagination-btn.active {
      background-color: #3498db;
      color: white;
      border-color: #3498db;
    }
    
    .pagination-btn:hover:not(.active):not(:disabled) {
      background-color: #f8f9fa;
    }
    
    .pagination-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }
    
    /* Calendário de provas */
    .calendar-container {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      padding: 20px;
      margin-bottom: 20px;
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
      width: 30px;
      height: 30px;
      border-radius: 4px;
      border: 1px solid #ced4da;
      background-color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .calendar-nav-btn:hover {
      background-color: #f8f9fa;
    }
    
    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 5px;
    }
    
    .calendar-day-header {
      text-align: center;
      font-weight: 600;
      color: #495057;
      padding: 10px;
    }
    
    .calendar-day {
      min-height: 80px;
      border: 1px solid #e9ecef;
      border-radius: 4px;
      padding: 5px;
      position: relative;
    }
    
    .calendar-day.today {
      background-color: rgba(52, 152, 219, 0.05);
      border-color: #3498db;
    }
    
    .calendar-day.other-month {
      background-color: #f8f9fa;
      color: #adb5bd;
    }
    
    .calendar-day-number {
      font-weight: 600;
      margin-bottom: 5px;
    }
    
    .calendar-event {
      font-size: 0.8rem;
      padding: 2px 5px;
      border-radius: 3px;
      margin-bottom: 3px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      cursor: pointer;
    }
    
    .calendar-event.math {
      background-color: rgba(52, 152, 219, 0.1);
      color: #3498db;
    }
    
    .calendar-event.portuguese {
      background-color: rgba(46, 204, 113, 0.1);
      color: #2ecc71;
    }
    
    .calendar-event.english {
      background-color: rgba(155, 89, 182, 0.1);
      color: #9b59b6;
    }
    
    .calendar-event.physics {
      background-color: rgba(230, 126, 34, 0.1);
      color: #e67e22;
    }
    
    .calendar-event.chemistry {
      background-color: rgba(231, 76, 60, 0.1);
      color: #e74c3c;
    }
    
    .calendar-event.informatics {
      background-color: rgba(52, 73, 94, 0.1);
      color: #34495e;
    }
    
    /* Visualização de lista/calendário */
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
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .view-btn.active {
      background-color: #3498db;
      color: white;
      border-color: #3498db;
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
      
      .calendar-grid {
        grid-template-columns: repeat(1, 1fr);
      }
      
      .calendar-day-header:not(:first-child) {
        display: none;
      }
      
      .calendar-day {
        display: flex;
        flex-direction: column;
      }
      
      .calendar-day-number {
        display: flex;
        align-items: center;
        gap: 5px;
      }
      
      .calendar-day-number::before {
        content: attr(data-day);
        font-weight: normal;
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
    
    /* Estilos para o upload de arquivos */
    .file-upload {
      border: 2px dashed #ced4da;
      border-radius: 4px;
      padding: 20px;
      text-align: center;
      margin-bottom: 15px;
      transition: border-color 0.3s;
    }
    
    .file-upload:hover {
      border-color: #3498db;
    }
    
    .file-upload-icon {
      font-size: 2rem;
      color: #6c757d;
      margin-bottom: 10px;
    }
    
    .file-upload-text {
      margin-bottom: 10px;
      color: #495057;
    }
    
    .file-upload-btn {
      display: inline-block;
      padding: 8px 15px;
      background-color: #3498db;
      color: white;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .file-upload-btn:hover {
      background-color: #2980b9;
    }
    
    .file-upload input[type="file"] {
      display: none;
    }
    
    .file-list {
      margin-top: 15px;
    }
    
    .file-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 8px 10px;
      background-color: #f8f9fa;
      border-radius: 4px;
      margin-bottom: 5px;
    }
    
    .file-item-name {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .file-item-icon {
      color: #3498db;
    }
    
    .file-item-remove {
      color: #e74c3c;
      cursor: pointer;
    }
    
    /* Estilos para o modal de visualização de prova */
    .test-details {
      margin-bottom: 20px;
    }
    
    .test-details-item {
      display: flex;
      margin-bottom: 10px;
    }
    
    .test-details-label {
      font-weight: 600;
      width: 150px;
      color: #495057;
    }
    
    .test-details-value {
      flex: 1;
    }
    
    .test-files {
      margin-top: 20px;
    }
    
    .test-files-title {
      font-weight: 600;
      margin-bottom: 10px;
      color: #495057;
    }
    
    .test-file-link {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 10px;
      background-color: #f8f9fa;
      border-radius: 4px;
      margin-bottom: 5px;
      color: #3498db;
      text-decoration: none;
    }
    
    .test-file-link:hover {
      background-color: #e9ecef;
    }
    
    /* Estilos para o modal de lançamento de notas */
    .grade-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    
    .grade-table th,
    .grade-table td {
      padding: 10px;
      border: 1px solid #e9ecef;
    }
    
    .grade-table th {
      background-color: #f8f9fa;
      font-weight: 600;
      text-align: left;
    }
    
    .grade-input {
      width: 60px;
      padding: 5px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      text-align: center;
    }
    
    .grade-input:focus {
      border-color: #3498db;
      outline: none;
    }
    
    .grade-status {
      display: inline-block;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      margin-right: 5px;
    }
    
    .grade-status.approved {
      background-color: #2ecc71;
    }
    
    .grade-status.failed {
      background-color: #e74c3c;
    }
    
    .grade-status.pending {
      background-color: #f39c12;
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
                <input type="text" placeholder="Pesquisar provas..." id="search-tests">
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
                <h2>Gestão de Provas e Exames</h2>
                <button class="action-btn" id="add-test-btn">
                    <i class="fas fa-plus"></i> Agendar Nova Prova
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
                    <div class="stat-value">8</div>
                    <div class="stat-label">Provas Agendadas</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-value">3</div>
                    <div class="stat-label">Provas em Processo</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value">12</div>
                    <div class="stat-label">Provas Concluídas</div>
                </div>
                
            </div>
            
            <!-- Filtros para provas -->
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
                        <label for="filter-status">Filtrar por Estado</label>
                        <select id="filter-status">
                            <option value="">Todos os Estados</option>
                            <option value="scheduled">Agendado</option>
                            <option value="in-progress">Em Processo</option>
                            <option value="completed">Concluído</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-date-start">Data Inicial</label>
                        <input type="date" id="filter-date-start">
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-date-end">Data Final</label>
                        <input type="date" id="filter-date-end">
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
            
            <!-- Alternar entre visualização de lista e calendário -->
            <div class="view-toggle">
                <button class="view-btn active" data-view="list">
                    <i class="fas fa-list"></i> Visualização em Lista
                </button>
                <button class="view-btn" data-view="calendar">
                    <i class="fas fa-calendar-alt"></i> Visualização em Calendário
                </button>
            </div>
            
            <!-- Visualização em Lista -->
            <div id="list-view">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Disciplina</th>
                                <th>Turma</th>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Professor</th>
                                <th>Estado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="test-table-body">
                            <tr>
                                <td>TST001</td>
                                <td>Matemática</td>
                                <td>Turma 10ª A Informática</td>
                                <td>15/04/2025</td>
                                <td>08:00 - 10:00</td>
                                <td>Ana Costa</td>
                                <td><span class="status-badge scheduled">Agendado</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="table-action-btn edit" title="Editar" data-id="TST001">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="table-action-btn view" title="Ver Detalhes" data-id="TST001">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="table-action-btn delete" title="Excluir" data-id="TST001">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>TST002</td>
                                <td>Português</td>
                                <td>Turma 10ª B Informática</td>
                                <td>16/04/2025</td>
                                <td>10:30 - 12:30</td>
                                <td>Maria Santos</td>
                                <td><span class="status-badge scheduled">Agendado</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="table-action-btn edit" title="Editar" data-id="TST002">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="table-action-btn view" title="Ver Detalhes" data-id="TST002">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="table-action-btn delete" title="Excluir" data-id="TST002">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>TST003</td>
                                <td>Física</td>
                                <td>Turma 11ª Informática</td>
                                <td>10/04/2025</td>
                                <td>08:00 - 10:00</td>
                                <td>Carlos Ferreira</td>
                                <td><span class="status-badge in-progress">Em Processo</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="table-action-btn edit" title="Editar" data-id="TST003">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="table-action-btn view" title="Ver Detalhes" data-id="TST003">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="table-action-btn grade" title="Lançar Notas" data-id="TST003">
                                            <i class="fas fa-clipboard-check"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>TST004</td>
                                <td>Informática</td>
                                <td>Turma 12ª Informática</td>
                                <td>05/04/2025</td>
                                <td>13:00 - 15:00</td>
                                <td>Pedro Silva</td>
                                <td><span class="status-badge completed">Concluído</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="table-action-btn view" title="Ver Detalhes" data-id="TST004">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="table-action-btn grade" title="Ver Notas" data-id="TST004">
                                            <i class="fas fa-clipboard-check"></i>
                                        </button>
                                        <button class="table-action-btn delete" title="Excluir" data-id="TST004">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>TST005</td>
                                <td>Inglês</td>
                                <td>Turma 13ª Informática</td>
                                <td>02/04/2025</td>
                                <td>10:30 - 12:30</td>
                                <td>João Oliveira</td>
                                <td><span class="status-badge completed">Concluído</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="table-action-btn view" title="Ver Detalhes" data-id="TST005">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="table-action-btn grade" title="Ver Notas" data-id="TST005">
                                            <i class="fas fa-clipboard-check"></i>
                                        </button>
                                        <button class="table-action-btn delete" title="Excluir" data-id="TST005">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>TST006</td>
                                <td>Química</td>
                                <td>Turma 11ª Informática</td>
                                <td>18/04/2025</td>
                                <td>13:00 - 15:00</td>
                                <td>Carlos Ferreira</td>
                                <td><span class="status-badge scheduled">Agendado</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="table-action-btn edit" title="Editar" data-id="TST006">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="table-action-btn view" title="Ver Detalhes" data-id="TST006">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="table-action-btn delete" title="Excluir" data-id="TST006">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>TST007</td>
                                <td>Matemática</td>
                                <td>Turma 12ª Informática</td>
                                <td>20/04/2025</td>
                                <td>08:00 - 10:00</td>
                                <td>Ana Costa</td>
                                <td><span class="status-badge scheduled">Agendado</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="table-action-btn edit" title="Editar" data-id="TST007">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="table-action-btn view" title="Ver Detalhes" data-id="TST007">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="table-action-btn delete" title="Excluir" data-id="TST007">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>TST008</td>
                                <td>Português</td>
                                <td>Turma 13ª Informática</td>
                                <td>22/04/2025</td>
                                <td>10:30 - 12:30</td>
                                <td>Maria Santos</td>
                                <td><span class="status-badge in-progress">Em Processo</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="table-action-btn edit" title="Editar" data-id="TST008">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="table-action-btn view" title="Ver Detalhes" data-id="TST008">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="table-action-btn grade" title="Lançar Notas" data-id="TST008">
                                            <i class="fas fa-clipboard-check"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination">
                    <button class="pagination-btn" disabled><i class="fas fa-chevron-left"></i></button>
                    <button class="pagination-btn active">1</button>
                    <button class="pagination-btn">2</button>
                    <button class="pagination-btn">3</button>
                    <button class="pagination-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            
            <!-- Visualização em Calendário (inicialmente oculta) -->
            <div id="calendar-view" style="display: none;">
                <div class="calendar-container">
                    <div class="calendar-header">
                        <div class="calendar-title">Abril 2025</div>
                        <div class="calendar-nav">
                            <button class="calendar-nav-btn" id="prev-month"><i class="fas fa-chevron-left"></i></button>
                            <button class="calendar-nav-btn" id="next-month"><i class="fas fa-chevron-right"></i></button>
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
                        <div class="calendar-day other-month">
                            <div class="calendar-day-number" data-day="Domingo">30</div>
                        </div>
                        <div class="calendar-day other-month">
                            <div class="calendar-day-number" data-day="Segunda-feira">31</div>
                        </div>
                        
                        <!-- Dias do mês atual -->
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Terça-feira">1</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Quarta-feira">2</div>
                            <div class="calendar-event english" data-id="TST005">
                                Inglês - 10:30
                            </div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Quinta-feira">3</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Sexta-feira">4</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Sábado">5</div>
                        </div>
                        
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Domingo">6</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Segunda-feira">7</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Terça-feira">8</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Quarta-feira">9</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Quinta-feira">10</div>
                            <div class="calendar-event physics" data-id="TST003">
                                Física - 08:00
                            </div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Sexta-feira">11</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Sábado">12</div>
                        </div>
                        
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Domingo">13</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Segunda-feira">14</div>
                        </div>
                        <div class="calendar-day today">
                            <div class="calendar-day-number" data-day="Terça-feira">15</div>
                            <div class="calendar-event math" data-id="TST001">
                                Matemática - 08:00
                            </div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Quarta-feira">16</div>
                            <div class="calendar-event portuguese" data-id="TST002">
                                Português - 10:30
                            </div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Quinta-feira">17</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Sexta-feira">18</div>
                            <div class="calendar-event chemistry" data-id="TST006">
                                Química - 13:00
                            </div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Sábado">19</div>
                        </div>
                        
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Domingo">20</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Segunda-feira">21</div>
                            <div class="calendar-event math" data-id="TST007">
                                Matemática - 08:00
                            </div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Terça-feira">22</div>
                            <div class="calendar-event portuguese" data-id="TST008">
                                Português - 10:30
                            </div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Quarta-feira">23</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Quinta-feira">24</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Sexta-feira">25</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Sábado">26</div>
                        </div>
                        
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Domingo">27</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Segunda-feira">28</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Terça-feira">29</div>
                        </div>
                        <div class="calendar-day">
                            <div class="calendar-day-number" data-day="Quarta-feira">30</div>
                        </div>
                        
                        <!-- Dias do próximo mês -->
                        <div class="calendar-day other-month">
                            <div class="calendar-day-number" data-day="Quinta-feira">1</div>
                        </div>
                        <div class="calendar-day other-month">
                            <div class="calendar-day-number" data-day="Sexta-feira">2</div>
                        </div>
                        <div class="calendar-day other-month">
                            <div class="calendar-day-number" data-day="Sábado">3</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal para agendar/editar provas -->
<div class="modal" id="test-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="test-modal-title">Agendar Nova Prova</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-container">
                <form id="test-form">
                    <div class="form-section">
                        <h3>Informações Básicas</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="test-subject">Disciplina*</label>
                                <select id="test-subject" name="test_subject" required>
                                    <option value="">Selecionar Disciplina</option>
                                    <option value="math">Matemática</option>
                                    <option value="portuguese">Português</option>
                                    <option value="english">Inglês</option>
                                    <option value="physics">Física</option>
                                    <option value="chemistry">Química</option>
                                    <option value="informatics">Informática</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="test-class">Turma*</label>
                                <select id="test-class" name="test_class" required>
                                    <option value="">Selecionar Turma</option>
                                    <option value="CLS001">Turma 10ª A Informática</option>
                                    <option value="CLS002">Turma 10ª B Informática</option>
                                    <option value="CLS003">Turma 11ª Informática</option>
                                    <option value="CLS004">Turma 12ª Informática</option>
                                    <option value="CLS005">Turma 13ª Informática</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="test-date">Data*</label>
                                <input type="date" id="test-date" name="test_date" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="test-time">Horário*</label>
                                <select id="test-time" name="test_time" required>
                                    <option value="">Selecionar Horário</option>
                                    <option value="1">08:00 - 10:00</option>
                                    <option value="2">10:30 - 12:30</option>
                                    <option value="3">13:00 - 15:00</option>
                                    <option value="4">15:30 - 17:30</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Detalhes da Prova</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="test-teacher">Professor*</label>
                                <select id="test-teacher" name="test_teacher" required>
                                    <option value="">Selecionar Professor</option>
                                    <option value="1">Maria Santos</option>
                                    <option value="2">João Oliveira</option>
                                    <option value="3">Ana Costa</option>
                                    <option value="4">Carlos Ferreira</option>
                                    <option value="5">Pedro Silva</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="test-status">Estado*</label>
                                <select id="test-status" name="test_status" required>
                                    <option value="scheduled">Agendado</option>
                                    <option value="in-progress">Em Processo</option>
                                    <option value="completed">Concluído</option>
                                    <option value="cancelled">Cancelado</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="test-room">Sala</label>
                            <input type="text" id="test-room" name="test_room" placeholder="Ex: Sala 101">
                        </div>
                        
                        <div class="form-group">
                            <label for="test-description">Descrição da Prova</label>
                            <textarea id="test-description" name="test_description" rows="3" placeholder="Descreva o conteúdo e objetivos da prova..."></textarea>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Materiais da Prova</h3>
                        
                        <div class="file-upload">
                            <div class="file-upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="file-upload-text">
                                Arraste e solte arquivos aqui ou clique para selecionar
                            </div>
                            <label for="test-files" class="file-upload-btn">Selecionar Arquivos</label>
                            <input type="file" id="test-files" name="test_files" multiple>
                            
                            <div class="file-list" id="file-list">
                                <!-- Arquivos serão adicionados aqui via JavaScript -->
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="test-notes">Observações</label>
                            <textarea id="test-notes" name="test_notes" rows="2" placeholder="Observações adicionais sobre a prova..."></textarea>
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn-cancel" id="cancel-test">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Salvar Prova
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar detalhes da prova -->
<div class="modal" id="view-test-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalhes da Prova</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="test-details">
                <div class="test-details-item">
                    <div class="test-details-label">ID:</div>
                    <div class="test-details-value" id="view-test-id">TST001</div>
                </div>
                <div class="test-details-item">
                    <div class="test-details-label">Disciplina:</div>
                    <div class="test-details-value" id="view-test-subject">Matemática</div>
                </div>
                <div class="test-details-item">
                    <div class="test-details-label">Turma:</div>
                    <div class="test-details-value" id="view-test-class">Turma 10ª A Informática</div>
                </div>
                <div class="test-details-item">
                    <div class="test-details-label">Data:</div>
                    <div class="test-details-value" id="view-test-date">15/04/2025</div>
                </div>
                <div class="test-details-item">
                    <div class="test-details-label">Horário:</div>
                    <div class="test-details-value" id="view-test-time">08:00 - 10:00</div>
                </div>
                <div class="test-details-item">
                    <div class="test-details-label">Professor:</div>
                    <div class="test-details-value" id="view-test-teacher">Ana Costa</div>
                </div>
                <div class="test-details-item">
                    <div class="test-details-label">Sala:</div>
                    <div class="test-details-value" id="view-test-room">Sala 101</div>
                </div>
                <div class="test-details-item">
                    <div class="test-details-label">Estado:</div>
                    <div class="test-details-value" id="view-test-status">
                        <span class="status-badge scheduled">Agendado</span>
                    </div>
                </div>
                <div class="test-details-item">
                    <div class="test-details-label">Descrição:</div>
                    <div class="test-details-value" id="view-test-description">
                        Prova de Matemática sobre funções quadráticas e exponenciais. Os alunos devem trazer calculadora científica.
                    </div>
                </div>
                <div class="test-details-item">
                    <div class="test-details-label">Observações:</div>
                    <div class="test-details-value" id="view-test-notes">
                        Alunos com necessidades especiais terão tempo adicional de 30 minutos.
                    </div>
                </div>
                
                <div class="test-files">
                    <div class="test-files-title">Arquivos da Prova:</div>
                    <a href="#" class="test-file-link">
                        <i class="fas fa-file-pdf"></i> Prova_Matematica_10A.pdf
                    </a>
                    <a href="#" class="test-file-link">
                        <i class="fas fa-file-word"></i> Gabarito_Prova_Matematica.docx
                    </a>
                </div>
            </div>
            
            <div class="form-buttons">
                <button type="button" class="btn-cancel" id="close-view-test">
                    <i class="fas fa-times"></i> Fechar
                </button>
                <button type="button" class="btn-submit" id="edit-from-view">
                    <i class="fas fa-edit"></i> Editar Prova
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para lançamento de notas -->
<div class="modal" id="grade-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="grade-modal-title">Lançamento de Notas</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="test-details">
                <div class="test-details-item">
                    <div class="test-details-label">Prova:</div>
                    <div class="test-details-value" id="grade-test-id">TST003 - Física</div>
                </div>
                <div class="test-details-item">
                    <div class="test-details-label">Turma:</div>
                    <div class="test-details-value" id="grade-test-class">Turma 11ª Informática</div>
                </div>
                <div class="test-details-item">
                    <div class="test-details-label">Data:</div>
                    <div class="test-details-value" id="grade-test-date">10/04/2025</div>
                </div>
            </div>
            
            <table class="grade-table">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Aluno</th>
                        <th>Nota (0-20)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Ana Silva</td>
                        <td><input type="number" class="grade-input" min="0" max="20" value="15"></td>
                        <td><span class="grade-status approved"></span> Aprovado</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Bruno Santos</td>
                        <td><input type="number" class="grade-input" min="0" max="20" value="12"></td>
                        <td><span class="grade-status approved"></span> Aprovado</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Carla Oliveira</td>
                        <td><input type="number" class="grade-input" min="0" max="20" value="8"></td>
                        <td><span class="grade-status failed"></span> Reprovado</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Daniel Costa</td>
                        <td><input type="number" class="grade-input" min="0" max="20" value="18"></td>
                        <td><span class="grade-status approved"></span> Aprovado</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Elena Ferreira</td>
                        <td><input type="number" class="grade-input" min="0" max="20" value="10"></td>
                        <td><span class="grade-status approved"></span> Aprovado</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Fábio Martins</td>
                        <td><input type="number" class="grade-input" min="0" max="20"></td>
                        <td><span class="grade-status pending"></span> Pendente</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="form-buttons">
                <button type="button" class="btn-cancel" id="close-grade-modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn-submit" id="save-grades">
                    <i class="fas fa-save"></i> Salvar Notas
                </button>
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
    
    // Alternar entre visualizações de lista e calendário
    const viewBtns = document.querySelectorAll('.view-btn');
    const listView = document.getElementById('list-view');
    const calendarView = document.getElementById('calendar-view');
    
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.getAttribute('data-view');
            
            // Remover classe active de todos os botões
            viewBtns.forEach(b => b.classList.remove('active'));
            
            // Adicionar classe active ao botão clicado
            this.classList.add('active');
            
            // Mostrar a visualização correspondente
            if (view === 'list') {
                listView.style.display = 'block';
                calendarView.style.display = 'none';
            } else if (view === 'calendar') {
                listView.style.display = 'none';
                calendarView.style.display = 'block';
            }
        });
    });
    
    // Controle do modal de prova
    const testModal = document.getElementById('test-modal');
    const viewTestModal = document.getElementById('view-test-modal');
    const gradeModal = document.getElementById('grade-modal');
    const addTestBtn = document.getElementById('add-test-btn');
    const closeModalBtns = document.querySelectorAll('.close-modal');
    const cancelTestBtn = document.getElementById('cancel-test');
    const closeViewTestBtn = document.getElementById('close-view-test');
    const closeGradeModalBtn = document.getElementById('close-grade-modal');
    
    // Abrir modal de adicionar prova
    if (addTestBtn) {
        addTestBtn.addEventListener('click', function() {
            document.getElementById('test-modal-title').textContent = 'Agendar Nova Prova';
            document.getElementById('test-form').reset();
            testModal.style.display = 'block';
        });
    }
    
    // Fechar modais
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            testModal.style.display = 'none';
            viewTestModal.style.display = 'none';
            gradeModal.style.display = 'none';
        });
    });
    
    if (cancelTestBtn) {
        cancelTestBtn.addEventListener('click', function() {
            testModal.style.display = 'none';
        });
    }
    
    if (closeViewTestBtn) {
        closeViewTestBtn.addEventListener('click', function() {
            viewTestModal.style.display = 'none';
        });
    }
    
    if (closeGradeModalBtn) {
        closeGradeModalBtn.addEventListener('click', function() {
            gradeModal.style.display = 'none';
        });
    }
    
    // Fechar modal ao clicar fora
    window.addEventListener('click', function(e) {
        if (e.target === testModal) {
            testModal.style.display = 'none';
        }
        if (e.target === viewTestModal) {
            viewTestModal.style.display = 'none';
        }
        if (e.target === gradeModal) {
            gradeModal.style.display = 'none';
        }
    });
    
    // Botões de ação na tabela
    const editButtons = document.querySelectorAll('.table-action-btn.edit');
    const viewButtons = document.querySelectorAll('.table-action-btn.view');
    const deleteButtons = document.querySelectorAll('.table-action-btn.delete');
    const gradeButtons = document.querySelectorAll('.table-action-btn.grade');
    
    // Editar prova
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const testId = this.getAttribute('data-id');
            editTest(testId);
        });
    });
    
    // Ver detalhes da prova
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const testId = this.getAttribute('data-id');
            viewTest(testId);
        });
    });
    
    // Excluir prova
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const testId = this.getAttribute('data-id');
            deleteTest(testId);
        });
    });
    
    // Lançar/ver notas
    gradeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const testId = this.getAttribute('data-id');
            openGradeModal(testId);
        });
    });
    
    // Eventos do calendário
    const calendarEvents = document.querySelectorAll('.calendar-event');
    
    calendarEvents.forEach(event => {
        event.addEventListener('click', function() {
            const testId = this.getAttribute('data-id');
            viewTest(testId);
        });
    });
    
    // Botão para editar a partir da visualização
    const editFromViewBtn = document.getElementById('edit-from-view');
    
    if (editFromViewBtn) {
        editFromViewBtn.addEventListener('click', function() {
            const testId = document.getElementById('view-test-id').textContent;
            viewTestModal.style.display = 'none';
            editTest(testId);
        });
    }
    
    // Função para editar prova
    function editTest(id) {
        // Aqui você faria uma requisição AJAX para obter os dados da prova
        // Por enquanto, vamos simular preenchendo o formulário com dados estáticos
        
        document.getElementById('test-modal-title').textContent = 'Editar Prova';
        
        // Preencher o formulário com dados simulados
        document.getElementById('test-subject').value = 'math';
        document.getElementById('test-class').value = 'CLS001';
        document.getElementById('test-date').value = '2025-04-15';
        document.getElementById('test-time').value = '1';
        document.getElementById('test-teacher').value = '3';
        document.getElementById('test-status').value = 'scheduled';
        document.getElementById('test-room').value = 'Sala 101';
        document.getElementById('test-description').value = 'Prova de Matemática sobre funções quadráticas e exponenciais. Os alunos devem trazer calculadora científica.';
        document.getElementById('test-notes').value = 'Alunos com necessidades especiais terão tempo adicional de 30 minutos.';
        
        // Exibir o modal
        testModal.style.display = 'block';
    }
    
    // Função para visualizar prova
    function viewTest(id) {
        // Aqui você faria uma requisição AJAX para obter os dados da prova
        // Por enquanto, vamos simular com dados estáticos
        
        // Exibir o modal
        viewTestModal.style.display = 'block';
    }
    
    // Função para excluir prova
    function deleteTest(id) {
        if (confirm(`Tem certeza que deseja excluir a prova ${id}? Esta ação não pode ser desfeita.`)) {
            // Aqui você faria uma requisição AJAX para excluir a prova
            alert(`Prova ${id} excluída com sucesso!`);
            // Recarregar a tabela após excluir
            // loadTests();
        }
    }
    
    // Função para abrir modal de notas
    function openGradeModal(id) {
        // Aqui você faria uma requisição AJAX para obter os dados da prova e alunos
        // Por enquanto, vamos simular com dados estáticos
        
        // Exibir o modal
        gradeModal.style.display = 'block';
    }
    
    // Validação do formulário de prova
    const testForm = document.getElementById('test-form');
    
    if (testForm) {
        testForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const testSubject = document.getElementById('test-subject').value;
            const testClass = document.getElementById('test-class').value;
            const testDate = document.getElementById('test-date').value;
            const testTime = document.getElementById('test-time').value;
            const testTeacher = document.getElementById('test-teacher').value;
            
            if (!testSubject || !testClass || !testDate || !testTime || !testTeacher) {
                alert('Por favor, preencha todos os campos obrigatórios!');
                return false;
            }
            
            // Simular salvamento
            alert('Prova salva com sucesso!');
            testModal.style.display = 'none';
            
            // Aqui você implementaria a lógica para salvar no banco de dados
            console.log('Dados da prova:', {
                testSubject,
                testClass,
                testDate,
                testTime,
                testTeacher
            });
        });
    }
    
    // Salvar notas
    const saveGradesBtn = document.getElementById('save-grades');
    
    if (saveGradesBtn) {
        saveGradesBtn.addEventListener('click', function() {
            // Aqui você implementaria a lógica para salvar as notas
            alert('Notas salvas com sucesso!');
            gradeModal.style.display = 'none';
        });
    }
    
    // Upload de arquivos
    const fileInput = document.getElementById('test-files');
    const fileList = document.getElementById('file-list');
    
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            // Limpar a lista de arquivos
            fileList.innerHTML = '';
            
            // Adicionar os arquivos selecionados à lista
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                
                const fileName = document.createElement('div');
                fileName.className = 'file-item-name';
                
                // Determinar o ícone com base no tipo de arquivo
                let fileIcon = 'fa-file';
                if (file.type.includes('pdf')) {
                    fileIcon = 'fa-file-pdf';
                } else if (file.type.includes('word') || file.name.endsWith('.doc') || file.name.endsWith('.docx')) {
                    fileIcon = 'fa-file-word';
                } else if (file.type.includes('excel') || file.name.endsWith('.xls') || file.name.endsWith('.xlsx')) {
                    fileIcon = 'fa-file-excel';
                } else if (file.type.includes('image')) {
                    fileIcon = 'fa-file-image';
                }
                
                fileName.innerHTML = `<i class="fas ${fileIcon} file-item-icon"></i> ${file.name}`;
                
                const removeBtn = document.createElement('div');
                removeBtn.className = 'file-item-remove';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.addEventListener('click', function() {
                    fileItem.remove();
                });
                
                fileItem.appendChild(fileName);
                fileItem.appendChild(removeBtn);
                fileList.appendChild(fileItem);
            }
        });
    }
    
    // Filtros e pesquisa
    const searchInput = document.getElementById('search-tests');
    const filterClass = document.getElementById('filter-class');
    const filterSubject = document.getElementById('filter-subject');
    const filterTeacher = document.getElementById('filter-teacher');
    const filterStatus = document.getElementById('filter-status');
    const filterDateStart = document.getElementById('filter-date-start');
    const filterDateEnd = document.getElementById('filter-date-end');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            const classFilter = filterClass ? filterClass.value : '';
            const subjectFilter = filterSubject ? filterSubject.value : '';
            const teacherFilter = filterTeacher ? filterTeacher.value : '';
            const statusFilter = filterStatus ? filterStatus.value : '';
            const dateStartFilter = filterDateStart ? filterDateStart.value : '';
            const dateEndFilter = filterDateEnd ? filterDateEnd.value : '';
            
            // Implementar lógica de filtro
            filterTests(searchTerm, classFilter, subjectFilter, teacherFilter, statusFilter, dateStartFilter, dateEndFilter);
        });
    }
    
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (filterClass) filterClass.value = '';
            if (filterSubject) filterSubject.value = '';
            if (filterTeacher) filterTeacher.value = '';
            if (filterStatus) filterStatus.value = '';
            if (filterDateStart) filterDateStart.value = '';
            if (filterDateEnd) filterDateEnd.value = '';
            
            // Limpar filtros
            filterTests('', '', '', '', '', '', '');
        });
    }
    
    // Função para filtrar provas
    function filterTests(search, classFilter, subjectFilter, teacherFilter, statusFilter, dateStartFilter, dateEndFilter) {
        // Em um ambiente real, você faria uma requisição AJAX para obter os dados filtrados
        // Por enquanto, vamos simular com um alerta
        alert(`Filtros aplicados: Pesquisa="${search}", Turma="${classFilter}", Disciplina="${subjectFilter}", Professor="${teacherFilter}", Estado="${statusFilter}", Data Inicial="${dateStartFilter}", Data Final="${dateEndFilter}"`);
    }
    
    // Navegação do calendário
    const prevMonthBtn = document.getElementById('prev-month');
    const nextMonthBtn = document.getElementById('next-month');
    const calendarTitle = document.querySelector('.calendar-title');
    
    if (prevMonthBtn && nextMonthBtn) {
        let currentMonth = 3; // Abril (0-indexed)
        let currentYear = 2025;
        
        prevMonthBtn.addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            updateCalendarTitle();
        });
        
        nextMonthBtn.addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            updateCalendarTitle();
        });
        
        function updateCalendarTitle() {
            const months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            calendarTitle.textContent = `${months[currentMonth]} ${currentYear}`;
            
            // Aqui você implementaria a lógica para atualizar o calendário
            // loadCalendarData(currentMonth, currentYear);
        }
    }
    
    // Inicializar a página
    function initPage() {
        // Carregar dados iniciais
        console.log('Página de provas inicializada');
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