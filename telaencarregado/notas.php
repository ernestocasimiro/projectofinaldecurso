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

// Processar exportação de boletim
if (isset($_POST['export_boletim'])) {
    $studentId = $_POST['student_id'];
    
    // Verificar se o estudante pertence ao encarregado
    $validStudent = false;
    foreach ($students as $student) {
        if ($student['id'] == $studentId) {
            $validStudent = true;
            $selectedStudent = $student;
            break;
        }
    }
    
    if ($validStudent) {
        require_once('tcpdf/tcpdf.php');
        
        // Criar novo documento PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Configurações do documento
        $pdf->SetCreator('Sistema Escolar');
        $pdf->SetAuthor('Escola Pitruca Camama');
        $pdf->SetTitle('Boletim Escolar - ' . $selectedStudent['fname'] . ' ' . $selectedStudent['lname']);
        $pdf->SetSubject('Boletim Escolar');
        $pdf->SetKeywords('Boletim, Escola, Notas');
        
        // Adicionar página
        $pdf->AddPage();
        
        // Conteúdo do boletim
        $html = '
        <style>
            h1 {
                color: #4361ee;
                font-size: 18px;
                text-align: center;
                margin-bottom: 10px;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .school-info {
                font-size: 12px;
                color: #555;
                margin-bottom: 5px;
            }
            .student-info {
                border: 1px solid #ddd;
                padding: 10px;
                margin-bottom: 15px;
                background-color: #f9f9f9;
            }
            .info-row {
                display: flex;
                margin-bottom: 5px;
            }
            .info-label {
                width: 120px;
                font-weight: bold;
            }
            .info-value {
                flex: 1;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
            }
            th {
                background-color: #4361ee;
                color: white;
                padding: 8px;
                text-align: left;
                font-size: 10px;
            }
            td {
                padding: 8px;
                border-bottom: 1px solid #ddd;
                font-size: 10px;
            }
            .footer {
                margin-top: 20px;
                font-size: 10px;
                text-align: center;
                color: #777;
            }
            .signature {
                margin-top: 40px;
                border-top: 1px solid #000;
                width: 200px;
                text-align: center;
                padding-top: 5px;
                font-size: 10px;
            }
        </style>
        
        <div class="header">
            <h1>BOLETIM ESCOLAR</h1>
            <div class="school-info">Escola Pitruca Camama</div>
            <div class="school-info">'.$trimestre.' - '.$anoLetivo.'</div>
        </div>
        
        <div class="student-info">
            <div class="info-row">
                <div class="info-label">Nome do Aluno:</div>
                <div class="info-value">'.$selectedStudent['fname'].' '.$selectedStudent['lname'].'</div>
            </div>
            <div class="info-row">
                <div class="info-label">Turma:</div>
                <div class="info-value">'.$selectedStudent['class_name'].' - '.$selectedStudent['class_grade'].'</div>
            </div>
            <div class="info-row">
                <div class="info-label">Curso:</div>
                <div class="info-value">'.$selectedStudent['class_course'].'</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">'.($selectedStudent['status'] == 1 ? 'Ativo' : 'Inativo').'</div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Disciplina</th>
                    <th>1ª Avaliação</th>
                    <th>2ª Avaliação</th>
                    <th>Trabalhos</th>
                    <th>Média</th>
                    <th>Situação</th>
                </tr>
            </thead>
            <tbody>';
        
        // Adicionar notas fictícias baseadas no aluno selecionado
        if ($selectedStudent['fname'] == 'Steeve') {
            $html .= '
                <tr>
                    <td>Matemática</td>
                    <td>9.0</td>
                    <td>8.5</td>
                    <td>9.2</td>
                    <td>8.9</td>
                    <td>Aprovado</td>
                </tr>
                <tr>
                    <td>Português</td>
                    <td>8.0</td>
                    <td>9.0</td>
                    <td>8.5</td>
                    <td>8.5</td>
                    <td>Aprovado</td>
                </tr>
                <tr>
                    <td>Física</td>
                    <td>7.0</td>
                    <td>7.5</td>
                    <td>7.0</td>
                    <td>7.2</td>
                    <td>Recuperação</td>
                </tr>
                <tr>
                    <td>História</td>
                    <td>8.5</td>
                    <td>9.0</td>
                    <td>8.0</td>
                    <td>8.5</td>
                    <td>Aprovado</td>
                </tr>';
        } else {
            $html .= '
                <tr>
                    <td>Matemática</td>
                    <td>8.0</td>
                    <td>7.5</td>
                    <td>8.0</td>
                    <td>7.8</td>
                    <td>Aprovado</td>
                </tr>
                <tr>
                    <td>Português</td>
                    <td>9.0</td>
                    <td>9.5</td>
                    <td>9.0</td>
                    <td>9.2</td>
                    <td>Aprovado</td>
                </tr>
                <tr>
                    <td>Inglês</td>
                    <td>8.0</td>
                    <td>8.0</td>
                    <td>8.0</td>
                    <td>8.0</td>
                    <td>Aprovado</td>
                </tr>';
        }
        
        $html .= '
            </tbody>
        </table>
        
        <div class="footer">
            <div>Data de emissão: '.date('d/m/Y').'</div>
            <div style="margin-top: 30px; display: flex; justify-content: space-between;">
                <div class="signature">Assinatura do Diretor</div>
                <div class="signature">Assinatura do Coordenador</div>
            </div>
        </div>';
        
        // Gerar PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Saída do PDF
        $pdf->Output('boletim_'.strtolower(str_replace(' ', '_', $selectedStudent['fname'].'_'.$selectedStudent['lname'])).'.pdf', 'D');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas - Dashboard Encarregados</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #eef2ff;
            --secondary-color: #f8f9fa;
            --accent-color: #3f37c9;
            --text-color: #2b2d42;
            --text-light: #6c757d;
            --text-lighter: #adb5bd;
            --border-color: #e9ecef;
            --card-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            --card-shadow-hover: 0 5px 15px rgba(0, 0, 0, 0.1);
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fb;
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background-color: var(--white);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            z-index: 100;
            position: fixed;
            height: 100vh;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70px;
        }

        .sidebar-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }

        .profile {
            padding: 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .profile-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.2rem;
        }

        .profile-info h3 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .profile-info p {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        .menu {
            flex: 1;
            padding: 15px 0;
            overflow-y: auto;
        }

        .menu ul {
            list-style: none;
        }

        .menu li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .menu li a:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .menu li a .material-symbols-outlined {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .menu li.active a {
            background-color: var(--primary-light);
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
        }

        .sidebar-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--border-color);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            padding: 10px 0;
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar-footer a:hover {
            color: var(--primary-color);
        }

        .sidebar-footer a.logout:hover {
            color: var(--danger-color);
        }

        .sidebar-footer a .material-symbols-outlined {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Main Content Styles */
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin-left: 280px;
            width: calc(100% - 280px);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .search-container {
            position: relative;
            width: 300px;
        }

        .search-container .material-symbols-outlined {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-lighter);
            font-size: 1.2rem;
        }

        .search-container input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .search-container input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .top-bar-actions .material-symbols-outlined {
            color: var(--text-light);
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1.4rem;
        }

        .top-bar-actions .material-symbols-outlined:hover {
            color: var(--primary-color);
        }

        .top-bar-actions .notification {
            position: relative;
        }

        .top-bar-actions .notification::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background-color: var(--danger-color);
            border-radius: 50%;
            border: 2px solid var(--white);
        }

        .dashboard-content {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            background-color: #f5f7fb;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-light);
        }

        .btn-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background-color: var(--primary-light);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: var(--white);
            margin: 10% auto;
            padding: 25px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            animation: modalopen 0.3s;
        }

        @keyframes modalopen {
            from {opacity: 0; transform: translateY(-50px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            font-size: 1.3rem;
            color: var(--primary-color);
        }

        .close-modal {
            color: var(--text-light);
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .close-modal:hover {
            color: var(--danger-color);
        }

        .modal-body {
            margin-bottom: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* Filter Container */
        .filter-container {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 200px;
        }

        .filter-group label {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-bottom: 5px;
        }

        .filter-select {
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            background-color: var(--white);
            transition: all 0.2s;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        /* Student Grades */
        .student-grades {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .student-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .student-info h2 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .student-info p {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-bottom: 10px;
        }

        .average-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .average-badge.good {
            background-color: rgba(76, 201, 240, 0.1);
            color: #4cc9f0;
        }

        /* Grades Table */
        .grades-table-container {
            overflow-x: auto;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
        }

        .grades-table th, 
        .grades-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .grades-table th {
            font-size: 0.85rem;
            color: var(--text-light);
            font-weight: 500;
            white-space: nowrap;
        }

        .subject-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .subject-info .material-symbols-outlined {
            color: var(--primary-color);
        }

        .grade {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .grade.excellent {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }

        .grade.good {
            background-color: rgba(76, 201, 240, 0.1);
            color: #4cc9f0;
        }

        .grade.average {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning-color);
        }

        .grade.poor {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--danger-color);
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-badge.active {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }

        .status-badge.warning {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning-color);
        }

        /* Dashboard Card */
        .dashboard-card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-top: 25px;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 1.2rem;
        }

        .chart-filters {
            display: flex;
            gap: 10px;
        }

        .chart-container {
            padding: 20px;
        }

        .chart-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 300px;
            background-color: var(--gray-100);
            border-radius: 8px;
            color: var(--text-light);
        }

        .chart-placeholder .material-symbols-outlined {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--text-lighter);
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .content {
                margin-left: 0;
                width: 100%;
            }

            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1000;
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }

            .sidebar-header h2, 
            .profile-info, 
            .menu-text, 
            .sidebar-footer .menu-text {
                display: none;
            }

            .profile {
                justify-content: center;
                padding: 15px 0;
            }

            .profile-avatar {
                margin-right: 0;
            }

            .menu li a {
                justify-content: center;
                padding: 15px 0;
            }

            .menu li a .material-symbols-outlined {
                margin-right: 0;
                font-size: 1.4rem;
            }

            .sidebar-footer {
                padding: 10px 0;
            }

            .sidebar-footer a {
                justify-content: center;
                padding: 10px 0;
            }

            .sidebar-footer a .material-symbols-outlined {
                margin-right: 0;
                font-size: 1.4rem;
            }

            .top-bar {
                padding: 12px 15px;
            }

            .search-container {
                width: 200px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .filter-container {
                flex-direction: column;
                gap: 10px;
            }

            .filter-group {
                min-width: 100%;
            }
        }

        @media (max-width: 576px) {
            .search-container {
                width: 150px;
            }

            .grades-table th, 
            .grades-table td {
                padding: 10px 8px;
                font-size: 0.8rem;
            }

            .subject-info span:last-child {
                display: none;
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
                <div class="profile-avatar">
                    <?php 
                        $names = explode(' ', $guardian['fname']);
                        $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                        echo $initials;
                    ?>
                </div>
                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($guardian['fname'] . ' ' . $guardian['lname']); ?></h3>
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
                    <li class="active">
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
                </ul>
            </nav>
            <div class="sidebar-footer">
                <!--
                <a href="configuracoes.php">
                    <span class="material-symbols-outlined">settings</span>
                    <span class="menu-text">Configurações</span>
                </a>
    -->
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
                    <input type="text" placeholder="Pesquisar disciplina...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Notas</h1>
                    <div class="header-actions">
                        <button class="btn-outline" id="exportBtn">
                            <span class="material-symbols-outlined">download</span>
                            Exportar Boletim
                        </button>
                    </div>
                </div>

                <!-- Modal para seleção do estudante -->
                <div id="exportModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Exportar Boletim</h2>
                            <span class="close-modal">&times;</span>
                        </div>
                        <div class="modal-body">
                            <p>Selecione o educando para exportar o boletim:</p>
                            <form id="exportForm" method="post" action="">
                                <select name="student_id" class="filter-select" style="width: 100%; margin-top: 10px;">
                                    <?php foreach ($students as $student): ?>
                                        <option value="<?php echo $student['id']; ?>">
                                            <?php echo htmlspecialchars($student['fname'] . ' ' . $student['lname']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button class="btn-outline close-modal-btn">Cancelar</button>
                            <button type="submit" form="exportForm" name="export_boletim" class="btn-primary">Exportar PDF</button>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-container">
                    <div class="filter-group">
                        <label>Filho:</label>
                        <select class="filter-select" id="studentFilter">
                            <option value="all">Todos</option>
                            <option value="joao">Steeve Salvador</option>
                            <option value="ana">Kelton Gonçalves</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Período:</label>
                        <select class="filter-select">
                            <option>2º Trimestre 2025</option>
                            <option>1º Trimestre 2025</option>
                            <option>3º Trimestre 2024</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Disciplina:</label>
                        <select class="filter-select">
                            <option>Todas</option>
                            <option>Matemática</option>
                            <option>Português</option>
                            <option>História</option>
                            <option>Geografia</option>
                        </select>
                    </div>
                </div>

                <!-- Steeve Salvador Grades -->
                <div class="student-grades" id="joao-grades">
                    <div class="student-header">
                        <div class="student-info">
                            <div>
                                <h2>Steeve Salvador</h2>
                                <p>9º Ano A - Ensino Fundamental</p>
                                <span class="average-badge good">Média Geral: 8.7</span>
                            </div>
                        </div>
                    </div>

                    <div class="grades-table-container">
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Disciplina</th>
                                    <th>Professor</th>
                                    <th>1ª Avaliação</th>
                                    <th>2ª Avaliação</th>
                                    <th>Trabalhos</th>
                                    <th>Média</th>
                                    <th>Situação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">calculate</span>
                                            <span>Matemática</span>
                                        </div>
                                    </td>
                                    <td>Prof. Carlos Silva</td>
                                    <td><span class="grade excellent">9.0</span></td>
                                    <td><span class="grade good">8.5</span></td>
                                    <td><span class="grade excellent">9.2</span></td>
                                    <td><span class="grade excellent">8.9</span></td>                                  <td><span class="status-badge active">Aprovado</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">menu_book</span>
                                            <span>Português</span>
                                        </div>
                                    </td>
                                    <td>Prof. Ana Santos</td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade excellent">9.0</span></td>
                                    <td><span class="grade good">8.5</span></td>
                                    <td><span class="grade good">8.5</span></td>
                                    <td><span class="status-badge active">Aprovado</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">science</span>
                                            <span>Física</span>
                                        </div>
                                    </td>
                                    <td>Prof. João Pereira</td>
                                    <td><span class="grade average">7.0</span></td>
                                    <td><span class="grade average">7.5</span></td>
                                    <td><span class="grade average">7.0</span></td>
                                    <td><span class="grade average">7.2</span></td>
                                    <td><span class="status-badge warning">Recuperação</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">history</span>
                                            <span>História</span>
                                        </div>
                                    </td>
                                    <td>Prof. Maria Fernandes</td>
                                    <td><span class="grade excellent">8.5</span></td>
                                    <td><span class="grade excellent">9.0</span></td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade excellent">8.5</span></td>
                                    <td><span class="status-badge active">Aprovado</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Kelton Gonçalves Grades -->
                <div class="student-grades" id="ana-grades">
                    <div class="student-header">
                        <div class="student-info">
                            <div>
                                <h2>Kelton Gonçalves</h2>
                                <p>7º Ano B - Ensino Fundamental</p>
                                <span class="average-badge good">Média Geral: 8.3</span>
                            </div>
                        </div>
                    </div>

                    <div class="grades-table-container">
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Disciplina</th>
                                    <th>Professor</th>
                                    <th>1ª Avaliação</th>
                                    <th>2ª Avaliação</th>
                                    <th>Trabalhos</th>
                                    <th>Média</th>
                                    <th>Situação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">calculate</span>
                                            <span>Matemática</span>
                                        </div>
                                    </td>
                                    <td>Prof. Carlos Silva</td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade average">7.5</span></td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade good">7.8</span></td>
                                    <td><span class="status-badge active">Aprovado</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">menu_book</span>
                                            <span>Português</span>
                                        </div>
                                    </td>
                                    <td>Prof. Ana Santos</td>
                                    <td><span class="grade excellent">9.0</span></td>
                                    <td><span class="grade excellent">9.5</span></td>
                                    <td><span class="grade excellent">9.0</span></td>
                                    <td><span class="grade excellent">9.2</span></td>
                                    <td><span class="status-badge active">Aprovado</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <span class="material-symbols-outlined">translate</span>
                                            <span>Inglês</span>
                                        </div>
                                    </td>
                                    <td>Prof. Luísa Mendes</td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="grade good">8.0</span></td>
                                    <td><span class="status-badge active">Aprovado</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Modal functionality
        const exportBtn = document.getElementById('exportBtn');
        const modal = document.getElementById('exportModal');
        const closeModal = document.querySelector('.close-modal');
        const closeModalBtn = document.querySelector('.close-modal-btn');

        exportBtn.addEventListener('click', () => {
            modal.style.display = 'block';
        });

        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        closeModalBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

        // Filter functionality
        const studentFilter = document.getElementById('studentFilter');
        
        studentFilter.addEventListener('change', () => {
            const value = studentFilter.value;
            
            if (value === 'all') {
                document.getElementById('joao-grades').style.display = 'block';
                document.getElementById('ana-grades').style.display = 'block';
            } else {
                document.getElementById('joao-grades').style.display = 'none';
                document.getElementById('ana-grades').style.display = 'none';
                document.getElementById(`${value}-grades`).style.display = 'block';
            }
        });
    </script>
</body>
</html>