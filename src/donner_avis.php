<?php
require_once 'connexion/log.php';
require_once 'connexion/session_prive.php';

$id_covoiruage = $_GET['id_c']??null;
if($id_covoiruage === null){
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
    <style>
        .rating-section {
            margin: 15px 0;
        }

        .rating-stars {
            display: flex;
            gap: 5px;
            margin: 10px 0;
        }

        .star {
            width: 30px;
            height: 30px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .star:hover {
            transform: scale(1.1);
        }

        #note-text {
            margin-left: 10px;
            font-weight: bold;
            color: #666;
        }
    </style>

</head>

<body>

    <?php include 'includes/header.php' ?>

    <form method="POST" action="mon_compte.php">
    <div class="rating-section">
        <label>Votre note :</label>
        <div class="rating-stars" id="rating">
            <img src="assets/icons/Star_black.png" class="star" data-rating="1" alt="1 étoile">
            <img src="assets/icons/Star_black.png" class="star" data-rating="2" alt="2 étoiles">
            <img src="assets/icons/Star_black.png" class="star" data-rating="3" alt="3 étoiles">
            <img src="assets/icons/Star_black.png" class="star" data-rating="4" alt="4 étoiles">
            <img src="assets/icons/Star_black.png" class="star" data-rating="5" alt="5 étoiles">
        </div>
        <input type="hidden" name="note" id="note-input" value="1">
        <input type="hidden" name="id_covoiturage" value="<?= $id_covoiruage?>">


        <span id="note-text">Très mauvais</span>
    </div>
    
    <textarea name="commentaire" placeholder="Votre commentaire..."></textarea>
    <input type="hidden" name="id_covoiturage" value="<?=$id_covoiruage?>">
    <button type="submit">Envoyer l'évaluation</button>
</form>

    <?php include 'includes/footer.php' ?>

    <script>
            document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const noteInput = document.getElementById('note-input');
            const noteText = document.getElementById('note-text');
            
            // Textes pour chaque note (plus de "0")
            const noteTextes = {
                1: 'Très mauvais',
                2: 'Mauvais', 
                3: 'Correct',
                4: 'Bon',
                5: 'Excellent'
            };
            
            // Initialiser avec 1 étoile par défaut
            setRating(1);
            
            // Gestion du clic sur une étoile
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.rating);
                    setRating(rating);
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
                noteText.textContent = noteTextes[rating];
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
