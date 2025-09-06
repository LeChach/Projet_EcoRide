<?php
require_once 'log.php';
require_once 'session_prive.php'; // utilisateur déjà connecté

// Récupération du POST
$id_covoiturage = (int)($_POST['id_covoiturage'] ?? 0);
$nb_place_voulu = (int)($_POST['nb_place'] ?? 0);

// Récupération des infos covoiturage
$prep_covoit = $pdo->prepare(
    "SELECT c.prix_personne, c.nb_place_dispo, c.id_conducteur, u.credit
     FROM covoiturage c
     INNER JOIN utilisateur u ON u.id_utilisateur = ? 
     WHERE c.id_covoiturage = ?"
);
$prep_covoit->execute([$id_utilisateur, $id_covoiturage]);
$covoit_info = $prep_covoit->fetch();

// Calcul du prix total
$prix_total = $nb_place_voulu * $covoit_info['prix_personne'];

try {
    $pdo->beginTransaction();

    // 1. Insert dans reservation
    $prep_res = $pdo->prepare(
        "INSERT INTO reservation (nb_place_reserve, id_passager, id_conducteur, id_covoiturage)
         VALUES (?, ?, ?, ?)"
    );
    $prep_res->execute([$nb_place_voulu, $id_utilisateur, $covoit_info['id_conducteur'], $id_covoiturage]);

    // 2. Mise à jour du crédit du passager
    $prep_credit_passager = $pdo->prepare(
        "UPDATE utilisateur SET credit = credit - ? WHERE id_utilisateur = ?"
    );
    $prep_credit_passager->execute([$prix_total, $id_utilisateur]);

    // 3. Mise à jour du nombre de places dispo
    $prep_places = $pdo->prepare(
        "UPDATE covoiturage SET nb_place_dispo = nb_place_dispo - ? WHERE id_covoiturage = ?"
    );
    $prep_places->execute([$nb_place_voulu, $id_covoiturage]);

    // 4. Création d'un virement en attente pour le conducteur
    $prep_virement = $pdo->prepare(
        "INSERT INTO virement (montant_virement, statut, id_passager, id_conducteur)
         VALUES (?,?, ?, ?)"
    );
    $prep_virement->execute([$prix_total, 'en_attente', $id_utilisateur, $covoit_info['id_conducteur']]);

    // Virement du passager (historique)
    $prep_virement_passager = $pdo->prepare(
        "INSERT INTO virement (montant_virement,statut, id_passager, id_conducteur)
        VALUES (?, ?, ?, ?)"
    );
    $prep_virement_passager->execute([$prix_total,'valider', $id_utilisateur, $covoit_info['id_conducteur']]);


    $pdo->commit();

    header("Location: ../mon_compte.php?message=reservation_validee");
    exit;


} catch (PDOException $e) {
    $pdo->rollBack();
    die("Erreur lors de la confirmation : " . $e->getMessage());
}
