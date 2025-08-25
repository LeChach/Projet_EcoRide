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

    <section class="hero">
        <h1>Partageons la route, Protégeons notre avenir</h1>
        <div class="hero_recherche">
            <nav>
                <ul>
                    <li>
                        <img src="assets/icons/home-05.png" alt="icone départ">
                        Départ
                    </li>
                    <li>
                        <img src="assets/icons/marker-05.png" alt="icone arrivé">
                        Destination
                    </li>
                    <li>
                        <img src="assets/icons/calendar-check.png" alt="icone calendrier">
                        Date
                    </li>
                    <li>
                        <img src="assets/icons/users-profiles-check.png" alt="icone personnes">
                        Passager
                    </li>
                </ul>
            </nav>

            <button class="bouton_recherche">Rechercher</button>
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
                <p>Devenir la référence du covoiturage écoresponsable, en offrant une expérience fluide et sécurisée, adaptée aussi bien aux conducteurs qu’aux passagers.                Que vous soyez à la recherche d’un trajet ou prêt à en proposer un, Éco Ride met à votre disposition un outil intuitif, rapide et fiable pour voyager malin… et partager la route tout en protégeant notre avenir. 
                </p>
            </div>
            <img src="assets/img/voiture_eco.jpg" alt="image voiture electrique">
        </div>
    </section>
    
    <?php include 'includes/footer.php' ?>
</body>
</html>