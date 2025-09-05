<?php
require_once 'connexion/log.php';
require_once 'connexion/session.php';
require_once 'connexion/avoir_detail_covoit.php';
require_once 'function_php/img_preference.php'
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Ride - Partageons la route, protégeons notre avenir</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>
<body>

    <div class="recap_info_utilisateur_voiture">

        <div class="detail_utilisateur">

            <img src="assets/pp/<?= htmlspecialchars($info_utilisateur['photo'])?>" alt="photo profil utilisateur">
            <p><?= htmlspecialchars($info_utilisateur['pseudo'])?></p>

            <div class="note">
                <img src="assets/icons/star.png" alt="logo étoile note">
                <p><?= htmlspecialchars($info_utilisateur['note'])?></p>
            </div>

            <p>Genre du conducteur : <?=htmlspecialchars($info_utilisateur['sexe'])?></p>

        </div>

        <div class="detail_voiture">
            <div class="energie_supprimer">
                <?php if($detail_covoit['v_energie'] === 'Hybride' || $detail_covoit['v_energie'] === 'Electrique'){
                    $icone_voiture = 'assets/icons/icon_card_voiture_verte.png';
                }else{
                    $icone_voiture = 'assets/icons/icon_card_voiture.png';
                }?>
                <img src="<?= htmlspecialchars($icone_voiture)?>" alt="icone voiture">
                <p><?= htmlspecialchars($detail_covoit['energie'])?></p>
            </div>
            <div class="Card_info">
                <p><span class="gauche"><?=htmlspecialchars($detail_covoit['v_marque'])?></span>  <span class="droite"><?=htmlspecialchars($detail_covoit['v_modele'])?></span></p>
                <p><span class="gauche">Couleur</span>  <span class="droite"><?=htmlspecialchars($detail_covoit['v_couleur'])?></span></p>
                <p><span class="gauche">Places Disponibles</span>  <span class="droite"><?=htmlspecialchars($detail_covoit['c_nb_place_dispo'])?></span></p>
            </div>  
    
        </div>

    </div>

    <div class="detail_preference">
        <?php foreach ($preference as $label_preference => $valeur) : ?>
            <?php if ($valeur == 'accepter') : ?>
                <img src="<?=cheminImgPreference($label_preference)?>" alt="icone <?=$label_preference?>" style="width:24px; height:24px;">
                <p><?= htmlspecialchars($label_preference)?></p>
            <?php endif;?>
        <?php endforeach;?>    
    </div>

    <div class="div_avis">
        <?php if (!empty($avis)) : ?>
            <?php foreach ($avis_covoiturage as $avis) : ?>
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

    <?php include 'includes/footer.php' ?>

</body>
</html>
