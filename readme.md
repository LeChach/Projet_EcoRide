# EcoRide - Plateforme de Covoiturage

## Description
EcoRide est une plateforme web de covoiturage développée en PHP pur avec MySQL, permettant aux utilisateurs de proposer et rechercher des trajets partagés.

## Fonctionnalités principales
- Système d'authentification sécurisé
- Gestion des profils utilisateurs avec préférences
- Création et gestion de covoiturages
- Recherche avancée avec filtres
- Système de réservation et paiement par crédits
- Système d'avis et notation
- Interface d'administration
- Gestion des rôles (Utilisateur/Employé/Administrateur)

## Technologies utilisées
- **Backend :** PHP 8+ pur (sans framework)
- **Base de données :** MySQL
- **Frontend :** HTML5, CSS3 (avec variables CSS), JavaScript
- **Déploiement :** Heroku avec JawsDB MySQL
- **Versionning :** Git

## Installation locale

### Prérequis
- XAMPP (PHP 8+, MySQL, Apache)
- Git

### Étapes d'installation

1. **Cloner le repository**
Dans le dossier htdocs de xamp créer Projet_EcoRide
```bash
git clone https://github.com/LeChach/Projet_EcoRide.git
cd eco-ride-2025
```

2. **Configurer l'environnement**
- Démarrer XAMPP (Apache + MySQL)
- Accéder à phpMyAdmin : `http://localhost/phpmyadmin`

3. **Créer la base de données**
- Créer une base nommée `bdd_eco-ride`
- Importer le fichier `sql/bdd_ecoride.sql`

4. **Configuration**
Le fichier `src/config/database.php` détecte automatiquement l'environnement :
- Local : utilise les paramètres XAMPP par défaut
- Production : utilise la variable `DATABASE_URL` d'Heroku

5. **Accéder à l'application**
```
http://localhost/Projet_EcoRide/src/index.php
```

## Comptes de test

### Administrateur
- **pseudo :** Admin
- **Mot de passe :** VotreMotDePasseAdmin123!
- **Rôles :** Utilisateur + Employé + Administrateur

### Utilisateurs standards
- **pseudo :** user1 / **Mot de passe :** azER/*-123
- **pseudo :** user2 / **Mot de passe :** azER/*-123
- **pseudo :** user5 / **Mot de passe :** azER/*-123

## Déploiement Heroku

### Prérequis
- Compte Heroku
- Heroku CLI installé

### Commandes de déploiement
```bash
# Créer l'application
heroku create nom-app

# Ajouter la base de données MySQL
heroku addons:create jawsdb:kitefin

# Configurer les variables
heroku config:set HEROKU_PHP_DOCUMENT_ROOT=src

# Déployer
git push heroku main
```

### Configuration automatique
- `Procfile` : configure Apache pour pointer vers `src/`
- `composer.json` : définit la version PHP et les dépendances
- Variable `DATABASE_URL` : configurée automatiquement par JawsDB

## Structure du projet

```
projet_ecoride/
├── docs/                   # Maquettes et design
├── sql/                    # Scripts de base de données
│   └── bdd_ecoride.sql
├── src/                    # Code source principal
│   ├── assets/             # CSS, JS, images
│   ├── classes/            # Classes métier
│   │   ├── Connexion.php
│   │   ├── Covoiturage.php
│   │   └── MonCompte.php
│   ├── config/             # Configuration
│   │   └── database.php
│   ├── connexion/          # Gestion des sessions
│   ├── includes/           # Templates réutilisables
│   ├── fonction_php/       # Fonctions utilitaires
│   └── *.php              # Pages principales
├── composer.json           # Configuration Heroku
├── Procfile               # Configuration serveur web
└── README.md              # Documentation
```

## Architecture de sécurité

### Authentification
- Hachage des mots de passe avec `password_hash()` et `PASSWORD_DEFAULT`
- Validation robuste des mots de passe (8 caractères, majuscule, minuscule, chiffre, caractère spécial)
- Sessions sécurisées avec vérification d'existence utilisateur

### Base de données
- Requêtes préparées PDO sur toutes les interactions
- Transactions pour maintenir l'intégrité des données
- Validation et échappement des données d'entrée

### Configuration
- Séparation environnement local/production
- Variables d'environnement pour les informations sensibles
- Logs d'erreur sécurisés

## API Principale

### Classes métier
- **Connexion** : Inscription, authentification
- **Covoiturage** : CRUD covoiturages, recherche, réservations
- **MonCompte** : Gestion profil, préférences, administration

### Workflow utilisateur
1. Inscription/Connexion
2. Configuration profil et préférences
3. Ajout véhicule (conducteurs)
4. Création/Recherche de covoiturages
5. Système de réservation avec crédits
6. Notation et avis post-trajet

## Tests et validation

### Tests fonctionnels recommandés
1. Parcours complet inscription → premier covoiturage
2. Recherche et réservation
3. Gestion administrative
4. Responsive design sur mobile/tablette

### Données de test
La base contient des données fictives permettant de tester toutes les fonctionnalités.

## Contribution

### Workflow Git
- Branche principale : `main`
- Branche de développement : `covoiturage`,`voiture`,`preference`,`inscription`

### Standards de code
- PSR-4 pour l'autoloading des classes
- Commentaires PHPDoc sur les méthodes publiques
- Variables CSS pour la cohérence graphique

## Support et maintenance

Pour toute question ou problème :
1. Vérifier les logs d'erreur PHP
2. Consulter la documentation technique
3. Tester en environnement local d'abord

## Licence
Projet étudiant - ECF Développeur Web Studi