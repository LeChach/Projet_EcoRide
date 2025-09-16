<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';
require_once 'classes/MonCompte.php';

$avis_attente = MonCompte::chargerAvis($pdo,$id_utilisateur);
if(!$avis_attente['success']){
    $_SESSION['erreur'] = $avis_attente['message'];
    header("Location: mon_compte.php");
    exit;
}

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

        <?php foreach($avis_attente['avis'] as $avis): ?>
            <div class="avis_attente">
                <div class="entete">
                    <strong><?= htmlspecialchars($avis['pseudo']) ?></strong>
                    <span>(Covoiturage : le <?= htmlspecialchars($avis['date_depart']) ?>)</span>
                </div>
                <div class="contenu">
                    <p>Note : <?= htmlspecialchars($avis['note']) ?>/5</p>
                    <p>Commentaire : <?= htmlspecialchars($avis['commentaire']) ?></p>
                </div>
                <form method="POST" action="mon_compte.php">
                    <input type="hidden" name="id_avis" value="<?= $avis['id_avis'] ?>">
                    <input type="hidden" name="type_POST" value="valider_avis">
                    <button type="submit" name="validation" value="valider">Valider</button>
                    <button type="submit" name="validation" value="refuser">Refuser</button>
                </form>
            </div>
        <?php endforeach; ?>


    <?php include 'includes/footer.php' ?>


</body>
</html>
