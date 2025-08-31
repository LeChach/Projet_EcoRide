<?php

if (session_status() === PHP_SESSION_NONE){
    session_start();
}

//supprimer toute les variable de session principalement id
$_SESSION = [];

//supprime la session cote serveur 
session_destroy();

//redirige
header('Location: ../connexion.php');
exit;
?>
