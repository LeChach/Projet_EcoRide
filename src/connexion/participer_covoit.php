<?php 
require_once 'log.php';
require_once 'session.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    if (!isset($id_utilisateur)) {
        // Stocker la page où l'utilisateur voulait aller
        $_SESSION['redirect_after_login'] = "confirmation_covoit.php?id_covoit=" . $_POST['id_covoiturage'];
        header('Location: connexion.php');
        exit;
    }


}catch (PDOException $e){
    die("Erreur de BBD : ".$e->getMessage());
}

?>