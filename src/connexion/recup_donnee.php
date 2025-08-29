<?php
session_start();
require_once 'connexion/log.php';
if(!isset($_SESSION['user_id'])){
    header('Location: connexion.php');
    exit;
}

//recup des info pour un affichage perso
$user_id = $_SESSION['user_id'];
try {

    //prep pour la table user
    $prep = $pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
    $prep->execute([$user_id]);
    $user_info = $prep->fetch();

    if($user_info){
        $pseudo = $user_info['pseudo'];
        $email = $user_info['email'];
        $telephone = $user_info['telephone'];
        $photo = "assets/pp/".$user_info['photo'];
        $credit = $user_info['credit'];
        $type_u = $user_info['type_utilisateur'];
    }

} catch (PDOException $e) {
    die("Erreur de BBD : ".$e->getMessage());
}


?>
