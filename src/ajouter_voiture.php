<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajoutez Voiture - Eco Ride</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    /* Conteneur principal */
    .conteneur-couleur {
      width: 250px;
      margin: 20px auto;
      position: relative;
    }

    label {
      display: block;
      margin-bottom: 8px;
    }

    /* Champ texte qui affichera le nom de la couleur */
    input#champ-couleur {
      border: 1px solid #aaa;
      padding: 8px;
      width: 150px;
      text-transform: capitalize;
    }

    /* Petit carré qui montre la couleur sélectionnée */
    .apercu-couleur {
      display: inline-block;
      width: 40px;
      height: 36px;
      border: 1px solid #aaa;
      margin-left: 8px;
      vertical-align: middle;
    }

    /* La palette complète (liste des couleurs) */
    .palette-couleur {
      display: none; /* cachée par défaut */
      position: absolute;
      top: 60px;
      left: 0;
      background: #f3f3f3;
      padding: 5px;
      border: 1px solid #ccc;
      box-shadow: 0 0 5px rgba(0,0,0,0.2);
      width: 200px;
    }

    /* Chaque couleur de la palette */
    .option-couleur {
      width: 20px;
      height: 20px;
      margin: 3px;
      float: left;
      border: 1px solid #ddd;
      cursor: pointer;
    }

    .option-couleur:hover {
      border: 1px solid #333;
    }

    .clearfix::after {
      content: "";
      display: table;
      clear: both;
    }
  </style>

</head>

<body>

    <?php include 'includes/header.php' ?>

    <form action="connexion/new_voiture.php" method="POST">
        <fieldset>
            <legend>Ajouter une nouvelle voiture :</legend>

            <?php if ($erreur_ajout_voiture): ?>
              <p style="color:red;"><?= $erreur_ajout_voiture ?></p>
            <?php endif; ?>            

            <label for="marque">Marque :</label>
            <input type="text" name="marque" id="marque" required>

            <label for="modele">Modèle :</label>
            <input type="text" name="modele" id="modele" required>

            <label for="immat">Immatriculation :</label>
            <input type="text" name="immat" id="immat" required>

            <label for="immat">Date de première immatriculation :</label>
            <input type="date" name="date_premiere_immat" id="immat" value="2025-01-01" required>

            <fieldset>
                <legend>Je roule en :</legend>
                    <input type="radio" name ="energie" value="Essence" required>
                    <label for="energie">thermique (Essence)</label>
                    <input type="radio" name ="energie" value="Diesel">
                    <label for="energie">thermique (Diesel)</label>
                    <input type="radio" name ="energie" value="Hybride">
                    <label for="energie">Hybride</label>
                    <input type="radio" name ="energie" value="Electrique">
                    <label for="energie">Electrique</label>
            </fieldset>

            <fieldset>
                <legend>Choisir la couleur de votre voiture</legend>
                <div class="conteneur-couleur">
                    <label for="champ-couleur">Couleur :</label>
                    <input type="text" name="couleur" id="champ-couleur" readonly placeholder="Appuyez ici pour selectionner une couleur" required>
                    <div class="apercu-couleur" id="apercu-couleur"></div>
                    <div class="palette-couleur clearfix" id="palette-couleur"></div>
                </div>
            </fieldset>

            <label for="nb_place">Nombre de place Disponible :</label>
            <input type="number" name="nb_place" min="1" required>

        </fieldset>

        <button type="submit">Ajoutez ma nouvelle voiture</button>

    </form>

<?php include 'includes/footer.php' ?>
<script>
    const couleurs = {
        "Noir": "#000000",
        "Blanc": "#FFFFFF",
        "Gris foncé": "#A9A9A9",
        "Gris": "#808080",
        "Bordeau": "#800000",
        "Rouge": "#FF0000",
        "Bleu foncé": "#00008B",
        "Bleu": "#0000FF",
        "Vert foncé": "#006400",
        "Vert": "#008000",
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

    //REMPLISSAGE DE LAPERCU DES COULEURS
    for (let nom in couleurs){
        let div = document.createElement("div");
        div.classList.add("option-couleur");
        div.style.backgroundColor = couleurs[nom];
        //pour stocker le nom a afficher
        div.dataset.nom = nom;
        div.dataset.hex = couleurs[nom];
        paletteCouleur.appendChild(div);
    }

    champCouleur.addEventListener("click", (e)=>{
        e.stopPropagation();
        paletteCouleur.style.display="block";
    });

    paletteCouleur.addEventListener("click", (e)=>{
        if(e.target.classList.contains("option-couleur")){
            let nom = e.target.dataset.nom;
            let hex = e.target.dataset.hex;
            champCouleur.value = nom;
            apercuCouleur.style.backgroundColor = hex;
            paletteCouleur.style.display="none";
        }});
    document.body.addEventListener("click", ()=>{
        paletteCouleur.style.display="none";
        });
</script>

</body>
</html>