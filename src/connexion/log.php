<?php 
//fichier de creation de pdo pour connexion securise a la bdd

$host = "localhost";
$db_name = "bdd_eco-ride";
$username = "root";
$password = "";
$dsn = "mysql:host=$host;dbname=$db_name;charset=utf8";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];

try {
    $pdo = new PDO ($dsn,$username,$password,$options);
} catch (PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}

?>