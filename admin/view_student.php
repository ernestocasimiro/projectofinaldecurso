<?php
// view_student.php
session_start();
include('dbconnection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$id = (int)$_GET['id'];

try {
    // Buscando dados do estudante + turma + encarregado
    $stmt = $conn->prepare("
        SELECT 
            e.*, 
            t.class_name, t.class_grade,
            en.fname AS encarregado_fname, en.lname AS encarregado_lname,
            en.telefone AS encarregado_telefone, en.email AS encarregado_email
        FROM estudantes e
        LEFT JOIN turma t ON e.turma_id = t.id
        LEFT JOIN encarregados en ON e.encarregado_id = en.id
        WHERE e.id = ?
    ");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Estudante não encontrado.");
    }
} catch (PDOException $e) {
    die("Erro ao buscar estudante: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Visualizar estudante - <?= htmlspecialchars($student['fname'] . ' ' . $student['lname']); ?></title>
    <style>
        /* Mesmos estilos do seu view_teacher.php adaptados */
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
        .card {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            align-items: flex-start;
        }
        .card-image {
            flex: 0 0 250px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
        }
        .card-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        .bi-photos {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            justify-content: center;
        }
        .bi-photos img {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.07);
            border: 1px solid #ddd;
        }
        .card-info {
            flex: 1;
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
        @media (max-width: 600px) {
            .card {
                flex-direction: column;
            }
            .card-image {
                width: 100%;
                flex: none;
            }
            .bi-photos {
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Detalhes do estudante</h1>
    <p class="subtitle"><?= htmlspecialchars($student['fname'] . ' ' . $student['lname']); ?></p>

    <div class="card">
        <div class="card-image">
            <?php if (!empty($student['fotoperfil']) && file_exists($student['fotoperfil'])): ?>
                <img src="<?= htmlspecialchars($student['fotoperfil']); ?>" alt="Foto de <?= htmlspecialchars($student['fname']); ?>" />
            <?php else: ?>
                <img src="https://via.placeholder.com/250x300?text=Sem+Foto" alt="Sem foto de perfil" />
            <?php endif; ?>

            <div class="bi-photos">
                <?php if (!empty($student['foto_bi1']) && file_exists($student['foto_bi1'])): ?>
                    <img src="<?= htmlspecialchars($student['foto_bi1']); ?>" alt="Foto BI Frente" title="Foto BI Frente" />
                <?php else: ?>
                    <img src="https://via.placeholder.com/120x80?text=BI+Frente" alt="Sem foto BI Frente" />
                <?php endif; ?>

                <?php if (!empty($student['foto_bi2']) && file_exists($student['foto_bi2'])): ?>
                    <img src="<?= htmlspecialchars($student['foto_bi2']); ?>" alt="Foto BI Verso" title="Foto BI Verso" />
                <?php else: ?>
                    <img src="https://via.placeholder.com/120x80?text=BI+Verso" alt="Sem foto BI Verso" />
                <?php endif; ?>
            </div>
        </div>

        <div class="card-info">
            <div class="info-section">
                <h2>Informações Pessoais</h2>
                <div class="info-row"><div class="info-label">Nome Completo:</div><div class="info-value"><?= htmlspecialchars($student['fname'] . ' ' . $student['lname']); ?></div></div>
                <div class="info-row"><div class="info-label">Gênero:</div><div class="info-value"><?= htmlspecialchars($student['genero']); ?></div></div>
                <div class="info-row"><div class="info-label">Data de Nascimento:</div><div class="info-value"><?= htmlspecialchars($student['data_nascimento']); ?></div></div>
                <div class="info-row"><div class="info-label">Número do BI:</div><div class="info-value"><?= htmlspecialchars($student['num_bi']); ?></div></div>
                <div class="info-row"><div class="info-label">Endereço:</div><div class="info-value"><?= htmlspecialchars($student['endereco']); ?></div></div>
            </div>

            <div class="info-section">
                <h2>Contato</h2>
                <div class="info-row"><div class="info-label">Telefone:</div><div class="info-value"><?= htmlspecialchars($student['telefone']); ?></div></div>
                <div class="info-row"><div class="info-label">Email:</div><div class="info-value"><?= htmlspecialchars($student['email']); ?></div></div>
            </div>

            <div class="info-section">
                <h2>Turma</h2>
                <div class="info-row"><div class="info-label">Ano e Turma:</div><div class="info-value"><?= htmlspecialchars($student['class_grade'] . 'º Ano - Turma ' . $student['class_name']); ?></div></div>
            </div>

            <div class="info-section">
                <h2>Encarregado</h2>
                <div class="info-row"><div class="info-label">Nome:</div><div class="info-value"><?= htmlspecialchars($student['encarregado_fname'] . ' ' . $student['encarregado_lname']); ?></div></div>
                <div class="info-row"><div class="info-label">Telefone:</div><div class="info-value"><?= htmlspecialchars($student['encarregado_telefone']); ?></div></div>
                <div class="info-row"><div class="info-label">Email:</div><div class="info-value"><?= htmlspecialchars($student['encarregado_email']); ?></div></div>
            </div>

            <a href="student.php" class="btn-back">← Voltar para Lista</a>
        </div>
    </div>
</div>

</body>
</html>
