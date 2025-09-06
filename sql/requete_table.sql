CREATE TABLE utilisateur (
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(50)  NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    sexe ENUM('Homme','Femme','Non précisé') NOT NULL,
    telephone VARCHAR(50) NOT NULL,
    photo VARCHAR(255) DEFAULT 'avatar_default.png',
    credit DECIMAL(7,2) DEFAULT 20,
    date_inscription DATE DEFAULT CURRENT_DATE,
    note INT,
    type_utilisateur ENUM('Passager','Conducteur','Passager et Conducteur') NOT NULL,
    statut ENUM('actif','suspendu') DEFAULT 'actif'
);


CREATE TABLE role (
    id_role INT PRIMARY KEY,
    libelle ENUM('Utilisateur','Employe','Administrateur')
);

CREATE TABLE possede (
    id_utilisateur INT,
    id_role INT,
    PRIMARY KEY (id_utilisateur, id_role),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY (id_role) REFERENCES role(id_role)
);

CREATE TABLE preference (
    id_utilisateur INT PRIMARY KEY,
    etre_fumeur ENUM('accepter','refuser') DEFAULT 'accepter',
    avoir_animal ENUM('accepter','refuser') DEFAULT 'accepter',
    avec_silence ENUM('accepter','refuser') DEFAULT 'refuser',
    avec_musique ENUM('accepter','refuser') DEFAULT 'accepter',
    avec_climatisation ENUM('accepter','refuser') DEFAULT 'accepter',
    avec_velo ENUM('accepter','refuser') DEFAULT 'refuser',
    place_coffre ENUM('accepter','refuser') DEFAULT 'accepter',
    ladies_only ENUM('accepter','refuser') DEFAULT 'refuser',
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)
);

CREATE TABLE voiture(
    id_voiture INT PRIMARY KEY AUTO_INCREMENT,
    id_conducteur INT NOT NULL,
    marque VARCHAR(50) NOT NULL,
    modele VARCHAR(50) NOT NULL,
    immat VARCHAR(20) NOT NULL UNIQUE,
    date_premiere_immat DATE NOT NULL,
    energie ENUM('Essence','Diesel','Hybride','Electrique') NOT NULL,
    couleur ENUM('Noir','Blanc','Gris foncé','Gris','Bordeaux','Rouge','Bleu foncé','Bleu','Vert Foncé','Vert','Marron','Beige','Orange','Jaune','Violet','Rose') NOT NULL,
    nb_place INT NOT NULL,
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur (id_utilisateur)
);

CREATE TABLE covoiturage (
    id_covoiturage INT PRIMARY KEY AUTO_INCREMENT,
    date_depart DATE NOT NULL,
    heure_depart TIME NOT NULL,
    duree_voyage TIME NOT NULL,
    lieu_depart VARCHAR(50) NOT NULL,
    lieu_arrive VARCHAR(50) NOT NULL,
    nb_place_dispo INT NOT NULL,
    prix_personne DECIMAL(7,2) NOT NULL,
    statut_convoit ENUM('planifier', 'en_cours', 'terminer', 'annuler') DEFAULT 'planifier',
    id_conducteur INT NOT NULL ,
    id_voiture INT NOT NULL,
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur (id_utilisateur),
    FOREIGN KEY (id_voiture) REFERENCES voiture (id_voiture)
);


CREATE TABLE avis (
    id_avis INT PRIMARY KEY AUTO_INCREMENT,
    commentaire TEXT,
    note INT NOT NULL,
    statut_avis ENUM('en_attente','refuser','valider') DEFAULT 'en_attente',
    date_avis DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_passager INT NOT NULL,
    id_conducteur INT NOT NULL,
    id_covoiturage INT NOT NULL,
    FOREIGN KEY (id_passager) REFERENCES utilisateur (id_utilisateur),
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur (id_utilisateur),
    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage (id_covoiturage)
);


CREATE TABLE virement (
    id_virement INT PRIMARY KEY AUTO_INCREMENT,
    montant_virement DECIMAL(5,2) NOT NULL,
    date_virement DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente','refuser','valider'),
    id_passager INT NOT NULL,
    id_conducteur INT NOT NULL,
    FOREIGN KEY (id_passager) REFERENCES  utilisateur (id_utilisateur),
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur (id_utilisateur)
);


CREATE TABLE reservation (
    id_reservation INT PRIMARY KEY AUTO_INCREMENT,
    nb_place_reserve INT NOT NULL,
    statut_reservation ENUM('active','annulee') DEFAULT 'active',
    id_passager INT NOT NULL,
    id_conducteur INT NOT NULL,
    id_covoiturage INT NOT NULL,
    FOREIGN KEY (id_passager) REFERENCES  utilisateur (id_utilisateur),
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur (id_utilisateur),
    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage (id_covoiturage)
);


CREATE TABLE validation_trajet (
    id_validation INT PRIMARY KEY AUTO_INCREMENT,
    trajet_valide BOOLEAN NOT NULL,
    probleme BOOLEAN DEFAULT FALSE,
    commentaire_probleme TEXT,
    date_validation DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_conducteur INT NOT NULL,
    id_passager INT NOT NULL,
    id_covoiturage INT NOT NULL,
    FOREIGN KEY (id_passager) REFERENCES  utilisateur (id_utilisateur),
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur (id_utilisateur),
    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage (id_covoiturage)
);