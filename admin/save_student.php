<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se a senha foi enviada
    if (empty($_POST['password'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'A senha é obrigatória.'
        ]);
        exit;
    }

    // Coletar dados do formulário
    $dados = [
        'nome_completo' => $_POST['student-name'] ?? '',
        'genero' => $_POST['student-gender'] ?? '',
        'data_nascimento' => $_POST['student-dob'] ?? '',
        'bi_numero' => $_POST['bi-number'] ?? '',
        'endereco' => $_POST['student-address'] ?? '',
        'turma' => $_POST['student-class'] ?? '',
        'escola_anterior' => $_POST['previous-school'] ?? '',
        'codigo_pais' => $_POST['country-code'] ?? '+244',
        'telefone' => $_POST['student-contact'] ?? '',
        'email' => $_POST['student-email'] ?? '',
        'encarregado_id' => $_POST['parents'] ?? null,
        'senha' => password_hash($_POST['password'], PASSWORD_DEFAULT)
    ];

    // Gerar código do aluno (exemplo: STU + timestamp)
    $dados['codigo_aluno'] = 'STU' . substr(time(), -6);

    try {
        // Diretório para uploads
        $uploadDir = 'uploads/bi/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Processar BI frente
        if (isset($_FILES['bi-front']) && $_FILES['bi-front']['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['bi-front']['name'], PATHINFO_EXTENSION);
            $nomeArquivo = 'bi_frente_' . $dados['codigo_aluno'] . '.' . $extensao;
            $caminhoCompleto = $uploadDir . $nomeArquivo;

            if (move_uploaded_file($_FILES['bi-front']['tmp_name'], $caminhoCompleto)) {
                $dados['bi_frente'] = $caminhoCompleto;
            }
        }

        // Processar BI verso
        if (isset($_FILES['bi-back']) && $_FILES['bi-back']['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['bi-back']['name'], PATHINFO_EXTENSION);
            $nomeArquivo = 'bi_verso_' . $dados['codigo_aluno'] . '.' . $extensao;
            $caminhoCompleto = $uploadDir . $nomeArquivo;

            if (move_uploaded_file($_FILES['bi-back']['tmp_name'], $caminhoCompleto)) {
                $dados['bi_verso'] = $caminhoCompleto;
            }
        }

        // Inserir no banco de dados
        $campos = implode(', ', array_keys($dados));
        $valores = ':' . implode(', :', array_keys($dados));

        $sql = "INSERT INTO alunos ($campos) VALUES ($valores)";
        $stmt = $pdo->prepare($sql);

        foreach ($dados as $campo => $valor) {
            $stmt->bindValue(':' . $campo, $valor);
        }

        $stmt->execute();

        echo json_encode([
            'status' => 'success',
            'message' => 'Aluno cadastrado com sucesso!',
            'codigo_aluno' => $dados['codigo_aluno']
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao cadastrar aluno: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Método inválido. Use POST.'
    ]);
}
?>
