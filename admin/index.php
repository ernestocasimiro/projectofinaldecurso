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

    // Inclui a conexão com a base de dados
    require_once('dbconnection.php');

    // Código para obter os totais
    try {
        // Total de alunos
        $stmt = $conn->query("SELECT COUNT(*) AS total FROM estudantes");
        $totalAlunos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total de professores
        $stmt = $conn->query("SELECT COUNT(*) AS total FROM professores");
        $totalProfessores = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total de turmas
        $stmt = $conn->query("SELECT COUNT(*) AS total FROM turma");
        $totalTurmas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    } catch (PDOException $e) {
        echo "Erro ao buscar dados: " . $e->getMessage();
        $totalAlunos = $totalProfessores = $totalTurmas = 0;
    }
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema - Pitruca Camama</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>

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

.fixed-logout-btn:hover {
    background-color: #c82333;
    transform: scale(1.1);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3); /* Sombra um pouco maior no hover */
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
    
    /* Estilos para o painel */
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
    
    .tab-pane h2 {
      margin-bottom: 20px;
      color: #2c3e50;
      font-weight: 600;
    }
    
    /* Estatísticas */
    .stats-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .stat-card {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
      display: flex;
      align-items: center;
      position: relative;
      overflow: hidden;
      transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
    }
    
    .stat-card::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 5px;
    }
    
    .stat-card:nth-child(1)::before {
      background-color: #4a6cf7;
    }
    
    .stat-card:nth-child(2)::before {
      background-color: #8c54ff;
    }
    
    .stat-card:nth-child(3)::before {
      background-color: #ff9800;
    }
    
    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 20px;
      font-size: 1.5rem;
    }
    
    .student-icon {
      background-color: rgba(74, 108, 247, 0.15);
      color: #4a6cf7;
    }
    
    .teacher-icon {
      background-color: rgba(140, 84, 255, 0.15);
      color: #8c54ff;
    }
    
    .course-icon {
      background-color: rgba(255, 152, 0, 0.15);
      color: #ff9800;
    }
    
    .stat-info {
      flex: 1;
    }
    
    .stat-info h3 {
      font-size: 0.95rem;
      font-weight: 500;
      margin: 0 0 5px 0;
      color: #555;
    }
    
    .stat-info p {
      font-size: 1.75rem;
      font-weight: 700;
      margin: 0;
      color: #333;
    }
    
    /* Gráficos */
    .charts-container {
      display: grid;
      grid-template-columns: 1fr;
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .chart {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }
    
    .chart h3 {
      margin-top: 0;
      margin-bottom: 20px;
      font-size: 1.2rem;
      font-weight: 600;
      color: #2c3e50;
    }
    
    .chart-placeholder {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      height: 200px;
      padding: 20px 0;
    }
    
    .bar {
      position: relative;
      width: 40px;
      background-color: #4a6cf7;
      border-radius: 5px 5px 0 0;
      transition: height 0.5s ease;
    }
    
    .bar::before {
      content: attr(data-value);
      position: absolute;
      top: -25px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 0.85rem;
      font-weight: 600;
      color: #555;
    }
    
    .bar span {
      position: absolute;
      bottom: -25px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 0.85rem;
      color: #666;
    }
    
    /* Atividade recente */
    .recent-activity {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
      margin-top: 20px;
    }
    
    .recent-activity h3 {
      margin-top: 0;
      margin-bottom: 20px;
      font-size: 1.2rem;
      font-weight: 600;
      color: #2c3e50;
    }
    
    .activity-list {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    
    .activity-item {
      display: flex;
      align-items: flex-start;
      gap: 15px;
      padding-bottom: 15px;
      border-bottom: 1px solid #f0f0f0;
    }
    
    .activity-item:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }
    
    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      background-color: rgba(74, 108, 247, 0.15);
      color: #4a6cf7;
    }
    
    .activity-details p {
      margin: 0 0 5px 0;
      font-size: 0.95rem;
    }
    
    .activity-details .time {
      font-size: 0.85rem;
      color: #777;
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
      
      .stats-container {
        grid-template-columns: 1fr;
      }
      
      .search-bar {
        width: 200px;
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
            
            <!-- Outros menus mantidos como no original -->
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
                <input type="text" placeholder="Pesquisar...">
            </div>
            <div class="user-info">
                <div class="user user-dropdown">
<<<<<<< HEAD
=======
                    <img src="login/images/semft-removebg-preview.png" alt="" class="userOptions__avatar img-circle" width="42" height="42">
>>>>>>> 799fa082992a47807b821e9d39588f5fb432ef31
                    <span><?php echo $_SESSION['fname']; ?></span>
                </div>
            </div>
        </header>
        <div class="tab-content">
   <!-- Painel (Visão Geral) -->
<div class="tab-pane active" id="dashboard">
    <div class="dashboard-header" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
        <h2 style="margin: 0; flex-grow: 1;">Painel (Visão Geral)</h2>
        <div style="display: flex; gap: 10px;">
            <a href="access_codes.php" class="btn-coordinator-access" style="white-space: nowrap;">
                <i class="fas fa-key"></i> Acessos de Coordenadores
            </a>
        </div>
    </div>
</div>
        
        <!-- Cards de estatísticas melhorados -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon student-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-info">
                    <h3>Total de Alunos</h3>
                    <p><?php echo $totalAlunos; ?></p>
                    <span class="trend"><i class="fas fa-arrow-up"></i> 12% desde o último mês</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon teacher-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-info">
                    <h3>Total de Professores</h3>
                    <p><?php echo $totalProfessores; ?></p>
                    <span class="trend"><i class="fas fa-arrow-up"></i> 4% desde o último mês</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon course-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3>Total de Turmas</h3>
                    <p><?php echo $totalTurmas; ?></p>
                    <span class="trend"><i class="fas fa-arrow-up"></i> 2 novas turmas este semestre</span>
                </div>
            </div>
        </div>
        
        <div class="charts-container">
            <div class="chart">
                <h3>Visão Geral de Presença</h3>
                <div class="chart-placeholder">
                    <div class="bar" style="height: 70%;" data-value="70%">
                        <span>Seg</span>
                    </div>
                    <div class="bar" style="height: 85%;" data-value="85%">
                        <span>Ter</span>
                    </div>
                    <div class="bar" style="height: 75%;" data-value="75%">
                        <span>Qua</span>
                    </div>
                    <div class="bar" style="height: 90%;" data-value="90%">
                        <span>Qui</span>
                    </div>
                    <div class="bar" style="height: 65%;" data-value="65%">
                        <span>Sex</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="recent-activity">
            <h3>Atividade Recente</h3>
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="activity-details">
                        <p>Novo aluno <strong>João Silva</strong> registrado</p>
                        <span class="time">2 horas atrás</span>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="activity-details">
                        <p>Professor <strong>Maria Santos</strong> carregou novos materiais do curso</p>
                        <span class="time">4 horas atrás</span>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="activity-details">
                        <p>Presença para <strong>Turma 10B</strong> foi atualizada</p>
                        <span class="time">Ontem</span>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="activity-details">
                        <p>Cronograma de exame final para <strong>Semestre de Primavera</strong> publicado</p>
                        <span class="time">2 dias atrás</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos para o novo botão e layout */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .btn-coordinator-access {
        background-color: #6c5ce7;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(108, 92, 231, 0.2);
        border: none;
        cursor: pointer;
    }
    
    .btn-coordinator-access:hover {
        background-color: #5649c0;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(108, 92, 231, 0.3);
    }
    
    .btn-coordinator-access i {
        font-size: 16px;
    }
    
    @media (max-width: 768px) {
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .btn-coordinator-access {
            width: 100%;
            justify-content: center;
        }
    }
</style>

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
});
</script>
</body>
</html>

<?php }else{
    header("Location: ../login.php");
    exit;
} ?>