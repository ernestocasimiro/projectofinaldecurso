<?php
session_start();
include('dbconnection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$id = (int)$_GET['id'];

// Processar submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitização
    $fname = htmlspecialchars(trim($_POST['fname']));
    $lname = htmlspecialchars(trim($_POST['lname']));
    $genero = $_POST['genero'];
    $dataa = $_POST['dataa'];
    $numbi = $_POST['numbi'];
    $endereco = htmlspecialchars(trim($_POST['endereco']));
    $telefone = $_POST['telefone'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $profissao = $_POST['profissao'];
    $parentesco = $_POST['parentesco'];

    // Validações
    $errors = [];

    if (!preg_match('/^\d{7}[A-Za-z]{2}\d{3}$/', $numbi)) {
        $errors[] = "Formato do BI inválido. Use o formato: 0000000LA000";
    }

    if (!preg_match('/^\d{9}$/', $telefone)) {
        $errors[] = "Número de telefone inválido. Use 9 dígitos.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido.";
    }

    $username = htmlspecialchars(trim($_POST['username']));

if (strlen($username) < 4 || strlen($username) > 20) {
    $errors[] = "O nome de usuário deve ter entre 4 e 20 caracteres.";
}


    // Função upload reutilizável
    function uploadFile($file, $targetDir = "uploads/guardians/", $allowedTypes = ['jpg', 'jpeg', 'png']) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null; // sem upload novo
        }

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedTypes)) {
            return false;
        }

        $uniqueName = uniqid('file_', true) . '.' . $fileExtension;
        $destination = $targetDir . $uniqueName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $destination;
        }

        return false;
    }

    if (empty($errors)) {
        // Buscar dados atuais para preservar imagens se não enviar novas
        $stmtCurrent = $conn->prepare("SELECT foto_bi1, foto_bi2, fotoperfil FROM encarregados WHERE id = ?");
        $stmtCurrent->execute([$id]);
        $current = $stmtCurrent->fetch(PDO::FETCH_ASSOC);

        if (!$current) {
            die("Encarregado não encontrado.");
        }

        // Upload das imagens, se novas imagens forem enviadas
        $foto_bi1 = uploadFile($_FILES['foto_bi1']);
        $foto_bi2 = uploadFile($_FILES['foto_bi2']);
        $fotoperfil = uploadFile($_FILES['fotoperfil']);

        // Se upload falhou (retornou false) para algum arquivo, dar erro
        if ($foto_bi1 === false || $foto_bi2 === false || $fotoperfil === false) {
            $errors[] = "Erro no upload dos arquivos. Só são aceitos JPG, JPEG e PNG.";
        } else {
            // Caso não tenha upload, manter imagem atual
            $foto_bi1 = $foto_bi1 ?? $current['foto_bi1'];
            $foto_bi2 = $foto_bi2 ?? $current['foto_bi2'];
            $fotoperfil = $fotoperfil ?? $current['fotoperfil'];
        }
    }

    // Se não houver erros, atualizar banco
    if (empty($errors)) {
        try {
            $sql = "UPDATE encarregados SET
                fname = :fname,
                lname = :lname,
                genero = :genero,
                data_nascimento = :dataa,
                num_bi = :numbi,
                foto_bi1 = :foto_bi1,
                foto_bi2 = :foto_bi2,
                endereco = :endereco,
                fotoperfil = :fotoperfil,
                telefone = :telefone,
                email = :email,
                profissao = :profissao,
                parentesco = :parentesco,
                username = :username
                WHERE id = :id";


            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':fname', $fname);
            $stmt->bindParam(':lname', $lname);
            $stmt->bindParam(':genero', $genero);
            $stmt->bindParam(':dataa', $dataa);
            $stmt->bindParam(':numbi', $numbi);
            $stmt->bindParam(':foto_bi1', $foto_bi1);
            $stmt->bindParam(':foto_bi2', $foto_bi2);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':fotoperfil', $fotoperfil);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':profissao', $profissao);
            $stmt->bindParam(':parentesco', $parentesco);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $_SESSION['success'] = "Encarregado atualizado com sucesso!";
            header("Location: guardians.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Erro ao atualizar encarregado: " . $e->getMessage();
        }
    }
} else {
    // Carregar dados para preencher formulário na primeira carga
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
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Editar Encarregado - <?= htmlspecialchars($g['fname'] . ' ' . $g['lname']); ?></title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f8fa;
        color: #333;
        margin: 0;
        padding: 20px;
    }
    .container {
        max-width: 700px;
        background: white;
        margin: 40px auto;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 0 25px rgba(0,0,0,0.1);
    }
    h1 {
        margin-bottom: 20px;
        color: #2c3e50;
        font-weight: 700;
        text-align: center;
    }
    form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
        color: #34495e;
    }
    input[type="text"],
    input[type="date"],
    input[type="email"],
    select {
        width: 100%;
        padding: 10px 12px;
        border-radius: 6px;
        border: 1.5px solid #ccc;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }
    input[type="text"]:focus,
    input[type="date"]:focus,
    input[type="email"]:focus,
    select:focus {
        border-color: #4a90e2;
        outline: none;
    }
    .file-input {
        font-size: 0.9rem;
    }
    .current-img {
        margin-top: 6px;
        max-width: 150px;
        max-height: 110px;
        border-radius: 8px;
        border: 1px solid #ddd;
        box-shadow: 0 0 8px rgba(0,0,0,0.05);
    }
    .img-label {
        margin-top: 4px;
        font-size: 0.85rem;
        color: #555;
    }
    .btn-group {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    button[type="submit"],
    .btn-cancel {
        padding: 12px 28px;
        font-size: 1rem;
        font-weight: 700;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
        user-select: none;
    }
    button[type="submit"] {
        background-color: #4a90e2;
        color: white;
    }
    button[type="submit"]:hover {
        background-color: #357ABD;
    }
    .btn-cancel {
        background-color: #aaa;
        color: white;
        text-decoration: none;
        text-align: center;
        line-height: 38px;
    }
    .btn-cancel:hover {
        background-color: #888;
    }
    .errors {
        background: #ffe5e5;
        border: 1px solid #ff7b7b;
        color: #a70000;
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 6px;
    }
</style>
</head>
<body>
<div class="container">
    <h1>Editar Encarregado</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="edit_guardian.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
        <label for="fname">Nome</label>
        <input type="text" id="fname" name="fname" required value="<?= isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : htmlspecialchars($g['fname']); ?>" />

        <label for="lname">Sobrenome</label>
        <input type="text" id="lname" name="lname" required value="<?= isset($_POST['lname']) ? htmlspecialchars($_POST['lname']) : htmlspecialchars($g['lname']); ?>" />

        <label for="username">Nome de Usuário</label>
        <input type="text" id="username" name="username" required minlength="4" maxlength="20" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : htmlspecialchars($g['username']); ?>" />

       <label for="genero">Gênero</label>
        <select id="genero" name="genero" required>
            <option value="" disabled <?= empty($_POST) && empty($g['genero']) ? 'selected' : '' ?>>Selecione o gênero</option>
            <option value="Masculino" <?= (($_POST['genero'] ?? $g['genero']) === 'Masculino') ? 'selected' : '' ?>>Masculino</option>
            <option value="Feminino" <?= (($_POST['genero'] ?? $g['genero']) === 'Feminino') ? 'selected' : '' ?>>Feminino</option>
            <option value="Outro" <?= (($_POST['genero'] ?? $g['genero']) === 'Outro') ? 'selected' : '' ?>>Outro</option>
        </select>


        <label for="dataa">Data de Nascimento</label>
        <input type="date" id="dataa" name="dataa" required value="<?= isset($_POST['dataa']) ? htmlspecialchars($_POST['dataa']) : htmlspecialchars($g['data_nascimento']); ?>" />

        <label for="numbi">Número BI</label>
        <input type="text" id="numbi" name="numbi" maxlength="12" required placeholder="Ex: 0000000LA000" value="<?= isset($_POST['numbi']) ? htmlspecialchars($_POST['numbi']) : htmlspecialchars($g['num_bi']); ?>" />

        <label for="foto_bi1">Foto BI Frente (jpg, png)</label>
        <input class="file-input" type="file" id="foto_bi1" name="foto_bi1" accept=".jpg,.jpeg,.png" />
        <?php if (!empty($g['foto_bi1'])): ?>
            <img class="current-img" src="<?= htmlspecialchars($g['foto_bi1']) ?>" alt="Foto BI Frente" />
        <?php endif; ?>

        <label for="foto_bi2">Foto BI Verso (jpg, png)</label>
        <input class="file-input" type="file" id="foto_bi2" name="foto_bi2" accept=".jpg,.jpeg,.png" />
        <?php if (!empty($g['foto_bi2'])): ?>
            <img class="current-img" src="<?= htmlspecialchars($g['foto_bi2']) ?>" alt="Foto BI Verso" />
        <?php endif; ?>

        <label for="endereco">Endereço</label>
        <input type="text" id="endereco" name="endereco" required value="<?= isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco']) : htmlspecialchars($g['endereco']); ?>" />

        <label for="fotoperfil">Foto Perfil (jpg, png)</label>
        <input class="file-input" type="file" id="fotoperfil" name="fotoperfil" accept=".jpg,.jpeg,.png" />
        <?php if (!empty($g['fotoperfil'])): ?>
            <img class="current-img" src="<?= htmlspecialchars($g['fotoperfil']) ?>" alt="Foto Perfil" />
        <?php endif; ?>

        <label for="telefone">Telefone (9 dígitos)</label>
        <input type="text" id="telefone" name="telefone" maxlength="9" required placeholder="Ex: 912345678" value="<?= isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : htmlspecialchars($g['telefone']); ?>" />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($g['email']); ?>" />

       <label for="profissao">Profissão</label>
            <input 
                type="text" 
                id="profissao" 
                name="profissao" 
                value="<?= htmlspecialchars($_POST['profissao'] ?? $g['profissao'] ?? '') ?>" 
                required
                placeholder="Digite a profissão do encarregado"
            />


        <label for="parentesco">Parentesco</label>
        <select id="parentesco" name="parentesco" required>
            <?php
            $parentescos = ['Pai', 'Mãe', 'Tio(a)', 'Avô(ó)', 'Outro'];
            $selPar = $_POST['parentesco'] ?? $g['parentesco'];
            foreach ($parentescos as $par) {
                $sel = ($par == $selPar) ? 'selected' : '';
                echo "<option value=\"$par\" $sel>$par</option>";
            }
            ?>
        </select>

        <div class="btn-group">
            <button type="submit">Atualizar</button>
            <a href="guardians.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
