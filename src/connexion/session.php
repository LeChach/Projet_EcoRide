<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//permet de recup l'id de l'utilisateur connecté
$id_utilisateur = $_SESSION['id_utilisateur'] ?? null;

$erreur = $_SESSION['erreur'] ?? null;
unset($_SESSION['erreur']);

$erreur_connexion = $_SESSION['erreur_connexion'] ?? null;
unset($_SESSION['erreur_connexion']);

$erreur_inscription = $_SESSION['erreur_inscription'] ?? null;
unset($_SESSION['erreur_inscription']);

$erreur_ajout_voiture = $_SESSION['erreur_ajout_voiture'] ?? null;
unset($_SESSION['erreur_ajout_voiture']);

$erreur_ajout_covoiturage = $_SESSION['erreur_ajout_covoiturage'] ?? null;
unset($_SESSION['erreur_ajout_covoiturage']);

$erreur_avis = $_SESSION['erreur_avis'] ?? null;
unset($_SESSION['erreur_avis']);




$recherche_covoit = $_SESSION['recherche_covoit'] ?? null;
unset($_SESSION['recherche_covoit']);
?>