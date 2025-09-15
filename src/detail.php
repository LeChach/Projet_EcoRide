<?php
require_once 'connexion/log.php';
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

    <div class="recap_info_utilisateur_voiture">

        <!-- Infos utilisateur / conducteur -->
        <div class="detail_utilisateur">
            <img src="assets/pp/<?= htmlspecialchars($details_utilisateur['photo']) ?>" alt="photo profil utilisateur">
            <p><?= htmlspecialchars($details_utilisateur['pseudo']) ?></p>

            <div class="note">
                <img src="assets/icons/star.png" alt="logo étoile note">
                <p><?= htmlspecialchars($details_utilisateur['note']) ?></p>
            </div>

            <p>Genre du conducteur : <?= htmlspecialchars($details_utilisateur['sexe']) ?></p>
        </div>

        <!-- Infos voiture et covoiturage -->
        <div class="detail_voiture">
            <div class="energie_supprimer">
                <?php 
                $icone_voiture = ($details_covoiturage['v_energie'] === 'Hybride' || $details_covoiturage['v_energie'] === 'Electrique') 
                    ? 'assets/icons/icon_card_voiture_verte.png' 
                    : 'assets/icons/icon_card_voiture.png';
                ?>
                <img src="<?= htmlspecialchars($icone_voiture) ?>" alt="icone voiture">
                <p><?= htmlspecialchars($details_covoiturage['v_energie']) ?></p>
            </div>
            <div class="Card_info">
                <p><span class="gauche"><?= htmlspecialchars($details_covoiturage['v_marque']) ?></span>  
                   <span class="droite"><?= htmlspecialchars($details_covoiturage['v_modele']) ?></span></p>
                <p><span class="gauche">Couleur</span>  
                   <span class="droite"><?= htmlspecialchars($details_covoiturage['v_couleur']) ?></span></p>
                <p><span class="gauche">Places Disponibles</span>  
                   <span class="droite"><?= htmlspecialchars($details_covoiturage['c_nb_place_dispo']) ?></span></p>
            </div>  
        </div>

    </div>

    <!-- Préférences conducteur -->
    <div class="detail_preference">
        <?php if($details_preference):?>
            <?php foreach ($details_preference as $label_preference => $valeur):?>
                <?php if ($valeur == 'accepter'):?>
                    <div class="preference_item">
                        <img src="<?= cheminImgPreference($label_preference) ?>" alt="icone <?= $label_preference ?>" style="width:24px; height:24px;">
                        <p><?= ucfirst(str_replace('_', ' ', $label_preference)) ?> : <?= $valeur ?></p>
                    </div>
                <?php  endif;?>
            <?php endforeach;?>
       <?php  endif;?>
    </div>

    <!-- Avis des passagers -->
    <div class="div_avis">
        <?php if (!empty($details_avis)) : ?>
            <?php foreach ($details_avis as $avis) : ?>
                <div class="avis_personne">
                    <div class="avis-entete">
                        <img src="<?= htmlspecialchars($avis['u_photo']) ?>" alt="Photo de <?= htmlspecialchars($avis['u_pseudo']) ?>" class="photo-profil">
                        <p class="pseudo"><?= htmlspecialchars($avis['u_pseudo']) ?></p>
                        <div class="note">
                            <img src="assets/icons/star.png" alt="Étoile">
                            <p><?= htmlspecialchars($avis['a_note']) ?>/5</p>
                        </div>
                        <p class="date"><?= date('d/m/Y', strtotime($avis['a_date'])) ?></p>
                    </div>
                    <div class="avis-commentaire">
                        <p><?= nl2br(htmlspecialchars($avis['a_commentaire'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Aucun avis pour ce covoiturage.</p>
        <?php endif; ?>
    </div>

    <a href="recherche.php
        ?lieu_depart=<?= urlencode($lieu_depart) ?>
        &lieu_arrive=<?= urlencode($lieu_arrive) ?>
        &date_depart=<?= urlencode($date_depart) ?>
        &nb_place=<?= urlencode($nb_place) ?>">
    Fermer</a>


    <?php include 'includes/footer.php' ?>

</body>
</html>
