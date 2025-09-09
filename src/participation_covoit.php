<?php
require_once 'connexion/log.php';
require_once 'connexion/session.php';
require_once 'classes/Covoiturage.php';

    // Vérification de la session active
    if(!isset($id_utilisateur)){
        $_SESSION['redirection_covoit'] = "confirmation_covoiturage.php?idCovoit=" . ($_POST['id_covoiturage'] ?? '');
        header("Location: connexion.php");
        exit;
    }

    // Récupération de l'id du covoiturage et du nombre de places souhaitées
    $id_covoit = (int)($_POST['id_covoiturage'] ?? $_GET['idCovoit'] ?? 0);
    $nb_place_voulu = (int)($_POST['nb_place'] ?? 1);

    if($id_covoit <= 0 || $nb_place_voulu <= 0){
        die("Paramètres invalides pour la réservation");
    }

    // Récupération des informations du covoiturage
    $prep_covoit = $pdo->prepare(
        "SELECT prix_personne, nb_place_dispo, id_conducteur
        FROM covoiturage
        WHERE id_covoiturage = ?"
    );
    $prep_covoit->execute([$id_covoit]);
    $covoit_info = $prep_covoit->fetch(PDO::FETCH_ASSOC);

    if(!$covoit_info){
        die("Covoiturage introuvable");
    }

    // Vérification des places disponibles
    if($covoit_info['nb_place_dispo'] < $nb_place_voulu){
        die("Pas assez de places disponibles pour ce covoiturage");
    }

    // Récupération du crédit de l'utilisateur
    $prep_utilisateur = $pdo->prepare(
        "SELECT credit, pseudo
        FROM utilisateur
        WHERE id_utilisateur = ?"
    );
    $prep_utilisateur->execute([$id_utilisateur]);
    $utilisateur_info = $prep_utilisateur->fetch(PDO::FETCH_ASSOC);

    if(!$utilisateur_info){
        die("Utilisateur introuvable");
    }

    // Calcul du prix total du covoiturage
    $prix_total = $nb_place_voulu * $covoit_info['prix_personne'];

    // Vérification du crédit disponible
    if($prix_total > $utilisateur_info['credit']){
        die("Solde insuffisant pour ce covoiturage");
    }

    // Gestion de la confirmation
    $message_result = "";

    // Vérification si l'utilisateur a déjà réservé ce covoiturage
    $prep_check = $pdo->prepare(
        "SELECT COUNT(*) as nb
        FROM reservation
        WHERE id_passager = ? AND id_covoiturage = ? AND statut_reservation = 'active'"
    );
    $prep_check->execute([$id_utilisateur, $id_covoit]);
    $already_participated = $prep_check->fetch(PDO::FETCH_ASSOC)['nb'] > 0;

    if($already_participated){
        $message_result = "<p style='color:red'>Vous avez déjà réservé ce covoiturage.</p>";
    } elseif(isset($_POST['confirmer'])){
        // Appel à la méthode de participation
        $result = Covoiturage::participerCovoiturage($pdo, $id_utilisateur, $id_covoit, $nb_place_voulu);
        $message_result = $result['success']
            ? "<p style='color:green'>{$result['message']}</p>"
            : "<p style='color:red'>{$result['message']}</p>";
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

    <?= $message_result ?>

    <p>Nombre de places demandées : <?= $nb_place_voulu ?></p>
    <p>Prix total : <?= $prix_total ?> crédits</p>
    <p>Crédit restant après réservation : <?= $utilisateur_info['credit'] - $prix_total ?></p>

    <?php if(!$already_participated): ?>
        <form method="POST" action="">
            <input type="hidden" name="id_covoiturage" value="<?= $id_covoit ?>">
            <input type="hidden" name="nb_place" value="<?= $nb_place_voulu ?>">
            <button type="submit" name="confirmer">Confirmer ma participation</button>
        </form>
    <?php endif; ?>

    <a href="recherche.php">Fermer</a>

    <?php include 'includes/footer.php' ?>

</body>
</html>
