<?php
require_once 'config/database.php';
require_once 'connexion/session.php';
require_once 'classes/Covoiturage.php';
require_once 'fonction_php/fonction.php';

$id_covoit = (int)$_GET['id'] ?? 0;
$lieu_depart = $_GET['lieu_depart'];
$lieu_arrive = $_GET['lieu_arrive'];
$date_depart = $_GET['date_depart'];
$nb_place = $_GET['nb_place'];

$details = Covoiturage::detailCovoiturage($pdo,$id_covoit);
if(!$details){
    die('Covoiturage introuvable');
}
$details_utilisateur = $details['utilisateur'];
$details_covoiturage = $details['covoiturage'];
$details_preference = $details['preferences'];
$details_avis = $details['avis'];
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du covoiturage - Eco Ride</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php' ?>

    <main>
        <div class="detail-container">

            <!-- Section récap utilisateur et voiture -->
            <section class="recap-section">
                <div class="recap-content">
                    
                    <!-- Infos conducteur -->
                    <div class="driver-info">
                        <h3>Conducteur</h3>
                        <img src="assets/pp/<?= htmlspecialchars($details_utilisateur['photo']) ?>" 
                             alt="Photo de profil" class="driver-avatar">
                        <p class="driver-name"><?= htmlspecialchars($details_utilisateur['pseudo']) ?></p>
                        
                        <div class="driver-rating">
                            <img src="assets/icons/star.png" alt="Étoile">
                            <p><?= htmlspecialchars($details_utilisateur['note']) ?>/5</p>
                        </div>
                        
                        <p class="driver-gender">Genre : <?= htmlspecialchars($details_utilisateur['sexe']) ?></p>
                    </div>

                    <!-- Infos voiture -->
                    <div class="vehicle-info">
                        <h3>Véhicule</h3>
                        
                        <div class="vehicle-header">
                            <?php 
                            $icone_voiture = ($details_covoiturage['v_energie'] === 'Hybride' || $details_covoiturage['v_energie'] === 'Electrique') 
                                ? 'assets/icons/icon_card_voiture_verte.png' 
                                : 'assets/icons/icon_card_voiture.png';
                            ?>
                            <img src="<?= htmlspecialchars($icone_voiture) ?>" alt="Icône voiture">
                            <p class="vehicle-energy"><?= htmlspecialchars($details_covoiturage['v_energie']) ?></p>
                        </div>
                        
                        <div class="vehicle-details">
                            <div class="vehicle-row">
                                <span class="label">Marque</span>
                                <span class="value"><?= htmlspecialchars($details_covoiturage['v_marque']) ?></span>
                            </div>
                            <div class="vehicle-row">
                                <span class="label">Modèle</span>
                                <span class="value"><?= htmlspecialchars($details_covoiturage['v_modele']) ?></span>
                            </div>
                            <div class="vehicle-row">
                                <span class="label">Couleur</span>
                                <span class="value"><?= htmlspecialchars($details_covoiturage['v_couleur']) ?></span>
                            </div>
                            <div class="vehicle-row">
                                <span class="label">Places disponibles</span>
                                <span class="value"><?= htmlspecialchars($details_covoiturage['c_nb_place_dispo']) ?></span>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            <!-- Section préférences -->
            <section class="preferences-section">
                <h3>Préférences du conducteur</h3>
                
                <?php if($details_preference && !empty(array_filter($details_preference, function($val) { return $val == 'accepter'; }))): ?>
                    <div class="preferences-grid">
                        <?php foreach ($details_preference as $label_preference => $valeur): ?>
                            <?php if ($valeur == 'accepter'): ?>
                                <div class="preference-card">
                                    <img src="<?= cheminImgPreference($label_preference) ?>" 
                                         alt="Icône <?= $label_preference ?>">
                                    <?php 
                                    $pref_label = ucfirst(str_replace('_', ' ', $label_preference)); 
                                    $pref_label = str_replace('E', 'Ê', $pref_label);
                                    ?>
                                    <p><?= htmlspecialchars($pref_label) ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-preferences">
                        <p>Aucune préférence particulière spécifiée</p>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Section avis -->
            <section class="reviews-section">
                <h3>Avis des passagers</h3>
                
                <?php if (!empty($details_avis)): ?>
                    <div class="reviews-list">
                        <?php foreach ($details_avis as $avis): ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <img src="assets/pp/<?= htmlspecialchars($avis['u_photo']) ?>" 
                                         alt="Photo de <?= htmlspecialchars($avis['u_pseudo']) ?>" 
                                         class="review-avatar">
                                    <p class="review-author"><?= htmlspecialchars($avis['u_pseudo']) ?></p>
                                    
                                    <div class="review-rating">
                                        <img src="assets/icons/star.png" alt="Étoile">
                                        <p><?= htmlspecialchars($avis['a_note']) ?>/5</p>
                                    </div>
                                    
                                    <p class="review-date"><?= date('d/m/Y', strtotime($avis['a_date'])) ?></p>
                                </div>
                                
                                <div class="review-comment">
                                    <p><?= nl2br(htmlspecialchars($avis['a_commentaire'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-reviews">
                        <p>Aucun avis pour ce covoiturage pour le moment</p>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Bouton de retour -->
            <div class="close-button-section">
                <a href="recherche.php?lieu_depart=<?= urlencode($lieu_depart) ?>&lieu_arrive=<?= urlencode($lieu_arrive) ?>&date_depart=<?= urlencode($date_depart) ?>&nb_place=<?= urlencode($nb_place) ?>" 
                   class="close-button">
                    Retour aux résultats
                </a>
            </div>

        </div>
    </main>

    <?php include 'includes/footer.php' ?>
</body>
</html>