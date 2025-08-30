<?php
require_once 'session.php';

//verif de la session active
if(!isset($_SESSION['user_id'])){
    header('Location: connexion.php');
    exit;
}
?>