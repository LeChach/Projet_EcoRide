<?php

class MonCompte {


    /**
     * permet de récupérer les données utilisateur 
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_utilisateur : l'id de l'utilisateur
     * @return array ['success' => bool, 'message'=>string, 'info_utilisateur'=>[],'info_preference'=>[],'info_voiture'=>[],'info_covoiturage_c'=>[],'info_covoiturage_p'=>[],'info_role'=>[], 'info_avis_attente'=>[] ]
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
                    WHERE r.id_passager = ?
                    AND r.statut_reservation = ?
                    AND c.statut_covoit IN ('planifier', 'en_cours', 'terminer')"
                );
                $prep_covoit_passager->execute([$id_utilisateur,'active']);
                $covoit_passager = $prep_covoit_passager->fetchAll(PDO::FETCH_ASSOC);
           
            //PREPARATION DE LHISTORIQUE DU CONDUCTEUR
                $prep_historique_c = $pdo->prepare(
                    "SELECT c.*,a.*
                    FROM covoiturage c
                    LEFT JOIN avis a ON c.id_covoiturage = a.id_covoiturage
                    WHERE c.id_conducteur = ?
                    AND (c.statut_covoit = ? OR c.statut_covoit = ?)
                    ORDER BY c.date_depart DESC
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
                    AND (c.statut_covoit = ? OR c.statut_covoit = ?)
                    AND r.statut_reservation = ?
                    ORDER BY c.date_depart DESC
                    ");
                $prep_historique_p->execute([$id_utilisateur, 'terminer', 'annuler','terminer']);
                $historique_p = $prep_historique_p->fetchAll(PDO::FETCH_ASSOC);
            //RECUPERATION DU ROLE DE LUTILISATEUR
                $prep_role = $pdo->prepare(
                    "SELECT libelle
                    FROM role r INNER JOIN possede p ON r.id_role = p.id_role
                    WHERE p.id_utilisateur = ?
                ");
                $prep_role->execute([$id_utilisateur]);
                $role = $prep_role->fetchAll(PDO::FETCH_ASSOC);
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
     * @return array : ['success' => bool, 'message' => string ]
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
     * @return array : ['success' => bool, 'message' => string ]
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
     * @return array ['success' => bool, 'message' => string]
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
     * permet de supprimer une voiture de l'utilisateur
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_utilisateur : l'id de l'utilisateur
     * @param int $id_voiture : l'id de la voiture
     * @return array ['success' => bool, 'message' => string]
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
     * permet de voir les avis de notre covoiturage
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_utilisateur : l'id de l'utilisateur
     * @param int $id_covoiturage : l'id du covoiturage
     * @return array ['success' => bool, 'message' => string, 'avis'=>[] ]
     */
    public static function voirAvis (PDO $pdo, int $id_utilisateur, $id_covoiturage): array {
        try{
            //PERMET DE RECUPERER LES AVIS DU CONDUCTEUR
                $prep_avis_c = $pdo->prepare(
                    "SELECT a.id_avis, a.commentaire, a.note, u.id_utilisateur, u.pseudo as passager, u.photo, c.date_depart
                    FROM avis a
                    INNER JOIN utilisateur u ON a.id_passager = u.id_utilisateur
                    INNER JOIN covoiturage c ON a.id_covoiturage = c.id_covoiturage
                    WHERE a.statut_avis = ?
                    AND a.id_covoiturage = ?
                    AND a.id_conducteur = ?
                    ORDER BY a.date_avis DESC
                    "); 
                $prep_avis_c->execute(['valider',$id_covoiturage,$id_utilisateur]);
                $avis = $prep_avis_c->fetchAll();

            return ['success' => true, 'message' => 'avis chargés', 'avis' => $avis];

        } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['success' => false, 'message' => 'Echec des données des avis'];
        }
    }

