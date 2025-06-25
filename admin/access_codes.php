<?php
session_start();
require_once 'dbconnection.php';

$idCoordinator = $_SESSION['id'] ?? null;
$mensagemCodigo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_code'])) {
    if (!$idCoordinator) {
        $mensagemCodigo = "<div class='alert error'>Acesso não autorizado.</div>";
    } else {
        try {
            $diasValidade = $_POST['days'] ?? 30;
            $area = $_POST['area'] ?? '';
            $codigoAcesso = bin2hex(random_bytes(4));
            $dataExpiracao = date('Y-m-d H:i:s', strtotime("+$diasValidade days"));

            if (empty($area)) {
                $mensagemCodigo = "<div class='alert error'>Selecione uma área para gerar o código.</div>";
            } else {
                $stmt = $conn->prepare("INSERT INTO coordenador_acessos 
                                      (coordenador_id, codigo_acesso, data_expiracao, area) 
                                      VALUES (:id, :codigo, :expiracao, :area)");
                $stmt->bindParam(':id', $idCoordinator);
                $stmt->bindParam(':codigo', $codigoAcesso);
                $stmt->bindParam(':expiracao', $dataExpiracao);
                $stmt->bindParam(':area', $area);
                $stmt->execute();

                $mensagemCodigo = "<div class='alert success'>✅ Código gerado para $area: <strong>$codigoAcesso</strong><br>Válido até: $dataExpiracao</div>";
            }
        } catch (PDOException $e) {
            $mensagemCodigo = "<div class='alert error'>Erro ao gerar código: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Códigos de Acesso | Sistema Administrativo</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --error-color: #e74c3c;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --border-radius: 4px;
            --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
            padding: 0;
            margin: 0;
        }
        
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .header {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-content {
            flex: 1;
        }
        
        h1 {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #6c757d;
            font-size: 14px;
            font-weight: 400;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-gray);
        }
        
        input[type="number"],
        select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: border 0.3s;
        }
        
        input[type="number"]:focus,
        select:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-block;
            text-decoration: none;
            text-align: center;
        }
        
        .btn:hover {
            background-color: #2980b9;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background-color: var(--light-gray);
            color: var(--dark-gray);
        }
        
        .btn-secondary:hover {
            background-color: #e2e6ea;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .alert {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-top: 20px;
            font-size: 15px;
            text-align: center;
        }
        
        .alert.success {
            background-color: rgba(39, 174, 96, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
        }
        
        .alert.error {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 4px solid var(--error-color);
            color: var(--error-color);
        }
        
        .code-display {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <h1>Gerar Código de Acesso</h1>
                <p class="subtitle">Gere códigos de acesso temporários para coordenadores</p>
            </div>
            <a href="index.php" class="btn btn-secondary">Voltar</a>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="days">Dias de validade:</label>
                <input type="number" name="days" value="30" min="1" required>
            </div>
            
            <div class="form-group">
                <label for="area">Área de atuação:</label>
                <select name="area" id="area" required>
                    <option value="">Selecione uma área</option>
                    <option value="Iº Ciclo">Iº Ciclo</option>
                    <option value="IIº Ciclo">IIº Ciclo</option>
                    <option value="Cursos Puniv">Cursos Puniv</option>
                    <option value="Cursos Técnicos">Cursos Técnicos</option>
                </select>
            </div>
            
            <div class="button-group">
                <input type="hidden" name="generate_code" value="1">
                <button type="submit" class="btn">Gerar Código</button>
            </div>
        </form>
        
        <?php if (!empty($mensagemCodigo)): ?>
            <div style="display: flex; justify-content: center;">
                <?= $mensagemCodigo ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>