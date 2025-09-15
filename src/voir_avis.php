<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';
require_once 'classes/MonCompte.php';

$id_covoiturage = $_GET['id_c']??null;
if($id_covoiturage === null){
    $_SESSION['erreur_avis'] = 'Covoiturage non trouvé';
    header("Location: mon_compte.php");
    exit;
}

$avis = MonCompte::voirAvis($pdo,$id_utilisateur,$id_covoiturage);

?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donnez votre avis - Eco Ride</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    

</head>

<body>

    <?php include 'includes/header.php' ?>

    <div class = "div_avis">
        <?php if (empty($avis['avis'])) :?>
            <span>Aucun avis trouvé</span>
        <?php else : ?>

        <?php foreach($avis['avis'] as $avis_passager): ?>
            <div class="avis">
                <div class = "entete_avis"> 
                    <?=htmlspecialchars($avis_passager['pseudo'])?>
                    <img src="assets/pp/<?=htmlspecialchars($avis_passager['photo'])?>" alt="photo de profil">
                </div>
                <div class="note">
                    <?=htmlspecialchars($avis_passager['note'])?>
                    <?php switch ($avis_passager['note']){
                        case 1:
                            $notation = 'Très mauvais';
                            break;
                        case 2:
                            $notation = 'Mauvais';
                            break;
                        case 3:
                            $notation = 'Correct';
                            break;
                        case 4:
                            $notation = 'Bon';
                            break;
                        case 5:
                            $notation = 'Excellent';
                            break;
                    }?>
                    <?=htmlspecialchars($notation)?>                   
                    <?php if(empty($avis_passager['commentaire'])) :?>
                        <span>Pas de commentaire</span>
                    <?php endif;?>
                </div>
            </div>
        <?php endforeach;?>
        <?php endif;?>

        <a href="mon_compte.php">fermer</a>

    </div>

    <?php include 'includes/footer.php' ?>

</body>
</html>
