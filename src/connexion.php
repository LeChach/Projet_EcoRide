<?php
require_once 'connexion/log.php';
require_once 'connexion/session.php';
require_once 'classes/Connexion.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $connexion = Connexion::connexionMonCompte($pdo, $_POST);
    if($connexion['success']){
        $_SESSION['id_utilisateur'] = $connexion['id_utilisateur'];
        header('Location: mon_compte.php');
    }else{
        $_SESSION['erreur_connexion'] = $connexion['message'];
        header('Location: connexion.php');
    }
}

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
            <h1>Connexion</h1>
            <form method="POST">
                
                <label for="identifiant">Pseudo ou Email :</label>
                <input type="text" id="identifiant" name="identifiant" required>

                <label for="mot_de_passe">Mot de Passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>

                <button type="submit">Se Connecter</button>
            </form>
        </section>

        <div class="div_inscription">
            <p>Vous n'avez pas de compte ?</p>
            <a href="inscription.php" class="bouton_inscription">S'inscrire</a>
        </div>
    </main>

    <?php if ($erreur_connexion): ?>
        <p style="color:red;"><?= htmlspecialchars($erreur_connexion) ?></p>
    <?php endif; ?>

    <?php include 'includes/footer.php' ?>

</body>
</html>