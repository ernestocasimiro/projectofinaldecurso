<?php
// admin/save_guardian.php
include 'includes/db.php';

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados do formulário
    $name       = $_POST['name'] ?? '';
    $gender     = $_POST['gender'] ?? '';
    $dob        = $_POST['dob'] ?? '';
    $bi_number  = $_POST['bi'] ?? '';
    $address    = $_POST['address'] ?? '';
    $phone      = $_POST['phone'] ?? '';
    $email      = $_POST['email'] ?? '';
    $password   = $_POST['password'] ?? '';

    // Validações básicas (opcional: você pode expandir)
    if (empty($name) || empty($gender) || empty($dob) || empty($bi_number) || empty($address) || empty($phone) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Preencha todos os campos obrigatórios.']);
        exit;
    }

    // Hash da palavra-passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepara a query
    $stmt = $conn->prepare("INSERT INTO guardians (nome, genero, data_nascimento, bi, endereco, telefone, email, senha) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $gender, $dob, $bi_number, $address, $phone, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Encarregado cadastrado com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar os dados: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido.']);
}
?>
