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
            $confirmation = Covoiturage::participerCovoiturage($pdo,$id_utilisateur,$_POST['id_covoiturage'],$_POST['nb_place']);
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php' ?>

    <main>
        <div class="participation-container">
            <h2>Confirmation de participation</h2>

            <!-- Card avec détails de la réservation -->
            <div class="confirmation-card">
                <div class="booking-details">
                    
                    <div class="detail-row">
                        <span class="detail-label">
                            <span class="places-icon"></span>
                            Nombre de places demandées
                        </span>
                        <span class="detail-value"><?= htmlspecialchars($nb_place_voulu) ?> place<?= $nb_place_voulu > 1 ? 's' : '' ?></span>
                    </div>

                    <div class="detail-row price-highlight">
                        <span class="detail-label">
                            <span class="price-icon"></span>
                            Prix total
                        </span>
                        <span class="detail-value"><?= htmlspecialchars($prix_total) ?> €</span>
                    </div>

                    <div class="detail-row balance-info">
                        <span class="detail-label">
                            <span class="wallet-icon"></span>
                            Crédit restant après réservation
                        </span>
                        <span class="detail-value"><?= htmlspecialchars($new_solde) ?> €</span>
                    </div>

                </div>
            </div>

            <!-- Section de confirmation -->
            <div class="confirmation-section">
                <div class="confirmation-warning">
                    Cette action débitera vos crédits. Voulez-vous vraiment confirmer votre participation ?
                </div>

                <div class="action-buttons">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="type_POST" value="confirmation_participation">
                        <input type="hidden" name="id_covoiturage" value="<?= htmlspecialchars($id_covoiturage) ?>">
                        <input type="hidden" name="nb_place" value="<?= htmlspecialchars($nb_place_voulu) ?>">
                        <button type="submit" name="confirmer" class="confirm-button">
                            Confirmer ma participation
                        </button>
                    </form>

                    <a href="recherche.php" class="cancel-button">Annuler</a>
                </div>
            </div>

            <!-- Informations complémentaires -->
            <div class="info-section">
                <h4>Informations importantes</h4>
                <p>
                    Une fois votre participation confirmée, vos crédits seront immédiatement débités. 
                    Vous recevrez une confirmation par email avec les détails du covoiturage.
                </p>
            </div>

        </div>
    </main>

    <?php include 'includes/footer.php' ?>
</body>
</html>