<?php 
session_start();
include('dbconnection.php');

// Exemplo: carregando lista de professores para o select de diretor de turma
try {
    $stmtProf = $conn->query("SELECT id, fname, lname FROM professores ORDER BY fname, lname");
    $professores = $stmtProf->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar professores: " . $e->getMessage());
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}
$id = (int)$_GET['id'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar inputs
    $nome_turma = trim($_POST['class_name'] ?? '');
    $ano_letivo = trim($_POST['class_year'] ?? '');
    $serie = trim($_POST['class_grade'] ?? '');
    $curso = trim($_POST['class_course'] ?? '');
    $capacidade = (int)($_POST['class_capacity'] ?? 0);
    $sala = trim($_POST['class_room'] ?? '');
    $diretor_id = (int)($_POST['class_director_id'] ?? 0);
    $periodo = trim($_POST['class_period'] ?? '');
    $descricao = trim($_POST['class_description'] ?? '');
    $observacoes = trim($_POST['class_observations'] ?? '');

    // Validações básicas
    if ($nome_turma === '') {
        $errors[] = "O nome da turma é obrigatório.";
    }
    if (!in_array($ano_letivo, ['2023', '2024'])) {
        $errors[] = "Ano letivo inválido.";
    }
    if (!in_array($serie, ['10', '11', '12', '13'])) {
        $errors[] = "Ano/Série inválido.";
    }
    if (!in_array($curso, ['informatica', 'contabilidade', 'Economica', 'Ciencia'])) {
        $errors[] = "Curso inválido.";
    }
    if ($capacidade < 1 || $capacidade > 30) {
        $errors[] = "Capacidade deve ser entre 1 e 30.";
    }
    if ($diretor_id <= 0) {
        $errors[] = "Diretor de turma é obrigatório.";
    }
    if (!in_array($periodo, ['morning', 'afternoon'])) {
        $errors[] = "Período inválido.";
    }

    if (empty($errors)) {
        // Atualizar turma
        $sql = "UPDATE turma SET
                class_name = :class_name,
                class_year = :class_year,
                class_grade = :class_grade,
                class_course = :class_course,
                class_capacity = :class_capacity,
                class_room = :class_room,
                class_director_id = :class_director_id,
                class_period = :class_period,
                class_description = :class_description,
                class_observations = :class_observations
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':class_name', $nome_turma);
        $stmt->bindParam(':class_year', $ano_letivo);
        $stmt->bindParam(':class_grade', $serie);
        $stmt->bindParam(':class_course', $curso);
        $stmt->bindParam(':class_capacity', $capacidade, PDO::PARAM_INT);
        $stmt->bindParam(':class_room', $sala);
        $stmt->bindParam(':class_director_id', $diretor_id, PDO::PARAM_INT);
        $stmt->bindParam(':class_period', $periodo);
        $stmt->bindParam(':class_description', $descricao);
        $stmt->bindParam(':class_observations', $observacoes);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Turma atualizada com sucesso!";
            header("Location: classes.php");
            exit;
        } else {
            $errors[] = "Erro ao atualizar turma.";
        }
    }
} else {
    // Carregar dados da turma para edição
    $stmt = $conn->prepare("SELECT * FROM turma WHERE id = ?");
    $stmt->execute([$id]);
    $turma = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$turma) {
        die("Turma não encontrada.");
    }
    // Preencher variáveis para formulário
    $nome_turma = $turma['class_name'];
    $ano_letivo = $turma['class_year'];
    $serie = $turma['class_grade'];
    $curso = $turma['class_course'];
    $capacidade = $turma['class_capacity'];
    $sala = $turma['class_room'] ?? '';
    $diretor_id = $turma['class_director_id'];
    $periodo = $turma['class_period'];
    $descricao = $turma['class_description'] ?? '';
    $observacoes = $turma['class_observations'] ?? '';
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Editar Turma - <?= htmlspecialchars($nome_turma) ?></title>
<style>
  /* Reset simples para margin/padding */
  * {
    box-sizing: border-box;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #eef2f7;
    margin: 0;
    padding: 20px;
    color: #333;
  }

  .container {
    max-width: 720px;
    margin: 0 auto;
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgb(0 0 0 / 0.08);
  }

  h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #2c3e50;
    font-weight: 700;
    font-size: 2rem;
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
  input[type="number"],
  select,
  textarea {
    width: 100%;
    padding: 12px 14px;
    font-size: 1rem;
    border: 1.8px solid #bdc3c7;
    border-radius: 8px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    font-family: inherit;
    resize: vertical;
  }

  input[type="text"]:focus,
  input[type="number"]:focus,
  select:focus,
  textarea:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 8px rgba(74, 144, 226, 0.4);
    outline: none;
  }

  textarea {
    min-height: 90px;
  }

  small {
    font-size: 0.85rem;
    color: #7f8c8d;
    margin-top: -12px;
    margin-bottom: 8px;
    display: block;
  }

  .errors {
    background-color: #ffe6e6;
    border: 1px solid #ff4d4d;
    padding: 15px 20px;
    border-radius: 8px;
    color: #cc0000;
    margin-bottom: 20px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgb(255 77 77 / 0.3);
  }

  .errors ul {
    margin: 0;
    padding-left: 20px;
  }

  .form-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 30px;
  }

  button {
    padding: 12px 28px;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.25s ease;
  }

  button.save {
    background-color: #4a90e2;
    color: #fff;
  }

  button.save:hover {
    background-color: #357abd;
  }

  button.cancel {
    background-color: #aaa;
    color: #fff;
  }

  button.cancel:hover {
    background-color: #888;
  }

  /* Responsividade simples */
  @media (max-width: 480px) {
    .container {
      padding: 20px 15px;
    }

    button {
      width: 100%;
      padding: 14px 0;
    }

    .form-buttons {
      flex-direction: column;
      gap: 10px;
    }
  }
