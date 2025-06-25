<?php
session_start();
require_once('../dbconnection.php');

if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $conn->prepare("
            SELECT id, estudante_id, mes, ano, valor 
            FROM mensalidades 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $mensalidade = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($mensalidade) {
            echo json_encode(['success' => true, ...$mensalidade]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Mensalidade não encontrada']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
}
?>