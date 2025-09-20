<?php

$database_url = getenv('DATABASE_URL');

if ($database_url) {
    $url = parse_url($database_url);
    $host = $url['host'];
    $username = $url['user'];
    $password = $url['pass'];
    $database = ltrim($url['path'], '/');
    $port = $url['port'] ?? 3306;
    
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
    
} else {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'bdd_eco-ride';
    $port = 3306;
    
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

$dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    if ($database_url) {
        error_log("Erreur BDD: " . $e->getMessage());
        die("Service temporairement indisponible.");
    } else {
        die("Erreur connexion BDD : " . $e->getMessage());
    }
}
?>