</style>
</head>
<body>
<div class="container">
    <h1>Editar Turma</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="edit_class.php?id=<?= $id ?>" method="POST" autocomplete="off">
        <label for="class-name">Nome da Turma*</label>
        <input type="text" id="class-name" name="class_name" required
            value="<?= htmlspecialchars($_POST['class_name'] ?? $nome_turma) ?>">

        <label for="class-grade">Ano / Série*</label>
        <select id="class-grade" name="class_grade" required>
            <option value="">Selecione</option>
            <?php
            $series = ['10', '11', '12', '13'];
            foreach ($series as $s) {
                $selected = ((($_POST['class_grade'] ?? $serie) === $s) ? 'selected' : '');
                echo "<option value='$s' $selected>$sº Ano</option>";
            }
            ?>
        </select>

        <label for="class-year">Ano Letivo*</label>
        <select id="class-year" name="class_year" required>
            <option value="">Selecione</option>
            <?php
            $anos = ['2023', '2024'];
            foreach ($anos as $ano) {
                $selected = ((($_POST['class_year'] ?? $ano_letivo) === $ano) ? 'selected' : '');
                echo "<option value='$ano' $selected>$ano</option>";
            }
            ?>
        </select>

        <label for="class-course">Curso*</label>
        <select id="class-course" name="class_course" required>
            <option value="">Selecione</option>
            <?php
            $cursos = ['informatica' => 'Informática', 'contabilidade' => 'Contabilidade', 'Economica' => 'Economia', 'Ciencia' => 'Ciência'];
            $curso_atual = $_POST['class_course'] ?? $curso;
            foreach ($cursos as $key => $label) {
                $selected = ($curso_atual === $key) ? 'selected' : '';
                echo "<option value='$key' $selected>$label</option>";
            }
            ?>
        </select>

        <label for="class-capacity">Capacidade (1-30)*</label>
        <input type="number" id="class-capacity" name="class_capacity" min="1" max="30" required
            value="<?= htmlspecialchars($_POST['class_capacity'] ?? $capacidade) ?>">

        <label for="class-room">Sala</label>
        <input type="text" id="class-room" name="class_room"
            value="<?= htmlspecialchars($_POST['class_room'] ?? $sala) ?>">

        <label for="class-director">Diretor de Turma*</label>
        <select id="class-director" name="class_director_id" required>
            <option value="">Selecione</option>
            <?php
            $diretor_atual = (int)($_POST['class_director_id'] ?? $diretor_id);
            foreach ($professores as $prof) {
                $selected = ($diretor_atual === (int)$prof['id']) ? 'selected' : '';
                $nomeCompleto = htmlspecialchars($prof['fname'] . ' ' . $prof['lname']);
                echo "<option value='{$prof['id']}' $selected>$nomeCompleto</option>";
            }
            ?>
        </select>

        <label for="class-period">Período*</label>
        <select id="class-period" name="class_period" required>
            <option value="">Selecione</option>
            <?php
            $periodos = ['morning' => 'Manhã', 'afternoon' => 'Tarde'];
            $periodo_atual = $_POST['class_period'] ?? $periodo;
            foreach ($periodos as $key => $label) {
                $selected = ($periodo_atual === $key) ? 'selected' : '';
                echo "<option value='$key' $selected>$label</option>";
            }
            ?>
        </select>

        <label for="class-description">Descrição</label>
        <textarea id="class-description" name="class_description" rows="3"><?= htmlspecialchars($_POST['class_description'] ?? $descricao) ?></textarea>

        <label for="class-observations">Observações</label>
        <textarea id="class-observations" name="class_observations" rows="3"><?= htmlspecialchars($_POST['class_observations'] ?? $observacoes) ?></textarea>

        <div class="form-buttons">
            <a href="classes.php" class="cancel-link" style="text-decoration:none;">
              <button type="button" class="cancel">Cancelar</button>
            </a>
            <button type="submit" class="save">Salvar</button>
        </div>
    </form>
</div>
</body>
</html>
