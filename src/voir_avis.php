<?php
require_once 'config/database.php';
require_once 'connexion/session_prive.php';
require_once 'classes/MonCompte.php';

$id_covoiturage = $_GET['id_c']??null;
if($id_covoiturage === null){
    $_SESSION['erreur_avis'] = 'Covoiturage non trouvé';
    header("Location: mon_compte.php");
    exit;
}

$avis = MonCompte::voirAvis($pdo,$id_utilisateur,$id_covoiturage);
if(!$avis['success']){
    $_SESSION['erreur'] = $avis['message'];
    header("Location: mon_compte.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis reçus - Eco Ride</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include 'includes/header.php' ?>

    <main>
        <div class="avis-container">
            <h1>Avis reçus</h1>
        </div>

        <div class="div_avis">
            <?php if (empty($avis['avis'])) : ?>
                <div class="empty-reviews">
                    Aucun avis trouvé pour ce covoiturage
                </div>
            <?php else : ?>

                <?php foreach($avis['avis'] as $avis_passager): ?>
                    <div class="avis">
                        <div class="entete_avis"> 
                            <?= htmlspecialchars($avis_passager['passager']) ?>
                            <img src="assets/pp/<?= htmlspecialchars($avis_passager['photo']) ?>" alt="photo de profil">
                        </div>
                        
                        <div class="note">
                            <?php 
                            $note = $avis_passager['note'];
                            switch ($note) {
                                case 1:
                                    $notation = 'Très mauvais';
                                    $classe_note = 'note-very-bad';
                                    break;
                                case 2:
                                    $notation = 'Mauvais';
                                    $classe_note = 'note-bad';
                                    break;
                                case 3:
                                    $notation = 'Correct';
                                    $classe_note = 'note-correct';
                                    break;
                                case 4:
                                    $notation = 'Bon';
                                    $classe_note = 'note-good';
                                    break;
                                case 5:
                                    $notation = 'Excellent';
                                    $classe_note = 'note-excellent';
                                    break;
                                default:
                                    $notation = '';
                                    $classe_note = '';
                            }
                            ?>
                            
                            <div class="note-rating <?= $classe_note ?>">
                                <div class="note-number"><?= htmlspecialchars($note) ?>/5</div>
                                <div class="note-stars">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <span class="<?= $i <= $note ? 'star-filled' : 'star-empty' ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                                <div class="note-text"><?= htmlspecialchars($notation) ?></div>
                            </div>

                            <div class="comment-section">
                                <?php if(empty($avis_passager['commentaire'])) : ?>
                                    <p class="no-comment">Pas de commentaire</p>
                                <?php else: ?>
                                    <p class="comment-content"><?= htmlspecialchars($avis_passager['commentaire']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="close-section">
                <a href="mon_compte.php" class="close-button">Fermer</a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php' ?>
</body>
</html>