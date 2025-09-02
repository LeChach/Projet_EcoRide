<?php
require_once 'log.php';
require_once 'session_prive.php';

    try {

        //PREPARATION REQUETE RECUP PREFERENCE
        $prep_pref = $pdo->prepare(
            "SELECT * 
            FROM preference 
            WHERE id_utilisateur = ?"
        );
        
        $prep_pref->execute([$id_utilisateur]);
        $pref_utilisateur = $prep_pref->fetch();

        $pref_fumeur = $pref_utilisateur['etre_fumeur'] ?? 'accepter';
        $pref_animal = $pref_utilisateur['avoir_animal'] ?? 'accepter';
        $pref_silence = $pref_utilisateur['avec_silence'] ?? 'refuser';
        $pref_musique = $pref_utilisateur['avec_musique'] ?? 'accepter';
        $pref_clim = $pref_utilisateur['avec_climatisation'] ?? 'accepter';
        $pref_velo = $pref_utilisateur['avec_velo'] ?? 'refuser';
        $pref_coffre = $pref_utilisateur['place_coffre'] ?? 'accepter';
        $pref_ladies_only = $pref_utilisateur['ladies_only'] ?? null;

    } catch (PDOException $e) {
        die ("Erreur connexion BDD : ".$e->getMessage());
    }

?>