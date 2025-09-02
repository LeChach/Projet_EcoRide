<?php
require_once 'log.php';
require_once 'session_prive.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST'){

        try{

            //BESOIN DE RECUPERER LE SEXE POUR METTRE A JOUR LES PREF
            $prep_sexe = $pdo->prepare(
                "SELECT sexe 
                FROM utilisateur 
                WHERE id_utilisateur = ?"
            );
            $prep_sexe->execute([$id_utilisateur]);
            $sexe_utilisateur = $prep_sexe->fetch();

            //on recupere les nouvelles infos meme si ca na pas changer
            //si coché $post = accepter , si decocher $post = null donc refuser 
            $new_pref_fumeur = $_POST['fumeur'] ?? 'refuser';
            $new_pref_animal = $_POST['animaux'] ?? 'refuser';
            $new_pref_silence = $_POST['silence'] ?? 'refuser';
            $new_pref_musique = $_POST['musique'] ?? 'refuser';
            $new_pref_clim = $_POST['climatisation'] ?? 'refuser';
            $new_pref_velo = $_POST['velo'] ?? 'refuser';
            $new_pref_coffre = $_POST['place_coffre'] ?? 'refuser';
            if ($sexe_utilisateur['sexe'] == 'Femme'){
                $new_pref_ladies_only = $_POST['ladies_only'] ?? 'refuser';
            }else{
                $new_pref_ladies_only = 'non concerne';
            }

            //PREPARATION POUR PREFERENCE
            $prep_new_pref = $pdo->prepare(
            "UPDATE preference 
            SET etre_fumeur=?,
            avoir_animal=?,
            avec_silence=?,
            avec_musique=?,
            avec_climatisation=?,
            avec_velo=?,
            place_coffre=?,
            ladies_only=?
            WHERE id_utilisateur = ?"
            );
            
            $prep_new_pref->execute([
                $new_pref_fumeur,
                $new_pref_animal,
                $new_pref_silence,
                $new_pref_musique,
                $new_pref_clim,
                $new_pref_velo,
                $new_pref_coffre,
                $new_pref_ladies_only,
                $id_utilisateur
            ]); 

            header("Location: ../mon_compte.php");
            exit;
        
        } catch (PDOException $e) {
        die ("Erreur connexion BDD : ".$e->getMessage());
        }
    }
?>