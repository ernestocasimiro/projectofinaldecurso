<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

session_unset(); // Remove todas as variáveis da sessão
session_destroy(); // Destroi a sessão

header("Location: /dashboardpitruca/login/login.php");
exit;
?>
