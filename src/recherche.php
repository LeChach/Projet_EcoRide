<?php
require_once 'connexion/log.php';
require_once 'connexion/session.php';
require_once 'fonction_php/fonction.php';
require_once 'classes/Covoiturage.php';



$lieu_depart = $_GET['lieu_depart'] ?? null;
$lieu_arrive = $_GET['lieu_arrive'] ?? null;
$date_depart = $_GET['date_depart'] ?? null;
$nb_places_voulu_par_le_passager = $_GET['nb_place'] ?? 1;

if ($lieu_depart && $lieu_arrive && $date_depart) {
    $recherche_covoit = Covoiturage::rechercheCovoiturage($pdo,$lieu_depart,$lieu_arrive,$date_depart,$nb_places_voulu_par_le_passager);
}
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
        .resultat_covoit {
            max-width: 900px;
            margin: 20px auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            font-family: 'Poppins', sans-serif;
        }

        .div_info_utilisateur_covoit {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .entete_resultat {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .entete_resultat img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .note {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .div_covoit {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            background-color: #fff;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #eee;
        }

        .date_covoit {
            font-size: 14px;
            color: #555;
            min-width: 70px;
            text-align: center;
        }

        .trajet {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .lieu_duree {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            flex: 1;
            font-weight: 500;
        }

        .lieu_duree::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 20%;
            right: 20%;
            transform: translateY(-50%);
            height: 2px;
            background-color: #ccc;
            z-index: 1;
        }

        .lieu_duree::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 6px;
            height: 6px;
            background-color: #333;
            border-radius: 50%;
            z-index: 2;
        }

        .heure {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .heure::after {
            content: "🚗";
            font-size: 16px; /* réduit la voiture */
            display: block;
        }

        .nb_dtl_supp {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .nb_dtl_supp a,
        .nb_dtl_supp button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
        }

        .nb_dtl_supp a:hover,
        .nb_dtl_supp button:hover {
            background-color: #45a049;
        }

        .nb_dtl_supp span {
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
    
<body>
    <?php include 'includes/header.php' ?>
    <?php include 'includes/bar_recherche.php' ?>

    <?php if ($participation_ok): ?>
        <p style="color:green;"><?= htmlspecialchars($participation_ok) ?></p>
    <?php elseif ($erreur_participation): ?>
        <p style="color:red;"><?= htmlspecialchars($erreur_participation) ?></p>
    <?php endif;?>

    <?php if ($recherche_covoit && !empty($recherche_covoit['info_covoiturage'])) : ?>
        <div class="recherche">
            <div class="filtre">
            </div>

            <div class="resultat_covoit">
                <?php foreach ($recherche_covoit['info_covoiturage'] as $covoit) : ?>
                    <?php if ($covoit['u_id'] === $id_utilisateur) continue; // on ne veux pas afficher notre covoiturage ?>
                    <div class="div_info_utilisateur_covoit">

                        <div class="entete_resultat" style="display: flex;align-items: center;gap: 10px;">
                            <img src="<?= htmlspecialchars($covoit['u_photo'])?>" alt="photo profil utilisateur">
                            <p><?= htmlspecialchars($covoit['u_pseudo'])?></p>
                            <div class="note">
                                <img src="assets/icons/star.png" alt="logo étoile note">
                                <p><?= htmlspecialchars($covoit['u_note'])?></p>
                            </div>
                            <?php if ($covoit['v_energie'] == "Hybride" || $covoit['v_energie'] == "Electrique"):?>
                                <img src="assets/icons/icon_card_voiture_verte.png" alt="voiture écologique">
                            <?php else : ?>   
                                <img src="assets/icons/icon_card_voiture.png" alt="voiture">
                            <?php endif ;?>
                        </div>

                        <div class="div_covoit">
                            <div class="date_covoit">
                                <span>Le <?= date('d/m/Y',strtotime($covoit['c_date_depart']))?></span>
                            </div>
                            <div class="trajet">
                                <div class="lieu_duree">
                                    <p><?= htmlspecialchars($covoit['c_lieu_depart']) ?></p>
                                    <p><?= htmlspecialchars($covoit['c_duree_voyage']) ?></p>
                                    <p><?= htmlspecialchars($covoit['c_lieu_arrive']) ?></p>
                                </div>
                                <div class="heure">
                                    <p><?= htmlspecialchars($covoit['c_heure_depart']) ?></p>       
                                    <p>
                                        <?php 
                                        $heure_depart = strtotime($covoit['c_heure_depart']);
                                        $duree = strtotime($covoit['c_duree_voyage']) - strtotime('00:00:00');
                                        echo date('H:i', $heure_depart + $duree);
                                        ?>
                                    </p>                           
                                </div>
                            </div>
                            <div class="nb_dtl_supp">
                                <span><?= htmlspecialchars($covoit['c_prix_personne'])?> € </span>
                                <span><?= htmlspecialchars($covoit['c_nb_place_dispo'])?> Place<?= ($covoit['c_nb_place_dispo'] > 1) ?'s':''?> Disponible <?= ($covoit['c_nb_place_dispo'] > 1) ?'s':''?> </span>
                                <a href="detail.php
                                        ?id=<?= $covoit['c_id'] ?>
                                        &lieu_depart=<?= urlencode($_GET['lieu_depart']) ?>
                                        &lieu_arrive=<?= urlencode($_GET['lieu_arrive']) ?>
                                        &date_depart=<?= urlencode($_GET['date_depart']) ?>
                                        &nb_place=<?= urlencode($_GET['nb_place']) ?>">
                                Détails</a>
                                <form action="participation_covoit.php" method="POST">
                                    <input type="hidden" name="type_POST" value="affichage_double_participation">
                                    <input name="id_covoiturage" type="hidden" value="<?= $covoit['c_id'] ?>">
                                    <input name="nb_place" type="hidden" value="<?= $nb_places_voulu_par_le_passager ?>">
                                    <button type="submit">Participer</button>
                                </form>
                            </div>
                        </div>

                    </div>
                <?php endforeach ; ?>
            </div>
        </div>

    <?php elseif (empty($recherche_covoit['info_covoiturage'])) : ?>
        <p>Aucun covoiturage trouvé pour votre recherche.</p>
    <?php else : ?>
        <p>Veuillez remplir le formulaire pour trouver un covoiturage</p>
    <?php endif; ?>

    <?php include 'includes/footer.php' ?>

    <script>
        document.getElementById('date_auj').value = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>
