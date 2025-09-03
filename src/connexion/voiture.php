<?php
require_once 'log.php';
require_once 'session_prive.php';

try{

    $prep_voiture = $pdo->prepare(
        "SELECT * 
        FROM voiture
        WHERE id_conducteur = ?"
    );
    $prep_voiture->execute([$id_utilisateur]);
    $voitures_utilisateur = $prep_voiture->fetchAll();

} catch (PDOException $e) {
        die ("Erreur connexion BDD : ".$e->getMessage());
}
?>