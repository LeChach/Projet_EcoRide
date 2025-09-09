<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';
require_once 'classes/MonCompte.php';
require_once 'classes/Connexion.php';

$info_utilisateur = MonCompte::recupDonnee($pdo,$id_utilisateur);
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    switch ($_POST['type_POST']){
        case 'maj_type_u':
            $changer_type_u = MonCompte::changerTypeUtilisateur($pdo,$id_utilisateur,$_POST['type_u']);
        default:
            header('Location: mon_compte.php');
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

    <?= $info_utilisateur['message']?>

    <div class="info_utilisateur">

        <div class="pp_pseudo_info">

            <div class="pp_pseudo">
                <div class="profil_avatar">
                    <img src="assets/pp/<?= htmlspecialchars($info_utilisateur['info_utilisateur']['photo'])?>" alt="Photo de profil">
                </div>
                <h2 class="pseudo"><?= htmlspecialchars($info_utilisateur['info_utilisateur']['pseudo'])?></h2>
            </div>

            <div class="info_principales">
                <div class="info_ligne">
                    <span>Émail :</span>
                    <span><?= htmlspecialchars($info_utilisateur['info_utilisateur']['email'])?></span>
                </div>
                <div class="info_ligne">
                    <span>Crédit :</span>
                    <span><?= htmlspecialchars($info_utilisateur['info_utilisateur']['credit'])?></span>
                </div>
            </div>

        </div>

    </div>
    <p></p>
    <form method="POST" action="connexion/deconnexion.php">
        <button type="submit">se déconnecter</button>
    </form>
    <p></p>
    <p></p>
    <p></p>
    <div class="type_u_preference">

        <div class="type_utilisateur">

            <span>Type utilisateur :</span>
            <span><?= htmlspecialchars($info_utilisateur['info_utilisateur']['type_utilisateur'])?></span>
            <button id="btn_maj_type_u">Modifié de type</button>

            <div id="maj_type_u" >

                <form method="POST">
                    <input type="hidden" name="type_POST" value="maj_type_u">
                    <label for="selection_type_u">Je veux être :</label>
                    <select name="type_u">
                        <option value="Passager">Passager</option>
                        <option value="Conducteur">Conducteur</option>
                        <option value="Passager et Conducteur">Les deux à la fois</option>
                    </select>
                    <button id="btn_confirmer_maj_type_u" type="submit">Confirmé</button>                        
                </form>
                <button id="btn_annuler_maj_type_u">Annulé</button>
            </div>
        </div>

                
        <div class="preference">

            <button class="btn-preferences" id="btn_preferences">Préférences</button>
            <div class="liste_preference">

                <form method = "POST">
                    <?php foreach ($info_utilisateur['info_preference'] as $preference => $valeur_pref):?>
                            <img src="<?= cheminImgPreference($preference) ?>" alt="icone <?= $preference ?>" style="width:24px; height:24px;">
                            <label>
                                <?= ucfirst(str_replace('_', ' ', htmlspecialchars($preference))) ?>
                                <input type="checkbox" name="<?= htmlspecialchars($preference)?>" 
                                value = "accepter" <?=($valeur_pref === 'accepter') ? 'checked' : ''?>>
                            </label>
                    <?php endforeach; ?>
                    <button type="submit">Valider mes préférences</button>
                    <button id="btn_fermer" type="button">Fermer</button>
                </form>
            </div>
        </div>  
    </div>     
    <p></p>
    <p></p>
    <p></p>
    <p></p>
    <?php include 'includes/footer.php' ?>

</body>

<script>
        document.getElementById('btn_preference').addEventListener('click', () => {
            document.getElementById('liste_preference').style.display ='block';
        });

        document.getElementById('btn_fermer').addEventListener('click', () => {
        document.getElementById('liste_preference').style.display ='none';
        });

        document.getElementById('btn_maj_type_u').addEventListener('click', () => {
            document.getElementById('maj_type_u').style.display ='block';
        });

        document.getElementById('btn_confirmer_maj_type_u').addEventListener('click', () => {
        document.getElementById('maj_type_u').style.display ='none';
        });

        document.getElementById('btn_annuler_maj_type_u').addEventListener('click', () => {
        document.getElementById('maj_type_u').style.display ='none';
        });
        
</script>

</html>