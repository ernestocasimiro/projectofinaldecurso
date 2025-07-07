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
  <title>Gestão de Boletins - Sistema Pitruca Camama</title>
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
    
    /* Estilos para o conteúdo da página de boletins */
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
    
    /* Filtros para boletins */
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
    
    /* Tabela de boletins */
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
    
    .status-badge.approved {
      background-color: rgba(46, 204, 113, 0.1);
      color: #2ecc71;
    }
    
    .status-badge.recovery {
      background-color: rgba(230, 126, 34, 0.1);
      color: #e67e22;
    }
    
    .status-badge.failed {
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
    
    .table-action-btn.print {
      background-color: rgba(155, 89, 182, 0.1);
      color: #9b59b6;
    }
    
    .table-action-btn.delete {
      background-color: rgba(231, 76, 60, 0.1);
      color: #e74c3c;
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
    
    /* Visualização de boletim */
    .bulletin-view {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      padding: 20px;
      margin-bottom: 20px;
    }
    
    .bulletin-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #e9ecef;
    }
    
    .bulletin-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: #2c3e50;
    }
    
    .bulletin-actions {
      display: flex;
      gap: 10px;
    }
    
    .bulletin-info {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 20px;
    }
    
    .bulletin-info-item {
      display: flex;
      flex-direction: column;
    }
    
    .bulletin-info-label {
      font-size: 0.9rem;
      font-weight: 500;
      color: #7f8c8d;
      margin-bottom: 5px;
    }
    
    .bulletin-info-value {
      font-size: 1rem;
      color: #2c3e50;
    }
    
    .bulletin-grades {
      margin-bottom: 20px;
    }
    
    .bulletin-grades-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 15px;
    }
    
    .grades-table {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 0;
    }
    
    .grades-table th,
    .grades-table td {
      padding: 10px 15px;
      border: 1px solid #e9ecef;
    }
    
    .grades-table th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: #495057;
    }
    
    .grades-table td.grade-value {
      text-align: center;
      font-weight: 500;
    }
    
    .grade-value.approved {
      color: #2ecc71;
    }
    
    .grade-value.recovery {
      color: #e67e22;
    }
    
    .grade-value.failed {
      color: #e74c3c;
    }
    
    .bulletin-summary {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    
    .bulletin-average {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    
    .bulletin-average-label {
      font-size: 0.9rem;
      font-weight: 500;
      color: #7f8c8d;
      margin-bottom: 5px;
    }
    
    .bulletin-average-value {
      font-size: 1.5rem;
      font-weight: 700;
    }
    
    .bulletin-average-value.approved {
      color: #2ecc71;
    }
    
    .bulletin-average-value.recovery {
      color: #e67e22;
    }
    
    .bulletin-average-value.failed {
      color: #e74c3c;
    }
    
    .bulletin-status {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    
    .bulletin-status-label {
      font-size: 0.9rem;
      font-weight: 500;
      color: #7f8c8d;
      margin-bottom: 5px;
    }
    
    .bulletin-status-value {
      font-size: 1.2rem;
      font-weight: 600;
      padding: 5px 15px;
      border-radius: 20px;
    }
    
    .bulletin-status-value.approved {
      background-color: rgba(46, 204, 113, 0.1);
      color: #2ecc71;
    }
    
    .bulletin-status-value.recovery {
      background-color: rgba(230, 126, 34, 0.1);
      color: #e67e22;
    }
    
    .bulletin-status-value.failed {
      background-color: rgba(231, 76, 60, 0.1);
      color: #e74c3c;
    }
    
    .bulletin-comments {
      margin-bottom: 20px;
    }
    
    .bulletin-comments-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 10px;
    }
    
    .bulletin-comments-content {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      color: #495057;
    }
    
    .bulletin-signature {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
    }
    
    .signature-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 30%;
    }
    
    .signature-line {
      width: 100%;
      height: 1px;
      background-color: #2c3e50;
      margin-bottom: 5px;
    }
    
    .signature-name {
      font-size: 0.9rem;
      font-weight: 500;
      color: #2c3e50;
    }
    
    /* Gráficos de desempenho */
    .performance-chart {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      padding: 20px;
      margin-bottom: 20px;
    }
    
    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .chart-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #2c3e50;
    }
    
    .chart-filters {
      display: flex;
      gap: 10px;
    }
    
    .chart-filter {
      padding: 5px 10px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      font-size: 0.9rem;
      color: #495057;
      background-color: #fff;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .chart-filter.active {
      background-color: #3498db;
      color: white;
      border-color: #3498db;
    }
    
    .chart-container {
      height: 300px;
      position: relative;
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
      
      .bulletin-info {
        grid-template-columns: 1fr;
      }
      
      .bulletin-summary {
        flex-direction: column;
        gap: 15px;
      }
      
      .bulletin-signature {
        flex-direction: column;
        gap: 20px;
      }
      
      .signature-item {
        width: 100%;
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
    
    /* Estilos para o modal de visualização de boletim */
    .bulletin-print-view {
      background-color: #fff;
      padding: 30px;
      max-width: 800px;
      margin: 0 auto;
    }
    
    .bulletin-print-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 2px solid #2c3e50;
    }
    
    .school-info {
      display: flex;
      flex-direction: column;
    }
    
    .school-name {
      font-size: 1.5rem;
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 5px;
    }
    
    .school-address {
      font-size: 0.9rem;
      color: #7f8c8d;
    }
    
    .bulletin-print-title {
      font-size: 1.8rem;
      font-weight: 700;
      color: #2c3e50;
      text-align: center;
      margin-bottom: 20px;
    }
    
    .bulletin-print-subtitle {
      font-size: 1.2rem;
      font-weight: 600;
      color: #2c3e50;
      text-align: center;
      margin-bottom: 30px;
    }
    
    .bulletin-print-info {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
      margin-bottom: 30px;
      padding: 15px;
      background-color: #f8f9fa;
      border-radius: 8px;
    }
    
    .bulletin-print-info-item {
      display: flex;
      flex-direction: column;
    }
    
    .bulletin-print-info-label {
      font-size: 0.9rem;
      font-weight: 600;
      color: #7f8c8d;
      margin-bottom: 5px;
    }
    
    .bulletin-print-info-value {
      font-size: 1rem;
      color: #2c3e50;
    }
    
    .bulletin-print-grades {
      margin-bottom: 30px;
    }
    
    .bulletin-print-grades-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 15px;
    }
    
    .bulletin-print-table {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 0;
    }
    
    .bulletin-print-table th,
    .bulletin-print-table td {
      padding: 12px 15px;
      border: 1px solid #2c3e50;
    }
    
    .bulletin-print-table th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: #2c3e50;
    }
    
    .bulletin-print-table td.grade-value {
      text-align: center;
      font-weight: 500;
    }
    
    .bulletin-print-summary {
      display: flex;
      justify-content: space-between;
      margin-bottom: 30px;
      padding: 15px;
      background-color: #f8f9fa;
      border-radius: 8px;
    }
    
    .bulletin-print-average {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    
    .bulletin-print-average-label {
      font-size: 1rem;
      font-weight: 600;
      color: #7f8c8d;
      margin-bottom: 5px;
    }
    
    .bulletin-print-average-value {
      font-size: 1.8rem;
      font-weight: 700;
      color: #2c3e50;
    }
    
    .bulletin-print-status {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    
    .bulletin-print-status-label {
      font-size: 1rem;
      font-weight: 600;
      color: #7f8c8d;
      margin-bottom: 5px;
    }
    
    .bulletin-print-status-value {
      font-size: 1.5rem;
      font-weight: 700;
    }
    
    .bulletin-print-status-value.approved {
      color: #2ecc71;
    }
    
    .bulletin-print-status-value.recovery {
      color: #e67e22;
    }
    
    .bulletin-print-status-value.failed {
      color: #e74c3c;
    }
    
    .bulletin-print-comments {
      margin-bottom: 30px;
    }
    
    .bulletin-print-comments-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 10px;
    }
    
    .bulletin-print-comments-content {
      padding: 15px;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      color: #495057;
      min-height: 100px;
    }
    
    .bulletin-print-signature {
      display: flex;
      justify-content: space-between;
      margin-top: 50px;
    }
    
    .bulletin-print-signature-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 30%;
    }
    
    .bulletin-print-signature-line {
      width: 100%;
      height: 1px;
      background-color: #2c3e50;
      margin-bottom: 5px;
    }
    
    .bulletin-print-signature-name {
      font-size: 1rem;
      font-weight: 500;
      color: #2c3e50;
    }
    
    .bulletin-print-date {
      text-align: right;
      margin-top: 30px;
      font-size: 0.9rem;
      color: #7f8c8d;
    }
    
    /* Estilos para o gráfico de comparação */
    .comparison-chart {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      padding: 20px;
      margin-bottom: 20px;
    }
    
    .comparison-chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .comparison-chart-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #2c3e50;
    }
    
    .comparison-chart-filters {
      display: flex;
      gap: 10px;
    }
    
    .comparison-chart-filter {
      padding: 5px 10px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      font-size: 0.9rem;
      color: #495057;
      background-color: #fff;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .comparison-chart-filter.active {
      background-color: #3498db;
      color: white;
      border-color: #3498db;
    }
    
    .comparison-chart-container {
      height: 300px;
      position: relative;
    }
    
    /* Estilos para o modal de lançamento de notas em massa */
    .bulk-grades-table {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 0;
      margin-bottom: 20px;
    }
    
    .bulk-grades-table th,
    .bulk-grades-table td {
      padding: 10px;
      border: 1px solid #e9ecef;
    }
    
    .bulk-grades-table th {
      background-color: #f8f9fa;
      font-weight: 600;
      text-align: left;
      position: sticky;
      top: 0;
      z-index: 10;
    }
    
    .bulk-grades-table tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    .bulk-grade-input {
      width: 60px;
      padding: 5px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      text-align: center;
    }
    
    .bulk-grade-input:focus {
      border-color: #3498db;
      outline: none;
    }
    
    .bulk-grade-status {
      display: inline-block;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      margin-right: 5px;
    }
    
    .bulk-grade-status.approved {
      background-color: #2ecc71;
    }
    
    .bulk-grade-status.recovery {
      background-color: #e67e22;
    }
    
    .bulk-grade-status.failed {
      background-color: #e74c3c;
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
                <input type="text" placeholder="Pesquisar boletins..." id="search-bulletins">
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
                <h2>Gestão de Boletins</h2>
                <div class="action-buttons">
                    <button class="action-btn" id="create-bulletin-btn">
                        <i class="fas fa-plus"></i> Criar Boletim Individual
                    </button>
                    <button class="action-btn" id="bulk-bulletin-btn" style="margin-left: 10px; background-color: #2ecc71;">
                        <i class="fas fa-list-check"></i> Lançar Notas em Massa
                    </button>
                </div>
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
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stat-value">125</div>
                    <div class="stat-label">Boletins Emitidos</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value">85%</div>
                    <div class="stat-label">Taxa de Aprovação</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-value">10%</div>
                    <div class="stat-label">Em Recuperação</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-value">5%</div>
                    <div class="stat-label">Taxa de Reprovação</div>
                </div>
            </div>
            
            <!-- Filtros para boletins -->
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
                        <label for="filter-period">Filtrar por Período</label>
                        <select id="filter-period">
                            <option value="">Todos os Períodos</option>
                            <option value="1">1º Trimestre</option>
                            <option value="2">2º Trimestre</option>
                            <option value="3">3º Trimestre</option>
                            <option value="final">Final</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-status">Filtrar por Situação</label>
                        <select id="filter-status">
                            <option value="">Todas as Situações</option>
                            <option value="approved">Aprovado</option>
                            <option value="recovery">Recuperação</option>
                            <option value="failed">Reprovado</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-student">Pesquisar Aluno</label>
                        <input type="text" id="filter-student" placeholder="Nome do aluno...">
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
            
            <!-- Tabela de boletins -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Aluno</th>
                            <th>Turma</th>
                            <th>Período</th>
                            <th>Média</th>
                            <th>Situação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="bulletin-table-body">
                        <tr>
                            <td>BLT001</td>
                            <td>João Silva</td>
                            <td>Turma 10ª A Informática</td>
                            <td>1º Trimestre</td>
                            <td>15.7</td>
                            <td><span class="status-badge approved">Aprovado</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="table-action-btn edit" title="Editar" data-id="BLT001">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="table-action-btn view" title="Ver Detalhes" data-id="BLT001">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="table-action-btn print" title="Imprimir" data-id="BLT001">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>BLT002</td>
                            <td>João</td>
                            <td>Turma 10ª A Informática</td>
                            <td>1º Trimestre</td>
                            <td>17.2</td>
                            <td><span class="status-badge approved">Aprovado</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="table-action-btn edit" title="Editar" data-id="BLT002">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="table-action-btn view" title="Ver Detalhes" data-id="BLT002">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="table-action-btn print" title="Imprimir" data-id="BLT002">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>BLT003</td>
                            <td>Pedro Oliveira</td>
                            <td>Turma 10ª B Informática</td>
                            <td>1º Trimestre</td>
                            <td>12.8</td>
                            <td><span class="status-badge recovery">Recuperação</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="table-action-btn edit" title="Editar" data-id="BLT003">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="table-action-btn view" title="Ver Detalhes" data-id="BLT003">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="table-action-btn print" title="Imprimir" data-id="BLT003">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>BLT004</td>
                            <td>Ana Costa</td>
                            <td>Turma 10ª A Informática</td>
                            <td>1º Trimestre</td>
                            <td>18.5</td>
                            <td><span class="status-badge approved">Aprovado</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="table-action-btn edit" title="Editar" data-id="BLT004">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="table-action-btn view" title="Ver Detalhes" data-id="BLT004">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="table-action-btn print" title="Imprimir" data-id="BLT004">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>BLT005</td>
                            <td>Carlos Ferreira</td>
                            <td>Turma 11ª Informática</td>
                            <td>1º Trimestre</td>
                            <td>9.5</td>
                            <td><span class="status-badge failed">Reprovado</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="table-action-btn edit" title="Editar" data-id="BLT005">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="table-action-btn view" title="Ver Detalhes" data-id="BLT005">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="table-action-btn print" title="Imprimir" data-id="BLT005">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>BLT006</td>
                            <td>Sofia Martins</td>
                            <td>Turma 10ª B Informática</td>
                            <td>1º Trimestre</td>
                            <td>16.3</td>
                            <td><span class="status-badge approved">Aprovado</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="table-action-btn edit" title="Editar" data-id="BLT006">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="table-action-btn view" title="Ver Detalhes" data-id="BLT006">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="table-action-btn print" title="Imprimir" data-id="BLT006">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>BLT007</td>
                            <td>Lucas Pereira</td>
                            <td>Turma 12ª Informática</td>
                            <td>1º Trimestre</td>
                            <td>14.2</td>
                            <td><span class="status-badge approved">Aprovado</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="table-action-btn edit" title="Editar" data-id="BLT007">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="table-action-btn view" title="Ver Detalhes" data-id="BLT007">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="table-action-btn print" title="Imprimir" data-id="BLT007">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>BLT008</td>
                            <td>Beatriz Santos</td>
                            <td>Turma 13ª Informática</td>
                            <td>1º Trimestre</td>
                            <td>11.5</td>
                            <td><span class="status-badge recovery">Recuperação</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="table-action-btn edit" title="Editar" data-id="BLT008">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="table-action-btn view" title="Ver Detalhes" data-id="BLT008">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="table-action-btn print" title="Imprimir" data-id="BLT008">
                                        <i class="fas fa-print"></i>
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
            
            <!-- Gráfico de desempenho -->
            <div class="performance-chart">
                <div class="chart-header">
                    <div class="chart-title">Desempenho por Disciplina</div>
                    <div class="chart-filters">
                        <button class="chart-filter active" data-period="1">1º Trimestre</button>
                        <button class="chart-filter" data-period="2">2º Trimestre</button>
                        <button class="chart-filter" data-period="3">3º Trimestre</button>
                        <button class="chart-filter" data-period="final">Final</button>
                    </div>
                </div>
                <div class="chart-container" id="performance-chart-container">
                    <!-- Gráfico será renderizado aqui via JavaScript -->
                    <canvas id="performance-chart"></canvas>
                </div>
            </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal para criar/editar boletim individual -->
<div class="modal" id="bulletin-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="bulletin-modal-title">Criar Novo Boletim</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-container">
                <form id="bulletin-form">
                    <div class="form-section">
                        <h3>Informações Básicas</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bulletin-student">Aluno*</label>
                                <select id="bulletin-student" name="bulletin_student" required>
                                    <option value="">Selecionar Aluno</option>
                                    <option value="STU001">João Silva</option>
                                    <option value="STU002">Maria Santos</option>
                                    <option value="STU003">Pedro Oliveira</option>
                                    <option value="STU004">Ana Costa</option>
                                    <option value="STU005">Carlos Ferreira</option>
                                    <option value="STU006">Sofia Martins</option>
                                    <option value="STU007">Lucas Pereira</option>
                                    <option value="STU008">Beatriz Santos</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="bulletin-period">Período*</label>
                                <select id="bulletin-period" name="bulletin_period" required>
                                    <option value="">Selecionar Período</option>
                                    <option value="1">1º Trimestre</option>
                                    <option value="2">2º Trimestre</option>
                                    <option value="3">3º Trimestre</option>
                                    <option value="final">Final</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="bulletin-class">Turma*</label>
                            <select id="bulletin-class" name="bulletin_class" required>
                                <option value="">Selecionar Turma</option>
                                <option value="CLS001">Turma 10ª A Informática</option>
                                <option value="CLS002">Turma 10ª B Informática</option>
                                <option value="CLS003">Turma 11ª Informática</option>
                                <option value="CLS004">Turma 12ª Informática</option>
                                <option value="CLS005">Turma 13ª Informática</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Notas por Disciplina</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="grade-math">Matemática*</label>
                                <input type="number" id="grade-math" name="grade_math" min="0" max="20" step="0.1" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="grade-portuguese">Português*</label>
                                <input type="number" id="grade-portuguese" name="grade_portuguese" min="0" max="20" step="0.1" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="grade-english">Inglês*</label>
                                <input type="number" id="grade-english" name="grade_english" min="0" max="20" step="0.1" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="grade-physics">Física*</label>
                                <input type="number" id="grade-physics" name="grade_physics" min="0" max="20" step="0.1" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="grade-chemistry">Química*</label>
                                <input type="number" id="grade-chemistry" name="grade_chemistry" min="0" max="20" step="0.1" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="grade-informatics">Informática*</label>
                                <input type="number" id="grade-informatics" name="grade_informatics" min="0" max="20" step="0.1" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Informações Adicionais</h3>
                        
                        <div class="form-group">
                            <label for="bulletin-comments">Observações</label>
                            <textarea id="bulletin-comments" name="bulletin_comments" rows="3" placeholder="Observações sobre o desempenho do aluno..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="bulletin-attendance">Frequência (%)</label>
                            <input type="number" id="bulletin-attendance" name="bulletin_attendance" min="0" max="100" step="0.1" value="100">
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn-cancel" id="cancel-bulletin">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Salvar Boletim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para lançamento de notas em massa -->
<div class="modal" id="bulk-bulletin-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Lançamento de Notas em Massa</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-container">
                <div class="form-section">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bulk-class">Turma*</label>
                            <select id="bulk-class" required>
                                <option value="">Selecionar Turma</option>
                                <option value="CLS001">Turma 10ª A Informática</option>
                                <option value="CLS002">Turma 10ª B Informática</option>
                                <option value="CLS003">Turma 11ª Informática</option>
                                <option value="CLS004">Turma 12ª Informática</option>
                                <option value="CLS005">Turma 13ª Informática</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="bulk-period">Período*</label>
                            <select id="bulk-period" required>
                                <option value="">Selecionar Período</option>
                                <option value="1">1º Trimestre</option>
                                <option value="2">2º Trimestre</option>
                                <option value="3">3º Trimestre</option>
                                <option value="final">Final</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="bulk-subject">Disciplina*</label>
                            <select id="bulk-subject" required>
                                <option value="">Selecionar Disciplina</option>
                                <option value="math">Matemática</option>
                                <option value="portuguese">Português</option>
                                <option value="english">Inglês</option>
                                <option value="physics">Física</option>
                                <option value="chemistry">Química</option>
                                <option value="informatics">Informática</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="button" class="btn-submit" id="load-students" style="margin-top: 10px;">
                        <i class="fas fa-sync-alt"></i> Carregar Alunos
                    </button>
                </div>
                
                <div class="form-section" id="bulk-grades-section" style="display: none;">
                    <h3>Notas dos Alunos</h3>
                    
                    <div class="table-container">
                        <table class="bulk-grades-table">
                            <thead>
                                <tr>
                                    <th>Nº</th>
                                    <th>Aluno</th>
                                    <th>Nota (0-20)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="bulk-grades-body">
                                <!-- Alunos serão carregados aqui via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="btn-cancel" id="cancel-bulk-bulletin">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn-submit" id="save-bulk-bulletin">
                        <i class="fas fa-save"></i> Salvar Notas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar boletim -->
<div class="modal" id="view-bulletin-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Visualizar Boletim</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="bulletin-print-view">
                <div class="bulletin-print-header">
                    <div class="school-info">
                        <div class="school-name">Escola Pitruca Camama</div>
                        <div class="school-address">Camama, Luanda - Angola</div>
                    </div>
                    <img src="login/images/semft-removebg-preview.png" alt="Logo" width="80" height="80">
                </div>
                
                <div class="bulletin-print-title">BOLETIM ESCOLAR</div>
                <div class="bulletin-print-subtitle" id="view-bulletin-period">1º Trimestre - 2025</div>
                
                <div class="bulletin-print-info">
                    <div class="bulletin-print-info-item">
                        <div class="bulletin-print-info-label">Aluno:</div>
                        <div class="bulletin-print-info-value" id="view-bulletin-student">João Silva</div>
                    </div>
                    <div class="bulletin-print-info-item">
                        <div class="bulletin-print-info-label">Matrícula:</div>
                        <div class="bulletin-print-info-value" id="view-bulletin-id">STU001</div>
                    </div>
                    <div class="bulletin-print-info-item">
                        <div class="bulletin-print-info-label">Turma:</div>
                        <div class="bulletin-print-info-value" id="view-bulletin-class">Turma 10ª A Informática</div>
                    </div>
                    <div class="bulletin-print-info-item">
                        <div class="bulletin-print-info-label">Frequência:</div>
                        <div class="bulletin-print-info-value" id="view-bulletin-attendance">95%</div>
                    </div>
                </div>
                
                <div class="bulletin-print-grades">
                    <div class="bulletin-print-grades-title">Notas por Disciplina</div>
                    
                    <table class="bulletin-print-table">
                        <thead>
                            <tr>
                                <th>Disciplina</th>
                                <th>Nota</th>
                                <th>Situação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Matemática</td>
                                <td class="grade-value">15.5</td>
                                <td>Aprovado</td>
                            </tr>
                            <tr>
                                <td>Português</td>
                                <td class="grade-value">16.0</td>
                                <td>Aprovado</td>
                            </tr>
                            <tr>
                                <td>Inglês</td>
                                <td class="grade-value">14.5</td>
                                <td>Aprovado</td>
                            </tr>
                            <tr>
                                <td>Física</td>
                                <td class="grade-value">17.0</td>
                                <td>Aprovado</td>
                            </tr>
                            <tr>
                                <td>Química</td>
                                <td class="grade-value">15.0</td>
                                <td>Aprovado</td>
                            </tr>
                            <tr>
                                <td>Informática</td>
                                <td class="grade-value">16.5</td>
                                <td>Aprovado</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="bulletin-print-summary">
                    <div class="bulletin-print-average">
                        <div class="bulletin-print-average-label">Média Geral</div>
                        <div class="bulletin-print-average-value" id="view-bulletin-average">15.7</div>
                    </div>
                    <div class="bulletin-print-status">
                        <div class="bulletin-print-status-label">Situação Final</div>
                        <div class="bulletin-print-status-value approved" id="view-bulletin-status">Aprovado</div>
                    </div>
                </div>
                
                <div class="bulletin-print-comments">
                    <div class="bulletin-print-comments-title">Observações</div>
                    <div class="bulletin-print-comments-content" id="view-bulletin-comments">
                        O aluno demonstrou bom desempenho durante o trimestre. Recomenda-se maior atenção nas atividades de Inglês para melhorar o rendimento.
                    </div>
                </div>
                
                <div class="bulletin-print-signature">
                    <div class="bulletin-print-signature-item">
                        <div class="bulletin-print-signature-line"></div>
                        <div class="bulletin-print-signature-name">Coordenador Pedagógico</div>
                    </div>
                    <div class="bulletin-print-signature-item">
                        <div class="bulletin-print-signature-line"></div>
                        <div class="bulletin-print-signature-name">Diretor</div>
                    </div>
                    <div class="bulletin-print-signature-item">
                        <div class="bulletin-print-signature-line"></div>
                        <div class="bulletin-print-signature-name">Encarregado de Educação</div>
                    </div>
                </div>
                
                <div class="bulletin-print-date">
                    Luanda, <span id="view-bulletin-date">15 de Abril de 2025</span>
                </div>
            </div>
            
            <div class="form-buttons">
                <button type="button" class="btn-cancel" id="close-view-bulletin">
                    <i class="fas fa-times"></i> Fechar
                </button>
                <button type="button" class="btn-submit" id="print-bulletin">
                    <i class="fas fa-print"></i> Imprimir Boletim
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
    
    // Controle dos modais
    const bulletinModal = document.getElementById('bulletin-modal');
    const bulkBulletinModal = document.getElementById('bulk-bulletin-modal');
    const viewBulletinModal = document.getElementById('view-bulletin-modal');
    const createBulletinBtn = document.getElementById('create-bulletin-btn');
    const bulkBulletinBtn = document.getElementById('bulk-bulletin-btn');
    const closeModalBtns = document.querySelectorAll('.close-modal');
    const cancelBulletinBtn = document.getElementById('cancel-bulletin');
    const cancelBulkBulletinBtn = document.getElementById('cancel-bulk-bulletin');
    const closeViewBulletinBtn = document.getElementById('close-view-bulletin');
    
    // Abrir modal de criar boletim
    if (createBulletinBtn) {
        createBulletinBtn.addEventListener('click', function() {
            document.getElementById('bulletin-modal-title').textContent = 'Criar Novo Boletim';
            document.getElementById('bulletin-form').reset();
            bulletinModal.style.display = 'block';
        });
    }
    
    // Abrir modal de lançamento em massa
    if (bulkBulletinBtn) {
        bulkBulletinBtn.addEventListener('click', function() {
            document.getElementById('bulk-grades-section').style.display = 'none';
            bulkBulletinModal.style.display = 'block';
        });
    }
    
    // Fechar modais
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            bulletinModal.style.display = 'none';
            bulkBulletinModal.style.display = 'none';
            viewBulletinModal.style.display = 'none';
        });
    });
    
    if (cancelBulletinBtn) {
        cancelBulletinBtn.addEventListener('click', function() {
            bulletinModal.style.display = 'none';
        });
    }
    
    if (cancelBulkBulletinBtn) {
        cancelBulkBulletinBtn.addEventListener('click', function() {
            bulkBulletinModal.style.display = 'none';
        });
    }
    
    if (closeViewBulletinBtn) {
        closeViewBulletinBtn.addEventListener('click', function() {
            viewBulletinModal.style.display = 'none';
        });
    }
    
    // Fechar modal ao clicar fora
    window.addEventListener('click', function(e) {
        if (e.target === bulletinModal) {
            bulletinModal.style.display = 'none';
        }
        if (e.target === bulkBulletinModal) {
            bulkBulletinModal.style.display = 'none';
        }
        if (e.target === viewBulletinModal) {
            viewBulletinModal.style.display = 'none';
        }
    });
    
    // Botões de ação na tabela
    const editButtons = document.querySelectorAll('.table-action-btn.edit');
    const viewButtons = document.querySelectorAll('.table-action-btn.view');
    const printButtons = document.querySelectorAll('.table-action-btn.print');
    
    // Editar boletim
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const bulletinId = this.getAttribute('data-id');
            editBulletin(bulletinId);
        });
    });
    
    // Ver boletim
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const bulletinId = this.getAttribute('data-id');
            viewBulletin(bulletinId);
        });
    });
    
    // Imprimir boletim
    printButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const bulletinId = this.getAttribute('data-id');
            printBulletin(bulletinId);
        });
    });
    
    // Botão para imprimir a partir da visualização
    const printBulletinBtn = document.getElementById('print-bulletin');
    
    if (printBulletinBtn) {
        printBulletinBtn.addEventListener('click', function() {
            // Implementar lógica de impressão
            window.print();
        });
    }
    
    // Função para editar boletim
    function editBulletin(id) {
        // Aqui você faria uma requisição AJAX para obter os dados do boletim
        // Por enquanto, vamos simular preenchendo o formulário com dados estáticos
        
        document.getElementById('bulletin-modal-title').textContent = 'Editar Boletim';
        
        // Preencher o formulário com dados simulados
        document.getElementById('bulletin-student').value = 'STU001';
        document.getElementById('bulletin-period').value = '1';
        document.getElementById('bulletin-class').value = 'CLS001';
        document.getElementById('grade-math').value = '15.5';
        document.getElementById('grade-portuguese').value = '16.0';
        document.getElementById('grade-english').value = '14.5';
        document.getElementById('grade-physics').value = '17.0';
        document.getElementById('grade-chemistry').value = '15.0';
        document.getElementById('grade-informatics').value = '16.5';
        document.getElementById('bulletin-comments').value = 'O aluno demonstrou bom desempenho durante o trimestre. Recomenda-se maior atenção nas atividades de Inglês para melhorar o rendimento.';
        document.getElementById('bulletin-attendance').value = '95';
        
        // Exibir o modal
        bulletinModal.style.display = 'block';
    }
    
    // Função para visualizar boletim
    function viewBulletin(id) {
        // Aqui você faria uma requisição AJAX para obter os dados do boletim
        // Por enquanto, vamos simular com dados estáticos
        
        // Exibir o modal
        viewBulletinModal.style.display = 'block';
    }
    
    // Função para imprimir boletim
    function printBulletin(id) {
        // Primeiro visualiza o boletim
        viewBulletin(id);
        
        // Depois aciona a impressão
        setTimeout(() => {
            window.print();
        }, 500);
    }
    
    // Carregar alunos para lançamento em massa
    const loadStudentsBtn = document.getElementById('load-students');
    const bulkGradesSection = document.getElementById('bulk-grades-section');
    const bulkGradesBody = document.getElementById('bulk-grades-body');
    
    if (loadStudentsBtn) {
        loadStudentsBtn.addEventListener('click', function() {
            const bulkClass = document.getElementById('bulk-class').value;
            const bulkPeriod = document.getElementById('bulk-period').value;
            const bulkSubject = document.getElementById('bulk-subject').value;
            
            if (!bulkClass || !bulkPeriod || !bulkSubject) {
                alert('Por favor, selecione a turma, o período e a disciplina.');
                return;
            }
            
            // Aqui você faria uma requisição AJAX para obter os alunos da turma
            // Por enquanto, vamos simular com dados estáticos
            
            // Limpar a tabela
            bulkGradesBody.innerHTML = '';
            
            // Adicionar alunos simulados
            const students = [
                { id: 'STU001', name: 'João Silva', grade: 15.5 },
                { id: 'STU002', name: 'Maria Santos', grade: 17.2 },
                { id: 'STU003', name: 'Pedro Oliveira', grade: 12.8 },
                { id: 'STU004', name: 'Ana Costa', grade: 18.5 },
                { id: 'STU005', name: 'Carlos Ferreira', grade: 9.5 },
                { id: 'STU006', name: 'Sofia Martins', grade: 16.3 },
                { id: 'STU007', name: 'Lucas Pereira', grade: 14.2 },
                { id: 'STU008', name: 'Beatriz Santos', grade: 11.5 }
            ];
            
            students.forEach((student, index) => {
                const row = document.createElement('tr');
                
                // Determinar o status com base na nota
                let statusClass = 'approved';
                let statusText = 'Aprovado';
                
                if (student.grade < 10) {
                    statusClass = 'failed';
                    statusText = 'Reprovado';
                } else if (student.grade < 14) {
                    statusClass = 'recovery';
                    statusText = 'Recuperação';
                }
                
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${student.name}</td>
                    <td><input type="number" class="bulk-grade-input" min="0" max="20" step="0.1" value="${student.grade}" data-student-id="${student.id}"></td>
                    <td><span class="bulk-grade-status ${statusClass}"></span> ${statusText}</td>
                `;
                
                bulkGradesBody.appendChild(row);
            });
            
            // Mostrar a seção de notas
            bulkGradesSection.style.display = 'block';
            
            // Adicionar evento para atualizar o status ao alterar a nota
            const gradeInputs = document.querySelectorAll('.bulk-grade-input');
            
            gradeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const grade = parseFloat(this.value);
                    const statusElement = this.parentElement.nextElementSibling;
                    const statusIcon = statusElement.querySelector('.bulk-grade-status');
                    
                    let statusClass = 'approved';
                    let statusText = 'Aprovado';
                    
                    if (grade < 10) {
                        statusClass = 'failed';
                        statusText = 'Reprovado';
                    } else if (grade < 14) {
                        statusClass = 'recovery';
                        statusText = 'Recuperação';
                    }
                    
                    statusIcon.className = `bulk-grade-status ${statusClass}`;
                    statusElement.innerHTML = `<span class="bulk-grade-status ${statusClass}"></span> ${statusText}`;
                });
            });
        });
    }
    
    // Salvar notas em massa
    const saveBulkBulletinBtn = document.getElementById('save-bulk-bulletin');
    
    if (saveBulkBulletinBtn) {
        saveBulkBulletinBtn.addEventListener('click', function() {
            const bulkClass = document.getElementById('bulk-class').value;
            const bulkPeriod = document.getElementById('bulk-period').value;
            const bulkSubject = document.getElementById('bulk-subject').value;
            
            if (!bulkClass || !bulkPeriod || !bulkSubject) {
                alert('Por favor, selecione a turma, o período e a disciplina.');
                return;
            }
            
            // Coletar as notas
            const gradeInputs = document.querySelectorAll('.bulk-grade-input');
            const grades = [];
            
            gradeInputs.forEach(input => {
                grades.push({
                    studentId: input.getAttribute('data-student-id'),
                    grade: parseFloat(input.value)
                });
            });
            
            // Aqui você implementaria a lógica para salvar as notas
            console.log('Notas a serem salvas:', {
                class: bulkClass,
                period: bulkPeriod,
                subject: bulkSubject,
                grades: grades
            });
            
            // Simular salvamento
            alert('Notas salvas com sucesso!');
            bulkBulletinModal.style.display = 'none';
        });
    }
    
    // Validação do formulário de boletim
    const bulletinForm = document.getElementById('bulletin-form');
    
    if (bulletinForm) {
        bulletinForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const bulletinStudent = document.getElementById('bulletin-student').value;
            const bulletinPeriod = document.getElementById('bulletin-period').value;
            const bulletinClass = document.getElementById('bulletin-class').value;
            
            if (!bulletinStudent || !bulletinPeriod || !bulletinClass) {
                alert('Por favor, preencha todos os campos obrigatórios!');
                return false;
            }
            
            // Simular salvamento
            alert('Boletim salvo com sucesso!');
            bulletinModal.style.display = 'none';
            
            // Aqui você implementaria a lógica para salvar no banco de dados
            console.log('Dados do boletim:', {
                student: bulletinStudent,
                period: bulletinPeriod,
                class: bulletinClass,
                grades: {
                    math: document.getElementById('grade-math').value,
                    portuguese: document.getElementById('grade-portuguese').value,
                    english: document.getElementById('grade-english').value,
                    physics: document.getElementById('grade-physics').value,
                    chemistry: document.getElementById('grade-chemistry').value,
                    informatics: document.getElementById('grade-informatics').value
                },
                comments: document.getElementById('bulletin-comments').value,
                attendance: document.getElementById('bulletin-attendance').value
            });
        });
    }
    
    // Filtros e pesquisa
    const searchInput = document.getElementById('search-bulletins');
    const filterClass = document.getElementById('filter-class');
    const filterPeriod = document.getElementById('filter-period');
    const filterStatus = document.getElementById('filter-status');
    const filterStudent = document.getElementById('filter-student');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            const classFilter = filterClass ? filterClass.value : '';
            const periodFilter = filterPeriod ? filterPeriod.value : '';
            const statusFilter = filterStatus ? filterStatus.value : '';
            const studentFilter = filterStudent ? filterStudent.value.toLowerCase() : '';
            
            // Implementar lógica de filtro
            filterBulletins(searchTerm, classFilter, periodFilter, statusFilter, studentFilter);
        });
    }
    
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (filterClass) filterClass.value = '';
            if (filterPeriod) filterPeriod.value = '';
            if (filterStatus) filterStatus.value = '';
            if (filterStudent) filterStudent.value = '';
            
            // Limpar filtros
            filterBulletins('', '', '', '', '');
        });
    }
    
    // Função para filtrar boletins
    function filterBulletins(search, classFilter, periodFilter, statusFilter, studentFilter) {
        // Em um ambiente real, você faria uma requisição AJAX para obter os dados filtrados
        // Por enquanto, vamos simular com um alerta
        alert(`Filtros aplicados: Pesquisa="${search}", Turma="${classFilter}", Período="${periodFilter}", Situação="${statusFilter}", Aluno="${studentFilter}"`);
    }
    
    // Inicializar gráficos
    function initCharts() {
        // Aqui você implementaria a lógica para renderizar os gráficos
        // Usando uma biblioteca como Chart.js
        console.log('Gráficos inicializados');
    }
    
    // Filtros dos gráficos
    const chartFilters = document.querySelectorAll('.chart-filter');
    const comparisonChartFilters = document.querySelectorAll('.comparison-chart-filter');
    
    chartFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            // Remover classe active de todos os filtros
            chartFilters.forEach(f => f.classList.remove('active'));
            
            // Adicionar classe active ao filtro clicado
            this.classList.add('active');
            
            // Atualizar o gráfico
            const period = this.getAttribute('data-period');
            updatePerformanceChart(period);
        });
    });
    
    comparisonChartFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            // Remover classe active de todos os filtros
            comparisonChartFilters.forEach(f => f.classList.remove('active'));
            
            // Adicionar classe active ao filtro clicado
            this.classList.add('active');
            
            // Atualizar o gráfico
            const classFilter = this.getAttribute('data-class');
            updateComparisonChart(classFilter);
        });
    });
    
    // Funções para atualizar os gráficos
    function updatePerformanceChart(period) {
        // Aqui você implementaria a lógica para atualizar o gráfico de desempenho
        console.log(`Atualizando gráfico de desempenho para o período ${period}`);
    }
    
    function updateComparisonChart(classFilter) {
        // Aqui você implementaria a lógica para atualizar o gráfico de comparação
        console.log(`Atualizando gráfico de comparação para a turma ${classFilter}`);
    }
    
    // Inicializar a página
    function initPage() {
        // Carregar dados iniciais
        console.log('Página de boletins inicializada');
        
        // Inicializar gráficos
        initCharts();
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