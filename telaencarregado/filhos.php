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

// Verifica se é uma requisição para exportar dados
if (isset($_GET['export'])) {
    exportStudentData($conn, $idGuardian);
    exit;
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

    // Busca todos os professores para o modal de mensagem
    $stmt = $conn->prepare("SELECT id, fname, lname FROM professores");
    $stmt->execute();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit;
}

$dataAtual = '15 de Abril de 2025';
$trimestre = '2º trimestre';
$anoLetivo = '2025';

// Função para exportar dados do estudante
function exportStudentData($conn, $guardianId) {
    try {
        // Busca os estudantes vinculados ao encarregado
        $stmt = $conn->prepare("
            SELECT e.id, e.fname, e.lname, e.status, 
                   t.class_name, t.class_grade, t.class_course
            FROM estudantes e
            LEFT JOIN turma t ON e.area = t.id
            WHERE e.encarregado_id = :encarregado_id
        ");
        $stmt->bindParam(':encarregado_id', $guardianId, PDO::PARAM_INT);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($students)) {
            die("Nenhum estudante vinculado a este encarregado.");
        }

        // Preparar dados para exportação
        $exportData = array();
        
        foreach ($students as $student) {
            $studentId = $student['id'];
            
            /* Buscar notas por disciplina e período
            $stmt = $conn->prepare("
                SELECT d.nome AS disciplina, n.trimestre, n.nota, n.observacoes
                FROM notas n
                JOIN disciplinas d ON n.disciplina_id = d.id
                WHERE n.estudante_id = :estudante_id
                ORDER BY d.nome, n.trimestre
            "); 
            $stmt->bindParam(':estudante_id', $studentId, PDO::PARAM_INT);
            $stmt->execute();
            $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Buscar frequência/assiduidade
            $stmt = $conn->prepare("
                SELECT data, presente, justificativa
                FROM frequencia
                WHERE estudante_id = :estudante_id
                ORDER BY data
            ");
            $stmt->bindParam(':estudante_id', $studentId, PDO::PARAM_INT);
            $stmt->execute();
            $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Buscar comportamento
            $stmt = $conn->prepare("
                SELECT data, descricao, tipo
                FROM comportamento
                WHERE estudante_id = :estudante_id
                ORDER BY data DESC
            ");
            $stmt->bindParam(':estudante_id', $studentId, PDO::PARAM_INT);
            $stmt->execute();
            $behavior = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Buscar observações dos professores
            $stmt = $conn->prepare("
                SELECT p.fname AS professor_nome, p.lname AS professor_sobrenome, 
                       o.data, o.observacao
                FROM observacoes_professores o
                JOIN professores p ON o.professor_id = p.id
                WHERE o.estudante_id = :estudante_id
                ORDER BY o.data DESC
            ");
            $stmt->bindParam(':estudante_id', $studentId, PDO::PARAM_INT);
            $stmt->execute();
            $observations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Buscar situação final
            $stmt = $conn->prepare("
                SELECT situacao, ano_letivo
                FROM situacao_final
                WHERE estudante_id = :estudante_id
                ORDER BY ano_letivo DESC
            ");
            $stmt->bindParam(':estudante_id', $studentId, PDO::PARAM_INT);
            $stmt->execute();
            $finalStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);
            */
            // Organizar dados do estudante
            $studentData = array(
                'info' => array(
                    'nome' => $student['fname'] . ' ' . $student['lname'],
                    'turma' => $student['class_name'] ?? 'Não definida',
                    'classe' => $student['class_grade'] ?? 'Não definida',
                    'curso' => $student['class_course'] ?? 'Não definido',
                    'status' => $student['status']
                ),
                'frequencia' => $attendance,
                'comportamento' => $behavior,
                'observacoes' => $observations,
                'situacao_final' => $finalStatus
            );
            
            $exportData[] = $studentData;
        }
        
        // Gerar JSON para download
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="dados_educandos_' . date('Y-m-d') . '.json"');
        echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
    } catch (PDOException $e) {
        die("Erro ao exportar dados: " . $e->getMessage());
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Filhos - Dashboard Encarregados</title>
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
            height: 60px;
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

        /* Children Grid Styles */
        .children-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .child-card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            transition: all 0.3s ease;
        }

        .child-card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-2px);
        }

        .child-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .child-avatar {
            margin-right: 15px;
        }

        .child-avatar img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-light);
        }

        .child-basic-info {
            flex: 1;
        }

        .child-basic-info h2 {
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: var(--text-color);
        }

        .child-basic-info p {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-bottom: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-badge.active {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success-color);
        }

        .status-badge.inactive {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning-color);
        }

        .status-badge.pending {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--danger-color);
        }

        .child-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px 0;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }

        .stat-item {
            text-align: center;
        }

        .stat-label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-light);
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .stat-value.good {
            color: var(--success-color);
        }

        .stat-value.excellent {
            color: var(--primary-color);
        }

        .child-subjects {
            margin-bottom: 20px;
        }

        .child-subjects h4 {
            font-size: 0.9rem;
            margin-bottom: 10px;
            color: var(--text-color);
        }

        .subject-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .subject-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .subject-name {
            font-size: 0.85rem;
            color: var(--text-color);
        }

        .subject-grade {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .subject-grade.average {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning-color);
        }

        .child-actions {
            display: flex;
            gap: 10px;
        }

        .child-actions .btn {
            flex: 1;
        }

        .no-children {
            background-color: var(--white);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: var(--card-shadow);
        }

        /* Dashboard Card Styles */
        .dashboard-card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .filter-container {
            position: relative;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.85rem;
            background-color: var(--white);
            cursor: pointer;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .activity-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            border-radius: 8px;
            background-color: var(--gray-100);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
        }

        .activity-icon .material-symbols-outlined {
            font-size: 1.2rem;
        }

        .activity-content h4 {
            font-size: 0.95rem;
            margin-bottom: 5px;
            color: var(--text-color);
        }

        .activity-content p {
            font-size: 0.85rem;
            color: var(--text-light);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--white);
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            animation: modalFadeIn 0.3s ease;
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
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 1.2rem;
            color: var(--text-color);
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-light);
            transition: color 0.2s;
        }

        .modal-close:hover {
            color: var(--danger-color);
        }

        .modal-body {
            padding: 20px;
        }

        .teacher-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .teacher-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 8px;
            background-color: var(--gray-100);
            cursor: pointer;
            transition: all 0.2s;
        }

        .teacher-item:hover {
            background-color: var(--primary-light);
        }

        .teacher-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: 600;
        }

        .teacher-info h4 {
            font-size: 1rem;
            margin-bottom: 5px;
            color: var(--text-color);
        }

        .teacher-info p {
            font-size: 0.85rem;
            color: var(--text-light);
        }

        /* Student Details Modal */
        .student-details {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .student-details-section {
            background-color: var(--gray-100);
            border-radius: 8px;
            padding: 15px;
        }

        .student-details-section h4 {
            margin-bottom: 10px;
            color: var(--primary-color);
            font-size: 1rem;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.8rem;
            color: var(--text-light);
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .grades-table th, .grades-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .grades-table th {
            background-color: var(--primary-light);
            color: var(--primary-color);
            font-size: 0.85rem;
        }

        .grades-table td {
            font-size: 0.85rem;
        }

        /* Responsive Styles */
        @media (max-width: 1200px) {
            .children-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 250px;
            }
            
            .content {
                margin-left: 250px;
                width: calc(100% - 250px);
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

            .content {
                margin-left: 70px;
                width: calc(100% - 70px);
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

            .child-header {
                flex-direction: column;
                text-align: center;
            }

            .child-avatar {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .child-actions {
                flex-direction: column;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .search-container {
                width: 150px;
            }

            .children-grid {
                grid-template-columns: 1fr;
            }

            .child-stats {
                grid-template-columns: 1fr;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .modal-content {
                width: 95%;
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
                    <li class="active">
                        <a href="filhos.php">
                            <span class="material-symbols-outlined">family_restroom</span>
                            <span class="menu-text">Meus Filhos</span>
                        </a>
                    </li>
                    <li>
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
                    <input type="text" placeholder="Pesquisar...">
                </div>
                <div class="top-bar-actions">
                    <span class="material-symbols-outlined notification">notifications</span>
                    <span class="material-symbols-outlined">help</span>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1>Meus Filhos</h1>
                    <div class="header-actions">
                        <button class="btn-outline" id="exportDataBtn">
                            <span class="material-symbols-outlined">download</span>
                            Exportar Dados
                        </button>
                    </div>
                </div>

                <!-- Children Cards -->
                <div class="children-grid">
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $student): ?>
                            <div class="child-card">
                                <div class="child-header">
                                    <div class="child-avatar">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['fname'] . ' ' . $student['lname']); ?>&background=random" 
                                             alt="<?php echo htmlspecialchars($student['fname'] . ' ' . $student['lname']); ?>" 
                                             style="width: 80px; height: 80px; border-radius: 50%;">
                                    </div>
                                    <div class="child-basic-info">
                                        <h2><?php echo htmlspecialchars($student['fname'] . ' ' . $student['lname']); ?></h2>
                                        <?php if ($student['class_name']): ?>
                                            <p><?php echo htmlspecialchars($student['class_name']); ?> - <?php echo htmlspecialchars($student['class_grade']); ?>ª Classe</p>
                                        <?php else: ?>
                                            <p>Turma não definida</p>
                                        <?php endif; ?>
                                        <span class="status-badge <?php echo htmlspecialchars($student['status'] ?? 'pending'); ?>">
                                            <?php 
                                                switch($student['status']) {
                                                    case 'active': echo 'Ativo'; break;
                                                    case 'inactive': echo 'Inativo'; break;
                                                    default: echo 'Pendente'; break;
                                                }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="child-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Média Geral</span>
                                        <span class="stat-value good">-</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Frequência</span>
                                        <span class="stat-value excellent">-</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Disciplinas</span>
                                        <span class="stat-value">-</span>
                                    </div>
                                </div>

                                <div class="child-subjects">
                                     <h4>Disciplinas</h4>
                                    <div class="subject-list">
                                        <div class="subject-item">
                                            <span class="subject-name">Matemática</span>
                                            <span class="subject-grade average">-</span>
                                        </div>
                                        <div class="subject-item">
                                            <span class="subject-name">Português</span>
                                            <span class="subject-grade average">-</span>
                                        </div>
                                        <div class="subject-item">
                                            <span class="subject-name">Ciências</span>
                                            <span class="subject-grade average">-</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="child-actions">
                                    <button class="btn btn-outline view-details-btn" data-student-id="<?php echo $student['id']; ?>">
                                        <span class="material-symbols-outlined">visibility</span>
                                        Ver Detalhes
                                    </button>
                                    <button class="btn btn-primary message-btn" data-student-id="<?php echo $student['id']; ?>">
                                        <span class="material-symbols-outlined">mail</span>
                                        Mensagem
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-children">
                            <p>Nenhum estudante vinculado à sua conta.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Activity Card -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>Atividades Recentes</h2>
                        <div class="filter-container">
                            <select class="filter-select">
                                <option>Últimos 7 dias</option>
                                <option>Últimos 30 dias</option>
                                <option>Todo o período</option>
                            </select>
                        </div>
                    </div>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon">
                                <span class="material-symbols-outlined">assignment</span>
                            </div>
                            <div class="activity-content">
                                <h4>Nova avaliação de Matemática</h4>
                                <p>Publicada em <?php echo $dataAtual; ?> para o <?php echo $trimestre; ?></p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <span class="material-symbols-outlined">event</span>
                            </div>
                            <div class="activity-content">
                                <h4>Reunião de Pais e Encarregados</h4>
                                <p>Agendada para 20 de Abril de <?php echo $anoLetivo; ?></p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <span class="material-symbols-outlined">campaign</span>
                            </div>
                            <div class="activity-content">
                                <h4>Novo comunicado escolar</h4>
                                <p>Publicado em <?php echo $dataAtual; ?> pela direção</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Teacher Selection Modal -->
    <div class="modal" id="teacherModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Selecione o Professor</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="teacher-list">
                    <?php foreach ($teachers as $teacher): ?>
                        <div class="teacher-item" data-teacher-id="<?php echo $teacher['id']; ?>">
                            <div class="teacher-avatar">
                                <?php 
                                    $names = explode(' ', $teacher['fname']);
                                    echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                                ?>
                            </div>
                            <div class="teacher-info">
                                <h4><?php echo htmlspecialchars($teacher['fname'] . ' ' . $teacher['lname']); ?></h4>
                                <p>Professor de Disciplina</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Details Modal -->
    <div class="modal" id="studentDetailsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detalhes do Educando</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="student-details" id="studentDetailsContent">
                    <!-- Conteúdo será carregado via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const modals = document.querySelectorAll('.modal');
            const teacherModal = document.getElementById('teacherModal');
            const studentDetailsModal = document.getElementById('studentDetailsModal');
            const closeButtons = document.querySelectorAll('.modal-close');
            const messageButtons = document.querySelectorAll('.message-btn');
            const viewDetailsButtons = document.querySelectorAll('.view-details-btn');
            const exportDataBtn = document.getElementById('exportDataBtn');
            
            // Open teacher modal when message button is clicked
            messageButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const studentId = this.getAttribute('data-student-id');
                    teacherModal.style.display = 'flex';
                    // Store student ID for later use
                    teacherModal.setAttribute('data-student-id', studentId);
                });
            });
            
            // Open student details modal
            viewDetailsButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const studentId = this.getAttribute('data-student-id');
                    loadStudentDetails(studentId);
                    studentDetailsModal.style.display = 'flex';
                });
            });
            
            // Close modals
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    modal.style.display = 'none';
                });
            });
            
            // Close modal when clicking outside
            modals.forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            });
            
            // Teacher selection
            const teacherItems = document.querySelectorAll('.teacher-item');
            teacherItems.forEach(item => {
                item.addEventListener('click', function() {
                    const teacherId = this.getAttribute('data-teacher-id');
                    const studentId = teacherModal.getAttribute('data-student-id');
                    // Here you would redirect to messages page or open a message composer
                    window.location.href = `mensagens.php?teacher_id=${teacherId}&student_id=${studentId}`;
                });
            });
            
            // Export data button
            exportDataBtn.addEventListener('click', function() {
                window.location.href = 'filhos.php?export=1';
            });
            
            // Function to load student details via AJAX
            function loadStudentDetails(studentId) {
                const detailsContent = document.getElementById('studentDetailsContent');
                detailsContent.innerHTML = '<p>Carregando detalhes...</p>';
                
                // Simulate AJAX request (in a real app, you would fetch from server)
                setTimeout(() => {
                    // This would be replaced with actual data from the server
                    const mockData = {
                        info: {
                            nome: "Estudante " + studentId,
                            turma: "Turma A",
                            classe: "10ª Classe",
                            curso: "Ciências Físico-Biológicas",
                            status: "Ativo"
                        },
                        notas: [
                            { disciplina: "Matemática", trimestre: "1º Trimestre", nota: 15, observacoes: "Bom desempenho" },
                            { disciplina: "Português", trimestre: "1º Trimestre", nota: 13, observacoes: "Participação ativa" }
                        ],
                        frequencia: [
                            { data: "2025-04-01", presente: true, justificativa: "" },
                            { data: "2025-04-02", presente: false, justificativa: "Atestado médico" }
                        ],
                        comportamento: [
                            { data: "2025-03-15", descricao: "Excelente participação em aula", tipo: "positivo" },
                            { data: "2025-02-20", descricao: "Atraso na entrega de trabalho", tipo: "negativo" }
                        ],
                        observacoes: [
                            { professor_nome: "Maria", professor_sobrenome: "Silva", data: "2025-03-10", observacao: "O estudante demonstra grande interesse nas aulas." }
                        ],
                        situacao_final: [
                            { situacao: "Aprovado", ano_letivo: "2024" }
                        ]
                    };
                    
                    renderStudentDetails(mockData);
                }, 500);
                
                // In a real implementation, you would use fetch() to get data from the server
                /*
                fetch(`get_student_details.php?student_id=${studentId}`)
                    .then(response => response.json())
                    .then(data => renderStudentDetails(data))
                    .catch(error => {
                        detailsContent.innerHTML = '<p>Erro ao carregar detalhes do estudante.</p>';
                        console.error('Error:', error);
                    });
                */
            }
            
            // Function to render student details
            function renderStudentDetails(data) {
                const detailsContent = document.getElementById('studentDetailsContent');
                
                let html = `
                    <div class="student-details-section">
                        <h4>Informações Básicas</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <span class="detail-label">Nome Completo</span>
                                <span class="detail-value">${data.info.nome}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Turma</span>
                                <span class="detail-value">${data.info.turma}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Classe</span>
                                <span class="detail-value">${data.info.classe}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Curso</span>
                                <span class="detail-value">${data.info.curso}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status</span>
                                <span class="detail-value">${data.info.status}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="student-details-section">
                        <h4>Notas por Disciplina</h4>
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Disciplina</th>
                                    <th>Trimestre</th>
                                    <th>Nota</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.notas.forEach(grade => {
                    html += `
                        <tr>
                            <td>${grade.disciplina}</td>
                            <td>${grade.trimestre}</td>
                            <td>${grade.nota}</td>
                            <td>${grade.observacoes}</td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="student-details-section">
                        <h4>Frequência/Assiduidade</h4>
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Presente</th>
                                    <th>Justificativa</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.frequencia.forEach(att => {
                    html += `
                        <tr>
                            <td>${att.data}</td>
                            <td>${att.presente ? 'Sim' : 'Não'}</td>
                            <td>${att.justificativa || '-'}</td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="student-details-section">
                        <h4>Comportamento</h4>
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.comportamento.forEach(behavior => {
                    const typeClass = behavior.tipo === 'positivo' ? 'good' : 'average';
                    html += `
                        <tr>
                            <td>${behavior.data}</td>
                            <td><span class="stat-value ${typeClass}">${behavior.tipo === 'positivo' ? 'Positivo' : 'Negativo'}</span></td>
                            <td>${behavior.descricao}</td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="student-details-section">
                        <h4>Observações dos Professores</h4>
                        <div class="activity-list">
                `;
                
                data.observacoes.forEach(obs => {
                    html += `
                        <div class="activity-item">
                            <div class="activity-icon">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                            <div class="activity-content">
                                <h4>${obs.professor_nome} ${obs.professor_sobrenome}</h4>
                                <p>${obs.data}: ${obs.observacao}</p>
                            </div>
                        </div>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                    
                    <div class="student-details-section">
                        <h4>Situação Final</h4>
                        <div class="details-grid">
                `;
                
                data.situacao_final.forEach(status => {
                    html += `
                        <div class="detail-item">
                            <span class="detail-label">${status.ano_letivo}</span>
                            <span class="detail-value">${status.situacao}</span>
                        </div>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                `;
                
                detailsContent.innerHTML = html;
            }
        });
    </script>
</body>
</html>