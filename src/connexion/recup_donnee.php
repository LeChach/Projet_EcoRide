<?php
require_once 'log.php';
require_once 'session.php';
try {

    //prep pour la table Utilisateur
    $prep = $pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
    $prep->execute([$user_id]);
    $user_info = $prep->fetch();

    if($user_info){
        $pseudo = $user_info['pseudo'];
        $email = $user_info['email'];
        $telephone = $user_info['telephone'];
        $photo = "assets/pp/" . $user_info['photo'];
        $credit = $user_info['credit'];
        $type_u = $user_info['type_utilisateur'];
    }

} catch (PDOException $e) {
    die("Erreur de BBD : ".$e->getMessage());
}

?>
