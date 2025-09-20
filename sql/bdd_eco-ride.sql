-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 20 sep. 2025 à 17:48
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bdd_eco-ride`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id_avis` int(11) NOT NULL,
  `commentaire` text DEFAULT NULL,
  `note` int(11) NOT NULL,
  `statut_avis` enum('en_attente','refuser','valider') DEFAULT 'en_attente',
  `date_avis` datetime DEFAULT current_timestamp(),
  `id_passager` int(11) NOT NULL,
  `id_conducteur` int(11) NOT NULL,
  `id_covoiturage` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id_avis`, `commentaire`, `note`, `statut_avis`, `date_avis`, `id_passager`, `id_conducteur`, `id_covoiturage`) VALUES
(2, 'Très bon conducteur, ponctuel et sympa !', 4, 'valider', '2025-06-15 16:30:00', 4, 1, 3),
(3, 'Conduite un peu rapide mais trajet agréable', 3, 'valider', '2025-06-15 17:00:00', 5, 1, 3),
(4, 'Parfait, je recommande', 4, 'valider', '2025-09-20 10:00:00', 1, 1, 5),
(5, 'RAS, bon covoiturage', 4, 'valider', '2025-09-20 10:30:00', 4, 1, 5),
(6, 'Excellent conducteur, très professionnel', 4, 'valider', '2025-07-03 12:15:00', 3, 2, 4),
(7, 'Super trajet, conversation intéressante', 4, 'valider', '2025-07-03 12:30:00', 6, 2, 4),
(8, 'Conduite correcte mais retard au départ', 3, 'valider', '2025-07-20 19:15:00', 1, 3, 5),
(9, 'Trajet pas très agréable, musique trop forte', 2, 'valider', '2025-07-20 19:30:00', 4, 3, 5),
(10, 'Ça va, sans plus', 3, 'valider', '2025-09-05 16:15:00', 5, 3, 7),
(11, 'Conductrice parfaite ! Très agréable et sécurisante', 5, 'valider', '2025-08-12 13:15:00', 2, 6, 6),
(12, 'Excellent trajet, je recommande vivement', 5, 'valider', '2025-09-05 16:30:00', 6, 6, 7),
(13, 'Très bien, ponctuelle et sympathique', 4, 'valider', '2025-09-20 11:00:00', 5, 6, 7);

-- --------------------------------------------------------

--
-- Structure de la table `commission`
--

CREATE TABLE `commission` (
  `id_commission` int(11) NOT NULL,
  `id_covoiturage` int(11) NOT NULL,
  `id_conducteur` int(11) NOT NULL,
  `montant` decimal(7,2) NOT NULL DEFAULT 2.00,
  `date_commission` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commission`
--

INSERT INTO `commission` (`id_commission`, `id_covoiturage`, `id_conducteur`, `montant`, `date_commission`) VALUES
(1, 1, 4, 2.00, '2025-09-18 16:03:33'),
(2, 2, 4, 2.00, '2025-09-18 16:04:07'),
(3, 3, 1, 2.00, '2025-06-15 14:00:00'),
(4, 4, 2, 2.00, '2025-07-03 09:30:00'),
(5, 5, 3, 2.00, '2025-07-20 16:15:00'),
(6, 6, 6, 2.00, '2025-08-12 11:00:00'),
(7, 7, 3, 2.00, '2025-09-05 13:45:00'),
(8, 8, 2, -2.00, '2025-08-25 16:00:00'),
(9, 9, 1, 2.00, '2025-10-20 10:00:00'),
(10, 10, 2, 2.00, '2025-10-22 14:00:00'),
(11, 11, 3, 2.00, '2025-10-25 16:00:00'),
(12, 12, 6, 2.00, '2025-10-28 10:15:00'),
(13, 13, 3, 2.00, '2025-11-01 09:45:00'),
(14, 14, 1, 2.00, '2025-11-02 12:00:00'),
(15, 15, 2, -2.00, '2025-11-03 15:30:00');

