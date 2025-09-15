<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';
require_once 'classes/Covoiturage.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    switch ($_POST['type_POST']){
        case 'affichage_double_participation':
            $id_covoiturage = $_POST['id_covoiturage'];
            $nb_place_voulu = $_POST['nb_place'];

            $info_utilisateur = Covoiturage::voirCovoituragePourParticipation($pdo, $id_covoiturage, $id_utilisateur,$nb_place_voulu);
            if($info_utilisateur['success']){
                $prix_total = $info_utilisateur['prix_total'];
                $new_solde = $info_utilisateur['nouveau_solde'];
            }
            break;

        case 'confirmation_participation':
            $confirmation = Covoiturage::participerCovoiturage($pdo,$id_utilisateur,$_POST['id_covoituage'],$_POST['nb_place']);
            if($confirmation['success']){
                $_SESSION['covoiturage_participé'] = $confirmation['message'];
                header('Location: recherche.php');
                exit;
            }else{
                $_SESSION['erreur_participation'] = $confirmation['message'];
                header('Location: recherche.php');
                exit;  
            }
    }
}
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de participation - Eco Ride</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <?php include 'includes/header.php' ?>

    <h2>Confirmation de participation</h2>
    <p>Nombre de places demandées : <?= $nb_place_voulu ?></p>
    <p>Prix total : <?= $prix_total ?> crédits</p>
    <p>Crédit restant après réservation : <?= $new_solde ?></p>

        <form method="POST">
            <input type="hidden" name="type_POST" value="confirmation_participation">
            <input type="hidden" name="id_covoiturage" value="<?= $id_covoiturage ?>">
            <input type="hidden" name="nb_place" value="<?= $nb_place_voulu ?>">
            <button type="submit" name="confirmer">Confirmer ma participation</button>
        </form>

    <a href="recherche.php">Fermer</a>

    <?php include 'includes/footer.php' ?>

</body>
</html>
