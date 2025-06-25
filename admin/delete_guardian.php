<?php
session_start();
require 'dbconnection.php';

if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'admin'; // Para testes
}


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID inválido.";
    header("Location: guardians.php");
    exit;
}

$id = $_GET['id'];

// Verifica se o encarregado existe
$checkStmt = $conn->prepare("SELECT id FROM encarregados WHERE id = ?");
$checkStmt->execute([$id]);

if ($checkStmt->rowCount() === 0) {
    $_SESSION['error'] = "Encarregado não encontrado.";
    header("Location: guardians.php");
    exit;
}

// Deleta o encarregado
$deleteStmt = $conn->prepare("DELETE FROM encarregados WHERE id = ?");
try {
    $deleteStmt->execute([$id]);
    $_SESSION['success'] = "Encarregado deletado com sucesso.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Erro ao deletar: " . $e->getMessage();
}

header("Location: guardians.php");
exit;
?>
