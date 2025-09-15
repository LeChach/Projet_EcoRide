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
        $to = "support@ecoride.fr"; // 👉 change avec l'adresse de ton entreprise
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
    <title>Contact - EcoRide</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <?php include 'includes/header.php' ?>
    <h1>Contactez-nous</h1>

    <?php if ($message_envoye): ?>
        <p style="color:green;">✅ Merci <?= htmlspecialchars($nom) ?>, votre message a été envoyé avec succès !</p>
    <?php else: ?>
        <?php if (!empty($erreurs)): ?>
            <ul style="color:red;">
                <?php foreach ($erreurs as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post" action="contact.php">
            <label for="nom">Nom :</label><br>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>"><br><br>

            <label for="email">Email :</label><br>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>"><br><br>

            <label for="sujet">Sujet :</label><br>
            <input type="text" id="sujet" name="sujet" value="<?= htmlspecialchars($sujet ?? '') ?>"><br><br>

            <label for="message">Message :</label><br>
            <textarea id="message" name="message" rows="6"><?= htmlspecialchars($message ?? '') ?></textarea><br><br>

            <button type="submit">Envoyer</button>
        </form>
    <?php endif; ?>

    <?php include 'includes/footer.php' ?>
</body>
</html>
