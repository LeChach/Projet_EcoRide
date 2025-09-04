<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';
require_once 'connexion/recup_donnee.php';
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

    <?php include 'includes/header.php' ?>

    <form action="connexion/new_covoiturage.php" method="POST">
        <fieldset>
            <legend>Ajoutez un nouveau covoiturage</legend>

            <?php if ($erreur_ajout_covoiturage): ?>
              <p style="color:red;"><?= $erreur_ajout_covoiturage ?></p>
            <?php endif; ?>

            <label for="date_depart"> Veuillez sélectionner une date de départ :</label>
            <input type="date" name="date_depart" id="date_auj" value="" required><br>

            <label for="heure_depart"> Veuillez sélectionner une heure de départ :</label><br>
            <input type="text" name="heure_depart" required>  H  <input type="text" name="minute_depart" required>  min  <br>

            <label for="duree_voyage"> Veuillez sélectionner une durée de voyage :</label><br>
            <input type="text" name="duree_voyage_heure"required>  H  <input type="text" name="duree_voyage_min" required>  min  <br>

            <label for="lieu_depart">Veuillez sélectionner un lieu de départ :</label>
            <input type="text" name="lieu_depart" required><br>

            <label for="lieu_arrive">Veuillez sélectionner un lieu de d'arrivé :</label>
            <input type="text" name="lieu_arrive" required><br>

            <label for="prix_personne">Veuillez sélectionner un prix pour le trajet :</label>
            <input type="text" name="prix_personne" required> €<br>

            <label for="id_voiture">Veuillez sélectionner votre voiture</label>

            <select name="id_voiture" id="id_voiture">

            <?php if(empty($voitures_utilisateur)):?>
                <a href="ajouter_voiture.php" >Ajoutez une voiture</a>
            <?php else :?>
                <?php foreach ($voitures_utilisateur as $voiture) : ?>
                    <option value="<?=$voiture['id_voiture']?>" ><?=htmlspecialchars($voiture['marque'])?> - <?=htmlspecialchars($voiture['modele'])?>"></option>                
                <?php endforeach ?>
            </select>
            <?php endif ?>
            <br>

            <button type="submit">Confirmer mon Covoiturage</button>
            
        </fieldset>
    </form>
    

    <?php include 'includes/footer.php' ?>

    <script>
        document.getElementById('date_auj').value = new Date().toISOString().split('T')[0];
    </script>

</body>
</html>