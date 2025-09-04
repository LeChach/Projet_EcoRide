<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';
require_once 'connexion/recup_donnee.php';
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Eco Ride</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* Conteneur global de toutes les cartes */
    .liste-voitures {
    display: flex;
    flex-wrap: wrap; /* Permet d’avoir plusieurs lignes */
    gap: 20px; /* Espace entre les cartes */
    justify-content: flex-start;
    padding: 8px;
    }

    /* Chaque carte */
    .Card_voitures {
    display: flex;
    width: 40%; /* Deux cartes par ligne environ */
    background: #f5f5f5;
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    box-sizing: border-box;
    }

    /* Partie gauche : icône + énergie + supprimer */
    .energie_supprimer {
    width: 30%;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-right: 15px;
    }

    /* Icône voiture */
    .icone-voiture {
    width: 60px;
    height: auto;
    margin-bottom: 10px;
    }

    /* Énergie */
    .energie-voiture {
    font-weight: bold;
    margin-bottom: 10px;
    }

    /* Bouton supprimer */
    .energie_supprimer button {
    padding: 5px 10px;
    background-color: #d9534f;
    border: none;
    border-radius: 4px;
    color: white;
    cursor: pointer;
    }

    .energie_supprimer button:hover {
    background-color: #c9302c;
    }

    /* Partie droite : infos voiture */
    .Card_info {
    width: 70%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    }

    .Card_info p {
    display: flex;
    justify-content: space-between; /* espace entre les deux spans */
    margin: 8px 0; /* gap vertical uniforme */
    }

    .Card_info p span.gauche {
    text-align: right; /* label aligné à droite */
    width: 45%; /* ajuste si besoin */
    }

    .Card_info p span.droite {
    text-align: left; /* valeur alignée à gauche */
    width: 45%; /* ajuste si besoin */
    }

