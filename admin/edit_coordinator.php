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
    $area_coordenacao = $_POST['area_coordenacao'];
    $anos_experiencia = $_POST['anos_experiencia'];
    $nivel_academico = $_POST['nivel_academico'];

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

    // Função upload reutilizável
    function uploadFile($file, $targetDir = "uploads/teacher/", $allowedTypes = ['jpg', 'jpeg', 'png']) {
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
        $stmtCurrent = $conn->prepare("SELECT foto_bi1, foto_bi2, fotoperfil FROM coordenadores WHERE id = ?");
        $stmtCurrent->execute([$id]);
        $current = $stmtCurrent->fetch(PDO::FETCH_ASSOC);

        if (!$current) {
            die("coordenador não encontrado.");
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
            $sql = "UPDATE coordenadores SET
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
                area_coordenacao = :area_coordenacao,
                anos_experiencia = :anos_experiencia,
                nivel_academico = :nivel_academico
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
            $stmt->bindParam(':area_coordenacao', $area_coordenacao);
            $stmt->bindParam(':anos_experiencia', $anos_experiencia);
            $stmt->bindParam(':nivel_academico', $nivel_academico);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $_SESSION['success'] = "Coordenador atualizado com sucesso!";
            header("Location: coordinator.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Erro ao atualizar coordenador: " . $e->getMessage();
        }
    }
} else {
    // Carregar dados para preencher formulário na primeira carga
    try {
        $stmt = $conn->prepare("SELECT * FROM coordenadores WHERE id = ?");
        $stmt->execute([$id]);
        $g = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$g) {
            die("coordenador não encontrado.");
        }
    } catch (PDOException $e) {
        die("Erro ao buscar coordenador: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Editar coordenador - <?= htmlspecialchars($g['fname'] . ' ' . $g['lname']); ?></title>
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
    <h1>Editar coordenador</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="edit_coordinator.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
        <label for="fname">Nome</label>
        <input type="text" id="fname" name="fname" required value="<?= isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : htmlspecialchars($g['fname']); ?>" />

        <label for="lname">Sobrenome</label>
        <input type="text" id="lname" name="lname" required value="<?= isset($_POST['lname']) ? htmlspecialchars($_POST['lname']) : htmlspecialchars($g['lname']); ?>" />

        <label for="genero">Gênero</label>
        <select id="genero" name="genero" required>
            <?php
            $generos = ['Masculino', 'Feminino', 'Outro'];
            $selectedGenero = $_POST['genero'] ?? $g['genero'];
            foreach ($generos as $gen) {
                $sel = ($gen == $selectedGenero) ? 'selected' : '';
                echo "<option value=\"$gen\" $sel>$gen</option>";
            }
            ?>
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

       <label for="area_coordenacao">Área de Coordenação</label>
        <select id="area_coordenacao" name="area_coordenacao" required>
            <?php
            $area_coordenacao = ['Pedagógica', 'Administrativa', 'Disciplinar', 'Técnica', 'Outro'];
            $selPar = $_POST['area_coordenacao'] ?? $g['area_coordenacao'];
            foreach ($area_coordenacao as $par) {
                $sel = ($par == $selPar) ? 'selected' : '';
                echo "<option value=\"$par\" $sel>$par</option>";
            }
            ?>
        </select>

       <label for="anos_experiencia">Anos de Experiência</label>
            <input 
                type="text" 
                id="anos_experiencia" 
                name="anos_experiencia" 
                value="<?= htmlspecialchars($_POST['anos_experiencia'] ?? $g['anos_experiencia'] ?? '') ?>" 
                required
                placeholder="Digite os anos de experiência coordenador"
            />


        <label for="nivel_academico">Nível Acadêmico</label>
        <select id="nivel_academico" name="nivel_academico" required>
            <?php
            $nivel_academicos = ['Bacharelato', 'Licenciatura', 'Mestrado', 'Doutorado', 'Outro'];
            $selPar = $_POST['nivel_academico'] ?? $g['nivel_academico'];
            foreach ($nivel_academicos as $par) {
                $sel = ($par == $selPar) ? 'selected' : '';
                echo "<option value=\"$par\" $sel>$par</option>";
            }
            ?>
        </select>

        <div class="btn-group">
            <button type="submit">Atualizar</button>
            <a href="coordinator.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
