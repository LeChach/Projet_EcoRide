<?php

require_once 'fonction_php/fonction.php';

class Covoiturage {

    /**
     * Permet de créer un covoiturage
     * @param PDO $pdo : PDO pour connexion a la bdd
     * @param int $id_utilisateur : Id du conducteur
     * @param array $data : donnée du formulaire
     * @return array : ['success' => bool , 'message' => string]
     */
    public static function creationCovoiturage (PDO $pdo, int $id_utilisateur, array $data) : array{
        try{
            //ON COMMENCE PAR VALIDER LES DONNEES
            $lieu_depart = formaterVille($data['lieu_depart']);
            $lieu_arrive = formaterVille($data['lieu_arrive']);
            $date_depart = trim($data['date_depart']);
            $heure_depart = sprintf('%02d:%02d:00', (int)$data['heure_depart'] ?? 0, (int)$data['min_depart'] ?? 0);
            $duree_voyage = sprintf('%02d:%02d:00', (int)$data['duree_voyage_heure'] ?? 0, (int)$data['duree_voyage_min'] ?? 0);
            $prix_personne = $data['prix_personne'] ?? 0;
            $id_voiture = (int)$data['id_voiture'] ?? 0;
            $nb_place_dispo = (int)$data['nb_place_dispo'] ?? 0;

            $pdo->beginTransaction();

            //PUIS ON VA GERER TOUT LES CAS QUI POSERONS PROBLEME
                //cas ou les lieux n'ont pas étés inscris
                    if( !$lieu_arrive || !$lieu_depart ){
                        return ['success' => false, 'message' => 'Lieu de de départ ou d arrivé incomplet']; 
                    } 
                //cas du prix du voyage non correct
                    if( $prix_personne <= 0 || !is_numeric($prix_personne) ){
                        return ['success' => false, 'message' => 'Montant prix non valide'];
                    }
                //mesure de 1h minimum pour mettre un covoit en ligne
                    $TempsDepart = new DateTime("$date_depart $heure_depart");
                    $TempsMin = new DateTime('+1 hour');
                    if($TempsDepart < $TempsMin){
                        return ['success' => false, 'message' => 'Covoiturage programmé trop tot'];
                    }
                //verification si la voiture n'est pas deja prise
                    $prep_voiture_dispo = $pdo->prepare(
                        "SELECT COUNT(*) as nbr
                        FROM covoiturage
                        WHERE id_voiture = ?
                        AND date_depart = ?
                        AND heure_depart = ?
                        AND statut_covoit = ?"
                    );
                    $prep_voiture_dispo->execute([$id_voiture,$date_depart,$heure_depart,'planifier']);
                    $voiture_dispo = $prep_voiture_dispo->fetch(PDO::FETCH_ASSOC);
                    if($voiture_dispo['nbr']>0){
                        return ['success' => false, 'message' => 'Voiture déjà prise'];
                    }
                //verification du solde du credit du conducteur
                    $prep_credit_conducteur = $pdo->prepare(
                        "SELECT credit
                        FROM utilisateur
                        WHERE id_utilisateur = ?
                        ");
                    $prep_credit_conducteur->execute([$id_utilisateur]);
                    $credit_conducteur = $prep_credit_conducteur->fetch();
                    if(($credit_conducteur['credit'] - 2) < 0){
                        return ['success' => false, 'message' => 'Solde insufisant pour créer un nouveau covoiturage'];
                    }    
                //recuperation du nombre de place maximum de la voiture
                    $prep_voiture = $pdo->prepare("SELECT nb_place FROM voiture WHERE id_voiture = ? AND id_conducteur = ?");
                    $prep_voiture->execute([$id_voiture, $id_utilisateur]);
                    $voiture_info = $prep_voiture->fetch(PDO::FETCH_ASSOC);
                    if (!$voiture_info) {
                        return ['success' => false, 'message' => 'Voiture introuvable'];
                    }               
                // VALIDATION DU NOMBRE DE PLACES CHOISI
                    $nb_place_max = $voiture_info['nb_place'];
                    if ($nb_place_dispo < 1) {
                        return ['success' => false, 'message' => 'Vous devez proposer au moins 1 place'];
                    }
                    if ($nb_place_dispo > $nb_place_max) {
                        return ['success' => false, 'message' => 'Nombre de places supérieur à la capacité de votre voiture'];
                    }
            //UNE FOIS TOUT OK INSERTION DU NOUVEAU COVOIT
                $prep_nouveau_covoit = $pdo->prepare(
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
                $prep_nouveau_covoit->execute(
                [$date_depart,
                $heure_depart,
                $duree_voyage,
                $lieu_depart,
                $lieu_arrive,
                $nb_place_dispo,
                $prix_personne,
                $id_utilisateur,
                $id_voiture
                ]
                );
                $id_covoiturage = $pdo->lastInsertId();
            //MAJ DES CREDIT DU CONDUCTEUR ET DE LA COMMISSION
                $prep_credit = $pdo->prepare(
                    "UPDATE utilisateur
                    SET credit = credit - ?
                    WHERE id_utilisateur = ?"
                );
                $prep_credit->execute([2,$id_utilisateur]);

                $prep_commission = $pdo->prepare(
                    "INSERT INTO commission (id_covoiturage, id_conducteur)
                    VALUES (?,?)
                ");
                $prep_commission->execute([$id_covoiturage, $id_utilisateur]);      

            $pdo->commit();
            
            return ['success' => true, 'message' => 'Insertion covoiturage'];

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Echec insertion covoiturage'];
        }
    }

    /**
     * Permet à un passager de participer à un covoiturage
     * @param PDO $pdo : PDO pour connexion a la bdd
     * @param int $id_utilisateur : Id de l'utilisateur
     * @param int $id_covoiturage :  Id de covoiturage
     * @param int $nb_place_voulu : nombre de place que veux reserver l'utilisateur
     * @return array ['success' => bool, 'message' => string]
     */
    public static function participerCovoiturage (PDO $pdo, int $id_utilisateur, int $id_covoiturage, int $nb_place_voulu) : array {

        try {
            //Récupération des infos covoiturage
                $prep_covoit = $pdo->prepare(
                    "SELECT prix_personne, nb_place_dispo, id_conducteur
                    FROM covoiturage
                    WHERE id_covoiturage = ?"
                );
                $prep_covoit->execute([$id_covoiturage]);
                $covoit_info = $prep_covoit->fetch(PDO::FETCH_ASSOC);

            //Vérification si le covoit existe tjrs
                if (!$covoit_info) {
                    return ['success'=>false, 'message'=>'Covoiturage introuvable'];
                }

            // Vérification du solde avant débit

            $prix_total = $nb_place_voulu * $covoit_info['prix_personne'];
            $place_dispo_maj = $covoit_info['nb_place_dispo'] - $nb_place_voulu;
            
            $prep_check_solde = $pdo->prepare(
                "SELECT credit FROM utilisateur WHERE id_utilisateur = ?"
            );
            $prep_check_solde->execute([$id_utilisateur]);
            $solde_actuel = $prep_check_solde->fetch()['credit'];

            if ($solde_actuel < $prix_total) {
                return ['success' => false, 'message' => 'Solde insuffisant pour ce covoiturage'];
            }

            //VERIFICATION si le participant n'est pas juste un conducteur
                $prep_type = $pdo->prepare(
                    "SELECT type_utilisateur
                    FROM utilisateur
                    WHERE id_utilisateur = ?
                ");
                $prep_type->execute([$id_utilisateur]);
                $type_u = $prep_type->fetch(PDO::FETCH_ASSOC)['type_utilisateur'];
                if ($type_u === 'Conducteur'){
                    return ['success'=>false, 'message'=>'Vous n\'êtes que conducteur, vous ne pouvez participer a un covoiturage'];
                }
            
            $pdo->beginTransaction();

            //ON CREER LA RESERVATION
                $prep_res = $pdo->prepare(
                "INSERT INTO reservation 
                (nb_place_reserve, 
                id_passager, 
                id_conducteur, 
                id_covoiturage)
                VALUES (?,?,?,?)"
                );
                $prep_res->execute(
                [$nb_place_voulu, 
                $id_utilisateur, 
                $covoit_info['id_conducteur'], 
                $id_covoiturage]);

            //ON ACTUALISE LE SOLDE DE CREDIT DU PARTICIPANT
                $prep_credit_passager = $pdo->prepare(
                "UPDATE utilisateur SET credit = credit - ? WHERE id_utilisateur = ?"
                );
                $prep_credit_passager->execute([$prix_total, $id_utilisateur]);

            //ON MET A JOUR LE NOMBRE DE PLACE DISPO DANS COVOITURAGE
                $prep_places = $pdo->prepare(
                "UPDATE covoiturage SET nb_place_dispo = ?
                WHERE id_covoiturage = ? "
                );
                $prep_places->execute([$place_dispo_maj, $id_covoiturage]);

            //ON PREPARE LE SOLDE DE CREDIT DU CONDUCTEUR
                $prep_virement = $pdo->prepare(
                "INSERT INTO virement 
                (montant_virement, 
                statut, 
                id_passager, 
                id_conducteur,
                id_covoiturage)
                VALUES (?,?,?,?,?)"
                );
                $prep_virement->execute(
                [$prix_total, 
                'en_attente', 
                $id_utilisateur, 
                $covoit_info['id_conducteur'],
                $id_covoiturage]
                );

            //ON CREER LE VIREMENT INSTANTANEE DU PASSAGER
                $prep_virement_passager = $pdo->prepare(
                "INSERT INTO virement 
                (montant_virement,
                statut, 
                id_passager, 
                id_conducteur,
                id_covoiturage)
                VALUES (?,?,?,?,?)"
                );
                $prep_virement_passager->execute(
                [$prix_total,'valider', 
                $id_utilisateur, 
                $covoit_info['id_conducteur'],
                $id_covoiturage]
                );

            $pdo->commit();
            return ['success'=>true, 'message'=>'Participation confirmée !'];

        } catch(PDOException $e) {
            $pdo->rollBack();
            error_log($e->getMessage());
            return ['success'=>false, 'message'=>'Erreur lors de la participation'];
        }
    }

    /**
     * permet d'afficher les information pour confirmer la participation du covoiturage
     * @param PDO $pdo : PDO pour connexion a la bdd
     * @param int $id_covoiturage :  Id de covoiturage
     * @param int $id_utilisateur : Id de l'utilisateur
     * @param int $nb_place_voulu : nombre de place que veux reserver l'utilisateur
     * @return array ['success' => bool, 'message' => string]
     */
    public static function voirCovoituragePourParticipation(PDO $pdo, int $id_covoiturage, int $id_utilisateur,int $nb_place_voulu):array{
        try{
            //Récupération des infos covoiturage
                $prep_covoit = $pdo->prepare(
                    "SELECT prix_personne, nb_place_dispo, id_conducteur
                    FROM covoiturage
                    WHERE id_covoiturage = ?"
                );
                $prep_covoit->execute([$id_covoiturage]);
                $covoit_info = $prep_covoit->fetch(PDO::FETCH_ASSOC);
            
            //VERIFICATION QUE LE COVOITURAGE EXISTE
                if(!$covoit_info){
                    return ['success' => false , 'message' => 'Le covoiturage n existe plus'];
                }

            //verification que le participant ne soit pas le conducteur
                if ($id_utilisateur === $covoit_info['id_conducteur']) {
                    return ['success' => false, 'message' => 'Vous ne pouvez pas participer à votre propre covoiturage.'];            
                }

            // Vérification des places disponibles
                if($covoit_info['nb_place_dispo'] < $nb_place_voulu){
                    return ['success' => false , 'message' => 'Plus de place disponible'];
                }

            // Récupération du crédit de l'utilisateur
                $prep_utilisateur = $pdo->prepare(
                    "SELECT credit, pseudo
                    FROM utilisateur
                    WHERE id_utilisateur = ?"
                );
                $prep_utilisateur->execute([$id_utilisateur]);
                $utilisateur_info = $prep_utilisateur->fetch(PDO::FETCH_ASSOC);

            // Calcul du prix total du covoiturage
                $prix_total = $nb_place_voulu * $covoit_info['prix_personne'];

            // Vérification du crédit disponible
                $new_solde = $utilisateur_info['credit'] - $prix_total;
                if($new_solde < 0){
                    return ['success'=>false,'message'=>'Solde insuffisant pour ce covoiturage'];
                }
                
            // Verification si l'utilisateur a deja reservé ce covoit
                $prep_check = $pdo->prepare(
                    "SELECT COUNT(*) as nb
                    FROM reservation
                    WHERE id_passager = ? AND id_covoiturage = ? AND statut_reservation = 'active'"
                );
                $prep_check->execute([$id_utilisateur, $id_covoiturage]);
                $prep_verif = $prep_check->fetch(PDO::FETCH_ASSOC);
                if($prep_verif['nb']){
                    return ['success' => false, 'message' => 'Vous avez déja réservé ce covoiturage'];
                }

            return ['success' => true, 'message' => 'affichage ok', 'nouveau_solde' => $new_solde, 'prix_total' => $prix_total];


        } catch(PDOException $e) {
            $pdo->rollBack();
            error_log($e->getMessage());
            return ['success'=>false, 'message'=>'Erreur d affichage de la participation'];
        }

    }


    /**
     * Permet de charger les infos du conducteur, sa voiture, ses preferences ainsi que ses avis
     * @param PDO $pdo : PDO pour connexion a la bdd
     * @param int $id_covoiturage :  Id de covoiturage 
     * @return array ['utilisateur' => '', 'covoiturage' => '', 'preferences' => '', 'avis' => '']
     */
    public static function detailCovoiturage(PDO $pdo, int $id_covoit):array {

        try{
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
            $preference = $prep_pref->fetch(PDO::FETCH_ASSOC);


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

            return [
                'utilisateur' => $info_utilisateur,
                'covoiturage' => $detail_covoit,
                'preferences' => $preference,
                'avis' => $avis_covoiturage
            ];

        } catch (PDOException $e) {
            error_log("Erreur detailCovoiturage : " . $e->getMessage());
            return [
                'utilisateur' => null,
                'covoiturage' => null,
                'preferences' => null,
                'avis' => null
            ];
        }
    }


    /**
     * permet de faire une recherche parmis des critere de lieu et de date et de nombre de place
     * @param PDO $pdo : PDO pour connexion a la bdd.
     * @param string $lieu_depart : lieu de départ
     * @param string $lieu_arrive : lieu d'arrivé'
     * @param string $date_depart : date de départ recherché
     * @param int $nb_places_voulu_par_le_passager : nombre de place à reserver
     * @return array ['info_covoiturage' => '', 'message' => string]
     */
    public static function rechercheCovoiturage (PDO $pdo, string $lieu_depart, string $lieu_arrive, string $date_depart, int $nb_places_voulu_par_le_passager): array {
        try {
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
                AND c.date_depart >= ?
                AND c.nb_place_dispo >= ?
                AND c.statut_covoit = ?
                ORDER BY c.date_depart"
            );
            $prep_covoit->execute([$lieu_depart,$lieu_arrive,$date_depart,$nb_places_voulu_par_le_passager,'planifier']);
            $recherche_covoit = $prep_covoit->fetchAll();

            return [
                'info_covoiturage' => $recherche_covoit,
                'message' => 'Recherche correctement effectuée'
            ];
        } catch (PDOException $e){
            error_log($e->getMessage());
            return [
                'info_covoiturage'=> null, 
                'message'=>'Erreur lors de la participation'];
        }
        
    }

    /**
     * permet de refaure une recherche en prenant en compte les filtres
     * @param PDO $pdo : PDO pour connexion a la bdd.
     * @param string $lieu_depart : lieu de départ
     * @param string $lieu_arrive : lieu d'arrivé'
     * @param string $date_depart : date de départ recherché
     * @param int $nb_places_voulu_par_le_passager : nombre de place à reserver
     * @param ?string $type_energie : type de lenergie 
     * @param ?float $prix = null : prix max demandé
     * @param ?string $duree = null : durée max demandé
     * @param ?float $avis = null : note de conducteur minimum demandé
     */
    public static function rechercheFiltrerCovoiturage(PDO $pdo, string $lieu_depart, string $lieu_arrive, string $date_depart, int $nb_places_voulu_par_le_passager, ?string $type_energie = null, ?float $prix = null, ?string $duree = null, ?float $avis = null): array {
        try {
            $sql = "SELECT 
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
            AND c.date_depart >= ?
            AND c.nb_place_dispo >= ?
            AND c.statut_covoit = ?
            ";

            $params = [$lieu_depart, $lieu_arrive, $date_depart, $nb_places_voulu_par_le_passager,'planifier'];

            // Filtre énergie
            if (!empty($type_energie)) {
                $sql .= " AND v.energie = ?";
                $params[] = $type_energie;
            }

            // Filtre prix
            if (!empty($prix)) {
                $sql .= " AND c.prix_personne <= ?";
                $params[] = $prix;
            }

            // Filtre durée
            if (!empty($duree)) {
                $sql .= " AND c.duree_voyage <= ?";
                $params[] = $duree;
            }

            // Filtre avis
            if (!empty($avis)) {
                $sql .= " AND u.note >= ?";
                $params[] = $avis;
            }

            $sql .= " ORDER BY c.date_depart";

            $prep_covoit = $pdo->prepare($sql);
            $prep_covoit->execute($params);
            $recherche_covoit = $prep_covoit->fetchAll();

            return [
                'success' => true,
                'info_covoiturage' => $recherche_covoit,
                'message' => 'Recherche correctement effectuée avec filtre'
            ];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [
                'success'=> false,
                'info_covoiturage'=> null, 
                'message'=>'Erreur lors de la recherche avec filtre'
            ];
        }
    }
 

    /**
     * supprime un covoit crée par un conducteur
     * @param PDO $pdo : PDO pour connexion a la bdd.
     * @param int $id_conducteur : Id du conducteur
     * @param int $id_covoiturage :  Id de covoiturage 
     * @return array : ['success' => bool , 'message' => string];
     */
    public static function supprimerCovoiturage (PDO $pdo, int $id_conducteur, int $id_covoiturage): array {
        try{
            //on verifie que le covoiturage existe toujours
                $prep_verif_covoit = $pdo->prepare(
                    "SELECT id_covoiturage
                    FROM covoiturage
                    WHERE id_covoiturage = ? 
                    ");
                $prep_verif_covoit->execute([$id_covoiturage]);
                $resultat_verif_covoit = $prep_verif_covoit->fetch();
                if(!$resultat_verif_covoit){
                    return ['success' => false, 'message' => 'Covoiturage introuvable'];
                }
            //PREPARATION POUR SUPPRIMER LE COVOIT
                $pdo->beginTransaction();
            //ANNULE DES DEUX TABLES POUR LE COVOIT
                $prep_annule_covoiturage = $pdo->prepare(
                    "UPDATE covoiturage
                    SET statut_covoit = ?
                    WHERE id_covoiturage = ?
                    ");
                $prep_annule_covoiturage->execute(['annuler',$id_covoiturage]);

                $prep_annule_reservation = $pdo->prepare(
                    "UPDATE reservation
                    SET statut_reservation = ?
                    WHERE id_covoiturage = ?
                    ");
                $prep_annule_reservation->execute(['annuler',$id_covoiturage]);  
            //ON REDONNE LES CREDITS A CHAQUE UTILISATEUR
                //pour le conducteur
                    $prepare_remboursement_conducteur = $pdo->prepare(
                        "UPDATE utilisateur
                        SET credit = credit + ?
                        WHERE id_utilisateur = ?
                    ");
                    $prepare_remboursement_conducteur->execute([2,$id_conducteur]);
                //maj de commission
                    $prepare_remboursement_commission = $pdo->prepare(
                        "INSERT INTO commission (id_covoiturage,id_conducteur,montant)
                        VALUES (?,?,?)
                        ");
                    $prepare_remboursement_commission->execute([$id_covoiturage,$id_conducteur,-2]);
                //pour chaque passager
                //on prepare les donnees a maj
                    $prepare_liste_passager = $pdo->prepare(
                        "SELECT r.nb_place_reserve,r.id_passager,c.prix_personne
                        FROM reservation r INNER JOIN covoiturage c ON r.id_covoiturage = c.id_covoiturage
                        AND r.id_covoiturage = ?
                        ");
                    $prepare_liste_passager->execute([$id_covoiturage]);
                    $liste_passagers = $prepare_liste_passager->fetchAll();
            //on met a jour le statut du virement et on insere le remboursement
                foreach($liste_passagers as $passager){
                    $id_passager = $passager['id_passager'];
                    $montant = $passager['nb_place_reserve'] * $passager['prix_personne'];
                    $prepare_remboursement_passager = $pdo->prepare(
                        "UPDATE utilisateur
                        SET credit = credit + ? 
                        WHERE id_utilisateur = ?
                        ");
                    $prepare_remboursement_passager->execute([$montant,$passager['id_passager']]);

                    $prep_virement_annuler_passager = $pdo->prepare(
                        "UPDATE virement 
                        SET statut = ?
                        WHERE id_passager = ?
                        AND id_covoiturage = ?
                    ");
                    $prep_virement_annuler_passager->execute(['annuler',$passager['id_passager'],$id_covoiturage]);

                    $prep_virement_passager = $pdo->prepare(
                        "INSERT INTO virement (montant_virement,statut,id_passager,id_conducteur,id_covoiturage)
                        VALUES (?,?,?,?,?)
                        ");
                    $prep_virement_passager->execute([$montant,'remboursement',$id_passager,$id_conducteur,$id_covoiturage]);
                }

            $pdo->commit();

            return ['success' => true , 'message' => 'Covoiturage correctement annulé'];

        } catch (PDOException $e){
            error_log($e->getMessage());
            $pdo->rollBack();
            return ['success'=> false, 'message'=>'Erreur lors de la suppression'];
        }
    }

    /**
     * annule la participation dun passager pour un covoiturage
     * @param PDO $pdo : PDO pour connexion a la bdd.
     * @param int $id_passager : Id du passager
     * @param int $id_covoiturage :  Id de covoiturage 
     * @return array : ['success' => bool , 'message' => string];
     */
    public static function annulerCovoiturage (PDO $pdo, int $id_passager, int $id_covoiturage, ): array {

        try{

            //on verifie que le covoiturage existe toujours
                $prep_verif_covoit = $pdo->prepare(
                    "SELECT id_covoiturage
                    FROM covoiturage
                    WHERE id_covoiturage = ? 
                    ");
                $prep_verif_covoit->execute([$id_covoiturage]);
                $resultat_verif_covoit = $prep_verif_covoit->fetch();
                if(!$resultat_verif_covoit){
                    return ['success' => false, 'message' => 'Covoiturage introuvable'];
                }

            $pdo->beginTransaction();

            //PREPARATION DES DONNEES POUR MAJ
                //on recupere l'id du conducteur pour les updates
                    $prep_id_conducteur = $pdo->prepare(
                        "SELECT id_conducteur 
                        FROM covoiturage 
                        WHERE id_covoiturage = ?
                    ");
                    $prep_id_conducteur->execute([$id_covoiturage]);
                    $id_conducteur = $prep_id_conducteur->fetch();
                //on recupere le nombre de place qui avait ete reserve
                    $prep_nb_place_reservee = $pdo->prepare(
                        "SELECT nb_place_reserve
                        FROM reservation
                        WHERE id_passager = ?
                        AND id_covoiturage = ?
                    ");
                    $prep_nb_place_reservee->execute([$id_passager,$id_covoiturage]);
                    $nb_place_reserve = $prep_nb_place_reservee->fetch();
                //et on recupere le montant a rembourser
                    $prepare_remboursement_passager = $pdo->prepare(
                        "SELECT montant_virement
                        FROM virement
                        WHERE id_passager = ?
                        AND id_covoiturage = ?
                        AND id_conducteur = ?
                    ");
                    $prepare_remboursement_passager->execute([$id_passager,$id_covoiturage,$id_conducteur['id_conducteur']]);
                    $montant_rembourser = $prepare_remboursement_passager->fetch();

            
            //MISE A JOUR DES DONNEES DANS LES TABLES
                //on passe la reservation du passager en annulee
                    $prep_annule_covoiturage = $pdo->prepare(
                        "UPDATE reservation
                        SET statut_reservation = ?
                        WHERE id_covoiturage = ?
                        AND id_passager = ?
                    ");
                    $prep_annule_covoiturage->execute(['annuler',$id_covoiturage,$id_passager]);  

                //ON PROCEDE A TOUT LES VIREMENT ET REMBOURSEMENT
                //1.enrgistrement dans virement du virement de remboursement
                    $prep_montant_remboursement = $pdo->prepare(
                        "INSERT INTO virement (montant_virement,statut,id_passager,id_conducteur,id_covoiturage)
                        VALUES (?,?,?,?,?)          
                    ");
                    $prep_montant_remboursement->execute([$montant_rembourser['montant_virement'],'remboursement',$id_passager,$id_conducteur['id_conducteur'],$id_covoiturage]);
                //2.changement du virement effectué en virement annule
                    $prep_annule_virement = $pdo->prepare(
                        "UPDATE virement 
                        SET statut = ?
                        WHERE id_passager = ?
                        AND id_conducteur = ?
                        AND id_covoiturage = ?
                    ");
                    $prep_annule_virement->execute(['annuler',$id_passager,$id_conducteur['id_conducteur'],$id_covoiturage]);

                //on modifie le covoiturage pour liberer l'espace
                    $prep_nb_place = $pdo->prepare(
                        "UPDATE covoiturage
                        SET nb_place_dispo = nb_place_dispo + ?
                        WHERE id_covoiturage = ?
                    ");
                    $prep_nb_place->execute([$nb_place_reserve['nb_place_reserve'],$id_covoiturage]);

                //on rembourse le passager
                    $prep_remboursement_p = $pdo->prepare(
                        "UPDATE utilisateur
                        SET credit = credit + ?
                        WHERE id_utilisateur = ?
                    ");
                    $prep_remboursement_p->execute([$montant_rembourser['montant_virement'],$id_passager]);
            
            $pdo->commit();

            return ['success' => true, 'message' => 'Covoiturage annulé, Remboursement effectué'];
        
        } catch (PDOException $e){
            error_log($e->getMessage());
            $pdo->rollBack();
            return ['success'=> false, 'message' => 'Erreur lors de la suppression du covoit'];
        }
    }

    
    /**
     * demarre un covoit creer par un conducteur
     * @param PDO $pdo : PDO pour connexion a la bdd.
     * @param int $id_conducteurr : Id du conducteur
     * @param int $id_covoiturage :  Id de covoiturage 
     * @return array : ['success' => bool , 'message' => string]; 
     */
    public static function demarrerCovoiturage (PDO $pdo, int $id_conducteur, int $id_covoiturage): array {
        try{
            //VERIFICATION QUE LE JOUR POUR DEMARRER EST CORRECT

                $prep_demarrage_covoit = $pdo->prepare(
                    "SELECT date_depart, heure_depart
                    FROM covoiturage
                    WHERE id_conducteur = ?
                    AND id_covoiturage = ?
                ");
                $prep_demarrage_covoit->execute([$id_conducteur,$id_covoiturage]);
                if($prep_demarrage_covoit->rowCount()<1){
                    return ['success'=>false, 'message'=>'Covoiturage inexistant'];
                }


                // Forcer le fuseau horaire français
                date_default_timezone_set('Europe/Paris');
                $date_demarrage = $prep_demarrage_covoit->fetch(PDO::FETCH_ASSOC);
                // Créer les DateTime avec le bon fuseau
                $depart_covoit = new DateTime($date_demarrage['date_depart'].' '.$date_demarrage['heure_depart'], new DateTimeZone('Europe/Paris'));                
                $maintenant = new DateTime('now', new DateTimeZone('Europe/Paris'));
                if ($depart_covoit > $maintenant) {
                    return ['success' => false, 'message' => 'Il est trop tôt pour démarrer le covoiturage'];
                }

            //DEMARRAGE DU COVOIT
            //Mis a jour du statut du covoit
                $prep_maj_statut_covoit = $pdo->prepare(
                    "UPDATE covoiturage
                    SET statut_covoit = ?
                    WHERE id_conducteur = ?
                    AND id_covoiturage = ?
                ");
                $prep_maj_statut_covoit->execute(['en_cours',$id_conducteur,$id_covoiturage]);
            
            return ['success' => true , 'message' => 'Covoiturage démarré, Bonne route !'];

        } catch (PDOException $e){
            error_log($e->getMessage());
            return ['success'=> false, 'message' => 'Erreur lors du lancement du covoit'];
        }
    }

    /**
     * permet de valider un covoiturage qui vient davoir lieu
     * @param PDO $pdo : PDO pour connexion a la bdd.
     * @param int $id_conducteurr : Id du conducteur
     * @param int $id_covoiturage :  Id de covoiturage 
     * @return array : ['success' => bool , 'message' => string]; 
     */
    public static function terminerCovoiturage (PDO $pdo, int $id_conducteur, int $id_covoiturage){
        try{
            //VERIFICATION QUE LE COVOIT A BIEN EU LIEU MAINTENANT
                $prep_finir_covoit = $pdo->prepare(
                    "SELECT statut_covoit
                    FROM covoiturage
                    WHERE id_conducteur = ?
                    AND id_covoiturage = ?
                ");
                $prep_finir_covoit->execute([$id_conducteur,$id_covoiturage]);
                $verif_statut = $prep_finir_covoit->fetch();

                if($verif_statut['statut_covoit'] !== 'en_cours'){
                    return ['success'=>false, 'message'=>'Erreur covoiturage statut'];
                }
            //MIS A JOUR DU STATUT DU COVOIT
                $prep_maj_statut_covoit = $pdo->prepare(
                    "UPDATE covoiturage
                    SET statut_covoit = ?
                    WHERE id_conducteur = ?
                    AND id_covoiturage = ?
                ");
                $prep_maj_statut_covoit->execute(['terminer',$id_conducteur,$id_covoiturage]);

            //RECUPERATION DES EMAIL DES PARTICIPANTS POUR LES INVITER A NOTER
                $prep_email = $pdo->prepare(
                    "SELECT email
                    FROM utilisateur u
                    INNER JOIN reservation r ON u.id_utilisateur = r.id_passager
                    WHERE r.id_conducteur = ?
                    AND r.id_covoiturage = ?
                    ");
                $prep_email->execute([$id_conducteur,$id_covoiturage]);
                $email_passager = $prep_email->fetchAll(PDO::FETCH_COLUMN);

            return ['success' => true , 'message' => 'Covoiturage terminer !', 'email' => $email_passager];

        } catch (PDOException $e){
            error_log($e->getMessage());
            $pdo->rollBack();
            return ['success'=> false, 'message' => 'Erreur lors de l\'arriver du covoit'];
        }
    }

    /**
     * permet de donner les avis pour le covoiturage
     * @param PDO $pdo : PDO pour connexion a la bdd.
     * @param int $id_covoiturage :  Id de covoiturage 
     * @param int $id_passager : Id du passager
     * @param array $data : donner en post pour les avis
     * @return array : ['success' => bool , 'message' => string];
     */
    public static function donnerAvis(PDO $pdo, int $id_covoiturage, int $id_passager, array $data){
        try{
            //VERIFICATION DES POST
                $note = $data['note'];
                $commentaire = trim($data['commentaire']);
                //de la note
                if($note<1 || $note>5){
                    return ['success'=>false, 'message'=>'Erreur à la notation'];
                }

            //recuperation de l'id du condcuteur
                $prep_conducteur = $pdo->prepare(
                    "SELECT id_conducteur
                    FROM covoiturage
                    WHERE id_covoiturage = ?
                ");
                $prep_conducteur->execute([$id_covoiturage]);
                $id_conducteur = $prep_conducteur->fetch(PDO::FETCH_ASSOC);

            // Vérification si un avis existe déjà
                $prep_verif = $pdo->prepare(
                    "SELECT COUNT(*) as nb_avis
                    FROM avis
                    WHERE id_passager = ? 
                    AND id_covoiturage = ?
                    AND id_conducteur = ?"
                );
                $prep_verif->execute([$id_passager, $id_covoiturage, $id_conducteur['id_conducteur']]);
                $dejaDonne = $prep_verif->fetch();

            if ($dejaDonne['nb_avis']>0) {
                return ['success' => false, 'message' => 'Vous avez déjà donné un avis pour ce covoiturage'];
            }
                
            //INSERTION DANS LA BDD
            //table avis
                $prep_avis = $pdo->prepare(
                    "INSERT INTO avis (commentaire, note, id_passager, id_conducteur, id_covoiturage)
                    VALUES (?,?,?,?,?)
                ");
                $prep_avis->execute([$commentaire,$note,$id_passager,$id_conducteur['id_conducteur'],$id_covoiturage]);


        } catch (PDOException $e){
            error_log($e->getMessage());
            return ['success'=> false, 'message' => 'Erreur lors de l\'arriver du covoit'];
        }
        


    }

    /**
     * permet de confirmer un covoiturage coté passager pour boucler le covoiturage
     * @param PDO $pdo : PDO pour connexion a la bdd.
     * @param int $id_utilisateur :  Id de utilisateur 
     * @param int $id_covoiturage :  Id de covoiturage 
     * @return array : ['success' => bool , 'message' => string];
     */
    public static function confirmerCovoiturage($pdo, $id_utilisateur, $id_covoiturage) {
        try {
            // Commencer une transaction pour assurer la cohérence
            $pdo->beginTransaction();
            
            // Vérifier que l'utilisateur (passager) a participé à ce covoiturage
            $verif_stmt = $pdo->prepare("
                SELECT r.id_reservation, r.nb_place_reserve, c.id_conducteur, c.statut_covoit
                FROM reservation r
                JOIN covoiturage c ON r.id_covoiturage = c.id_covoiturage
                WHERE r.id_covoiturage = ? AND r.id_passager = ? AND r.statut_reservation = 'active'
            ");
            $verif_stmt->execute([$id_covoiturage, $id_utilisateur]);
            $reservation = $verif_stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$reservation) {
                $pdo->rollBack();
                return [
                    'success' => false,
                    'message' => 'Vous n\'avez pas participé à ce covoiturage ou il a déjà été confirmé'
                ];
            }
            
            // Vérifier que le covoiturage est bien terminé par le conducteur
            if ($reservation['statut_covoit'] !== 'terminer') {
                $pdo->rollBack();
                return [
                    'success' => false,
                    'message' => 'Ce covoiturage n\'a pas encore été terminé par le conducteur'
                ];
            }
            
            // Récupérer le virement en attente pour ce passager et ce covoiturage
            $virement_stmt = $pdo->prepare("
                SELECT id_virement, montant_virement
                FROM virement 
                WHERE id_covoiturage = ? AND id_passager = ? AND id_conducteur = ? AND statut = 'en_attente'
            ");
            $virement_stmt->execute([$id_covoiturage, $id_utilisateur, $reservation['id_conducteur']]);
            $virement = $virement_stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$virement) {
                $pdo->rollBack();
                return [
                    'success' => false,
                    'message' => 'Aucun virement en attente trouvé pour ce covoiturage'
                ];
            }
            
            // 1. Mettre à jour le statut du virement
            $update_virement_stmt = $pdo->prepare("
                UPDATE virement 
                SET statut = 'valider' 
                WHERE id_virement = ?
            ");
            $update_virement_stmt->execute([$virement['id_virement']]);
            
            // 2. Créditer le conducteur
            $update_credit_stmt = $pdo->prepare("
                UPDATE utilisateur 
                SET credit = credit + ? 
                WHERE id_utilisateur = ?
            ");
            $update_credit_stmt->execute([$virement['montant_virement'], $reservation['id_conducteur']]);
            
            // 3. Mettre à jour le statut de la réservation du passager
            $update_reservation_stmt = $pdo->prepare("
                UPDATE reservation 
                SET statut_reservation = 'terminer' 
                WHERE id_reservation = ?
            ");
            $update_reservation_stmt->execute([$reservation['id_reservation']]);
            
            // Valider la transaction
            $pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Covoiturage confirmé avec succès ! Le conducteur a reçu ses crédits.'
            ];
            
        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $pdo->rollBack();
            error_log("Erreur lors de la confirmation du covoiturage: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur technique lors de la confirmation'
            ];
        }
    }

}
