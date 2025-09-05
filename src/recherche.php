<?php
require_once 'connexion/log.php';
require_once 'connexion/session.php';
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Ride - Partageons la route, protégeons notre avenir</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .Covoit {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: Arial, sans-serif;
    }

    .Covoit h2 {
    font-size: 22px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
    margin-top: 0;
    }

    .div_covoit {
    background-color: #f0f0f0;
    border: 2px solid #d0d0d0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    }

    .date_covoit {
    flex-shrink: 0;
    width: 80px;
    }

    .date_covoit p {
    margin: 0;
    font-size: 12px;
    color: #666;
    font-weight: 500;
    }

    .trajet {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    }

    .lieu_duree {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    flex: 1;
    }

    .lieu_duree p:first-child,
    .lieu_duree p:last-child {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #333;
    z-index: 3;
    position: relative;
    }

    /* Ligne de connexion entre les villes */
    .lieu_duree::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 20%;
    right: 20%;
    transform: translateY(-50%);
    height: 2px;
    background-color: #333;
    z-index: 1;
    }

    /* Point noir au milieu de la ligne */
    .lieu_duree::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background-color: #333;
    border-radius: 50%;
    z-index: 2;
    }

    /* Durée du voyage au-dessus du point */
    .lieu_duree p:nth-child(2) {
    position: absolute;
    top: -25px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 12px;
    color: #666;
    font-weight: 400;
    z-index: 3;
    margin: 0;
    }

    .heure {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    }

    .heure p {
    margin: 0;
    font-size: 12px;
    font-weight: 500;
    color: #333;
    }

    /* Icône voiture au centre */
    .heure::after {
    content: "🚗";
    font-size: 20px;
    display: block;
    }

    /* Section droite avec passagers, détails et suppression */
    .nb_dtl_supp {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-shrink: 0;
    }

    .nb_dtl_supp p {
    margin: 0;
    font-size: 14px;
    color: #333;
    font-weight: 500;
    }

    .nb_dtl_supp p::after {
    content: " passagers";
    color: #666;
    }

    /* Bouton Détails */
    #detail_covoit {
    background-color: #4CAF50;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s ease;
    }

    #detail_covoit:hover {
    background-color: #45a049;
    }

    /* Bouton suppression (X) */
    #btn_supp_covoit {
    background-color: #fff;
    border: 2px solid #ccc;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    color: #666;
    transition: all 0.3s ease;
    }

    #btn_supp_covoit:hover {
    background-color: #f5f5f5;
    border-color: #999;
    color: #333;
    }

    /* Lien "Ajoutez un covoiturage" */
    .Covoit > a {
    display: inline-block;
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    margin-top: 10px;
    transition: background-color 0.3s ease;
    }

    .Covoit > a:hover {
    background-color: #45a049;
    }
</style>

</head>
    
<body>
    <?php include 'includes/header.php' ?>
    <?php include 'includes/bar_recherche.php' ?>

    <?php if ($recherche_covoit) :?>

        <div class="recherche">
            <div class="filtre">

            </div>

            <div class="resultat_covoit">
                <?php foreach ($recherche_covoit as $covoit) : ?>


                    <div class="div_info_utilisateur_covoit">

                        <div class="entete_resultat">
                            <img src="<?= htmlspecialchars($covoit['u_photo'])?>" alt="photo profil utilisateur">
                            <p><?= htmlspecialchars($covoit['u_pseudo'])?></p>
                            <div class="note">
                                <img src="assets/icons/star.png" alt="logo étoile note">
                                <p><?= htmlspecialchars($covoit['u_note'])?></p>
                            </div>
                            <?php if ($covoit['v_energie'] == "Hybride" || $covoit['v_energie'] == "Electrique"):?>
                                <img src="assets/icons/icon_card_voiture_verte.png" alt="voiture écologique">
                            <?php else : ?>   
                                <img src="assets/icons/icon_card_voiture.png" alt="voiture écologique">
                            <?php endif ;?>
                        </div>

                        <div class="div_covoit">
                            <div class="date_covoit">
                                <p><?=$covoit['c_date_depart']?></p>
                            </div>
                            <div class="trajet">
                                <div class="lieu_duree">
                                    <p><?=$covoit['c_lieu_depart']?></p>
                                    <p><?=$covoit['c_duree_voyage']?></p>
                                    <p><?=$covoit['c_lieu_arrive']?></p>
                                </div>
                                <div class="heure">
                                    <p><?=$covoit['c_heure_depart']?></p>
                                    <p><?= date('H:i', strtotime($covoit['c_heure_depart']) + strtotime($covoit['c_duree_voyage']) - strtotime('00:00:00')) ?></p>                           
                                </div>
                            </div>
                            <div class="nb_dtl_supp">
                                <span><?= htmlspecialchars($covoit['c_prix_personne'])?> € </span>
                                <span><?= htmlspecialchars($covoit['c_nb_place_dispo'])?> Places Disponibles </span>
                                <a href="detail.php?id=<?=$covoit['c_id']?>">Détails</a>
                                <form action="connexion/participer_covoit.php" method="POST">
                                    <input name="id_covoiturage" type="hidden" value="<?=$id_utilisateur?>">
                                    <button>Participer</button>
                                </form>
                            </div>
                        </div>

                    </div>

                <?php endforeach ; ?>

            </div>

        </div>

    <?php else : ?>
        <p>Veuillez remplir le formulaire pour trouver un covoiturage</p>
    <?php endif; ?>

    <?php include 'includes/footer.php' ?>

    <script>
        document.getElementById('date_auj').value = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>