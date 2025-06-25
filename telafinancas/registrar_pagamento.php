<?php
session_start();
require_once('../dbconnection.php');

if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    try {
        // Verificar se a mensalidade já existe
        $stmt = $conn->prepare("
            SELECT id FROM mensalidades 
            WHERE estudante_id = :estudante_id 
            AND mes = :mes 
            AND ano = :ano
        ");
        $stmt->bindParam(':estudante_id', $data['estudante_id']);
        $stmt->bindParam(':mes', $data['mes']);
        $stmt->bindParam(':ano', $data['ano']);
        $stmt->execute();
        
        $mensalidade = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($mensalidade) {
            // Atualizar mensalidade existente
            $stmt = $conn->prepare("
                UPDATE mensalidades 
                SET valor = :valor,
                    data_pagamento = :data_pagamento,
                    pago = 1,
                    metodo_pagamento = :metodo_pagamento,
                    referencia = :referencia,
                    observacoes = :observacoes
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $mensalidade['id']);
        } else {
            // Criar nova mensalidade
            $stmt = $conn->prepare("
                INSERT INTO mensalidades (
                    estudante_id, mes, ano, valor, 
                    data_pagamento, pago, metodo_pagamento, 
                    referencia, observacoes, data_vencimento
                ) VALUES (
                    :estudante_id, :mes, :ano, :valor,
                    :data_pagamento, 1, :metodo_pagamento,
                    :referencia, :observacoes, :data_vencimento
                )
            ");
            
            // Definir data de vencimento como último dia do mês
            $data_vencimento = date('Y-m-t', strtotime($data['ano'].'-'.$data['mes'].'-01'));
            $stmt->bindParam(':data_vencimento', $data_vencimento);
        }
        
        $stmt->bindParam(':estudante_id', $data['estudante_id']);
        $stmt->bindParam(':mes', $data['mes']);
        $stmt->bindParam(':ano', $data['ano']);
        $stmt->bindParam(':valor', $data['valor']);
        $stmt->bindParam(':data_pagamento', $data['data_pagamento']);
        $stmt->bindParam(':metodo_pagamento', $data['metodo_pagamento']);
        $stmt->bindParam(':referencia', $data['referencia']);
        $stmt->bindParam(':observacoes', $data['observacoes']);
        
        $stmt->execute();
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dados não recebidos']);
}
?>