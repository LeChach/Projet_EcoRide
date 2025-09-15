<?php

class MonCompte {


    /**
     * permet de récupérer les données utilisateur pour l'affichage
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_utilisateur : l'id de l'utilisateur
     * @return array ['success'=>bool,'message'=>'','info_utilisateur'=>[],'info_preference'=>[],'info_voiture'=>[],'info_covoiturage_c'=>[],'info_covoiturage_p'=>[] ]
     */
    public static function recupDonnee (PDO $pdo, int $id_utilisateur): array {

        try{
            //PREPARATION DE LA TABLE UTILISATEUR
                $prep_utilisateur = $pdo->prepare(
                    "SELECT * 
                    FROM utilisateur 
                    WHERE id_utilisateur = ?"
                    );
                    
                $prep_utilisateur->execute([$id_utilisateur]);
                $info_utilisateur = $prep_utilisateur->fetch(PDO::FETCH_ASSOC);

            //PREPARATION DE LA TABLE PREFERENCE
                $prep_pref = $pdo->prepare(
                        "SELECT 
                        etre_fumeur,
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
                $prep_pref->execute([$id_utilisateur]);
                $pref_utilisateur = $prep_pref->fetch(PDO::FETCH_ASSOC);
    
            //PREPARATION DE LA TABLE VOITURE
                $prep_voiture = $pdo->prepare(
                    "SELECT * 
                    FROM voiture
                    WHERE id_conducteur = ?"
                );
                $prep_voiture->execute([$id_utilisateur]);
                $voitures_utilisateur = $prep_voiture->fetchAll();


            //PREPARATION DE LA TABLE COVOITURAGE = pour quand je suis conducteur
                $prep_covoit_conducteur = $pdo->prepare(
                    "SELECT *
                    FROM covoiturage
                    WHERE id_conducteur = ?"
                );
                $prep_covoit_conducteur->execute([$id_utilisateur]);
                $covoit_conducteur = $prep_covoit_conducteur->fetchAll();

            //PREPARATION DE LA TABLE RESERVATION = pour quand je suis passager
                $prep_covoit_passager = $pdo->prepare(
                    "SELECT c.*, r.*
                    FROM covoiturage c
                    INNER JOIN reservation r ON c.id_covoiturage = r.id_covoiturage
                    WHERE r.id_passager = ?"
                );
                $prep_covoit_passager->execute([$id_utilisateur]);
                $covoit_passager = $prep_covoit_passager->fetchAll(PDO::FETCH_ASSOC);
           
            //PREPARATION DE LHISTORIQUE DU CONDUCTEUR
                $prep_historique_c = $pdo->prepare(
                    "SELECT c.*,a.*
                    FROM covoiturage c
                    LEFT JOIN avis a ON c.id_covoiturage = a.id_covoiturage
                    WHERE c.id_conducteur = ?
                    AND (c.statut_covoit = ? OR c.statut_covoit = ?)
                ");
                $prep_historique_c->execute([$id_utilisateur,'annuler','terminer']);
                $historique_c = $prep_historique_c->fetchAll();

            //PREPARATION DE LHISTORIQUE DU PASSAGER
                $prep_historique_p = $pdo->prepare(
                    "SELECT c.*, a.*, r.*
                    FROM covoiturage c
                    INNER JOIN reservation r ON c.id_covoiturage = r.id_covoiturage
                    LEFT JOIN avis a ON c.id_covoiturage = a.id_covoiturage 
                    AND a.statut_avis = 'valider'
                    WHERE r.id_passager = ?
                    AND (c.statut_covoit = ? OR c.statut_covoit = ?)"
                );
                $prep_historique_p->execute([$id_utilisateur, 'terminer', 'annuler']);
                $historique_p = $prep_historique_p->fetchAll(PDO::FETCH_ASSOC);
            //RECUPERATION DU ROLE DE LUTILISATEUR
                $prep_role = $pdo->prepare(
                    "SELECT libelle
                    FROM role r INNER JOIN possede p ON r.id_role = p.id_role
                    WHERE p.id_utilisateur = ?
                ");
                $prep_role->execute([$id_utilisateur]);
                $role = $prep_role->fetch();
            //RECUPERATION DU NOMBRE DAVIS EN ATTENTE POUR EMPLOYE
                $prep_avis_attente = $pdo->prepare(
                    "SELECT COUNT(*) as nb_attente 
                    FROM avis 
                    WHERE statut_avis = 'en_attente'"
                );
                $prep_avis_attente->execute();
                $nb_attente = $prep_avis_attente->fetch(PDO::FETCH_ASSOC);

            //RETURN
            return ['success' => true, 
                    'message' => 'Donnée correctement récupérer',
                    'info_utilisateur' => $info_utilisateur,
                    'info_preference' => $pref_utilisateur,
                    'info_voiture' => $voitures_utilisateur,
                    'info_covoiturage_c' => $covoit_conducteur,
                    'info_covoiturage_p' => $covoit_passager,
                    'info_historique_c' => $historique_c,
                    'info_historique_p'=> $historique_p,
                    'info_role' => $role,
                    'info_avis_attente' => $nb_attente                  
            ];

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Echec de la récupération des données'];
        }
    }

    /**
     * permet de changer de statut utilisateur
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_utilisateur : l'id de l'utilisateur
     * @param string $type_changement : le nouveau type de l'utilisateur
     * @return array : ['success'=> bool, 'message'=>'']
    */
    public static function changerTypeUtilisateur (PDO $pdo, int $id_utilisateur, string $type_changement): array {
        try{
            //verification de la conformité du type a insérer
            $verificiation = ['Passager','Conducteur','Passager et Conducteur'];
            if(!in_array($type_changement,$verificiation)){
                return ['success' => false, 'message' => 'type utilisateur non reconnu'];
            }
            //PREPARATION du changement du type utilisateur
            $prep_typeU = $pdo->prepare(
                "UPDATE utilisateur
                SET type_utilisateur = ?
                WHERE id_utilisateur = ?
            ");
            $prep_typeU->execute([$type_changement,$id_utilisateur]);

            return ['success' => true , 'message' => 'Changement correctement effectué'];
            
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Echec du changement des données'];
        }

    }

    /**
     * permet de changer les préférence utilisateur
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_utilisateur : l'id de l'utilisateur
     * @param array $data : le tableau des nouvelles preference
     */
    public static function changerPréférence (PDO $pdo, int $id_utilisateur, array $data): array {
        try{
            //RECUPERATION du sexe de l'id_utilisateur pour gérer la préférence ladies_only
            $prep_sexe = $pdo->prepare(
                "SELECT sexe 
                FROM utilisateur 
                WHERE id_utilisateur = ?"
            );
            $prep_sexe->execute([$id_utilisateur]);
            $sexe_utilisateur = $prep_sexe->fetch();

            //RECUPERATION DES NOUVELLE DONNEES (si coché = $data sera 'accepter' sinon $data sera null donc 'refuser')
            $new_pref_fumeur = $data['etre_fumeur'] ?? 'refuser';
            $new_pref_animal = $data['avoir_animal'] ?? 'refuser';
            $new_pref_silence = $data['avec_silence'] ?? 'refuser';
            $new_pref_musique = $data['avec_musique'] ?? 'refuser';
            $new_pref_clim = $data['avec_climatisation'] ?? 'refuser';
            $new_pref_velo = $data['avec_velo'] ?? 'refuser';
            $new_pref_coffre = $data['place_coffre'] ?? 'refuser';
            if ($sexe_utilisateur['sexe'] === 'Femme'){
                $new_pref_ladies_only = $data['ladies_only'] ?? 'refuser';
            }else{
                //cas pour les hommes et non précisé
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

            return ['success' => true, 'message' => 'Changement des préférences'];


        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Echec du changement des préférence'];
        }

    }

    /**
     * permet d'ajouter une nouvelle voiture pour l'utilisateur:
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_utilisateur : l'id de l'utilisateur
     * @param array $data : le tableau des infos de la voiture
     * @return array ['success' => bool, 'message' => '']
     */
    public static function ajouterVoiture (PDO $pdo, int $id_utilisateur, array $data): array {
        try{
            //VERIFICATION de son statut pour bien ajouter une voiture
            $prep_typeU = $pdo->prepare(
                "SELECT type_utilisateur
                FROM utilisateur
                WHERE id_utilisateur = ?
            ");
            $prep_typeU->execute([$id_utilisateur]);
            $type_utilisateur = $prep_typeU->fetch();
            if(!$type_utilisateur || $type_utilisateur['type_utilisateur'] === 'Passager'){
                return ['success' => false , 'message' => 'type_utilisateur non conforme'];
            }

            //INSERTION DES NOUVELLES DONNEES
            $marque = $data['marque'];
            $modele = $data['modele'];
            $immat = $data['immat'];
            $date_premiere_immat = $data['date_premiere_immat'];
            $energie = $data['energie'];
            $couleur = $data['couleur'];
            $nb_place = $data['nb_place'];

            //VERIFICATION DE LA PLAQUE IMMAT
            $prep_immat = $pdo->prepare(
                "SELECT id_voiture
                FROM voiture
                WHERE id_conducteur = ?
                AND immat = ?"
            );
            $prep_immat->execute([$id_utilisateur,$immat]);

            if($prep_immat->rowCount()>0){
                return ['success' => false , 'message' => 'Imatriculation déjà existante'];
            }

            //PREPARATION POUR AJOUTER VOITURE
            $prep_voiture = $pdo->prepare(
                "INSERT INTO voiture 
                (id_conducteur,marque,modele,immat,date_premiere_immat,energie,couleur,nb_place)
                VALUES (?,?,?,?,?,?,?,?)"
            );
            $prep_voiture->execute(
                [$id_utilisateur,$marque,$modele,$immat,$date_premiere_immat,$energie,$couleur,$nb_place]
            );

            return ['success' => true , 'message' => 'Nouvelle voiture ajoutée'];

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Echec du changement des données'];
        }
    }

    /**
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_utilisateur : l'id de l'utilisateur
     * @param int $id_voiture : l'id de la voiture
     * @return array ['success' => bool, 'message' => '']
    */
    public static function supprimerVoiture (PDO $pdo, int $id_utilisateur,int $id_voiture): array {
        try{
            //VERIFICATION que la voiture existe bien et appartient bien au conducteur
            $prep_voiture = $pdo->prepare(
                "SELECT id_voiture, id_conducteur
                FROM voiture
                WHERE id_voiture = ?
                AND id_conducteur = ?
            ");
            $prep_voiture->execute([$id_voiture,$id_utilisateur]);

            if($prep_voiture->rowCount() < 1){
                return ['success' => false , 'message' => 'Erreur, Voiture inexistante']; 
            }

            //PREPARATION POUR SUPPRIMER VOITURE
            $prep_voiture_supp = $pdo->prepare(
                "DELETE FROM voiture
                WHERE id_voiture = ?"
            );
            $prep_voiture_supp->execute([$id_voiture]);

            return ['success' => true, 'message' => 'Voiture correctement supprimée'];

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Echec du changement des données'];
        }
    }
 
    /**
     * 
     */
    public static function voirAvis (PDO $pdo, int $id_utilisateur, $id_covoiturage): array {
        try{
            //PERMET DE RECUPERER LES AVIS DU CONDUCTEUR
            $prep_avis_c = $pdo->prepare(
                "SELECT a.note, a.commentaire, u.pseudo, u.photo
                FROM avis a
                INNER JOIN utilisateur u ON a.id_passager = u.id_utilisateur
                WHERE a.id_covoiturage = ?
                AND a.id_conducteur = ?
                AND a.statut_avis = 'valider'
            "); 
            $prep_avis_c->execute([$id_covoiturage,$id_utilisateur]);
            $avis = $prep_avis_c->fetchAll();

            return ['success' => true, 'message' => 'avis chargés', 'avis' => $avis];

        } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['success' => false, 'message' => 'Echec des données des avis'];
        }
    }

    /**
     *  GERER AVIS RETURN FAUX A CORIGER
     */
    public static function chargerAvis(PDO $pdo, int $id_utilisateur): array {
        try{
            //verification que cest bien un employe
                $prep_role = $pdo->prepare(
                    "SELECT libelle
                    FROM role r INNER JOIN possede p ON r.id_role p.id_role
                    WHERE p.id_utilisateur = ?
                ");
                $prep_role->execute([$id_utilisateur]);
                $role = $prep_role->fetch();
                if($role['libelle'] !== 'Employe'){
                    return ['success' => false, 'message' => 'Seulement les employés peuvent valider les avis'];
                }

                $prep_avis = $pdo->prepare(
                    "SELECT a.id_avis, a.commentaire, a.note, u.pseudo, c.date_depart
                    FROM avis a
                    INNER JOIN utilisateur u ON a.id_passager = u.id_utilisateur
                    INNER JOIN covoiturage c ON a.id_covoiturage = c.id_covoiturage
                    WHERE a.statut_avis = 'en_attente'"
                );
                $prep_avis->execute();
                $avis_attente = $prep_avis->fetchAll(PDO::FETCH_ASSOC);

                return ['success' => true, 'message' => 'Avis chargé', 'avis' => $avis_attente];

        } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['success' => false, 'message' => 'Echec des données des avis'];
        }
    }

    /**
     * 
     */
    public static function changerAvis(PDO $pdo,int $id_avis,array $data){
        $validation = $data['validation'];
        $prep = $pdo->prepare(
            "UPDATE avis 
            SET statut_avis = ? 
            WHERE id_avis = ?
        ");
        $prep->execute([$validation,$id_avis]);
    }
}

?>