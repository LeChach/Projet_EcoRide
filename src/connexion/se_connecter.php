<?php
require_once 'log.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pseudo = htmlspecialchars($_POST['pseudo'], ENT_QUOTES, 'UTF-8'); //converti automatiquement en full caractere
    $mot_de_passe = $_POST['password']; //converti automatiquement en full caractere

    try {
        //PREPARATION DE LA REQUETE
        $pdo_prep = $pdo->prepare("SELECT * from utilisateur WHERE pseudo = ?"); //prepare l'info a mettre a la place de ?
        $pdo_prep->execute([$pseudo]); //execute en remplacent ? par $pseudo
        $user = $pdo_prep->fetch();

        if($user && password_verify($mot_de_passe,$user['mot_de_passe'])){
            $_SESSION['user_id'] = $user['id_utilisateur'];
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