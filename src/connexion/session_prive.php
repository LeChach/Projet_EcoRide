<?php
require_once 'session.php';

//verif de la session active
if(!isset($id_utilisateur)){
    header('Location: connexion.php');
    exit;
}
?>