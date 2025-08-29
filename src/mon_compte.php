<?php
require_once 'connexion/log.php';
require_once 'connexion/session.php';
require_once 'connexion/recup_donnee.php';
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Eco Ride</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>


<body>
    <?php include 'includes/header.php' ?>

    <main>
        <div>
            <h1>Bienvenue <?= htmlspecialchars($pseudo)?>!</h1>

            <img src="<?= htmlspecialchars($photo)?>" alt="avatar profil">

            <div class="info">
                <p>
                    Email :<?= htmlspecialchars($email)?>
                    Téléphone :<?= htmlspecialchars($telephone)?>
                    Solde du crédit :<?= htmlspecialchars($credit)?>
                    Vous êtes : <?= htmlspecialchars($type_u)?>
                </p>


            </div>
                <form action="connexion/deconnexion.php">
                    <button type="submit">se déconnecter</button>
                </form>
            </div>

            <div id="preference">
                <a href="preference.php">Mes Préférences</a>
            </div>

        </div>

        </div>

    </main>

    <?php include 'includes/footer.php' ?>
</body>
</html>