<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';
require_once 'connexion/recup_donnee.php';
require_once 'connexion/preference.php';
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
            <h1>Bienvenue <?= htmlspecialchars($pseudo)?>!</h1>

            <img src="<?= htmlspecialchars($photo)?>" alt="avatar profil">

            <div class="info">
                <p>
                    Email :<?= htmlspecialchars($email)?>
                    Téléphone :<?= htmlspecialchars($telephone)?>
                    Solde du crédit :<?= htmlspecialchars($credit)?>
                    Vous êtes : <?= htmlspecialchars($type_u)?>
                </p>
            </div>

            <form action="connexion/deconnexion.php">
                <button type="submit">se déconnecter</button>
            </form>



            <button id="btn_preference">Mes Préférences</button>

            <div id="preference" style="display: none;">
                <form action="connexion/new_preference.php" method="POST">


                    <img src="assets/icons/cigarette.png" alt="logo cigarette">
                    <label for="fumeur">Fumeur :
                    <input type="checkbox" name="fumeur" value="accepter" <?php if($pref_fumeur === 'accepter') echo 'checked' ?>>
                    </label><br>

                    <img src="assets/icons/pattes.png" alt="logo patte de chien">
                    <label for="animaux">Animaux :
                    <input type="checkbox" name="animaux" value="accepter" <?php if($pref_animal === 'accepter') echo 'checked' ?>>
                    </label><br>

                    <img src="assets/icons/silence.png" alt="logo silence">
                    <label for="silence">Silence :
                    <input type="checkbox" name="silence" value="accepter" <?php if($pref_silence === 'accepter') echo 'checked' ?>>
                    </label><br>

                    <img src="assets/icons/note-de-musique.png" alt="logo musique">
                    <label for="musique">Musique :
                    <input type="checkbox" name="musique" value="accepter" <?php if($pref_musique === 'accepter') echo 'checked' ?>>
                    </label><br>

                    <img src="assets/icons/climatisation.png" alt="logo climatisation">
                    <label for="climatisation">Climatisation :
                    <input type="checkbox" name="climatisation" value="accepter" <?php if($pref_clim === 'accepter') echo 'checked' ?>>
                    </label><br>

                    <img src="assets/icons/bicyclette.png" alt="logo vélo">
                    <label for="velo">Vélo :
                    <input type="checkbox" name="velo" value="accepter" <?php if($pref_velo === 'accepter') echo 'checked' ?>>
                    </label><br>

                    <img src="assets/icons/bonhomme-allumette.png" alt="logo coffre voiture bagage">
                    <label for="place_coffre">Place dans le coffre :
                    <input type="checkbox" name="place_coffre" value="accepter" <?php if($pref_coffre === 'accepter') echo 'checked' ?>>
                    </label><br>
                    
                    <?php if($pref_ladies_only == 'accepter' || $pref_ladies_only == 'refuser'): ?>
                        <img src="assets/icons/femme.png" alt="logo femme">
                        <label for="ladies_only">Femme Uniquement :
                        <input type="checkbox" name="ladies_only" value="accepter" <?php if($pref_ladies_only === 'accepter') echo 'checked' ?>>
                        </label>
                    <?php endif ?>
                        
                    <button type="submit">Valider mes préférences</button>
                    <button id="btn_fermer" type="button">Fermer</button>
                </form>
            </div>

    </main>

    <?php include 'includes/footer.php' ?>

    <script>
        document.getElementById('btn_preference').addEventListener('click', () => {
            document.getElementById('preference').style.display ='block';
        });

        document.getElementById('btn_fermer').addEventListener('click', () => {
        document.getElementById('preference').style.display ='none';
        });
    </script>

</body>
</html>