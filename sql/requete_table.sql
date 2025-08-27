CREATE TABLE utilisateur (
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(50)  NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(50) NOT NULL,
    photo VARCHAR(255) DEFAULT 'lien_vers_logo_avatar',
    credit DECIMAL(7,2) DEFAULT 20,
    date_inscription DATE DEFAULT CURRENT_DATE,
    type_utilisateur ENUM('passager','chauffeur','les deux') NOT NULL,
    statut ENUM('actif','suspendu') DEFAULT 'actif'
);


CREATE TABLE role (
    id_role INT PRIMARY KEY AUTO_INCREMENT,
    libelle ENUM('utilisateur','employe','administrateur')
);

CREATE TABLE possede (
    id_utilisateur INT,
    id_role INT,
    PRIMARY KEY (id_utilisateur, id_role),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY (id_role) REFERENCES role(id_role)
);

CREATE TABLE voiture(
    id_voiture INT PRIMARY KEY AUTO_INCREMENT,
    marque VARCHAR(50) NOT NULL,
    modele VARCHAR(50) NOT NULL,
    immat VARCHAR(20) NOT NULL UNIQUE,
    date_premiere_immat DATE NOT NULL,
    energie ENUM('essence','diesel','hybride','electrique') NOT NULL,
    couleur ENUM('Noir','Blanc','Gris foncé','Gris','Bordeaux','Rouge','Bleu foncé','Bleu','Vert Foncé','Vert','Marron','Beige','Orange','Jaune','Violet','Rose') NOT NULL,
    nb_place INT NOT NULL,
    id_conducteur INT NOT NULL,
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur (id_utilisateur)
);

CREATE TABLE preference (
    id_voiture INT PRIMARY KEY AUTO_INCREMENT,
    fumeur BOOLEAN NOT NULL,
    animaux BOOLEAN NOT NULL,
    climatisation BOOLEAN NOT NULL,
    FOREIGN KEY (id_voiture) REFERENCES voiture (id_voiture)
);


CREATE TABLE convoiturage (
    id_convoiturage INT PRIMARY KEY AUTO_INCREMENT,
    date_depart DATE NOT NULL,
    date_arrive DATE NOT NULL,
    heure_depart TIME NOT NULL,
    heure_arrive TIME NOT NULL,
    lieu_depart VARCHAR(50) NOT NULL,
    lieu_arrive VARCHAR(50) NOT NULL,
    nb_place_dispo INT NOT NULL,
    prix_personne DECIMAL(7,2) NOT NULL,
    statut_convoit ENUM('planifie', 'en_cours', 'termine', 'annule') DEFAULT 'planifie',
    id_conducteur INT NOT NULL ,
    id_voiture INT NOT NULL,
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur (id_utilisateur),
    FOREIGN KEY (id_voiture) REFERENCES voiture (id_voiture)
);


CREATE TABLE avis (
    avis_id INT PRIMARY KEY AUTO_INCREMENT,
    commentaire TEXT,
    note INT NOT NULL,
    statut_avis ENUM('en_attente','refuse','valide') DEFAULT 'en_attente',
    date_avis DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_passager INT NOT NULL,
    id_conducteur INT NOT NULL,
    id_convoiturage INT NOT NULL,
    FOREIGN KEY (id_passager) REFERENCES utilisateur (id_utilisateur),
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur (id_utilisateur),
    FOREIGN KEY (id_convoiturage) REFERENCES convoiturage (id_convoiturage)
);


CREATE TABLE virement (
    id_virement INT PRIMARY KEY AUTO_INCREMENT,
    montant_virement DECIMAL(5,2) NOT NULL,
    date_virement DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_passager INT NOT NULL,
    id_conducteur INT NOT NULL,
    FOREIGN KEY (id_passager) REFERENCES  utilisateur (id_utilisateur),
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur (id_utilisateur)
);


CREATE TABLE reservation (
    id_reservation INT PRIMARY KEY AUTO_INCREMENT,
    nb_place_reserve INT NOT NULL,
    etre_fumeur BOOLEAN DEFAULT FALSE,
    avoir_animaux BOOLEAN DEFAULT FALSE,
    statut_reservation ENUM('active','annulee') DEFAULT 'active',
    id_passager INT NOT NULL,
    id_conducteur INT NOT NULL,
    id_convoiturage INT NOT NULL,
    FOREIGN KEY (id_passager) REFERENCES  utilisateur (id_utilisateur),
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur (id_utilisateur),
    FOREIGN KEY (id_convoiturage) REFERENCES convoiturage (id_convoiturage)
);


CREATE TABLE validation_trajet (
    id_validation INT PRIMARY KEY AUTO_INCREMENT,
    trajet_valide BOOLEAN NOT NULL,
    probleme BOOLEAN DEFAULT FALSE,
    commentaire_probleme TEXT,
    date_validation DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_conducteur INT NOT NULL,
    id_passager INT NOT NULL,
    id_convoiturage INT NOT NULL,
    FOREIGN KEY (id_passager) REFERENCES  utilisateur (id_utilisateur),
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur (id_utilisateur),
    FOREIGN KEY (id_convoiturage) REFERENCES convoiturage (id_convoiturage)
);