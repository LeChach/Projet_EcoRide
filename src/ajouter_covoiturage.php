<?php
    require_once 'connexion/log.php';
    require_once 'connexion/session_prive.php';
    require_once 'connexion/recup_donnee.php';
    require_once 'classes/Covoiturage.php';

    $erreur_ajout_covoiturage = null;

    // Pré-remplissage
    $values = [
        'date_depart' => '',
        'heure_depart' => '',
        'min_depart' => '',
        'duree_voyage_heure' => '',
        'duree_voyage_min' => '',
        'lieu_depart' => '',
        'lieu_arrive' => '',
        'prix_personne' => '',
        'id_voiture' => ''
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $values = array_merge($values, $_POST);
        
        $result = Covoiturage::creationCovoiturage($pdo, $id_utilisateur, $_POST);

        if ($result['success']) {
            header("Location: mon_compte.php?msg=" . urlencode($result['message']));
            exit;
        } else {
            $erreur_ajout_covoiturage = $result['message'];
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
</head>
<body>

<?php include 'includes/header.php'; ?>

<form method="POST">
    <fieldset>
        <legend>Ajoutez un nouveau covoiturage</legend>

        <?php if (!empty($erreur_ajout_covoiturage)): ?>
            <p style="color:red;"><?= htmlspecialchars($erreur_ajout_covoiturage) ?></p>
        <?php endif; ?>

        <label for="date_depart">Date de départ :</label>
        <input type="date" name="date_depart" id="date_auj" value="<?= htmlspecialchars($values['date_depart']) ?>" required><br>

        <label>Heure de départ :</label>
        <input type="number" name="heure_depart" min="0" max="23" value="<?= htmlspecialchars($values['heure_depart']) ?>" required> H
        <input type="number" name="min_depart" min="0" max="59" value="<?= htmlspecialchars($values['min_depart']) ?>" required> min<br>

        <label>Durée du voyage :</label>
        <input type="number" name="duree_voyage_heure" min="0" value="<?= htmlspecialchars($values['duree_voyage_heure']) ?>" required> H
        <input type="number" name="duree_voyage_min" min="0" max="59" value="<?= htmlspecialchars($values['duree_voyage_min']) ?>" required> min<br>

        <label for="lieu_depart">Lieu de départ :</label>
        <input type="text" name="lieu_depart" value="<?= htmlspecialchars($values['lieu_depart']) ?>" required><br>

        <label for="lieu_arrive">Lieu d'arrivée :</label>
        <input type="text" name="lieu_arrive" value="<?= htmlspecialchars($values['lieu_arrive']) ?>" required><br>

        <label for="prix_personne">Prix par personne :</label>
        <input type="number" name="prix_personne" min="1" value="<?= htmlspecialchars($values['prix_personne']) ?>" required> €<br>

        <label for="id_voiture">Votre voiture :</label>
        <?php if (empty($voitures_utilisateur)): ?>
            <p><a href="ajouter_voiture.php">Ajoutez une voiture</a></p>
        <?php else: ?>
            <select name="id_voiture" id="id_voiture" required>
                <?php foreach ($voitures_utilisateur as $voiture): ?>
                    <option value="<?= $voiture['id_voiture'] ?>" <?= $values['id_voiture'] == $voiture['id_voiture'] ? 'selected' : '' ?>>
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

<script>
    document.getElementById('date_auj').value = '<?= htmlspecialchars($values['date_depart'] ?: date('Y-m-d')) ?>';
</script>

</body>
</html>
