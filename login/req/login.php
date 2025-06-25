<?php 
session_start();

if (isset($_POST['uname']) && 
    isset($_POST['pass']) && 
    isset($_POST['role'])) { 

    include "../DB_connection.php";

    $uname = $_POST['uname'];
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
            $sql = "SELECT * FROM admin WHERE username = ?";
            $role_label = "Admin";
        } else if ($role == '2') {
            $sql = "SELECT * FROM professores WHERE username = ?";
            $role_label = "Teacher";
        } else if ($role == '3') {
            $sql = "SELECT * FROM operadores_financeiros WHERE username = ?";
            $role_label = "Financial";
        } else if ($role == '4') {
            $sql = "SELECT * FROM coordenadores WHERE username = ?";
            $role_label = "Coordinator";
        } else {
            $em = "Inválido!";
            header("Location: ../login.php?error=$em");
            exit;
        }
        
        // Prepara a consulta SQL
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            $username = $user['username'];
            $password_hash = $user['password']; // Usa o campo password corretamente
            $fname = $user['fname'];
            $id = $user['id'];

            // Verifica se o nome de usuário corresponde e se a senha está correta
            if ($username === $uname) {
                if (password_verify($pass, $password_hash)) {
                    // Inicia a sessão e armazena os dados do usuário
                    $_SESSION['id'] = $id;
                    $_SESSION['fname'] = $fname;
                    $_SESSION['role'] = $role_label;

                    // Redireciona para a página específica com base no papel
                    if ($role_label == "Admin") {
                        header("Location: /dashboardpitruca/admin/index.php");
                    } else if ($role_label == "Teacher") {
                        header("Location: /dashboardpitruca/telaprofessor/index.php");
                    } else if ($role_label == "Financial") {
                        header("Location: /dashboardpitruca/telafinancas/index.php");
                    } else if ($role_label == "Coordinator") {
                        header("Location: /dashboardpitruca/telacoordenador/index.php");
                    }
                    exit;
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
        } else {
            $em = "Nome de Utilizador ou Palavra-Passe Incorretos!";
            header("Location: ../login.php?error=$em");
            exit;
        }
    }

} else {
    header("Location: ../login.php");
    exit;
}
?>
