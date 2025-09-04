<?php
require_once 'log.php';
require_once 'session_prive.php';
require_once '../function_php/convertir_ville.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    try{

        $date_depart = $_POST['date_depart'];
        $h_depart = (int)$_POST['heure_depart'];
        $min_depart = (int)$_POST['min_depart'];
        $duree_voyage_heure = (int)$_POST['duree_voyage_heure'];
        $duree_voyage_min = (int)$_POST['duree_voyage_min'];
        $lieu_depart = formaterVille($_POST['lieu_depart']);
        $lieu_arrive = formaterVille($_POST['lieu_arrive']);
        $prix_personne = $_POST['prix_personne'];
        $voiture_choisie = $_POST['id_voiture'];
        $heure_depart = sprintf('%02d:%02d:00',$h_depart,$min_depart);
        $duree_voyage = sprintf('%02d:%02d:00', $duree_voyage_heure, $duree_voyage_min);


        //PREPARATION POUR NB_PLACE DE LA VOITURE SELECTIONNEE
        $prep_nb_place = $pdo->prepare(
            "SELECT nb_place
            FROM voiture
            WHERE id_conducteur = ?
            AND id_voiture = ?"
        );
        $prep_nb_place->execute([$id_utilisateur,$voiture_choisie]);
        $nb_place = $prep_nb_place->fetch();

        //PREPARATION POUR AJOUTER COVOITURAGE
        $prep_covoiturage = $pdo->prepare(
            "INSERT INTO covoiturage 
            (date_depart,
            heure_depart,
            duree_voyage,
            lieu_depart,
            lieu_arrive,
            nb_place_dispo,
            prix_personne,
            id_conducteur,
            id_voiture)
            VALUES (?,?,?,?,?,?,?,?,?)
            ");
            
        $prep_covoiturage->execute(
            [$date_depart,
            $heure_depart,
            $duree_voyage,
            $lieu_depart,
            $lieu_arrive,
            $nb_place['nb_place'],
            $prix_personne,
            $id_utilisateur,
            $voiture_choisie]
        );

        header("Location: ../mon_compte.php");
        exit;

    } catch (PDOException $e) {
        die ("Erreur connexion BDD : ".$e->getMessage());
    }
}
?>