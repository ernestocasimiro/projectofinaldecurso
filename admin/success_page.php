<?php
session_start();

// Redireciona caso o utilizador não esteja autenticado
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Turma Cadastrada com Sucesso</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2e7d32;
            --background: #f4f6f8;
            --white: #ffffff;
            --success-light: #e6f4ea;
            --text-color: #333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .card {
            background-color: var(--white);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
            animation: fadeIn 0.5s ease-in-out;
        }

        .success-icon {
            font-size: 60px;
            color: var(--primary-color);
            background-color: var(--success-light);
            padding: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 24px;
            color: var(--text-color);
            margin-bottom: 10px;
        }

        p {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .actions a {
            display: inline-block;
            margin: 0 10px;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .actions a:hover {
            background-color: #1b5e20;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .card {
                padding: 30px 20px;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h1>Turma cadastrada com sucesso!</h1>
        <p>Os dados da turma foram armazenados corretamente no sistema.</p>

        <div class="actions">
            <a href="classes.php"><i class="fas fa-list"></i> Ver Lista de Turmas</a>
            <a href="add_class.php"><i class="fas fa-plus-circle"></i> Nova Turma</a>
        </div>
    </div>
</body>
</html>
