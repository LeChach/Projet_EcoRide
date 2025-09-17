<?php
require_once 'connexion/log.php';
require_once 'connexion/session.php';
require_once 'classes/Connexion.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $inscription = Connexion::inscriptionMonCompte($pdo,$_POST);
    if($inscription['success']){
        $_SESSION['id_utilisateur'] = $inscription['id_utilisateur'];
        header('Location: mon_compte.php');
    }else{
        $_SESSION['erreur_inscription'] = $inscription['message'];
        header('Location: inscription.php');
    }
}
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Eco Ride</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php' ?>

    <main>
        <div class="auth-container">
            <h1>Inscription</h1>
            
            <!-- Message d'erreur -->
            <?php if (isset($erreur_inscription)): ?>
                <div class="message message-error"><?= htmlspecialchars($erreur_inscription) ?></div>
            <?php endif; ?>

            <!-- Formulaire d'inscription -->
            <form method="POST" class="auth-form">
                
                <!-- Sexe -->
                <fieldset class="auth-fieldset">
                    <legend>Je suis</legend>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="homme" name="sexe" value="Homme" required>
                            <label for="homme">un Homme</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="femme" name="sexe" value="Femme" required>
                            <label for="femme">une Femme</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="non_precise" name="sexe" value="Non précisé" required>
                            <label for="non_precise">Je préfère ne pas préciser</label>
                        </div>
                    </div>
                </fieldset>

                <!-- Informations personnelles -->
                <div class="auth-form-group">
                    <label for="pseudo">Pseudo</label>
                    <input type="text" id="pseudo" name="pseudo" placeholder="Votre pseudo unique" required>
                </div>

                <div class="auth-form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" placeholder="votre.email@exemple.com" required>
                </div>

                <div class="auth-form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required
                           pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}"
                           title="Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial"
                           placeholder="Au moins 8 caractères avec majuscule, chiffre...">
                </div>

                <div class="auth-form-group">
                    <label for="phone">Numéro de téléphone</label>
                    <input type="tel" id="phone" name="phone" placeholder="06 12 34 56 78" required>
                </div>

                <!-- Type d'utilisateur -->
                <fieldset class="auth-fieldset">
                    <legend>Vous serez</legend>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="passager" name="type_utilisateur" value="Passager" required>
                            <label for="passager">Covoituré (je cherche des trajets)</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="conducteur" name="type_utilisateur" value="Conducteur" required>
                            <label for="conducteur">Covoitureur (je propose des trajets)</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="les_deux" name="type_utilisateur" value="Passager et Conducteur" required>
                            <label for="les_deux">Les deux (je cherche et je propose des trajets)</label>
                        </div>
                    </div>
                </fieldset>

                <button type="submit" class="btn btn-primary btn-large">Terminer mon inscription</button>
            </form>

            <!-- Section redirection vers connexion -->
            <div class="auth-redirect">
                <p>Vous avez déjà un compte ?</p>
                <a href="connexion.php" class="btn btn-secondary">Se connecter</a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php' ?>
</body>
</html>