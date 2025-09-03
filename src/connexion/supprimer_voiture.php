<?php
require_once 'log.php';
require_once 'session_prive.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    try{

        $id_voiture = $_POST['id_voiture'];


        //PREPARATION POUR SUPPRIMER VOITURE
        $prep_voiture_supp = $pdo->prepare(
            "DELETE FROM voiture
            WHERE id_utilisateur = ?
            AND id_voiture = ?"
        );
        $prep_voiture_supp->execute([$id_utilisateur,$id_voiture]);
        
        header("Location: ../mon_compte.php");
        exit;
        
    } catch (PDOException $e) {
        die ("Erreur connexion BDD : ".$e->getMessage());
    }
}
?>