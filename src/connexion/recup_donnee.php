<?php
require_once 'log.php';
require_once 'session_prive.php';

//GROSSE PREPARATION POUR RECUPERE UTILISATEUR, PREFERENCE, VOITURE, COVOIT, HISTORIQUE COVOIT
try {

    //PREPARATION DE LA TABLE UTILISATEUR
    $prep_utilisateur = $pdo->prepare(
        "SELECT * 
        FROM utilisateur 
        WHERE id_utilisateur = ?"
        );
        
    $prep_utilisateur->execute([$id_utilisateur]);
    $info_utilisateur = $prep_utilisateur->fetch();

    //PREPARATION DES DONNEES UTILISATEUR
    if($info_utilisateur){
        $pseudo = $info_utilisateur['pseudo'] ?? '';
        $email = $info_utilisateur['email'] ?? '';
        $telephone = $info_utilisateur['telephone'] ?? '';
        $photo = "assets/pp/".$info_utilisateur['photo'] ?? 'avatar_default.png';
        $credit = $info_utilisateur['credit'] ?? 20;
        $type_u = $info_utilisateur['type_utilisateur'] ?? '';
    }



    //PREPARATION DE LA TABLE PREFERENCE
    $prep_pref = $pdo->prepare(
            "SELECT * 
            FROM preference 
            WHERE id_utilisateur = ?"
        );
        
    $prep_pref->execute([$id_utilisateur]);
    $pref_utilisateur = $prep_pref->fetch();

    //PREPARATION DES DONNEES PREFERENCE
    if($pref_utilisateur){
        $pref_fumeur = $pref_utilisateur['etre_fumeur'] ?? 'accepter';
        $pref_animal = $pref_utilisateur['avoir_animal'] ?? 'accepter';
        $pref_silence = $pref_utilisateur['avec_silence'] ?? 'refuser';
        $pref_musique = $pref_utilisateur['avec_musique'] ?? 'accepter';
        $pref_clim = $pref_utilisateur['avec_climatisation'] ?? 'accepter';
        $pref_velo = $pref_utilisateur['avec_velo'] ?? 'refuser';
        $pref_coffre = $pref_utilisateur['place_coffre'] ?? 'accepter';
        $pref_ladies_only = $pref_utilisateur['ladies_only'] ?? 'non concerne';
    }


    //PREPARATION DE LA TABLE VOITURE
    $prep_voiture = $pdo->prepare(
        "SELECT * 
        FROM voiture
        WHERE id_conducteur = ?"
    );
    $prep_voiture->execute([$id_utilisateur]);
    $voitures_utilisateur = $prep_voiture->fetchAll();


    //PREPARATION DE LA TABLE COVOITURAGE
    $prep_covoit = $pdo->prepare(
        "SELECT *
        FROM covoiturage
        WHERE id_conducteur = ?"
    );
    $prep_covoit->execute([$id_utilisateur]);
    $covoit_utilisateur = $prep_covoit->fetchAll();


} catch (PDOException $e) {
    die("Erreur de BBD : ".$e->getMessage());
}

?>
