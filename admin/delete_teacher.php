<?php
session_start();
require 'dbconnection.php';

if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'admin'; // Para testes
}


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID inválido.";
    header("Location: teacher.php");
    exit;
}

$id = $_GET['id'];

// Verifica se o professor existe
$checkStmt = $conn->prepare("SELECT id FROM professores WHERE id = ?");
$checkStmt->execute([$id]);

if ($checkStmt->rowCount() === 0) {
    $_SESSION['error'] = "professor não encontrado.";
    header("Location: teacher.php");
    exit;
}

// Deleta o professor
$deleteStmt = $conn->prepare("DELETE FROM professores WHERE id = ?");
try {
    $deleteStmt->execute([$id]);
    $_SESSION['success'] = "Professor deletado com sucesso.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Erro ao deletar: " . $e->getMessage();
}

header("Location: teacher.php");
exit;
?>
