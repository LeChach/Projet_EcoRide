<?php

class Connexion {

    /**
     * Permet de s'inscrire
     * @param PDO $pdo lien avec la bdd
     * @param array $array liste de donnée du formulaire
     * @return array ['success' => bool, 'message' => '', 'id_utilisateur => ''];

     */ 
    public static function inscriptionMonCompte (PDO $pdo, array $data): array {

        try{

            $sexe = $data['sexe'];
            $pseudo = $data['pseudo'] ?? '';
            $mot_de_passe = $data['password'] ?? '';
            $email = trim($data['email']) ?? '';
            $telephone = trim($data['phone']) ?? '';
            $type_u = $data['type_utilisateur'];

            //gestion de la preference ladies_only non null si nouvelle utilisateur = femme
            if($sexe == 'Femme'){
                $pref_ladies = 'refuser';
            }else{
                $pref_ladies = 'non concerne';
            }

            //VERIFICATION si le mail et le pseudo ne sont pas deja pris
            $verif_mail = $pdo->prepare(
            "SELECT id_utilisateur 
            FROM utilisateur 
            WHERE email = ?"
            );
            $verif_mail->execute([$email]);
            $verif_pseudo = $pdo->prepare(
            "SELECT id_utilisateur 
            FROM utilisateur 
            WHERE pseudo = ?"
            );
            $verif_pseudo->execute([$pseudo]);
            if($verif_pseudo->rowCount()>0){
                return ['success' => false, 'message' => 'Pseudo déjà utilisé, Veuillez en prendre un autre'];
            }
            if ($verif_mail->rowCount()>0){
                return ['success' => false, 'message' => 'Email déjà utilisé, Veuillez en prendre un autre'];
            }

            //VERIFICATION si le mdp est suffisament robuste
            if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $mot_de_passe)) {
                return [
                    'success' => false,
                    'message' => 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.'
                ];
            }

            //INSERTION DES DONNEES
            //démarre une transaction pour valider toute les requete et empeche linscription si une table na pas eu de insert into
            $pdo->beginTransaction();
            //besoin de hacher le mdp pour secu++
            $mot_de_passe_hshd = password_hash($mot_de_passe,PASSWORD_DEFAULT);

            //PREPARATION POUR UTILISATEUR
            $prep_utilisateur = $pdo->prepare(
            "INSERT INTO utilisateur (pseudo,email,mot_de_passe,sexe,telephone,type_utilisateur)
            VALUES (?,?,?,?,?,?)"
            );
            $prep_utilisateur->execute([$pseudo,$email,$mot_de_passe_hshd,$sexe,$telephone,$type_u]);
            $id_utilisateur = $pdo->lastInsertId();

            //PREPARATION POUR PREFERENCE
            $prep_preference = $pdo->prepare(
                "INSERT INTO preference (id_utilisateur,ladies_only)
                VALUES (?,?)"
            );
            $prep_preference->execute([$id_utilisateur,$pref_ladies]);

            //PREPARATION POUR POSSEDE
            $prep_possede = $pdo->prepare(
                "INSERT INTO possede (id_utilisateur,id_role)
                VALUES (?,1)"
            );
            $prep_possede->execute([$id_utilisateur]);


            $pdo->commit();

            return ['success' => true, 'message' => 'Création de compte Valide', 'id_utilisateur' => $id_utilisateur];

        } catch (PDOException $e){
            $pdo->rollBack();
            return ['success' => false , 'message' => 'Création de compte non Valide', 'id_utilisateur' => null];
        }

    }

    /**
     * Permet de se connecter 
     * @param PDO $pdo lien avec la bdd
     * @param array $array liste de donnée du formulaire
     * @return array ['success' => bool, 'message' => '', 'id_utilisateur => ''];
     */
    public static function connexionMonCompte (PDO $pdo, array $data): array {

        try {

            $identifiant = htmlspecialchars($data['identifiant'] ?? '', ENT_QUOTES, 'UTF-8');
            $mot_de_passe = $data['mot_de_passe'] ?? '';

            //VERIFICATION DES DONNEES
            $prep_connexion = $pdo->prepare(
            "SELECT id_utilisateur, pseudo, email, mot_de_passe 
            FROM utilisateur 
            WHERE pseudo = ? 
            OR email = ?"
            );
            $prep_connexion->execute([$identifiant,$identifiant]);
            $info_utilisateur = $prep_connexion->fetch(PDO::FETCH_ASSOC);

            if($info_utilisateur && password_verify($mot_de_passe,$info_utilisateur['mot_de_passe'])){
                return ['success' => true, 'message' => 'Connexion réussi', 'id_utilisateur' => $info_utilisateur['id_utilisateur']];
            }else{
                return ['success' => false, 'message' => 'Echec de connexion', 'id_utilisateur' => null]; 
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Echec de connexion'];
        }
        
    }
}
?>