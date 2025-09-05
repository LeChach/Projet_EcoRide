<?php
require_once 'connexion/log.php';
require_once 'connexion/session.php';

try{
    if(!isset($_GET['id'])){
        die("covoit introuvable");
    }else{

        $id_covoit = (int)$_GET['id'];

        //PREPARATION POUR RECUPERER LID UTILISATEUR
        $prep_id_utilisateur = $pdo->prepare(
            "SELECT id_conducteur
            FROM covoiturage
            WHERE id_covoiturage = ?"
        );
        $prep_id_utilisateur->execute([$id_covoit]);
        $id_conducteur = $prep_id_utilisateur->fetch();

        //PREPARATION DE LA REQUETE POUR UTILISATEUR
        $prep_utilisateur = $pdo->prepare(
            "SELECT pseudo, photo, note, sexe
            FROM utilisateur
            WHERE id_utilisateur = ?"
        );
        $prep_utilisateur->execute([$id_conducteur['id_conducteur']]);
        $info_utilisateur = $prep_utilisateur->fetch(PDO::FETCH_ASSOC);


        //PREPARATION DE LA TABLE VOITURE ET COVOITURAGE
        $prep_detail = $pdo->prepare(
            "SELECT v.marque as v_marque,
            v.modele as v_modele,
            v.energie as v_energie,
            v.couleur as v_couleur,
            c.nb_place_dispo as c_nb_place_dispo
            FROM covoiturage c
            INNER JOIN voiture v ON c.id_voiture = v.id_voiture
            WHERE c.id_covoiturage = ?
        ");
        $prep_detail->execute([$id_covoit]);
        $detail_covoit = $prep_detail->fetch(PDO::FETCH_ASSOC);

        //PREPARATION POUR LES PREFERENCE
        $prep_pref = $pdo->prepare(
            "SELECT etre_fumeur,
            avoir_animal,
            avec_silence,
            avec_musique,
            avec_climatisation,
            avec_velo,
            place_coffre,
            ladies_only
            FROM preference 
            WHERE id_utilisateur = ?"
        );
        $prep_pref->execute([$id_conducteur['id_conducteur']]);
        $preference = $prep_pref->fetchAll();


        //PREPARATION DE LA TABLE AVIS
       $prep_avis = $pdo->prepare(
            "SELECT 
            a.commentaire AS a_commentaire,
            a.note AS a_note,
            a.date_avis AS a_date,
            u.pseudo AS u_pseudo,
            u.photo AS u_photo
            FROM avis a
            INNER JOIN utilisateur u ON a.id_passager = u.id_utilisateur
            WHERE a.id_covoiturage = ?
            AND a.statut_avis = 'valider'
            ORDER BY a.date_avis DESC
        ");
        $prep_avis->execute([$id_covoit]);
        $avis_covoiturage = $prep_avis->fetchAll();
    }

}catch (PDOException $e){
    die("Erreur de BBD : ".$e->getMessage());
}

?>