    /**
     * permet de recuperer tout les avis en attente pour que l'employer les valide ou non 
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_utilisateur : l'id de l'utilisateur
     * @return array ['success' => bool, 'message' => string, 'avis'=>[] ]
     */
    public static function chargerAvis(PDO $pdo, int $id_utilisateur): array {
        try {
            // Vérification que l'utilisateur est bien un employé
                $prep_role = $pdo->prepare(
                    "SELECT r.libelle
                    FROM role r
                    INNER JOIN possede p ON r.id_role = p.id_role
                    WHERE p.id_utilisateur = ?"
                );
                $prep_role->execute([$id_utilisateur]);
                $roles = array_column($prep_role->fetchAll(PDO::FETCH_ASSOC), 'libelle');

                if (!in_array('Employe', $roles)) {
                    return ['success' => false, 'message' => 'Vous n\'êtes pas employé'];
                }
            // Récupération des avis en attente
                $prep_avis = $pdo->prepare(
                    "SELECT a.id_avis, a.commentaire, a.note, u.id_utilisateur, u.pseudo as passager, uc.id_utilisateur, uc.pseudo as conducteur, c.date_depart
                    FROM avis a
                    INNER JOIN utilisateur u ON a.id_passager = u.id_utilisateur
                    INNER JOIN covoiturage c ON a.id_covoiturage = c.id_covoiturage
                    LEFT JOIN utilisateur uc ON c.id_conducteur = uc.id_utilisateur
                    WHERE a.statut_avis = ?
                    ORDER BY a.date_avis DESC"
                );
                $prep_avis->execute(['en_attente']);
                $avis_attente = $prep_avis->fetchAll(PDO::FETCH_ASSOC);

            return ['success' => true, 'message' => 'Avis chargés', 'avis' => $avis_attente];

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Échec de récupération des avis'];
        }
    }

