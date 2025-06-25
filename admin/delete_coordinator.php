<?php
session_start();
require 'dbconnection.php';

if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'admin'; // Para testes
}


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID inválido.";
    header("Location: coordinator.php");
    exit;
}

$id = $_GET['id'];

// Verifica se o coordenador existe
$checkStmt = $conn->prepare("SELECT id FROM coordenadores WHERE id = ?");
$checkStmt->execute([$id]);

if ($checkStmt->rowCount() === 0) {
    $_SESSION['error'] = "coordenador não encontrado.";
    header("Location: coordinator.php");
    exit;
}

// Deleta o coordenador
$deleteStmt = $conn->prepare("DELETE FROM coordenadores WHERE id = ?");
try {
    $deleteStmt->execute([$id]);
    $_SESSION['success'] = "Coordenador deletado com sucesso.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Erro ao deletar: " . $e->getMessage();
}

header("Location: coordinator.php");
exit;
?>
