<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Voiture - Eco Ride</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include 'includes/header.php' ?>

    <main>
        <div class="add-car-container">
            <h1>Ajouter une nouvelle voiture</h1>
            
            <!-- Message d'erreur -->
            <?php if (isset($erreur_ajout_voiture) && $erreur_ajout_voiture): ?>
                <div class="message message-error"><?= htmlspecialchars($erreur_ajout_voiture) ?></div>
            <?php endif; ?>

            <form action="mon_compte.php" method="POST" class="add-car-form">
                <input type="hidden" name="type_POST" value="ajouter_voiture">

                <!-- Section informations générales -->
                <div class="car-form-section">
                    <h3>Informations générales</h3>
                    <div class="car-form-grid">
                        <div class="car-form-group">
                            <label for="marque">Marque</label>
                            <input type="text" name="marque" id="marque" placeholder="Ex: Renault, Peugeot..." required>
                        </div>

                        <div class="car-form-group">
                            <label for="modele">Modèle</label>
                            <input type="text" name="modele" id="modele" placeholder="Ex: Clio, 208..." required>
                        </div>

                        <div class="car-form-group">
                            <label for="nb_place">Nombre de places disponibles</label>
                            <input type="number" name="nb_place" id="nb_place" min="1" max="8" value="1" required>
                        </div>
                    </div>
                </div>

                <!-- Section immatriculation -->
                <div class="car-form-section">
                    <h3>Immatriculation</h3>
                    <div class="car-form-grid">
                        <div class="car-form-group">
                            <label for="immat">Numéro d'immatriculation</label>
                            <input type="text" name="immat" id="immat" placeholder="Ex: AB-123-CD" required>
                        </div>

                        <div class="car-form-group">
                            <label for="date_premiere_immat">Date de première immatriculation</label>
                            <input type="date" name="date_premiere_immat" id="date_premiere_immat" value="2025-01-01" required>
                        </div>
                    </div>
                </div>

                <!-- Section énergie -->
                <div class="car-form-section energy-section">
                    <p>Je roule en :</p>
                    <div class="energy-options">
                        <div class="energy-option">
                            <input type="radio" name="energie" id="essence" value="Essence" required>
                            <label for="essence">Thermique (Essence)</label>
                        </div>
                        
                        <div class="energy-option">
                            <input type="radio" name="energie" id="diesel" value="Diesel">
                            <label for="diesel">Thermique (Diesel)</label>
                        </div>

                        <div class="energy-option">
                            <input type="radio" name="energie" id="hybride" value="Hybride">
                            <label for="hybride">Hybride</label>
                        </div>

                        <div class="energy-option">
                            <input type="radio" name="energie" id="electrique" value="Electrique">
                            <label for="electrique">Électrique</label>
                        </div>
                    </div>
                </div>

                <!-- Section couleur -->
                <div class="car-form-section color-section">
                    <legend>Choisir la couleur de votre voiture</legend>
                    <div class="color-container">
                        <div class="color-input-group">
                            <label for="champ-couleur">Couleur :</label>
                            <input type="text" name="couleur" id="champ-couleur" readonly 
                                   placeholder="Cliquez ici pour sélectionner une couleur" required>
                            <div class="color-preview" id="apercu-couleur"></div>
                        </div>
                        <div class="color-palette" id="palette-couleur">
                            <div class="color-palette-grid" id="color-grid"></div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary add-car-submit">Ajouter ma nouvelle voiture</button>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php' ?>

    <script>
        const couleurs = {
            "Noir": "#000000",
            "Blanc": "#FFFFFF",
            "Gris foncé": "#696969",
            "Gris": "#C0C0C0",
            "Bordeaux": "#800000",
            "Rouge": "#FF0000",
            "Bleu foncé": "#00008B",
            "Bleu": "#00BFFF",
            "Vert foncé": "#006400",
            "Vert": "#32CD32",
            "Marron": "#8B4513",
            "Beige": "#F5F5DC",
            "Orange": "#FFA500",
            "Jaune": "#FFFF00",
            "Violet": "#800080",
            "Rose": "#FFC0CB"
        };

        const champCouleur = document.getElementById("champ-couleur");
        const apercuCouleur = document.getElementById("apercu-couleur");
        const paletteCouleur = document.getElementById("palette-couleur");
        const colorGrid = document.getElementById("color-grid");

        // Création de la grille de couleurs
        for (let nom in couleurs) {
            let div = document.createElement("div");
            div.classList.add("color-option");
            div.style.backgroundColor = couleurs[nom];
            div.dataset.nom = nom;
            div.dataset.hex = couleurs[nom];
            div.title = nom;
            colorGrid.appendChild(div);
        }

        champCouleur.addEventListener("click", (e) => {
            e.stopPropagation();
            paletteCouleur.classList.toggle("show");
        });

        colorGrid.addEventListener("click", (e) => {
            if (e.target.classList.contains("color-option")) {
                let nom = e.target.dataset.nom;
                let hex = e.target.dataset.hex;
                champCouleur.value = nom;
                apercuCouleur.style.backgroundColor = hex;
                paletteCouleur.classList.remove("show");
            }
        });

        document.body.addEventListener("click", (e) => {
            if (!champCouleur.contains(e.target) && !paletteCouleur.contains(e.target)) {
                paletteCouleur.classList.remove("show");
            }
        });
    </script>
</body>
</html>