    /**
     * permet a un employer de valider ou refuser un avis
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_avis : l'avis a changer le statut
     * @param array $data : le refus ou la validation
     * @return array ['success' => bool, 'message' => string ]
     */
    public static function changerAvis(PDO $pdo,int $id_avis,array $data): array {
        try{
            //CONTIENT LE NOUVEAU STATUT DE VALIDATION
                $validation = $data['validation'];
                //on va chercher l'id du conducteur
                    $prep_conducteur = $pdo->prepare(
                        "SELECT id_conducteur, note
                        FROM avis
                        WHERE id_avis = ?
                    ");
                    $prep_conducteur->execute([$id_avis]);
                    $avis_conducteur = $prep_conducteur->fetch(PDO::FETCH_ASSOC);
            //SI VALIDER ON CHANGER LA NOTE Du conducteur
                if($validation === 'valider'){
                //puis on modifie sa note
                    $prep_note = $pdo->prepare(
                        "SELECT note
                        FROM utilisateur 
                        WHERE id_utilisateur = ?
                    ");
                    $prep_note->execute([$avis_conducteur['id_conducteur']]);
                    $note_conducteur = $prep_note->fetch(PDO::FETCH_ASSOC);
                //on calcul la nouvelle note
                        //cas lorsque le conducteur n'a jamais eu de note
                    if($note_conducteur['note'] === null){
                        $new_note = $avis_conducteur['note'];
                    }else{
                        $new_note = ($note_conducteur['note'] + $avis_conducteur['note'])/2;
                    }
                    if($new_note<=0){
                        return ['success'=>false,'message'=>'erreur lors de la note du conducteur'];
                    }
                //puis on MAJ sa nouvelle note
                    $maj_note = $pdo->prepare(
                        "UPDATE utilisateur
                        SET note = ?
                        WHERE id_utilisateur = ?
                    ");
                    $maj_note->execute([$avis_conducteur['id_conducteur'],$new_note]);
                }   
            //puis on change le statut de lavis
            $prep = $pdo->prepare(
                "UPDATE avis 
                SET statut_avis = ? 
                WHERE id_avis = ?
            ");
            $prep->execute([$validation,$id_avis]);

            return ['success'=>true,'message'=>'avis valdier ou refuser correctement'];
            
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Echec de validation des avis'];
            }
    }

    /**
     * permet de charger les données pour la section administrateur
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_utilisateur : l'id de l'utilisateur
     * @return array ['success'=>bool, 'message'=>string, 'info_nb_covoit_j'=>[], 'info_credit_j'=>[], 'info_credit_total'=>[], 'info_utilisateur'=>[] ]
     */
    public static function chargerUtilisateurAdmin(PDO $pdo,int $id_utilisateur): array {
        try{
            //verification si c'est bien l'admin
                $verif_admin = $pdo->prepare(
                    "SELECT r.libelle
                    FROM role r
                    INNER JOIN possede p ON r.id_role = p.id_role
                    WHERE p.id_utilisateur = ?
                ");
                $verif_admin->execute([$id_utilisateur]);
                $libelle = $verif_admin->fetchAll(PDO::FETCH_ASSOC);

                // On extrait tous les libellés dans un tableau simple
                $roles = array_column($libelle, 'libelle');

                if (!in_array('Administrateur', $roles)) {
                    return ['success' => false , 'message' => 'Vous n\'êtes pas administrateur'];
                }
            
            //PREPARATION DE RECUP DES DONNEES
                //recuperation du nbr de covoit par jour
                    $prep_covoit = $pdo->prepare(
                        "SELECT DATE(date_depart) AS jour, COUNT(*) AS nb_covoit
                        FROM covoiturage
                        GROUP BY DATE(date_depart)
                        ORDER BY DATE(date_depart) ASC
                    ");
                    $prep_covoit->execute();
                    $nb_covoit_j = $prep_covoit->fetchAll(PDO::FETCH_ASSOC);
                //recuperation du nbr de credit par jour
                    $prep_credits = $pdo->prepare(
                        "SELECT DATE(date_commission) AS jour, SUM(montant) AS total_credits
                        FROM commission
                        GROUP BY DATE(date_commission)
                        ORDER BY DATE(date_commission) ASC
                    ");
                    $prep_credits->execute();
                    $nb_credit_j = $prep_credits->fetchAll(PDO::FETCH_ASSOC);
                //recuperation du nbr total de credit
                    $prep_credit_total = $pdo->prepare(
                        "SELECT SUM(montant) AS total_credits
                    FROM commission
                    ");
                    $prep_credit_total->execute();
                    $totalCredits = (float)$prep_credit_total->fetch(PDO::FETCH_ASSOC)['total_credits'];
                //recuperation de la liste des utilisateur
                    $req_users = $pdo->prepare(
                        "SELECT u.*, r.libelle AS role
                        FROM utilisateur u
                        INNER JOIN possede p ON u.id_utilisateur = p.id_utilisateur
                        INNER JOIN role r ON p.id_role = r.id_role
                        WHERE u.statut = 'actif'
                        AND u.id_utilisateur != :id_admin
                        AND r.id_role = (
                            SELECT p2.id_role
                            FROM possede p2
                            JOIN role r2 ON p2.id_role = r2.id_role
                            WHERE p2.id_utilisateur = u.id_utilisateur
                                AND r2.libelle IN ('Employe','Utilisateur')
                            ORDER BY CASE 
                                WHEN r2.libelle = 'Employe' THEN 1
                                WHEN r2.libelle = 'Utilisateur' THEN 2
                            END
                            LIMIT 1
                        )
                        ORDER BY CASE 
                            WHEN r.libelle = 'Employe' THEN 1
                            WHEN r.libelle = 'Utilisateur' THEN 2
                        END
                    ");
                    $req_users->execute([":id_admin" => $id_utilisateur]);
                    $utilisateurs = $req_users->fetchAll(PDO::FETCH_ASSOC);

            return ['success'=> true, 'message' => 'Données correctement récupérées', 
            'info_nb_covoit_j' => $nb_covoit_j ,
            'info_credit_j'=> $nb_credit_j,
            'info_credit_total'=> $totalCredits,
            'info_utilisateur'=> $utilisateurs
            ];
            
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Echec de recuperation des données utilisateur'];
        }
    }

    /**
     * permet de suspendre un utilisateur
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_admin : l'id de l'administrateur
     * @param int $id_utilisateur : l'id de l'utilisateur a suspendre
     * @return array ['success' => bool, 'message' => string ]
     */
    public static function suspendreUtilisateur(PDO $pdo,int $id_admin, int $id_utilisateur): array {
        try{
            //verification si c'est bien l'admin
                $verif_admin = $pdo->prepare(
                    "SELECT r.libelle
                    FROM role r
                    INNER JOIN possede p ON r.id_role = p.id_role
                    WHERE p.id_utilisateur = ?
                ");
                $verif_admin->execute([$id_admin]);
                $libelle = $verif_admin->fetchAll(PDO::FETCH_ASSOC);

                // On extrait tous les libellés dans un tableau simple
                $roles = array_column($libelle, 'libelle');

                if (!in_array('Administrateur', $roles)) {
                    return ['success' => false , 'message' => 'Vous n\'êtes pas administrateur'];
                }
            //verification si utilisateur est present        
                $verif_ban = $pdo->prepare(
                    "SELECT id_utilisateur
                    FROM utilisateur
                    WHERE id_utilisateur = ?
                ");
                $verif_ban->execute([$id_utilisateur]);
                $id_ban = $verif_ban->fetch(PDO::FETCH_ASSOC);

                if (empty($id_ban)){
                    return ['success' => false , 'message' => 'Utilisateur non trouvé dans la base de donnée'];
                }
            //SUSPENDRE COMPTE UTILISATEUR
                $prep_ban = $pdo->prepare(
                    "UPDATE utilisateur
                    SET statut = ?
                    WHERE id_utilisateur = ?
                ");
                $prep_ban->execute(['suspendu',$id_utilisateur]);  

            return ['success'=>true,'message'=>'avis valdier ou refuser correctement'];
            
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Echec de suspension de l\'utilisateur'];
        }
    }

    /**
     * permet de promouvoir le statut en employe
     * @param PDO $pdo : le lien avec la bdd
     * @param int $id_admin : l'id de l'administrateur
     * @param int $id_utilisateur : l'id de l'utilisateur a promouvoir
     */
    public static function nouvelEmploye(PDO $pdo,int $id_admin, int $id_utilisateur): array {  
        try{
            //verification si c'est bien l'admin
                $verif_admin = $pdo->prepare(
                    "SELECT r.libelle
                    FROM role r
                    INNER JOIN possede p ON r.id_role = p.id_role
                    WHERE p.id_utilisateur = ?
                ");
                $verif_admin->execute([$id_admin]);
                $libelle = $verif_admin->fetchAll(PDO::FETCH_ASSOC);

                // On extrait tous les libellés dans un tableau simple
                $roles = array_column($libelle, 'libelle');

                if (!in_array('Administrateur', $roles)) {
                    return ['success' => false , 'message' => 'Vous n\'êtes pas administrateur'];
                }
            //verification si utilisateur est present        
                $verif_ban = $pdo->prepare(
                    "SELECT id_utilisateur
                    FROM utilisateur
                    WHERE id_utilisateur = ?
                ");
                $verif_ban->execute([$id_utilisateur]);
                $id_ban = $verif_ban->fetch(PDO::FETCH_ASSOC);

                if (empty($id_ban)){
                    return ['success' => false , 'message' => 'Utilisateur non trouvé dans la base de donnée'];
                }

            //verification que utilisateur est deja employe
                $verif_employe = $pdo->prepare(
                    "SELECT r.libelle
                    FROM role r
                    INNER JOIN possede p ON p.id_role = r.id_role
                    WHERE id_utilisateur = ?
                ");
                $verif_employe->execute([$id_utilisateur]);
                $libelle = $verif_employe->fetchAll(PDO::FETCH_ASSOC);

                $roles = array_column($libelle, 'libelle');

                if (in_array('Employe',$roles)){
                    return ['success' => false , 'message' => 'Cet Utilisateur est deja employé'];
                }    
            //AJOUT DE LA FONCTION EMPLOYE AU COMPTE UTILISATEUR
                $prep_employe = $pdo->prepare(
                    "INSERT INTO possede (id_utilisateur, id_role)
                    VALUES (?,?);
                ");
                $prep_employe->execute([$id_utilisateur,2]);  

            return ['success'=>true,'message'=>'Nouvel employé ajouté'];
            
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Echec de suspension de l\'utilisateur'];
        }
    }
}

?>