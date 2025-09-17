<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';

$id_covoiturage = $_GET['id_c']??null;
if($id_covoiturage === null){
    $_SESSION['erreur_avis'] = 'Covoiturage non trouvé';
    header("Location: mon_compte.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donnez votre avis - Eco Ride</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include 'includes/header.php' ?>

    <main>
        <div class="review-container">
            <h1>Donnez votre avis</h1>

            <!-- Information sur l'importance de l'avis -->
            <div class="helper-info">
                <h3>Votre avis compte !</h3>
                <p>Aidez la communauté EcoRide en partageant votre expérience. 
                   Votre évaluation permettra d'améliorer la qualité des trajets pour tous.</p>
            </div>

            <form method="POST" action="mon_compte.php" class="review-form">
                
                <!-- Section notation -->
                <div class="rating-section">
                    <label for="rating">Votre évaluation</label>
                    <div class="rating-stars" id="rating">
                        <img src="assets/icons/Star_black.png" class="star" data-rating="1" alt="1 étoile">
                        <img src="assets/icons/Star_black.png" class="star" data-rating="2" alt="2 étoiles">
                        <img src="assets/icons/Star_black.png" class="star" data-rating="3" alt="3 étoiles">
                        <img src="assets/icons/Star_black.png" class="star" data-rating="4" alt="4 étoiles">
                        <img src="assets/icons/Star_black.png" class="star" data-rating="5" alt="5 étoiles">
                    </div>
                    <input type="hidden" name="note" id="note-input" value="1">
                    <div class="note-text very-bad" id="note-text">Très mauvais</div>
                </div>

                <!-- Section commentaire -->
                <div class="comment-section">
                    <label for="commentaire">Votre commentaire (optionnel)</label>
                    <textarea name="commentaire" id="commentaire" class="comment-textarea" 
                              placeholder="Partagez votre expérience : ponctualité, convivialité, conduite, etc."></textarea>
                </div>

                <!-- Champs cachés -->
                <input type="hidden" name="type_POST" value="ajouter_avis">
                <input type="hidden" name="id_covoiturage" value="<?= htmlspecialchars($id_covoiturage) ?>">

                <!-- Bouton d'envoi -->
                <div class="submit-section">
                    <button type="submit" class="submit-button">Envoyer mon évaluation</button>
                </div>

            </form>
        </div>
    </main>

    <?php include 'includes/footer.php' ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const noteInput = document.getElementById('note-input');
            const noteText = document.getElementById('note-text');
            
            // Textes et classes pour chaque note
            const noteConfig = {
                1: { text: 'Très mauvais', class: 'very-bad' },
                2: { text: 'Mauvais', class: 'bad' }, 
                3: { text: 'Correct', class: 'correct' },
                4: { text: 'Bon', class: 'good' },
                5: { text: 'Excellent', class: 'excellent' }
            };
            
            // Initialiser avec 1 étoile par défaut
            setRating(1);
            
            // Gestion du clic sur une étoile
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.rating);
                    setRating(rating);
                    
                    // Animation sur l'étoile cliquée
                    this.classList.add('selected');
                    setTimeout(() => {
                        this.classList.remove('selected');
                    }, 1000);
                });
                
                // Effet hover
                star.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.dataset.rating);
                    highlightStars(rating);
                });
            });
            
            // Remettre l'affichage normal quand on sort des étoiles
            document.getElementById('rating').addEventListener('mouseleave', function() {
                const currentRating = parseInt(noteInput.value);
                setRating(currentRating);
            });
            
            function setRating(rating) {
                noteInput.value = rating;
                
                // Mettre à jour le texte et la classe
                const config = noteConfig[rating];
                noteText.textContent = config.text;
                noteText.className = 'note-text ' + config.class;
                
                updateStars(rating);
            }
            
            function highlightStars(rating) {
                updateStars(rating);
            }
            
            function updateStars(rating) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.src = 'assets/icons/star.png';
                    } else {
                        star.src = 'assets/icons/Star_black.png';
                    }
                });
            }
        });
    </script>
</body>
</html>