<?php
require_once 'log.php';
require_once 'session.php';
require_once '../function_php/convertir_ville.php';


if($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $lieu_depart = formaterVille($_POST['lieu_depart']);
        $lieu_arrive = formaterVille($_POST['lieu_arrive']);
        $date_depart = $_POST['date_depart'];
        $nb_place = $_POST['nb_place'];

        //PREPARATION DE LA RECHERCHE DE COVOIT
        $prep_covoit = $pdo->prepare(
            "SELECT 
            u.id_utilisateur as u_id,
            u.pseudo as u_pseudo,
            u.photo as u_photo,
            u.note as u_note,
            v.id_voiture as v_id,
            v.energie as v_energie,
            c.id_covoiturage as c_id,
            c.date_depart as c_date_depart,
            c.heure_depart as c_heure_depart,
            c.duree_voyage as c_duree_voyage,
            c.lieu_depart as c_lieu_depart,
            c.lieu_arrive as c_lieu_arrive,
            c.nb_place_dispo as c_nb_place_dispo,
            c.prix_personne as c_prix_personne
            FROM covoiturage c
            INNER JOIN utilisateur u ON c.id_conducteur = u.id_utilisateur
            INNER JOIN voiture v ON c.id_voiture = v.id_voiture
            WHERE c.lieu_depart = ?
            AND c.lieu_arrive = ?
            AND c.date_depart > ?
            AND c.nb_place_dispo > ?"
        );
        $prep_covoit->execute([$lieu_depart,$lieu_arrive,$date_depart,$nb_place]);
        $recherche_covoit = $prep_covoit->fetchAll();

        //ON STOCK LES RESULTATS DANS LA SESSION POUR LA REDIRECTION
        $_SESSION['recherche_covoit'] = $recherche_covoit;
        header('Location: ../recherche.php');
        exit;
        

    } catch (PDOException $e) {
    die("Erreur de BBD : ".$e->getMessage());
    }
}

?>