-- --------------------------------------------------------

--
-- Structure de la table `covoiturage`
--

CREATE TABLE `covoiturage` (
  `id_covoiturage` int(11) NOT NULL,
  `date_depart` date NOT NULL,
  `heure_depart` time NOT NULL,
  `duree_voyage` time NOT NULL,
  `lieu_depart` varchar(50) NOT NULL,
  `lieu_arrive` varchar(50) NOT NULL,
  `nb_place_dispo` int(11) NOT NULL,
  `prix_personne` decimal(7,2) NOT NULL,
  `statut_covoit` enum('planifier','en_cours','terminer','annuler') DEFAULT 'planifier',
  `id_conducteur` int(11) NOT NULL,
  `id_voiture` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `covoiturage`
--

INSERT INTO `covoiturage` (`id_covoiturage`, `date_depart`, `heure_depart`, `duree_voyage`, `lieu_depart`, `lieu_arrive`, `nb_place_dispo`, `prix_personne`, `statut_covoit`, `id_conducteur`, `id_voiture`) VALUES
(1, '2025-09-18', '16:00:00', '02:00:00', 'Nancy', 'Strasbourg', 0, 7.00, 'terminer', 4, 2),
(2, '2025-09-19', '20:00:00', '02:00:00', 'Strasbourg', 'Nancy', 2, 7.00, 'planifier', 4, 2),
(3, '2025-06-15', '14:00:00', '01:30:00', 'Nancy', 'Metz', 0, 8.00, 'terminer', 1, 3),
(4, '2025-07-03', '09:30:00', '02:45:00', 'Strasbourg', 'Reims', 0, 12.00, 'terminer', 2, 4),
(5, '2025-07-20', '16:15:00', '03:00:00', 'Metz', 'Mulhouse', 0, 15.00, 'terminer', 3, 5),
(6, '2025-08-12', '11:00:00', '02:15:00', 'Colmar', 'Nancy', 0, 10.00, 'terminer', 6, 7),
(7, '2025-09-05', '13:45:00', '02:30:00', 'Reims', 'Strasbourg', 0, 11.00, 'terminer', 3, 6),
(8, '2025-08-25', '15:30:00', '02:00:00', 'Nancy', 'Colmar', 3, 9.00, 'annuler', 2, 4),
(9, '2025-11-05', '08:00:00', '02:00:00', 'Nancy', 'Strasbourg', 2, 12.00, 'planifier', 1, 3),
(10, '2025-11-08', '14:30:00', '02:45:00', 'Metz', 'Reims', 1, 14.00, 'planifier', 2, 4),
(11, '2025-11-12', '16:00:00', '01:15:00', 'Strasbourg', 'Colmar', 3, 6.00, 'planifier', 3, 5),
(12, '2025-11-15', '10:15:00', '02:30:00', 'Colmar', 'Metz', 4, 11.00, 'planifier', 6, 7),
(13, '2025-11-20', '09:45:00', '03:15:00', 'Reims', 'Nancy', 2, 16.00, 'planifier', 3, 6),
(14, '2025-11-25', '12:00:00', '02:45:00', 'Mulhouse', 'Nancy', 1, 13.00, 'planifier', 1, 3),
(15, '2025-11-18', '15:30:00', '02:45:00', 'Nancy', 'Mulhouse', 3, 13.00, 'annuler', 2, 4);

-- --------------------------------------------------------

--
-- Structure de la table `possede`
--

CREATE TABLE `possede` (
  `id_utilisateur` int(11) NOT NULL,
  `id_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `possede`
--

INSERT INTO `possede` (`id_utilisateur`, `id_role`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(3, 1),
(4, 1),
(5, 1),
(6, 1);

-- --------------------------------------------------------

--
-- Structure de la table `preference`
--

CREATE TABLE `preference` (
  `id_utilisateur` int(11) NOT NULL,
  `etre_fumeur` enum('accepter','refuser') DEFAULT 'accepter',
  `avoir_animal` enum('accepter','refuser') DEFAULT 'accepter',
  `avec_silence` enum('accepter','refuser') DEFAULT 'refuser',
  `avec_musique` enum('accepter','refuser') DEFAULT 'accepter',
  `avec_climatisation` enum('accepter','refuser') DEFAULT 'accepter',
  `avec_velo` enum('accepter','refuser') DEFAULT 'refuser',
  `place_coffre` enum('accepter','refuser') DEFAULT 'accepter',
  `ladies_only` enum('accepter','refuser','non concerne') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `preference`
--

INSERT INTO `preference` (`id_utilisateur`, `etre_fumeur`, `avoir_animal`, `avec_silence`, `avec_musique`, `avec_climatisation`, `avec_velo`, `place_coffre`, `ladies_only`) VALUES
(1, 'accepter', 'accepter', 'accepter', 'accepter', 'accepter', 'accepter', 'accepter', 'non concerne'),
(2, 'accepter', 'accepter', 'refuser', 'accepter', 'accepter', 'refuser', 'accepter', 'non concerne'),
(3, 'accepter', 'accepter', 'refuser', 'accepter', 'accepter', 'refuser', 'accepter', 'refuser'),
(4, 'accepter', 'accepter', 'refuser', 'accepter', 'accepter', 'refuser', 'accepter', 'non concerne'),
(5, 'accepter', 'accepter', 'refuser', 'accepter', 'accepter', 'refuser', 'accepter', 'non concerne'),
(6, 'accepter', 'accepter', 'refuser', 'accepter', 'accepter', 'refuser', 'accepter', 'refuser');

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `id_reservation` int(11) NOT NULL,
  `nb_place_reserve` int(11) NOT NULL,
  `statut_reservation` enum('active','annuler','terminer') DEFAULT 'active',
  `id_passager` int(11) NOT NULL,
  `id_conducteur` int(11) NOT NULL,
  `id_covoiturage` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id_reservation`, `nb_place_reserve`, `statut_reservation`, `id_passager`, `id_conducteur`, `id_covoiturage`) VALUES
(1, 1, 'terminer', 3, 4, 1),
(2, 1, 'active', 1, 4, 1),
(3, 2, 'terminer', 4, 1, 3),
(4, 1, 'terminer', 5, 1, 3),
(5, 1, 'terminer', 3, 2, 4),
(6, 2, 'terminer', 6, 2, 4),
(7, 3, 'terminer', 1, 3, 5),
(8, 2, 'terminer', 4, 3, 5),
(9, 1, 'terminer', 2, 6, 6),
(10, 1, 'terminer', 5, 3, 7),
(11, 1, 'terminer', 6, 3, 7),
(12, 2, 'active', 4, 1, 9),
(13, 2, 'active', 5, 2, 10),
(14, 1, 'annuler', 6, 2, 10),
(15, 1, 'active', 2, 3, 13),
(16, 2, 'active', 5, 1, 14);

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `libelle` enum('Utilisateur','Employe','Administrateur') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id_role`, `libelle`) VALUES
(1, 'Utilisateur'),
(2, 'Employe'),
(3, 'Administrateur');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `sexe` enum('Homme','Femme','Non précisé') NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `photo` varchar(255) DEFAULT 'avatar_default.png',
  `credit` decimal(7,2) DEFAULT 20.00,
  `date_inscription` date DEFAULT '2025-01-01',
  `note` float DEFAULT NULL,
  `type_utilisateur` enum('Passager','Conducteur','Passager et Conducteur') NOT NULL,
  `statut` enum('actif','suspendu') DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `pseudo`, `email`, `mot_de_passe`, `sexe`, `telephone`, `photo`, `credit`, `date_inscription`, `note`, `type_utilisateur`, `statut`) VALUES
(1, 'Admin', 'admin@email.com', '$2y$10$846FvCf71I5XcnmUTU4l/O3vNTfqiWDHL7Gs8zOa9vZDV6J7QP3I6', 'Non précisé', '0611223344', 'avatar_default.png', 65.00, '2025-09-18', 3.8, 'Passager et Conducteur', 'actif'),
(2, 'user1', 'monemail@email.com', '$2y$10$f2aYSpisLUDFQghGhA7SpuEYKTJhQF8W23FJp4DzF684VZokI7s6i', 'Homme', '0606060606', 'avatar_default.png', 14.00, '2025-09-18', 4, 'Passager et Conducteur', 'actif'),
(3, 'user2', 'monemail2@email.com', '$2y$10$9/5mq.Z97y7W8lBTQvgDLu01s8gBZaxlOF7oxGtfj4t2ZMktwVHh2', 'Femme', '0606060607', 'avatar_default.png', 85.00, '2025-09-18', 2.8, 'Conducteur', 'actif'),
(4, 'user3', 'monemail3@email.com', '$2y$10$wwy48tArBP/XcIil//kj5udVNZyiO/Kr3DaY6q24qObNlDYyZyoW2', 'Non précisé', '0606060608', 'avatar_default.png', -50.00, '2025-09-18', 4, 'Conducteur', 'actif'),
(5, 'user4', 'monemail4@email.com', '$2y$10$nI1MC7jxWuHfk8jj6q75G.nlJSmOj.ZvfLjDLRvsV.W3FwOq2ikFm', 'Homme', '0606060605', 'avatar_default.png', -53.00, '2025-09-20', NULL, 'Passager', 'actif'),
(6, 'user5', 'monemail5@email.com', '$2y$10$hpQsb8..PFGeT1QdZhZ9h.Oq/Mfl7sMuFpE35kjOTR/CIw16Gd5S6', 'Femme', '0606060604', 'avatar_default.png', 9.00, '2025-09-20', 4.7, 'Conducteur', 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `virement`
--

CREATE TABLE `virement` (
  `id_virement` int(11) NOT NULL,
  `montant_virement` decimal(5,2) NOT NULL,
  `date_virement` datetime DEFAULT current_timestamp(),
  `statut` enum('en_attente','remboursement','valider','annuler') DEFAULT NULL,
  `id_passager` int(11) NOT NULL,
  `id_conducteur` int(11) NOT NULL,
  `id_covoiturage` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `virement`
--

INSERT INTO `virement` (`id_virement`, `montant_virement`, `date_virement`, `statut`, `id_passager`, `id_conducteur`, `id_covoiturage`) VALUES
(1, 7.00, '2025-09-18 16:05:28', 'valider', 3, 4, 1),
(2, 7.00, '2025-09-18 16:05:28', 'valider', 3, 4, 1),
(3, 7.00, '2025-09-18 16:05:56', 'en_attente', 1, 4, 1),
(4, 7.00, '2025-09-18 16:05:56', 'valider', 1, 4, 1),
(5, 16.00, '2025-09-20 17:09:04', 'valider', 4, 1, 3),
(6, 16.00, '2025-09-20 17:09:04', 'valider', 4, 1, 3),
(7, 8.00, '2025-09-20 17:09:04', 'valider', 5, 1, 3),
(8, 8.00, '2025-09-20 17:09:04', 'valider', 5, 1, 3),
(9, 12.00, '2025-09-20 17:09:04', 'valider', 3, 2, 4),
(10, 12.00, '2025-09-20 17:09:04', 'valider', 3, 2, 4),
(11, 24.00, '2025-09-20 17:09:04', 'valider', 6, 2, 4),
(12, 24.00, '2025-09-20 17:09:04', 'valider', 6, 2, 4),
(13, 45.00, '2025-09-20 17:09:04', 'valider', 1, 3, 5),
(14, 45.00, '2025-09-20 17:09:04', 'valider', 1, 3, 5),
(15, 30.00, '2025-09-20 17:09:04', 'valider', 4, 3, 5),
(16, 30.00, '2025-09-20 17:09:04', 'valider', 4, 3, 5),
(17, 10.00, '2025-09-20 17:09:04', 'valider', 2, 6, 6),
(18, 10.00, '2025-09-20 17:09:04', 'valider', 2, 6, 6),
(19, 11.00, '2025-09-20 17:09:04', 'valider', 5, 3, 7),
(20, 11.00, '2025-09-20 17:09:04', 'valider', 5, 3, 7),
(21, 11.00, '2025-09-20 17:09:04', 'valider', 6, 3, 7),
(22, 11.00, '2025-09-20 17:09:04', 'valider', 6, 3, 7),
(23, 24.00, '2025-09-20 17:09:04', 'valider', 4, 1, 9),
(24, 24.00, '2025-09-20 17:09:04', 'en_attente', 4, 1, 9),
(25, 28.00, '2025-09-20 17:09:04', 'valider', 5, 2, 10),
(26, 28.00, '2025-09-20 17:09:04', 'en_attente', 5, 2, 10),
(27, 14.00, '2025-09-20 17:09:04', 'remboursement', 6, 2, 10),
(28, 16.00, '2025-09-20 17:09:04', 'valider', 2, 3, 13),
(29, 16.00, '2025-09-20 17:09:04', 'en_attente', 2, 3, 13),
(30, 26.00, '2025-09-20 17:09:04', 'valider', 5, 1, 14),
(31, 26.00, '2025-09-20 17:09:04', 'en_attente', 5, 1, 14);

-- --------------------------------------------------------

--
-- Structure de la table `voiture`
--

CREATE TABLE `voiture` (
  `id_voiture` int(11) NOT NULL,
  `id_conducteur` int(11) NOT NULL,
  `marque` varchar(50) NOT NULL,
  `modele` varchar(50) NOT NULL,
  `immat` varchar(20) NOT NULL,
  `date_premiere_immat` date NOT NULL,
  `energie` enum('Essence','Diesel','Hybride','Electrique') NOT NULL,
  `couleur` enum('Noir','Blanc','Gris foncé','Gris','Bordeaux','Rouge','Bleu foncé','Bleu','Vert Foncé','Vert','Marron','Beige','Orange','Jaune','Violet','Rose') NOT NULL,
  `nb_place` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `voiture`
--

INSERT INTO `voiture` (`id_voiture`, `id_conducteur`, `marque`, `modele`, `immat`, `date_premiere_immat`, `energie`, `couleur`, `nb_place`) VALUES
(2, 4, 'Opel', 'Corsa', 'OO-055-CC', '2025-03-13', 'Hybride', 'Bleu foncé', 3),
(3, 1, 'Renault', 'Clio', 'AB-123-CD', '2022-05-15', 'Essence', 'Rouge', 4),
(4, 2, 'Peugeot', '208', 'EF-456-GH', '2023-03-20', 'Hybride', 'Blanc', 4),
(5, 3, 'Volkswagen', 'Golf', 'IJ-789-KL', '2021-11-08', 'Diesel', 'Gris', 5),
(6, 3, 'Tesla', 'Model 3', 'MN-012-OP', '2024-01-12', 'Electrique', 'Noir', 5),
(7, 6, 'Citroën', 'C3', 'QR-345-ST', '2022-09-30', 'Essence', 'Bleu', 4);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id_avis`),
  ADD KEY `id_passager` (`id_passager`),
  ADD KEY `id_conducteur` (`id_conducteur`),
  ADD KEY `id_covoiturage` (`id_covoiturage`);

