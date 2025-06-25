<?php
// view_guardian.php
session_start();
include('dbconnection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$id = (int)$_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM encarregados WHERE id = ?");
    $stmt->execute([$id]);
    $g = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$g) {
        die("Encarregado não encontrado.");
    }
} catch (PDOException $e) {
    die("Erro ao buscar encarregado: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Visualizar Encarregado - <?= htmlspecialchars($g['fname'] . ' ' . $g['lname']); ?></title>
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

        /* Responsividade */
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
    <h1>Detalhes do Encarregado</h1>
    <p class="subtitle"><?= htmlspecialchars($g['fname'] . ' ' . $g['lname']); ?></p>

    <div class="card">
        <div class="card-image">
            <?php if (!empty($g['fotoperfil']) && file_exists($g['fotoperfil'])): ?>
                <img src="<?= htmlspecialchars($g['fotoperfil']); ?>" alt="Foto de <?= htmlspecialchars($g['fname']); ?>" />
            <?php else: ?>
                <img src="https://via.placeholder.com/250x300?text=Sem+Foto" alt="Sem foto de perfil" />
            <?php endif; ?>

            <div class="bi-photos">
                <?php if (!empty($g['foto_bi1']) && file_exists($g['foto_bi1'])): ?>
                    <img src="<?= htmlspecialchars($g['foto_bi1']); ?>" alt="Foto BI Frente" title="Foto BI Frente" />
                <?php else: ?>
                    <img src="https://via.placeholder.com/120x80?text=BI+Frente" alt="Sem foto BI Frente" />
                <?php endif; ?>

                <?php if (!empty($g['foto_bi2']) && file_exists($g['foto_bi2'])): ?>
                    <img src="<?= htmlspecialchars($g['foto_bi2']); ?>" alt="Foto BI Verso" title="Foto BI Verso" />
                <?php else: ?>
                    <img src="https://via.placeholder.com/120x80?text=BI+Verso" alt="Sem foto BI Verso" />
                <?php endif; ?>
            </div>
        </div>

        <div class="card-info">
            <div class="info-section">
                <h2>Informações Pessoais</h2>
                <div class="info-row"><div class="info-label">Nome Completo:</div><div class="info-value"><?= htmlspecialchars($g['fname'] . ' ' . $g['lname']); ?></div></div>
                <div class="info-row"><div class="info-label">Username:</div><div class="info-value"><?= htmlspecialchars($g['username']); ?></div></div>

                <?php
                    // Melhor tratamento do campo genero
                    $genero = trim($g['genero'] ?? '');
                    $generoFormatado = 'Não especificado';

                    if ($genero !== '') {
                        $generoUpper = strtoupper($genero);
                        if ($generoUpper === 'M') {
                            $generoFormatado = 'Masculino';
                        } elseif ($generoUpper === 'F') {
                            $generoFormatado = 'Feminino';
                        } else {
                            // Caso tenha outro texto, exibe capitalizado
                            $generoFormatado = ucfirst(strtolower($genero));
                        }
                    }
                ?>
                <div class="info-row">
                    <div class="info-label">Gênero:</div>
                    <div class="info-value"><?= htmlspecialchars($generoFormatado); ?></div>
                </div>

                <div class="info-row"><div class="info-label">Data de Nascimento:</div><div class="info-value"><?= htmlspecialchars($g['data_nascimento']); ?></div></div>
                <div class="info-row"><div class="info-label">Número do BI:</div><div class="info-value"><?= htmlspecialchars($g['num_bi']); ?></div></div>
                <div class="info-row"><div class="info-label">Endereço:</div><div class="info-value"><?= htmlspecialchars($g['endereco']); ?></div></div>
            </div>

            <div class="info-section">
                <h2>Contato</h2>
                <div class="info-row"><div class="info-label">Telefone:</div><div class="info-value"><?= htmlspecialchars($g['telefone']); ?></div></div>
                <div class="info-row"><div class="info-label">Email:</div><div class="info-value"><?= htmlspecialchars($g['email']); ?></div></div>
            </div>

            <div class="info-section">
                <h2>Profissão e Parentesco</h2>
                <div class="info-row"><div class="info-label">Profissão:</div><div class="info-value"><?= htmlspecialchars($g['profissao']); ?></div></div>
                <div class="info-row"><div class="info-label">Parentesco:</div><div class="info-value"><?= htmlspecialchars($g['parentesco']); ?></div></div>
            </div>

            <a href="guardians.php" class="btn-back">Voltar para Lista</a>
        </div>
    </div>
</div>

</body>
</html>
