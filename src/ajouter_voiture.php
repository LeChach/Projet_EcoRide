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
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <?php include 'includes/header.php' ?>

    <form action="mon_compte.php" method="POST">
        <input type="hidden" name="type_POST" value="ajouter_voiture">

        <h1>Ajouter une nouvelle voiture :</h1>
        <?php if ($erreur_ajout_voiture): ?>
            <p style="color:red;"><?= $erreur_ajout_voiture ?></p>
        <?php endif; ?> 

        <div class="info_new_voiture">
            
            <span for="marque">Marque :</span>
            <input type="text" name="marque" id="marque" required>

            <span for="modele">Modèle :</span>
            <input type="text" name="modele" id="modele" required>

            <span for="nb_place">Nombre de place Disponible :</span>
            <input type="number" name="nb_place" min=1 value="1" required>

        </div>
        <p></p>
        <p></p>
        <div class="info_new_voiture">

            <span for="immat">Immatriculation :</span>
            <input type="text" name="immat" id="immat" required>

            <span for="immat">Date de première immatriculation :</span>
            <input type="date" name="date_premiere_immat" id="immat" value="2025-01-01" required>

        </div>
        <p></p>
        <p></p>
        <div class="info_new_voiture">

            <p>Je roule en :</p>
            <span for="energie">thermique (Essence)</span>
            <input type="radio" name ="energie" value="Essence" required><br>
            
            <span for="energie">thermique (Diesel)</span>
            <input type="radio" name ="energie" value="Diesel"><br>

            <span for="energie">Hybride</span>
            <input type="radio" name ="energie" value="Hybride"><br>
          
            <span for="energie">Electrique</span>
            <input type="radio" name ="energie" value="Electrique"><br>
                
        </div>
        <p></p>
        <p></p>
        <div class="info_new_voiture">
            <legend>Choisir la couleur de votre voiture</legend>
            <div class="conteneur-couleur">
            <span for="champ-couleur">Couleur :</span>
            <input type="text" name="couleur" id="champ-couleur" readonly placeholder="Appuyez ici pour selectionner une couleur" required>
            <div class="apercu-couleur" id="apercu-couleur"></div>
            <div class="palette-couleur clearfix" id="palette-couleur"></div>
        </div>
        </div>
        <p></p>
        <p></p>
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