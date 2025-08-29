<?php

//verif de la session active
if(!isset($_SESSION['user_id'])){
    header('Location: mon_compte.php');
    exit;
}

//permet de recup l'id de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

//gère les erreurs
//de log
$erreur_log = $_SESSION['erreur_login'] ?? null;
unset($_SESSION['erreur_login']);

//d'inscription
$erreur_inscription = $_SESSION['erreur_inscription'] ?? null;
unset($_SESSION['erreur_inscription']);
?>