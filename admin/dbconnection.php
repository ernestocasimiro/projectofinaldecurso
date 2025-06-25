<?php 

$sName="localhost";
$uNname="root";
$pass="";
$db_name="escolabd";

try{

    $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uNname, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
    exit;
}

