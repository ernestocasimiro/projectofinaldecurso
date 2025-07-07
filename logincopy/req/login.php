<?php 
session_start();

if (isset($_POST['uname']) && 
    isset($_POST['pass']) && 
    isset($_POST['role'])) { 

    include "../DB_connection.php";

    $uname = trim($_POST['uname']);
    $pass = $_POST['pass'];
    $role = $_POST['role'];

    // Validações
    if (empty($uname)) {
        $em = "Nome de utilizador é obrigatório!";
        header("Location: ../login.php?error=$em");
        exit;
    } else if (empty($pass)) {
        $em = "Palavra-passe é obrigatória!";
        header("Location: ../login.php?error=$em");
        exit;
    } else if (empty($role)) {
        $em = "Selecione uma opção!";
        header("Location: ../login.php?error=$em");
        exit;
    } else {
        // Define a SQL com base no papel selecionado
        if ($role == '1') {
            $sql = "SELECT * FROM encarregados WHERE username = ?";
            $role_label = "Guardian";
        } else {
            $em = "Tipo de utilizador inválido!";
            header("Location: ../login.php?error=$em");
            exit;
        }
        
        try {
            // Prepara e executa a consulta SQL
            $stmt = $conn->prepare($sql);
            $stmt->execute([$uname]);

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verifica a senha
                if (password_verify($pass, $user['password'])) {
                    // Inicia a sessão e armazena os dados do usuário
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['fname'] = $user['fname'];
                    $_SESSION['role'] = $role_label;
                    $_SESSION['username'] = $user['username'];

                    // Redireciona para a página específica
                    if ($role_label == "Guardian") {
                        header("Location: /dashboardpitruca/telaencarregado/index.php");
                        exit;
                    }
                } else {
                    $em = "Nome de Utilizador ou Palavra-Passe Incorretos!";
                    header("Location: ../login.php?error=$em");
                    exit;
                }
            } else {
                $em = "Nome de Utilizador ou Palavra-Passe Incorretos!";
                header("Location: ../login.php?error=$em");
                exit;
            }
        } catch (PDOException $e) {
            $em = "Erro no sistema. Por favor, tente novamente mais tarde.";
            header("Location: ../login.php?error=$em");
            exit;
        }
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>