<?php
require_once 'config/database.php';
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

</head>
    
<body>
    <?php include 'includes/header.php' ?>

    <main>

    <section class="hero">
        <h1>Partageons la route, Protégeons notre avenir</h1>
        <div class ="bar_recherche_wrapper" >
            <?php include 'includes/bar_recherche.php'?>
        </div>
    </section>

    <section class="information">
        <div class="info_entreprise">
            <img src="assets/img/eco_ride_pers.jpg" alt="personnels eco ride">
            <div class="txt_qui_sommes_nous">
                <h2>Qui sommes nous ?</h2>
                <p>Chez Éco Ride, nous croyons qu’il est possible de voyager autrement. Fondé en France, notre plateforme de covoiturage a pour mission de réduire l’impact environnemental des déplacements en encourageant le partage de trajets uniquement en voiture. Nous proposons une solution simple, économique et responsable pour relier les voyageurs soucieux de l’environnement et ceux qui souhaitent réduire leurs frais de transport.                    
                </p>
            </div>
        </div>

        <div class="info_objectif">
            <div class="txt_obj">
                <h2>Notre Objectif</h2>
                <p>Devenir la référence du covoiturage éco responsable, en offrant une expérience fluide et sécurisée, adaptée aussi bien aux conducteurs qu’aux passagers.                Que vous soyez à la recherche d’un trajet ou prêt à en proposer un, Éco Ride met à votre disposition un outil intuitif, rapide et fiable pour voyager malin… et partager la route tout en protégeant notre avenir. 
                </p>
            </div>
            <img src="assets/img/voiture_eco.jpg" alt="image voiture electrique">
        </div>
    </section>
    
    <section class="avantages">
        <h2>Pourquoi choisir Éco Ride</h2>
        <div class="trois_avantages">
            <div class="ecologie">
                <img src="assets/icons/ecologie.png" alt="icone écologie">
                <h3>Écologie</h3>
                <p>
                    Réduisez votre empreinte carbone
                </p>
            </div>
            <div class="economie">
                <img src="assets/icons/economie.png" alt="icone économie">
                <h3>Économie</h3>
                <p>
                    Réduisez vos frais de transport
                </p>
            </div>
            <div class="communautaire">
                <img src="assets/icons/communautaire.png" alt="icone social">
                <h3>Communautaire</h3>
                <p>
                    Rencontrez et partagez avec d’autres voyageurs                
                </p>
            </div>
        </div>
    </section>

    </main>

    <?php include 'includes/footer.php' ?>

</body>
</html>