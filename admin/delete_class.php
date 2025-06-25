<?php
session_start();
require 'dbconnection.php';

if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'admin'; // Para testes
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID inválido.";
    header("Location: classes.php");
    exit;
}

$id = $_GET['id'];

// Verifica se a turma existe
$checkStmt = $conn->prepare("SELECT id FROM turma WHERE id = ?");
$checkStmt->execute([$id]);

if ($checkStmt->rowCount() === 0) {
    $_SESSION['error'] = "Turma não encontrada.";
    header("Location: classes.php");
    exit;
}

// Deleta a turma
$deleteStmt = $conn->prepare("DELETE FROM turma WHERE id = ?");
try {
    $deleteStmt->execute([$id]);
    $_SESSION['success'] = "Turma deletada com sucesso.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Erro ao deletar: " . $e->getMessage();
}

header("Location: classes.php");
exit;
?>
