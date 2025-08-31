<?php 
require_once 'log.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $sexe = $_POST['sexe'];
    $pseudo = ($_POST['pseudo']);
    $mot_de_passe = $_POST['password'];
    $email = trim($_POST['email']);
    $telephone = trim($_POST['phone']);
    $type_u = $_POST['type_utilisateur'];

    //permet de verifier l'enum 

    try {
        //Verification si mail et pseudo déjà pris        
        $verif_mail = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
        $verif_mail->execute([$email]);
        $verif_pseudo = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE pseudo = ?");
        $verif_pseudo->execute([$pseudo]);
 
        if($verif_pseudo->rowCount()>0){
            $_SESSION['erreur_inscription'] = "Pseudo déjà existant, Veuillez en séléctionner un autre";
            header("Location: ../inscription.php");
            exit;
        }
        if ($verif_mail->rowCount()>0){
            $_SESSION['erreur_inscription'] = "Email déjà existant, Veuillez en séléctionner un autre";
            header("Location: ../inscription.php");
            exit;
        }
        
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
            "INSERT INTO preference (id_utilisateur)
            VALUES (?)"
        );
        $prep_preference->execute([$id_utilisateur]);

        //PREPARATION POUR POSSEDE
        $prep_possede = $pdo->prepare(
            "INSERT INTO possede (id_utilisateur,id_role)
            VALUES (?,1)"
        );
        $prep_possede->execute([$id_utilisateur]);

        $pdo->commit();
        $_SESSION['id_utilisateur'] = $id_utilisateur;
        header("Location: ../mon_compte.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['erreur_inscription'] = "Echec de l'inscription, Veuillez réessayer";
        header("Location: ../inscription.php");
            exit;

    }
}
?>