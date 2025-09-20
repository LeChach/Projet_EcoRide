<?php
require_once 'config/database.php';
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

    <main>
        <div class="validation-container">
            <h1>Validation des avis</h1>
            
            <?php foreach($avis_attente['avis'] as $avis): ?>
                <div class="avis_attente">
                    <div class="entete">
                        <p>Passager: <?= htmlspecialchars($avis['passager']) ?></p>
                        <p>Conducteur: <?= htmlspecialchars($avis['conducteur']) ?></p>
                        <span>(Covoiturage : le <?= htmlspecialchars(date('d/m/Y', strtotime($avis['date_depart'] ?? ''))) ?>)</span>
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

            <div class="close-section">
                <a href="mon_compte.php" class="close-button">Fermer</a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php' ?>
</body>
</html>
