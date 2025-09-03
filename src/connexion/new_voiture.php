<?php
require_once 'log.php';
require_once 'session_prive.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    try{

        $marque = $_POST['marque'];
        $modele = $_POST['modele'];
        $immat = $_POST['immat'];
        $date_premiere_immat = $_POST['date_premiere_immat'];
        $energie = $_POST['energie'];
        $couleur = $_POST['couleur'];
        $nb_place = $_POST['nb_place'];

        //PREPARATION POUR AJOUTER VOITURE
        $prep_voiture = $pdo->prepare(
            "INSERT INTO voiture (id_utilisateur,marque,modele,immat,date_premiere_immat,energie,couleur,nb_place)
            VALUES (?,?,?,?,?,?,?,?)"
        );
        $prep_voiture->execute([$id_utilisateur,$marque,$modele,$immat,$date_premiere_immat,$energie,$couleur,$nb_place]);
        
        header("Location: ../mon_compte.php");
        exit;
        
    } catch (PDOException $e) {
        die ("Erreur connexion BDD : ".$e->getMessage());
    }
}
?>