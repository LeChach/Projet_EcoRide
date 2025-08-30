<?php

if (session_status() === PHP_SESSION_NONE){
    session_start();
}

//supprimer toute les variable de session principalement id
$_SESSION = [];

//supprime la session en local : cookie
//verifie si cookie comporte l'id de session
if (ini_get("session.use_cookies")) {
    //recupere le tableau des infos cookie
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

//supprime la session cote serveur 
session_destroy();

//redirige
header('Location: ../connexion.php');
exit;
?>
