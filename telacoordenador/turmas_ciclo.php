<?php
// Conexão à base de dados
$sName = "localhost";
$uNname = "root";
$pass = "";
$db_name = "escolabd";

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uNname, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8");
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['criar_turma'])) {
        $classe = $_POST['classe'];
        $nomeTurma = $_POST['nome'];
        $class_period = $_POST['class_period'] ?? null;
        $class_year = $_POST['class_year'] ?? null;
        
        try {
            $conn->beginTransaction();
            
            $stmt = $conn->prepare("SELECT COUNT(*) FROM turma WHERE class_name = :nome AND class_year = :class_year AND class_grade = :classe");
            $stmt->bindParam(':nome', $nomeTurma);
            $stmt->bindParam(':class_year', $class_year);
            $stmt->bindParam(':classe', $classe);
            $stmt->execute();
            
            if ($stmt->fetchColumn() == 0) {
                $stmt = $conn->prepare("
                    INSERT INTO turma (
                        class_name, class_grade, 
                        class_period, class_year, class_director_id
                    ) VALUES (
                        :nome, :classe, 
                        :class_period, :class_year, 1
                    )
                ");
                
                $stmt->bindParam(':nome', $nomeTurma);
                $stmt->bindParam(':classe', $classe);
                $stmt->bindParam(':class_period', $class_period);
                $stmt->bindParam(':class_year', $class_year);
                $stmt->execute();
                
                $newTurmaId = $conn->lastInsertId();
                $mensagem = "Turma $nomeTurma criada com sucesso!";
            } else {
                $erro = "Já existe uma turma com este nome neste ano letivo para esta classe!";
            }
            
            $conn->commit();
        } catch(PDOException $e) {
            $conn->rollBack();
            $erro = "Erro ao criar turma: " . $e->getMessage();
        }
    } elseif (isset($_POST['adicionar_alunos'])) {
        $idTurma = $_POST['turma_id'];
        $alunosSelecionados = isset($_POST['alunos']) ? $_POST['alunos'] : [];
        
        try {
            $conn->beginTransaction();
            
            $stmtCheckTurma = $conn->prepare("SELECT id, class_capacity FROM turma WHERE id = :turma_id");
            $stmtCheckTurma->bindParam(':turma_id', $idTurma);
            $stmtCheckTurma->execute();
            
            if ($turma = $stmtCheckTurma->fetch(PDO::FETCH_ASSOC)) {
                $stmtCount = $conn->prepare("SELECT COUNT(*) FROM estudante_turma WHERE turma_id = :turma_id");
                $stmtCount->bindParam(':turma_id', $idTurma);
                $stmtCount->execute();
                $totalAlunos = $stmtCount->fetchColumn();
                
                if (($totalAlunos + count($alunosSelecionados)) > $turma['class_capacity']) {
                    $erro = "A turma não pode ter mais de {$turma['class_capacity']} alunos!";
                } else {
                    $stmtInsert = $conn->prepare("
                        INSERT INTO estudante_turma (estudante_id, turma_id) 
                        VALUES (:estudante_id, :turma_id)
                    ");
                    
                    $stmtCheck = $conn->prepare("
                        SELECT COUNT(*) FROM estudante_turma 
                        WHERE turma_id = :turma_id AND estudante_id = :estudante_id
                    ");
                    
                    $added = 0;
                    foreach ($alunosSelecionados as $idEstudante) {
                        $stmtCheck->bindParam(':turma_id', $idTurma);
                        $stmtCheck->bindParam(':estudante_id', $idEstudante);
                        $stmtCheck->execute();
                        
                        if ($stmtCheck->fetchColumn() == 0) {
                            $stmtInsert->bindParam(':turma_id', $idTurma);
                            $stmtInsert->bindParam(':estudante_id', $idEstudante);
                            $stmtInsert->execute();
                            $added++;
                        }
                    }
                    
                    $mensagem = "$added alunos adicionados à turma com sucesso!";
                }
            } else {
                $erro = "Turma não encontrada!";
            }
            
            $conn->commit();
        } catch(PDOException $e) {
            $conn->rollBack();
            $erro = "Erro ao adicionar alunos: " . $e->getMessage();
        }
    } elseif (isset($_POST['remover_aluno'])) {
        $idTurma = $_POST['turma_id'];
        $idEstudante = $_POST['estudante_id'];
        
        try {
            $stmt = $conn->prepare("
                DELETE FROM estudante_turma 
                WHERE turma_id = :turma_id AND estudante_id = :estudante_id
            ");
            $stmt->bindParam(':turma_id', $idTurma);
            $stmt->bindParam(':estudante_id', $idEstudante);
            $stmt->execute();
            
            $mensagem = "Aluno removido da turma com sucesso!";
        } catch(PDOException $e) {
            $erro = "Erro ao remover aluno: " . $e->getMessage();
        }
    }
}

$alunosTurma = [];
$alunosDisponiveis = [];

// Buscar todas as classes do IIº Ciclo disponíveis (7ª à 9ª classe)
$stmtClasses = $conn->query("SELECT DISTINCT class_grade FROM turma WHERE class_grade REGEXP '^[7-9]ª Classe$' ORDER BY class_grade");
$classes = $stmtClasses->fetchAll(PDO::FETCH_ASSOC);

// Verificar se foi selecionada uma classe específica
$classeSelecionada = isset($_GET['classe']) ? urldecode($_GET['classe']) : null;

if ($classeSelecionada) {
    // Buscar turmas da classe selecionada
    $stmtTurmas = $conn->prepare("
        SELECT * FROM turma 
        WHERE class_grade = :classe 
        ORDER BY class_name
    ");
    $stmtTurmas->bindParam(':classe', $classeSelecionada);
    $stmtTurmas->execute();
    $turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar alunos disponíveis para o IIº Ciclo
    $stmtAlunos = $conn->prepare("
        SELECT e.id, CONCAT(e.fname, ' ', e.lname) as nome 
        FROM estudantes e
        WHERE e.area = 'IIº Ciclo' AND NOT EXISTS (
            SELECT 1 FROM estudante_turma et 
            WHERE et.estudante_id = e.id
        )
        ORDER BY e.fname, e.lname
    ");
    $stmtAlunos->execute();
    $alunosDisponiveis = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);
    
    // Para cada turma, buscar seus alunos
    foreach ($turmas as &$turma) {
        $stmt = $conn->prepare("
            SELECT e.id, CONCAT(e.fname, ' ', e.lname) as nome 
            FROM estudantes e
            JOIN estudante_turma et ON e.id = et.estudante_id
            WHERE et.turma_id = :turma_id
            ORDER BY e.fname, e.lname
        ");
        $stmt->bindParam(':turma_id', $turma['id']);
        $stmt->execute();
        $turma['alunos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($turma);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - IIº Ciclo</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        :root {
            --primary-color: #3a5bb9;
            --secondary-color: #2d4a9e;
            --accent-color: #e74c3c;
            --light-color: #f5f7fa;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --text-color: #333;
            --text-light: #7f8c8d;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--light-color);
            padding: 0;
            margin: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: var(--dark-color);
            color: white;
            padding: 20px 0;
            box-shadow: var(--box-shadow);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
        }
        
        .logo span {
            color: var(--primary-color);
        }
        
        .main-title {
            margin: 30px 0;
            color: var(--dark-color);
            position: relative;
            padding-bottom: 10px;
            font-size: 1.8rem;
        }
        
        .main-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .classes-container, .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .class-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            transition: var(--transition);
            border-left: 4px solid var(--primary-color);
            box-shadow: var(--box-shadow);
            cursor: pointer;
            position: relative;
        }
        
        .class-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .class-card h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .class-card h3 .material-symbols-outlined {
            font-size: 1.5rem;
        }
        
        .class-info {
            color: #555;
            margin: 10px 0;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .class-info strong {
            color: #444;
            font-weight: 500;
        }
        
        .class-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }
        
        .class-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .class-meta-item .material-symbols-outlined {
            font-size: 1.1rem;
        }
        
        .turma-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
        }
        
        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .form-criar-turma {
            margin: 20px 0;
            padding: 25px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: 1px solid #ddd;
            max-width: 500px;
            border-left: 4px solid var(--primary-color);
        }
        
        .form-criar-turma h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-size: 1.3rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-family: inherit;
            font-size: 1rem;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid var(--success-color);
        }
        
        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid var(--accent-color);
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: var(--text-light);
            color: white;
            border-radius: var(--border-radius);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .back-button:hover {
            background-color: #6c7a7d;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 25px;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .alunos-list {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
            border: 1px solid #eee;
            border-radius: var(--border-radius);
            padding: 10px;
        }
        
        .aluno-select {
            display: flex;
            align-items: center;
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
        }
        
        .no-turmas {
            color: var(--text-light);
            font-style: italic;
            padding: 20px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .classes-container, .classes-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    
    <main class="container">
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>
        
        <h1 class="main-title"><?php echo $classeSelecionada ? "Turmas da " . htmlspecialchars($classeSelecionada) : "IIº Ciclo (7ª à 9ª Classe)"; ?></h1>
        
        <?php if (!$classeSelecionada): ?>
            <div class="classes-container">
                <?php foreach ($classes as $classe): ?>
                    <div class="class-card" onclick="window.location.href='turmas_ciclo.php?classe=<?php echo urlencode($classe['class_grade']); ?>'">
                        <h3>
                            <span class="material-symbols-outlined">school</span>
                            <?php echo htmlspecialchars($classe['class_grade']); ?>
                        </h3>
                        <p class="class-info">Clique para visualizar as turmas desta classe</p>
                        <div class="class-meta">
                            <span class="class-meta-item">
                                <span class="material-symbols-outlined">group</span>
                                Várias turmas
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="form-criar-turma">
                <h3><i class="fas fa-plus-circle"></i> Criar Nova Turma</h3>
                <form method="post">
                    <input type="hidden" name="classe" value="<?php echo htmlspecialchars($classeSelecionada); ?>">
                    <div class="form-group">
                        <label for="nome">Nome da Turma</label>
                        <input type="text" id="nome" name="nome" class="form-control" required placeholder="Ex: Turma A">
                    </div>
                    <div class="form-group">
                        <label for="class_period">Turno</label>
                        <select id="class_period" name="class_period" class="form-control" required>
                            <option value="">Selecione o turno</option>
                            <option value="Manhã">Manhã</option>
                            <option value="Tarde">Tarde</option>
                            <option value="Noite">Noite</option>
                        </select>
                    </div>
                    <input type="hidden" name="class_year" value="<?php echo date('Y'); ?>">
                    <button type="submit" name="criar_turma" class="btn">
                        <i class="fas fa-save"></i> Criar Turma
                    </button>
                </form>
            </div>
            
            <?php if (count($turmas) > 0): ?>
                <div class="classes-grid">
                    <?php foreach ($turmas as $turma): ?>
                        <div class="class-card" onclick="openTurmaDetails('<?php echo $turma['id']; ?>')">
                            <h3>
                                <span class="material-symbols-outlined">class</span>
                                <?php echo htmlspecialchars($turma['class_name']); ?>
                            </h3>
                            <p class="class-info"><strong>Turno:</strong> <?php echo htmlspecialchars($turma['class_period'] ?? 'Não definido'); ?></p>
                            <p class="class-info"><strong>Ano Letivo:</strong> <?php echo htmlspecialchars($turma['class_year'] ?? date('Y')); ?></p>
                            <div class="class-meta">
                                <span class="class-meta-item">
                                    <span class="material-symbols-outlined">group</span>
                                    <?php echo count($turma['alunos']); ?> alunos
                                </span>
                            </div>
                            <div class="turma-actions">
                                <?php if (count($alunosDisponiveis) > 0 && count($turma['alunos']) < 30): ?>
                                    <button class="btn" onclick="event.stopPropagation(); abrirModal('modal-<?php echo $turma['id']; ?>')">
                                        <i class="fas fa-user-plus"></i> Adicionar Alunos
                                    </button>
                                <?php endif; ?>
                                <a href="turma_detalhes.php?id=<?php echo $turma['id']; ?>" class="btn" onclick="event.stopPropagation(); return verificarTurmaExistente('<?php echo $turma['id']; ?>')">
                                    <i class="fas fa-eye"></i> Ver Detalhes
                                </a>
                            </div>
                        </div>
                        
                        <div id="modal-<?php echo $turma['id']; ?>" class="modal">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3>Adicionar Alunos à Turma <?php echo htmlspecialchars($turma['class_name']); ?></h3>
                                    <button class="close-modal" onclick="fecharModal('modal-<?php echo $turma['id']; ?>')">&times;</button>
                                </div>
                                <form method="post">
                                    <input type="hidden" name="turma_id" value="<?php echo $turma['id']; ?>">
                                    <div class="alunos-list">
                                        <?php if (count($alunosDisponiveis) > 0): ?>
                                            <?php foreach ($alunosDisponiveis as $aluno): ?>
                                                <div class="aluno-select">
                                                    <input type="checkbox" name="alunos[]" id="aluno-<?php echo $turma['id']; ?>-<?php echo $aluno['id']; ?>" value="<?php echo $aluno['id']; ?>">
                                                    <label for="aluno-<?php echo $turma['id']; ?>-<?php echo $aluno['id']; ?>">
                                                        <?php echo htmlspecialchars($aluno['nome']); ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p>Não há alunos disponíveis para adicionar a esta turma.</p>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (count($alunosDisponiveis) > 0): ?>
                                        <button type="submit" name="adicionar_alunos" class="btn">
                                            <i class="fas fa-save"></i> Adicionar Alunos Selecionados
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-turmas">Não há turmas cadastradas para esta classe.</p>
            <?php endif; ?>
            
            <a href="turmas.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        <?php endif; ?>
    </main>
    
    <script>
        function abrirModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        
        function fecharModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        
        function openTurmaDetails(id) {
            // Verifica se a turma existe antes de redirecionar
            verificarTurmaExistente(id);
        }
        
        function verificarTurmaExistente(id) {
            fetch(`verificar_turma.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.existe) {
                        window.location.href = 'turma_detalhes.php?id=' + id;
                    } else {
                        alert('Turma não encontrada!');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro ao verificar a turma');
                });
            
            // Retorna false para prevenir o comportamento padrão do link
            return false;
        }
        
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>