<?php 
require_once 'log.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $pseudo = trim($_POST['pseudo']);
    $mot_de_passe = $_POST['password'];
    $email = trim($_POST['email']);
    $telephone = trim($_POST['phone']);

    //permet de verifier l'enum 
    $type_u = $_POST['type_utilisateur'];
    $valeur_type_u = ['passager','chauffeur','passager et conducteur'];
    if (!in_array($type_u, $valeur_type_u)) {
        $_SESSION['erreur_inscription'] = "Type d'utilisateur invalide !";
        header("Location: ../inscription.php");
        exit;
    }

    try {
        //verif si mail disponible
        $verif_mail = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
        $verif_mail->execute([$email]);

        //si une ligne trouve dans la bdd avec ce mail
        if ($verif_mail->rowCount()>0){
            die ("email déjà existant");
        }else{
            //besoin de hacher le mdp pour secu++
            $mot_de_passe_hshd = password_hash($mot_de_passe,PASSWORD_DEFAULT);
            $prep = $pdo->prepare(
                "INSERT INTO utilisateur (pseudo,email,mot_de_passe,telephone,type_utilisateur)
                VALUES (?,?,?,?,?)"
                );
        
            if($prep->execute([$pseudo,$email,$mot_de_passe_hshd,$telephone,$type_u])){
                $_SESSION['user_id'] = $pdo->lastInsertId();
                header("Location: ../mon_compte.php");
                exit;
            }else{
                $_SESSION['erreur_inscription'] = "echec de l'inscription";
                header("Location: ../inscription.php");
                exit;
            }
        }
    } catch (PDOException $e) {
        die("Erreur connexion BDD : ".$e->getMessage());
    }
}
?>