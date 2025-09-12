<?php
    require_once 'connexion/log.php';
    require_once 'connexion/session_prive.php';
    require_once 'classes/Covoiturage.php';
    require_once 'classes/MonCompte.php';

    //besoin de récupérer la liste de voiture de la personne pour les afficher
    $info_utilisateur = MonCompte::recupDonnee($pdo,$id_utilisateur);
    if(!$info_utilisateur['success']){
        $_SESSION['erreur_connexion'] = $info_utilisateur['message'];
        header('location: connexion.php');
        exit;
    }

    $voitures_utilisateur = $info_utilisateur['info_voiture'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $result = Covoiturage::creationCovoiturage($pdo, $id_utilisateur, $_POST);
        if ($result['success']) {
            header("Location: mon_compte.php");
            exit;
        } else {
            $_SESSION['erreur_ajout_voiture'] = $result['message'];
            header("Location: ajouter_covoiturage.php");
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajoutez Covoiturage - Eco Ride</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>

    <?php include 'includes/header.php'; ?>

    <?php if ($erreur_ajout_voiture): ?>
        <p style="color:red;"><?= htmlspecialchars($erreur_ajout_voiture) ?></p>
    <?php endif; ?>

    <form method="POST">
        <fieldset>
            <legend>Ajoutez un nouveau covoiturage</legend>

            <label for="date_depart">Date de départ :</label>
            <input type="date" name="date_depart" id="date_auj" value="<?php echo (new DateTime())->format('Y-m-d'); ?>" required><br>

            <label>Heure de départ :</label>
            <input type="number" name="heure_depart" min="0" max="23" required> H
            <input type="number" name="min_depart" min="0" max="59" required> min<br>

            <label>Durée du voyage :</label>
            <input type="number" name="duree_voyage_heure" min="0" required> H
            <input type="number" name="duree_voyage_min" min="0" max="59" required> min<br>

            <label for="lieu_depart">Lieu de départ :</label>
            <input type="text" name="lieu_depart" required><br>

            <label for="lieu_arrive">Lieu d'arrivée :</label>
            <input type="text" name="lieu_arrive" required><br>

            <label for="prix_personne">Prix par personne :</label>
            <input type="number" name="prix_personne" min="1" required> €<br>

            <label for="id_voiture">Votre voiture :</label>
            <?php if (empty($voitures_utilisateur)): ?>
                <p><a href="ajouter_voiture.php">Ajoutez une voiture</a></p>
            <?php else: ?>
                <select name="id_voiture" id="id_voiture" required>
                    <?php foreach ($voitures_utilisateur as $voiture): ?>
                        <option value="<?= $voiture['id_voiture'] ?>">
                            <?= htmlspecialchars($voiture['marque']) ?> - <?= htmlspecialchars($voiture['modele']) ?> (<?= $voiture['nb_place'] ?> places)
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <br>

            <button type="submit">Confirmer mon covoiturage</button>

        </fieldset>
    </form>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
