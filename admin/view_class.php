<?php
// view_class.php
session_start();
include('dbconnection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$id = (int)$_GET['id'];

try {
    // Buscar dados da turma com info do diretor
    $stmt = $conn->prepare("SELECT t.*, p.fname, p.lname FROM turma t LEFT JOIN professores p ON t.class_director_id = p.id WHERE t.id = ?");
    $stmt->execute([$id]);
    $class = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$class) {
        die("Turma não encontrada.");
    }
} catch (PDOException $e) {
    die("Erro ao buscar turma: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Visualizar Turma - <?= htmlspecialchars($class['class_grade'] . 'º Ano') ?></title>
    <style>
        /* Reset básico */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fa;
            color: #333;
            margin: 0;
            padding: 0 15px;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px 40px;
        }

        h1 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 10px;
            color: #1e2a38;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            font-size: 1rem;
            color: #6b7c93;
            margin-bottom: 30px;
        }

        .card-info {
            min-width: 280px;
        }

        .info-section {
            margin-bottom: 25px;
        }

        .info-section h2 {
            font-size: 1.2rem;
            border-bottom: 2px solid #4a90e2;
            padding-bottom: 8px;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .info-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .info-label {
            flex: 0 0 150px;
            font-weight: 600;
            color: #34495e;
        }

        .info-value {
            flex: 1;
            color: #555;
            word-wrap: break-word;
        }

        .btn-back {
            display: inline-block;
            background-color: #4a90e2;
            color: white;
            padding: 12px 22px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .btn-back:hover {
            background-color: #357ABD;
        }

        /* Responsividade */
        @media (max-width: 600px) {
            .info-label, .info-value {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Detalhes da Turma</h1>
    <p class="subtitle"><?= htmlspecialchars($class['class_grade'] . 'º Ano - ' . ucfirst($class['class_course']) . ' (' . $class['class_year'] . ')') ?></p>

    <div class="card-info">
        <div class="info-section">
            <h2>Informações Gerais</h2>
            <div class="info-row"><div class="info-label">Classe:</div><div class="info-value"><?= htmlspecialchars($class['class_grade']) ?>º Ano</div></div>
            <div class="info-row"><div class="info-label">Ano Letivo:</div><div class="info-value"><?= htmlspecialchars($class['class_year']) ?></div></div>
            <div class="info-row"><div class="info-label">Curso:</div><div class="info-value"><?= htmlspecialchars(ucfirst($class['class_course'])) ?></div></div>
            <div class="info-row"><div class="info-label">Capacidade:</div><div class="info-value"><?= htmlspecialchars($class['class_capacity']) ?></div></div>
            <div class="info-row"><div class="info-label">Sala:</div><div class="info-value"><?= htmlspecialchars($class['class_room'] ?: '-') ?></div></div>
            <div class="info-row"><div class="info-label">Período:</div>
                <div class="info-value">
                    <?php
                        if ($class['class_period'] === 'morning') echo 'Manhã';
                        else if ($class['class_period'] === 'afternoon') echo 'Tarde';
                        else echo '-';
                    ?>
                </div>
            </div>
            <div class="info-row"><div class="info-label">Diretor de Turma:</div>
                <div class="info-value">
                    <?= htmlspecialchars($class['fname'] && $class['lname'] ? $class['fname'] . ' ' . $class['lname'] : '-') ?>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h2>Descrição</h2>
            <div class="info-value" style="white-space: pre-wrap;"><?= htmlspecialchars($class['class_description'] ?: '-') ?></div>
        </div>

        <div class="info-section">
            <h2>Observações</h2>
            <div class="info-value" style="white-space: pre-wrap;"><?= htmlspecialchars($class['class_observations'] ?: '-') ?></div>
        </div>
    </div>

    <a href="classes.php" class="btn-back">← Voltar para Lista</a>
</div>

</body>
</html>
