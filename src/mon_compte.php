<?php
session_start();
require_once 'connexion/log.php';
if(!isset($_SESSION['user_id'])){
    header('Location: connexion.php');
    exit;
}

//recup des info pour un affichage perso
$user_id = $_SESSION['user_id'];
try {
    $prep = $pdo->prepare("SELECT pseudo FROM utilisateur WHERE id_utilisateur = ?");
    $prep->execute([$user_id]);
    $pseudo = $prep->fetch();
} catch (PDOException $e) {
    die("Erreur de BBD : ".$e->getMessage());
}


?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Eco Ride</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>


<body>
    <?php include 'includes/header.php' ?>

    <main>
        <div>
            <h1>Bienvenue <?= htmlspecialchars($pseudo['pseudo'])?>!</h1>

            <form action="connexion/deconnexion.php">
                <button type="submit">se déconnecter</button>
            </form>
        </div>

    </main>

    <?php include 'includes/footer.php' ?>
</body>
</html>