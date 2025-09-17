<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';
require_once 'classes/Covoiturage.php';
require_once 'classes/MonCompte.php';

//besoin de récupérer la liste de voiture de la personne pour les afficher
$info_utilisateur = MonCompte::recupDonnee($pdo,$id_utilisateur);
if(!$info_utilisateur['success']){
    $_SESSION['erreur_connexion'] = $info_utilisateur['message'];
    header('location: connexion.php');
    exit;
}

$voitures_utilisateur = $info_utilisateur['info_voiture'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = Covoiturage::creationCovoiturage($pdo, $id_utilisateur, $_POST);
    if ($result['success']) {
        header("Location: mon_compte.php");
        exit;
    } else {
        $_SESSION['erreur_ajout_covoiturage'] = $result['message'];
        header("Location: ajouter_covoiturage.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Covoiturage - Eco Ride</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="add-ride-container">
            <h1>Créer un nouveau covoiturage</h1>
            
            <!-- Message d'erreur -->
            <?php if (isset($erreur_ajout_covoiturage) && $erreur_ajout_covoiturage): ?>
                <div class="message message-error"><?= htmlspecialchars($erreur_ajout_covoiturage) ?></div>
            <?php endif; ?>

            <form method="POST" class="add-ride-form">
                <fieldset class="add-ride-fieldset">
                    <legend>Informations du trajet</legend>

                    <!-- Section Date et horaires -->
                    <div class="ride-form-section">
                        <h3>Date et horaires</h3>
                        
                        <div class="ride-form-group">
                            <label for="date_depart">Date de départ</label>
                            <input type="date" name="date_depart" id="date_depart" 
                                   value="<?php echo (new DateTime())->format('Y-m-d'); ?>" required>
                        </div>

                        <div class="ride-form-group">
                            <label>Heure de départ</label>
                            <div class="time-input-group">
                                <input type="number" name="heure_depart" min="0" max="23" placeholder="14" required>
                                <span class="time-label">H</span>
                                <input type="number" name="min_depart" min="0" max="59" placeholder="30" required>
                                <span class="time-label">min</span>
                            </div>
                        </div>

                        <div class="ride-form-group">
                            <label>Durée du voyage</label>
                            <div class="time-input-group">
                                <input type="number" name="duree_voyage_heure" min="0" placeholder="2" required>
                                <span class="time-label">H</span>
                                <input type="number" name="duree_voyage_min" min="0" max="59" placeholder="15" required>
                                <span class="time-label">min</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section Trajet -->
                    <div class="ride-form-section">
                        <h3>Itinéraire</h3>
                        
                        <div class="ride-form-grid">
                            <div class="ride-form-group">
                                <label for="lieu_depart">Lieu de départ</label>
                                <input type="text" name="lieu_depart" id="lieu_depart" 
                                       placeholder="Ex: Nancy, Metz" required>
                            </div>

                            <div class="ride-form-group">
                                <label for="lieu_arrive">Lieu d'arrivée</label>
                                <input type="text" name="lieu_arrive" id="lieu_arrive" 
                                       placeholder="Ex: Metz, Paris" required>
                            </div>
                        </div>
                    </div>

                    <!-- Section Prix -->
                    <div class="ride-form-section">
                        <h3>Tarif</h3>
                        
                        <div class="ride-form-group">
                            <label for="prix_personne">Prix par passager</label>
                            <div class="price-input-group">
                                <input type="number" name="prix_personne" id="prix_personne" 
                                       min="1" placeholder="15" required>
                                <span class="price-symbol">€</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section Voiture -->
                    <div class="ride-form-section">
                        <h3>Véhicule</h3>
                        
                        <div class="car-selection">
                            <?php if (empty($voitures_utilisateur)): ?>
                                <div class="no-car-message">
                                    <p>Vous devez d'abord ajouter une voiture pour créer un covoiturage.</p>
                                    <a href="ajouter_voiture.php" class="btn btn-primary">Ajouter une voiture</a>
                                </div>
                            <?php else: ?>
                                <div class="ride-form-group">
                                    <label for="id_voiture">Choisissez votre voiture</label>
                                    <select name="id_voiture" id="id_voiture" class="car-select" required>
                                        <option value="">-- Sélectionner une voiture --</option>
                                        <?php foreach ($voitures_utilisateur as $voiture): ?>
                                            <option value="<?= $voiture['id_voiture'] ?>">
                                                <?= htmlspecialchars($voiture['marque']) ?> - <?= htmlspecialchars($voiture['modele']) ?> 
                                                (<?= $voiture['nb_place'] ?> place<?= $voiture['nb_place'] > 1 ? 's' : '' ?> - <?= htmlspecialchars($voiture['energie']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($voitures_utilisateur)): ?>
                        <button type="submit" class="add-ride-submit">Créer le covoiturage</button>
                    <?php endif; ?>

                </fieldset>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>