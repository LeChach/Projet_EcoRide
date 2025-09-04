<?php
require_once 'log.php';
require_once 'session_prive.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    try{

        $id_covoiturage = $_POST['id_covoiturage'];


        //PREPARATION POUR SUPPRIMER VOITURE
        $prep_covoit_supp = $pdo->prepare(
            "DELETE FROM covoiturage
            WHERE id_conducteur = ?
            AND id_covoiturage = ?"
        );
        $prep_covoit_supp->execute([$id_utilisateur,$id_covoiturage]);
        
        header("Location: ../mon_compte.php");
        exit;
        
    } catch (PDOException $e) {
        die ("Erreur connexion BDD : ".$e->getMessage());
    }
}
?>