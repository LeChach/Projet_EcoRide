<?php
require_once 'connexion/log.php';
require_once 'connexion/session.php';

$message_envoye = false;
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Vérifications simples
    if ($nom === '') $erreurs[] = "Le nom est obligatoire.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "L'email n'est pas valide.";
    if ($sujet === '') $erreurs[] = "Le sujet est obligatoire.";
    if ($message === '') $erreurs[] = "Le message est obligatoire.";

    // Si tout est ok
    if (empty($erreurs)) {
        $to = "support@ecoride.fr";
        $headers = "From: $nom <$email>\r\nReply-To: $email";
        $body = "Nom: $nom\nEmail: $email\n\nMessage:\n$message";

        if (mail($to, $sujet, $body, $headers)) {
            $message_envoye = true;
        } else {
            $erreurs[] = "Une erreur est survenue lors de l'envoi du message. Réessaie plus tard.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - EcoRide</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <?php include 'includes/header.php' ?>

    <div class="contact-container">
        <h1>Contactez-nous</h1>

        <?php if ($message_envoye): ?>
            <div class="message-success">
                Merci <?= htmlspecialchars($nom) ?>, votre message a été envoyé avec succès !
            </div>
            
            <!-- Section informations de contact optionnelle -->
            <div class="contact-info">
                <h3>Nous vous répondrons rapidement !</h3>
                <p>Notre équipe s'engage à répondre à votre message dans les 24h.</p>
                <p>Vous pouvez aussi nous joindre directement :</p>
                <p>📧 <a href="mailto:support@ecoride.fr">support@ecoride.fr</a></p>
                <p>📞 <a href="tel:+33123456789">01 23 45 67 89</a></p>
            </div>
            
        <?php else: ?>
            
            <?php if (!empty($erreurs)): ?>
                <ul class="error-list">
                    <?php foreach ($erreurs as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form class="contact-form" method="post" action="contact.php">
                <div class="form-group">
                    <label for="nom">Nom complet :</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>" placeholder="Votre nom et prénom">
                </div>

                <div class="form-group">
                    <label for="email">Adresse email :</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" placeholder="votre.email@exemple.fr">
                </div>

                <div class="form-group">
                    <label for="sujet">Sujet de votre message :</label>
                    <input type="text" id="sujet" name="sujet" value="<?= htmlspecialchars($sujet ?? '') ?>" placeholder="De quoi souhaitez-vous nous parler ?">
                </div>

                <div class="form-group">
                    <label for="message">Votre message :</label>
                    <textarea id="message" name="message" placeholder="Décrivez-nous votre demande, suggestion ou problème..."><?= htmlspecialchars($message ?? '') ?></textarea>
                </div>

                <button type="submit">Envoyer le message</button>
            </form>

            <!-- Section informations de contact -->
            <div class="contact-info">
                <h3>Autres moyens de nous contacter</h3>
                <p>📧 Email : <a href="mailto:support@ecoride.fr">support@ecoride.fr</a></p>
                <p>📞 Téléphone : <a href="tel:+33123456789">01 23 45 67 89</a></p>
                <p>🕐 Du lundi au vendredi, de 9h à 18h</p>
            </div>
            
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php' ?>
</body>
</html>