</style>
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

    <main>
            <h1>Bienvenue <?= htmlspecialchars($pseudo)?>!</h1>
            <img src="<?= htmlspecialchars($photo)?>" alt="avatar profil">

            <div class="info">
                <p>Email :<?= htmlspecialchars($email)?></p><br>
                <p>Téléphone :<?= htmlspecialchars($telephone)?></p><br>
                <p>Solde du crédit :<?= htmlspecialchars($credit)?></p><br>
                <p>Vous êtes : <?= htmlspecialchars($type_u)?></p><br>
            </div>

            <form action="connexion/deconnexion.php">
                <button type="submit">se déconnecter</button>
            </form>

            <?php if($type_u == 'Conducteur' || $type_u == 'Passager et Conducteur'):?>

            <button id="btn_preference">Mes Préférences</button>
            <div id="preference" style="display: none;">
                <form action="connexion/new_preference.php" method="POST">


                    <img src="assets/icons/cigarette.png" alt="logo cigarette">
                    <label for="fumeur">Fumeur :
                    <input type="checkbox" name="fumeur" value="accepter" <?php if($pref_fumeur === 'accepter') echo 'checked' ?>>
                    </label><br>

                    <img src="assets/icons/pattes.png" alt="logo patte de chien">
                    <label for="animaux">Animaux :
                    <input type="checkbox" name="animaux" value="accepter" <?php if($pref_animal === 'accepter') echo 'checked' ?>>
                    </label><br>

                    <img src="assets/icons/silence.png" alt="logo silence">
                    <label for="silence">Silence :
                    <input type="checkbox" name="silence" value="accepter" <?php if($pref_silence === 'accepter') echo 'checked' ?>>
                    </label><br>

                    <img src="assets/icons/note-de-musique.png" alt="logo musique">
                    <label for="musique">Musique :
                    <input type="checkbox" name="musique" value="accepter" <?php if($pref_musique === 'accepter') echo 'checked' ?>>
                    </label><br>

                    <img src="assets/icons/climatisation.png" alt="logo climatisation">
                    <label for="climatisation">Climatisation :
                    <input type="checkbox" name="climatisation" value="accepter" <?php if($pref_clim === 'accepter') echo 'checked' ?>>
                    </label><br>

                    <img src="assets/icons/bicyclette.png" alt="logo vélo">
                    <label for="velo">Vélo :
                    <input type="checkbox" name="velo" value="accepter" <?php if($pref_velo === 'accepter') echo 'checked' ?>>
                    </label><br>

                    <img src="assets/icons/bonhomme-allumette.png" alt="logo coffre voiture bagage">
                    <label for="place_coffre">Place dans le coffre :
                    <input type="checkbox" name="place_coffre" value="accepter" <?php if($pref_coffre === 'accepter') echo 'checked' ?>>
                    </label><br>
                    
                    <?php if($pref_ladies_only == 'accepter' || $pref_ladies_only == 'refuser'): ?>
                        <img src="assets/icons/femme.png" alt="logo femme">
                        <label for="ladies_only">Femme Uniquement :
                        <input type="checkbox" name="ladies_only" value="accepter" <?php if($pref_ladies_only === 'accepter') echo 'checked' ?>>
                        </label>
                    <?php endif ?>
                        
                    <button type="submit">Valider mes préférences</button>
                    <button id="btn_fermer" type="button">Fermer</button>
                </form>
            </div>

            <div id="liste-voitures">
                <h2>Mes Voitures :</h2>

                    <?php foreach($voitures_utilisateur as $voiture) : ?>

                        <div class="Card_voitures">
                            <div class="energie_supprimer">
                                <?php if($voiture['energie'] === 'Hybride' || $voiture['energie'] === 'Electrique'){
                                    $icone_voiture = 'assets/icons/icon_card_voiture_verte.png';
                                }else{
                                    $icone_voiture = 'assets/icons/icon_card_voiture.png';
                                }?>
                                <img src="<?= htmlspecialchars($icone_voiture)?>" alt="icone voiture">
                                <p><?= htmlspecialchars($voiture['energie'])?></p>
                                <form action="connexion/supprimer_voiture.php" method="POST">
                                    <input name="id_voiture" type="hidden" value="<?= $voiture['id_voiture']?>">
                                    <button type="submit">Supprimer</button>
                                </form>
                            </div>
                            <div class="Card_info">
                                <p><span class="gauche"><?=htmlspecialchars($voiture['marque'])?></span>  <span class="droite"><?=htmlspecialchars($voiture['modele'])?></span></p>
                                <p><span class="gauche">Immatriculation</span>  <span class="droite"><?=htmlspecialchars($voiture['immat'])?></span></p>
                                <p><span class="gauche">Couleur</span>  <span class="droite"><?=htmlspecialchars($voiture['couleur'])?></span></p>
                                <p><span class="gauche">Places Disponibles</span>  <span class="droite"><?=htmlspecialchars($voiture['nb_place'])?></span></p>
                            </div>  
                        </div>
                    <?php endforeach;?> 
            </div>
            <a href="ajouter_voiture.php" >Ajoutez une voiture</a>
            <?php endif ?>

            <div class="Covoit">
                <h2>Covoiturage en cours</h2>
                <?php foreach($covoit_utilisateur as $covoit):?>
                    <div class="div_covoit">
                        <div class = "date_covoit">
                            <p><?=$covoit['date_depart']?></p>
                        </div>
                        <div class="trajet">
                            <div class="lieu_duree">
                                <p><?=$covoit['lieu_depart']?></p>
                                <p><?=$covoit['duree_voyage']?></p>
                                <p><?=$covoit['lieu_arrive']?></p>
                            </div>
                            <div class="heure">
                                <p><?=$covoit['heure_depart']?></p>
                                <p><?= date('H:i', strtotime($covoit['heure_depart']) + strtotime($covoit['duree_voyage']) - strtotime('00:00:00')) ?></p>                           
                            </div>
                        </div>
                        <div class="nb_dtl_supp">
                            <p><?=htmlspecialchars($covoit['nb_place_dispo'])?></p>
                            <div id="detail_covoit">Détails</div>
                            <form action="connexion/supprimer_covoit.php" method="POST">
                                    <input name="id_covoiturage" type="hidden" value="<?= $covoit['id_covoiturage']?>">
                                    <button id="btn_supp_covoit" type="submit">x</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach?>
                <a href="ajouter_covoiturage.php">Ajoutez un covoiturage</a>
            </div>

    </main>

    <?php include 'includes/footer.php' ?>

    <script>
        document.getElementById('btn_preference').addEventListener('click', () => {
            document.getElementById('preference').style.display ='block';
        });

        document.getElementById('btn_fermer').addEventListener('click', () => {
        document.getElementById('preference').style.display ='none';
        });
    </script>

</body>
</html>