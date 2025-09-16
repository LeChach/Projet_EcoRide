<?php
require_once 'connexion/log.php';
require_once 'connexion/session.php';
require_once 'fonction_php/fonction.php';
require_once 'classes/Covoiturage.php';

// Valeurs par défaut pour le formulaire
$lieu_depart = $_GET['lieu_depart'] ?? null;
$lieu_arrive = $_GET['lieu_arrive'] ?? null;
$date_depart = $_GET['date_depart'] ?? null;
$nb_places_voulu_par_le_passager = $_GET['nb_place'] ?? 1;

$energies = null;
$prix_max = null;
$duree_max = null;
$avis_min = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lieu_depart = $_POST['lieu_depart'] ?? $lieu_depart;
    $lieu_arrive = $_POST['lieu_arrive'] ?? $lieu_arrive;
    $date_depart = $_POST['date_depart'] ?? $date_depart;
    $nb_places_voulu_par_le_passager = $_POST['nb_place'] ?? $nb_places_voulu_par_le_passager;

    $energies = !empty($_POST['energie']) ? $_POST['energie'] : null;
    $prix_max = isset($_POST['prix_max']) ? floatval($_POST['prix_max']) : null;
    $duree_max = isset($_POST['duree_max']) ? intval($_POST['duree_max']) : null;
    $avis_min = isset($_POST['avis_min']) ? floatval($_POST['avis_min']) : 0;

    // Conversion durée en format HH:MM:SS
    $heures = floor($duree_max / 60);
    $minutes = $duree_max % 60;
    $duree_time = sprintf('%02d:%02d:00', $heures, $minutes);

    $recherche_covoit = Covoiturage::rechercheFiltrerCovoiturage(
        $pdo,
        $lieu_depart,
        $lieu_arrive,
        $date_depart,
        $nb_places_voulu_par_le_passager,
        $energies,
        $prix_max,
        $duree_time,
        $avis_min
    );
} elseif ($lieu_depart && $lieu_arrive && $date_depart) {
    $recherche_covoit = Covoiturage::rechercheCovoiturage(
        $pdo,
        $lieu_depart,
        $lieu_arrive,
        $date_depart,
        $nb_places_voulu_par_le_passager
    );
} else {
    $recherche_covoit = null;
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
</head>
<body>

    <?php include 'includes/header.php' ?>

    <main class="recherche_page">

      
        <div class="bar_recherche_wrapper">
            <?php include 'includes/bar_recherche.php' ?>
        </div>

        <?php if ($participation_ok): ?>
            <p style="color:green;"><?= htmlspecialchars($participation_ok) ?></p>
        <?php elseif ($erreur_participation): ?>
            <p style="color:red;"><?= htmlspecialchars($erreur_participation) ?></p>
        <?php endif;?>

        <?php if ($recherche_covoit && !empty($recherche_covoit['info_covoiturage'])) : ?>

            <div class="conteneur_recherche">

                <button class="bouton_filtre_mobile" >Filtres</button>

                <aside class="colonne_filtre" id="filtersPanel">
                    <form action="recherche.php" method="POST" id="filtersForm">

                        <div class="filters-header">
                            <h2>Filtres</h2>
                            <button class="close-filters">&times;</button>
                        </div>

                        <!-- Type d'énergie -->
                        <div class="filter-section">
                            <h3>Type d'énergie</h3>
                            <label class="filter-option">
                                <img src="assets/icons/icon_car_profil_vert.png" alt="Électrique">
                                <span>Électrique</span>
                                <input type="radio" name="energie" value="Electrique">
                            </label>
                            <label class="filter-option">
                                <img src="assets/icons/icon_car_profil_vert.png" alt="Hybride">
                                <span>Hybride</span>
                                <input type="radio" name="energie" value="Hybride">
                            </label>
                            <label class="filter-option">
                                <img src="assets/icons/icon_car_profil.png" alt="Essence">
                                <span>Essence</span>
                                <input type="radio" name="energie" value="Essence">
                            </label>
                            <label class="filter-option">
                                <img src="assets/icons/icon_car_profil.png" alt="Diesel">
                                <span>Diesel</span>
                                <input type="radio" name="energie" value="Diesel">
                            </label>
                        </div>

                        <!-- Prix -->
                        <div class="filter-section">
                            <h3>Prix maximum</h3>
                            <input type="range" id="slider_prix" name="prix_max" min="0" max="100" step="2" value="50">
                            <span id="prix_value">50 €</span>
                        </div>


                        <!-- Durée -->
                        <div class="filter-section">
                            <h3>Durée maximum</h3>
                            <input type="range" name="duree_max" min="0" max="600" step="10" id="slider_duree" value="300">
                            <span id="duree_value">5h 0min</span>
                        </div>

                        <!-- Avis -->
                        <div class="filter-section">
                            <h3>Avis minimum</h3>
                            <select name="avis_min">
                                <option value="0">Tous</option>
                                <option value="1">⭐ et plus</option>
                                <option value="2">⭐⭐ et plus</option>
                                <option value="3">⭐⭐⭐ et plus</option>
                                <option value="4">⭐⭐⭐⭐ et plus</option>
                                <option value="5">⭐⭐⭐⭐⭐ uniquement</option>
                            </select>
                        </div>

                        <input type="hidden" name="lieu_depart" value="<?=$lieu_depart?>">
                        <input type="hidden" name="lieu_arrive" value="<?=$lieu_arrive?>">
                        <input type="hidden" name="date_depart" value="<?=$date_depart?>">
                        <input type="hidden" name="nb_place" value="<?=$nb_places_voulu_par_le_passager?>">
                        <!-- Bouton -->
                        <button type="submit" class="btn-filtrer">Filtrer</button>
                        <button type="reset" class="btn-reset">Réinitialiser</button>

                    </form>
                </aside>
                
                <section class="colonne_resultats">
                    <?php foreach ($recherche_covoit['info_covoiturage'] as $covoit) : ?>
                        <?php if ($covoit['u_id'] === $id_utilisateur) continue; ?>

                        <div class="covoiturage_card">

                            <div class="entete_covoit">
                                <img src="assets/pp/<?= htmlspecialchars($covoit['u_photo'])?>" alt="PP" class="pp_utilisateur">
                                <p class="pseudo"><?= htmlspecialchars($covoit['u_pseudo'])?></p>
                                <div class="note_utilisateur">
                                    <img src="assets/icons/star.png" alt="étoile">
                                    <span><?= htmlspecialchars($covoit['u_note'])?></span>
                                </div>
                                <img src="<?= ($covoit['v_energie'] == "Hybride" || $covoit['v_energie'] == "Electrique") ? 'assets/icons/icon_car_profil_vert.png' : 'assets/icons/icon_car_profil.png' ?>" 
                                     alt="voiture" class="voiture">
                                <span><?= ($covoit['v_energie'] == "Hybride" || $covoit['v_energie'] == "Electrique") ? 'Trajet écologique' : '' ?></span>
                            </div>

                            <div class="div_covoit">
                                <!-- Colonne 1 : date -->
                                <div class="date_covoit">
                                    <span>Le <?= date('d/m/Y', strtotime($covoit['c_date_depart'])) ?></span>
                                </div>

                                <!-- Colonne 2 : trajet + heures -->
                                <div class="trajet_heures">
                                    <div class="trajet">
                                        <p><?= htmlspecialchars($covoit['c_lieu_depart']) ?></p>
                                        <p><?= date('H:i',strtotime($covoit['c_duree_voyage'])) ?></p>
                                        <p><?= htmlspecialchars($covoit['c_lieu_arrive']) ?></p>
                                    </div>
                                    <div class="heure">
                                        <p><?= date('H:i',strtotime($covoit['c_heure_depart'])) ?></p>
                                        <p>🚗</p>
                                        <p>
                                            <?php 
                                            $heure_depart = strtotime($covoit['c_heure_depart']);
                                            $duree = strtotime($covoit['c_duree_voyage']) - strtotime('00:00:00');
                                            echo date('H:i', $heure_depart + $duree);
                                            ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Colonne 3 : nb places + boutons -->
                                <div class="actions_covoit">
                                    <span><?=htmlspecialchars($covoit['c_prix_personne'])?>€</span>
                                    <span><?= htmlspecialchars($covoit['c_nb_place_dispo']) ?> Place<?= ($covoit['c_nb_place_dispo'] > 1) ? 's' : '' ?> disponible<?= ($covoit['c_nb_place_dispo'] > 1) ? 's' : '' ?></span>
                                    <a href="detail.php
                                        ?id=<?= $covoit['c_id'] ?>
                                        &lieu_depart=<?=$lieu_depart?>
                                        &lieu_arrive=<?=$lieu_arrive?>
                                        &date_depart=<?=$date_depart?>
                                        &nb_place=<?=$nb_places_voulu_par_le_passager?>
                                    ">Détails</a>
                                    <form action="participation_covoit.php" method="POST">
                                        <input type="hidden" name="id_covoiturage" value="<?= $covoit['c_id'] ?>">
                                        <input type="hidden" name="nb_place" value="<?= $nb_places_voulu_par_le_passager ?>">
                                        <button type="submit">Participer</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </section>

            </div>

        <?php elseif (empty($recherche_covoit['info_covoiturage'])) : ?>
            <p>Aucun covoiturage trouvé pour votre recherche.</p>
        <?php else : ?>
            <p>Veuillez remplir le formulaire pour trouver un covoiturage</p>
        <?php endif; ?>

    </main>

    <?php include 'includes/footer.php' ?>

<script>
        document.addEventListener("DOMContentLoaded", () => {
            const filtersPanel = document.getElementById("filtersPanel");
            const openBtn = document.querySelector(".bouton_filtre_mobile");
            const closeBtn = document.querySelector(".close-filters");

            openBtn.addEventListener("click", () => {
                filtersPanel.classList.add("active");
            });

            closeBtn.addEventListener("click", () => {
                filtersPanel.classList.remove("active");
            });
        });

        const slider = document.getElementById('slider_duree');
        const output = document.getElementById('duree_value');

        slider.addEventListener('input', () => {
        let minutes = parseInt(slider.value, 10);
        if (minutes < 60) {
            output.textContent = `${minutes} min`;
        } else {
            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;
            output.textContent = `${hours}h${remainingMinutes > 0 ? remainingMinutes : ''}`;
        }
        });

        const sliderPrix = document.getElementById('slider_prix');
        const outputPrix = document.getElementById('prix_value');

        sliderPrix.addEventListener('input', () => {
            let prix = parseInt(sliderPrix.value, 10);

            outputPrix.textContent = `${prix} €`;
        });



</script>


</body>
</html>
