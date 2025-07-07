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
                    $class_grade = $_POST['class_grade'] ?? null;
                    $class_course = $_POST['class_course'] ?? null;
                    $class_period = $_POST['class_period'] ?? null;
                    $class_year = $_POST['class_year'] ?? null;
                    
                    try {
                        $conn->beginTransaction();
                        
                        // Verificar se a turma já existe
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM turma WHERE class_name = :nome AND class_year = :class_year");
                        $stmt->bindParam(':nome', $nomeTurma);
                        $stmt->bindParam(':class_year', $class_year);
                        $stmt->execute();
                        
                        if ($stmt->fetchColumn() == 0) {
                            // Criar nova turma com todos os campos obrigatórios
                            $stmt = $conn->prepare("
                                INSERT INTO turma (
                                    class_name, class_grade, class_course, 
                                    class_period, class_year, class_director_id
                                ) VALUES (
                                    :nome, :class_grade, :class_course, 
                                    :class_period, :class_year, 1
                                )
                            ");
                            
                            $stmt->bindParam(':nome', $nomeTurma);
                            $stmt->bindParam(':class_grade', $classe); // Use the class from form
                            $stmt->bindParam(':class_course', $class_course);
                            $stmt->bindParam(':class_period', $class_period);
                            $stmt->bindParam(':class_year', $class_year);
                            $stmt->execute();
                            
                            $newTurmaId = $conn->lastInsertId();
                            $mensagem = "Turma $nomeTurma criada com sucesso!";
                        } else {
                            $erro = "Já existe uma turma com este nome neste ano letivo!";
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
                        
                        // 1. Verificar se a turma existe
                        $stmtCheckTurma = $conn->prepare("SELECT id, class_capacity FROM turma WHERE id = :turma_id");
                        $stmtCheckTurma->bindParam(':turma_id', $idTurma);
                        $stmtCheckTurma->execute();
                        
                        if ($turma = $stmtCheckTurma->fetch(PDO::FETCH_ASSOC)) {
                            // 2. Verificar capacidade da turma
                            $stmtCount = $conn->prepare("SELECT COUNT(*) FROM estudante_turma WHERE turma_id = :turma_id");
                            $stmtCount->bindParam(':turma_id', $idTurma);
                            $stmtCount->execute();
                            $totalAlunos = $stmtCount->fetchColumn();
                            
                            if (($totalAlunos + count($alunosSelecionados)) > $turma['class_capacity']) {
                                $erro = "A turma não pode ter mais de {$turma['class_capacity']} alunos!";
                            } else {
                                // 3. Adicionar cada aluno selecionado
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

            $alunosTurma = []; // Garantir definição inicial como array vazio
            $alunosDisponiveis = []; // Inicializa como array vazio

            // Buscar todas as classes disponíveis (grades)
            $stmtClasses = $conn->query("SELECT DISTINCT class_grade FROM turma ORDER BY class_grade");
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
                
                // Buscar alunos disponíveis (não alocados em turmas)
                $stmtAlunos = $conn->prepare("
                    SELECT e.id, CONCAT(e.fname, ' ', e.lname) as nome 
                    FROM estudantes e
                    WHERE NOT EXISTS (
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
                unset($turma); // Quebrar a referência
            }
?>

<!DOCTYPE html>
<html lang="pt">
<head>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Turma</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        header h1 {
            text-align: center;
            font-size: 28px;
        }
        
        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .back-button:hover {
            background-color: #2980b9;
        }
        
        .class-details {
            background-color: white;
            border-radius: 5px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .class-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .class-title {
            font-size: 24px;
            color: #2c3e50;
        }
        
        .class-code {
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .class-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .info-card {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        
        .info-card h3 {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        .info-card p {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .section-title {
            font-size: 20px;
            color: #2c3e50;
            margin: 25px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .students-list {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
            color: #2c3e50;
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .status-active {
            color: #27ae60;
            font-weight: bold;
        }
        
        .status-inactive {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background-color: #f39c12;
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #d35400;
        }
        
        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #c0392b;
        }
        
        .btn-add {
            background-color: #2ecc71;
            color: white;
            margin-bottom: 20px;
        }
        
        .btn-add:hover {
            background-color: #27ae60;
        }
        
        .empty-message {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        
        
        <a href="turmas_classe.php?classe=1ª+Classe" class="back-button">← Voltar para listagem de turmas</a>
        
        <div class="class-details">
            <div class="class-header">
                <h2 class="class-title">Detalhes da Turma</h2>
                <span class="class-code">TURMA-MAT2023-01</span>
            </div>
            
            <div class="class-info">
                <div class="info-card">
                    <h3>Professor Responsável</h3>
                    <p>Carlos Eduardo Silva</p>
                </div>
                
                <div class="info-card">
                    <h3>Período</h3>
                    <p>Manhã</p>
                </div>
                
                <div class="info-card">
                    <h3>Nível</h3>
                    <p>Ensino Médio - 3º Ano</p>
                </div>
                
                <div class="info-card">
                    <h3>Dias de Aula</h3>
                    <p>Segunda à Sexta</p>
                </div>
                
                <div class="info-card">
                    <h3>Horário</h3>
                    <p>08:00 - 12:30</p>
                </div>
                
                <div class="info-card">
                    <h3>Estado</h3>
                    <p class="status-active">Ativa</p>
                </div>
            </div>
            
            <div class="info-card" style="grid-column: span 3;">
                <h3>Descrição</h3>
                <p>Turma voltada para alunos do 3º ano do ensino médio com foco em preparação para vestibulares e ENEM. Abordagem de tópicos avançados de matemática incluindo álgebra, geometria analítica e cálculo básico.</p>
            </div>
        </div>
        
        <button class="btn btn-add">+ Adicionar Aluno</button>
        
        <h3 class="section-title">Alunos Matriculados</h3>
        
        <div class="students-list">
            <table>
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Nome do Aluno</th>
                        <th>Data de Ingresso</th>
                        <th>Frequência</th>
                        <th>Média</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>20230001</td>
                        <td>Ana Carolina Oliveira</td>
                        <td>15/02/2023</td>
                        <td>92%</td>
                        <td>8.7</td>
                        <td class="status-active">Ativo</td>
                    </tr>
                    <tr>
                        <td>20230012</td>
                        <td>Bruno Martins Costa</td>
                        <td>15/02/2023</td>
                        <td>88%</td>
                        <td>7.5</td>
                        <td class="status-active">Ativo</td>
                    </tr>
                    <tr>
                        <td>20230025</td>
                        <td>Camila Fernandes Santos</td>
                        <td>15/02/2023</td>
                        <td>95%</td>
                        <td>9.2</td>
                        <td class="status-active">Ativo</td>
                    </tr>
                    <tr>
                        <td>20230033</td>
                        <td>Daniel Pereira Almeida</td>
                        <td>15/02/2023</td>
                        <td>76%</td>
                        <td>6.8</td>
                        <td class="status-active">Ativo</td>
                    </tr>
                    <tr>
                        <td>20230042</td>
                        <td>Eduardo Souza Lima</td>
                        <td>15/02/2023</td>
                        <td>82%</td>
                        <td>7.9</td>
                        <td class="status-active">Ativo</td>
                    </tr>
                    <tr>
                        <td>20230056</td>
                        <td>Fernanda Ribeiro Gomes</td>
                        <td>15/02/2023</td>
                        <td>90%</td>
                        <td>8.5</td>
                        <td class="status-active">Ativo</td>
                    </tr>
                    <tr>
                        <td>20230064</td>
                        <td>Gabriel Torres Nunes</td>
                        <td>15/02/2023</td>
                        <td>65%</td>
                        <td>5.5</td>
                        <td class="status-inactive">Inativo</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <h3 class="section-title">Avaliações e Atividades</h3>
        
        <div class="students-list">
            <table>
                <thead>
                    <tr>
                        <th>Atividade</th>
                        <th>Tipo</th>
                        <th>Data</th>
                        <th>Peso</th>
                        <th>Média da Turma</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Prova 1 - Álgebra</td>
                        <td>Avaliação</td>
                        <td>10/03/2023</td>
                        <td>30%</td>
                        <td>7.8</td>
                    </tr>
                    <tr>
                        <td>Trabalho em Grupo - Geometria</td>
                        <td>Trabalho</td>
                        <td>25/03/2023</td>
                        <td>20%</td>
                        <td>8.5</td>
                    </tr>
                    <tr>
                        <td>Prova 2 - Trigonometria</td>
                        <td>Avaliação</td>
                        <td>15/04/2023</td>
                        <td>30%</td>
                        <td>6.9</td>
                    </tr>
                    <tr>
                        <td>Lista de Exercícios - Cálculo</td>
                        <td>Exercícios</td>
                        <td>05/05/2023</td>
                        <td>10%</td>
                        <td>9.1</td>
                    </tr>
                    <tr>
                        <td>Prova 3 - Geral</td>
                        <td>Avaliação</td>
                        <td>30/05/2023</td>
                        <td>40%</td>
                        <td>8.2</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="actions">
            <button class="btn btn-edit">Editar Turma</button>
            <button class="btn btn-delete">Encerrar Turma</button>
        </div>
    </div>
</body>
</html>