--
-- Index pour la table `commission`
--
ALTER TABLE `commission`
  ADD PRIMARY KEY (`id_commission`),
  ADD KEY `id_covoiturage` (`id_covoiturage`),
  ADD KEY `id_conducteur` (`id_conducteur`);

--
-- Index pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  ADD PRIMARY KEY (`id_covoiturage`),
  ADD KEY `id_conducteur` (`id_conducteur`),
  ADD KEY `id_voiture` (`id_voiture`);

--
-- Index pour la table `possede`
--
ALTER TABLE `possede`
  ADD PRIMARY KEY (`id_utilisateur`,`id_role`),
  ADD KEY `id_role` (`id_role`);

--
-- Index pour la table `preference`
--
ALTER TABLE `preference`
  ADD PRIMARY KEY (`id_utilisateur`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id_reservation`),
  ADD KEY `id_passager` (`id_passager`),
  ADD KEY `id_conducteur` (`id_conducteur`),
  ADD KEY `id_covoiturage` (`id_covoiturage`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `pseudo` (`pseudo`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `virement`
--
ALTER TABLE `virement`
  ADD PRIMARY KEY (`id_virement`),
  ADD KEY `id_passager` (`id_passager`),
  ADD KEY `id_conducteur` (`id_conducteur`),
  ADD KEY `id_covoiturage` (`id_covoiturage`);

--
-- Index pour la table `voiture`
--
ALTER TABLE `voiture`
  ADD PRIMARY KEY (`id_voiture`),
  ADD UNIQUE KEY `immat` (`immat`),
  ADD KEY `id_conducteur` (`id_conducteur`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id_avis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `commission`
--
ALTER TABLE `commission`
  MODIFY `id_commission` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  MODIFY `id_covoiturage` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id_reservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `virement`
--
ALTER TABLE `virement`
  MODIFY `id_virement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT pour la table `voiture`
--
ALTER TABLE `voiture`
  MODIFY `id_voiture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`id_passager`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`id_conducteur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `avis_ibfk_3` FOREIGN KEY (`id_covoiturage`) REFERENCES `covoiturage` (`id_covoiturage`);

--
-- Contraintes pour la table `commission`
--
ALTER TABLE `commission`
  ADD CONSTRAINT `commission_ibfk_1` FOREIGN KEY (`id_covoiturage`) REFERENCES `covoiturage` (`id_covoiturage`),
  ADD CONSTRAINT `commission_ibfk_2` FOREIGN KEY (`id_conducteur`) REFERENCES `utilisateur` (`id_utilisateur`);

--
-- Contraintes pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  ADD CONSTRAINT `covoiturage_ibfk_1` FOREIGN KEY (`id_conducteur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `covoiturage_ibfk_2` FOREIGN KEY (`id_voiture`) REFERENCES `voiture` (`id_voiture`);

--
-- Contraintes pour la table `possede`
--
ALTER TABLE `possede`
  ADD CONSTRAINT `possede_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `possede_ibfk_2` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);

--
-- Contraintes pour la table `preference`
--
ALTER TABLE `preference`
  ADD CONSTRAINT `preference_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`);

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`id_passager`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`id_conducteur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `reservation_ibfk_3` FOREIGN KEY (`id_covoiturage`) REFERENCES `covoiturage` (`id_covoiturage`);

--
-- Contraintes pour la table `virement`
--
ALTER TABLE `virement`
  ADD CONSTRAINT `virement_ibfk_1` FOREIGN KEY (`id_passager`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `virement_ibfk_2` FOREIGN KEY (`id_conducteur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `virement_ibfk_3` FOREIGN KEY (`id_covoiturage`) REFERENCES `covoiturage` (`id_covoiturage`);

--
-- Contraintes pour la table `voiture`
--
ALTER TABLE `voiture`
  ADD CONSTRAINT `voiture_ibfk_1` FOREIGN KEY (`id_conducteur`) REFERENCES `utilisateur` (`id_utilisateur`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
