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
            <h1>Connexion</h1>
            <form action="donnees_connexion.php" method="POST">
                <label for="pseudo">Pseudo :</label>
                <input type="text" id="pseudo" name="pseudo" required>

                <label for="password">Mot de Passe :</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Se Connecter</button>
            </form>
        </section>
    </main>

    <?php include 'includes/footer.php' ?>
</body>
</html>