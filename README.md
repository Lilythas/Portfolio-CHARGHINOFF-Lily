# Projet Portfolio - Mon Portfolio Personnel

## Présentation du Projet

Ce projet est un site web de type portfolio développé en PHP & MySQL. Il permet de :

- Présenter une page « À propos » (profil personnel).
- Afficher et gérer une liste de compétences.
- Authentifier un utilisateur pour accéder à la zone d’administration.

## Contenu du Projet

Le code source contient les principaux fichiers suivants :

- **index.php** : Page d’accueil publique du portfolio.
- **moi.php** : Page « À propos » présentant le profil (photo, bio, coordonnées).
- **competence.php** : Interface d’affichage des compétences.
- **connexion.php** : Formulaire de connexion pour l’administrateur.
- **config.php** : Fichier de configuration de la connexion à la base de données.
- **database.sql** : Script SQL pour créer les tables nécessaires.

## Fonctionnalités Implémentées

### Page Publique

- **Accueil** (`index.php`) : Présentation générale et liens vers les sections du portfolio.
- **À propos** (`moi.php`) : Informations personnelles et photo de profil.
- **Compétences** (`competence.php`) : Liste statique/dynamique des compétences.

### Authentification Admin

- **Connexion** (`connexion.php`) : Formulaire sécurisé pour accéder à l’interface d’administration.
- **Sessions PHP** pour maintenir la connexion.

## Installation & Configuration

### Prérequis

- Serveur local (Apache ou Nginx).
- PHP 7.4+ et MySQL.

### Étapes

1. **Cloner ou copier** le projet dans le répertoire de votre serveur.
2. **Importer** la base de données :
   ```bash
   mysql -u root -p < database.sql
   ```
3. **Configurer** la connexion : mettre à jour `config.php` avec vos identifiants :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'nom_de_la_base');
   define('DB_USER', 'utilisateur');
   define('DB_PASS', 'motdepasse');
   ```
4. **Accéder** au site via votre navigateur à l’adresse configurée (ex. `http://localhost/portfolio`).

## Structure des Tables (aperçu)

Le script `database.sql` crée notamment :

- **users** : stocke les données de l’administrateur.
- **competences** : liste des compétences.

## Personnalisation

- Mettre à jour **moi.php** pour modifier votre biographie, photo et coordonnées.
- Adapter **competence.php** pour ajouter ou retirer des compétences.
- Modifier le **template HTML/CSS** selon vos préférences graphiques.

## Licence

Ce projet est sous licence MIT.

## Contact

Pour toute question ou suggestion, contactez-moi : `votre.email@exemple.com`

