<?php
require_once 'connexion/log.php';
require_once 'connexion/session.php';
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
        <section class="connexion">

            <h1>S'inscrire</h1>

            <form action="connexion/s_inscrire.php" method="POST">

                <fieldset>
                    <legend>Je suis :</legend>

                    <input type="radio" name="sexe" value="Homme" required>
                    <label for="Homme">un Homme</label><br>
                    
                    <input type="radio" name="sexe" value="Femme" required>
                    <label for="Femme">une Femme</label><br>

                    <input type="radio" name="sexe" value="Non précisé" required>
                    <label for="Non précisé">Je préfère ne pas préciser</label><br>

                </fieldset>            


                <label for="pseudo">Pseudo :</label>
                <input type="text" id="pseudo" name="pseudo" required>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Mot de Passe :</label>
                <input type="password" id="password" name="password" required>

                <label for="phone">Téléphone :</label>
                <input type="tel" id="phone" name="phone" required>

                <fieldset>
                    <legend>Vous serez :</legend>

                    <input type="radio" name="type_utilisateur" value="Passager" required>
                    <label for="covoiture">Covoituré (je cherche des trajets)</label><br>
                    
                    <input type="radio" name="type_utilisateur" value="Conducteur" required>
                    <label for="covoitureur">Covoitureur (je propose des trajets)</label><br>

                    <input type="radio" name="type_utilisateur" value="Passager et Conducteur" required>
                    <label for="les_deux">les deux (je cherche et je propose des trajets)</label><br>

                </fieldset>

                <button type="submit">Terminer mon inscription</button>

            </form>
        </section>
    </main>


    <?php if ($erreur_inscription): ?>
        <p style="color:red;"><?= $erreur_inscription ?></p>
    <?php endif; ?>
    

    <?php include 'includes/footer.php' ?>

</body>
</html>