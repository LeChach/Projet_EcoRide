<?php
require_once 'log.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $identifiant = htmlspecialchars($_POST['identifiant'] ?? '', ENT_QUOTES, 'UTF-8');
    $mot_de_passe = $_POST['password'] ?? '';

    try {

        //PREPARATION DE LA REQUETE
        $prep_connexion = $pdo->prepare(
            "SELECT id_utilisateur, pseudo, email, mot_de_passe 
            FROM utilisateur 
            WHERE pseudo = ? 
            OR email = ?"
            );
            
        $prep_connexion->execute([$identifiant,$identifiant]);
        $info_utilisateur = $prep_connexion->fetch();

        if($info_utilisateur && password_verify($mot_de_passe,$info_utilisateur['mot_de_passe'])){
            $_SESSION['id_utilisateur'] = $info_utilisateur['id_utilisateur'];
            header("Location: ../mon_compte.php");
            exit;
        } else {

            $_SESSION['erreur_login'] = "pseudo ou mot de passe incorrect";
            header("Location: ../connexion.php");
            exit;
        }

    } catch (PDOException $e) {
        die("Erreur connexion BDD : " . $e->getMessage());
    }

}

?>