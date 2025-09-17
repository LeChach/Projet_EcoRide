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
        <div class="auth-container">
            <h1>Connexion</h1>
            
            <!-- Message d'erreur -->
            <?php if ($erreur_connexion): ?>
                <div class="message message-error"><?= htmlspecialchars($erreur_connexion) ?></div>
            <?php endif; ?>

            <!-- Formulaire de connexion -->
            <form method="POST" class="auth-form">
                <div class="auth-form-group">
                    <label for="identifiant">Pseudo ou Email</label>
                    <input type="text" id="identifiant" name="identifiant" placeholder="Votre pseudo ou email" required>
                </div>

                <div class="auth-form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Votre mot de passe" required>
                </div>

                <button type="submit" class="btn btn-primary btn-large">Se connecter</button>
            </form>

            <!-- Section redirection vers inscription -->
            <div class="auth-redirect">
                <p>Vous n'avez pas encore de compte ?</p>
                <a href="inscription.php" class="btn btn-secondary">Créer un compte</a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php' ?>
</body>
</html>