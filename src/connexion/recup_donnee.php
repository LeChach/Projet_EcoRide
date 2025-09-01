<?php
require_once 'log.php';
require_once 'session_prive.php';
try {

    //prep pour la table Utilisateur
    $prep = $pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
    $prep->execute([$id_utilisateur]);
    $info_utilisateur = $prep->fetch();

    if($info_utilisateur){
        $pseudo = $info_utilisateur['pseudo'];
        $email = $info_utilisateur['email'];
        $telephone = $info_utilisateur['telephone'];
        $photo = "assets/pp/".$info_utilisateur['photo'];
        $credit = $info_utilisateur['credit'];
        $type_u = $info_utilisateur['type_utilisateur'];
    }

} catch (PDOException $e) {
    die("Erreur de BBD : ".$e->getMessage());
}

?>
