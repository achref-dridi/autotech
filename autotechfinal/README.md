# AutoTech - Plateforme de Location de Véhicules

## 📋 Description

AutoTech est une plateforme web de location de véhicules développée en PHP, permettant aux utilisateurs de:
- S'inscrire et se connecter
- Ajouter leurs véhicules à louer
- Consulter les véhicules disponibles avec les informations de contact des propriétaires
- Gérer leur profil utilisateur

## 🗂️ Structure du Projet

```
AutoTech_Integrated/
├── config/
│   └── config.php              # Configuration de la base de données
├── model/
│   ├── Utilisateur.php         # Modèle Utilisateur
│   └── Vehicule.php            # Modèle Véhicule
├── controller/
│   ├── UtilisateurController.php   # Gestion des utilisateurs
│   └── VehiculeController.php      # Gestion des véhicules
├── view/
│   ├── auth/
│   │   ├── login.php           # Page de connexion
│   │   ├── signup.php          # Page d'inscription
│   │   └── logout.php          # Déconnexion
│   ├── user/
│   │   ├── profil.php          # Profil utilisateur
│   │   ├── mes-vehicules.php   # Mes véhicules
│   │   └── ajouter-vehicule.php # Ajouter un véhicule
│   ├── public/
│   │   ├── index.php           # Page d'accueil
│   │   ├── voitures.php        # Liste des voitures
│   │   ├── voiture-details.php # Détails d'une voiture
│   │   ├── about.php           # À propos
│   │   ├── services.php        # Services
│   │   └── contact.php         # Contact
│   └── includes/
│       ├── header.php          # En-tête
│       └── footer.php          # Pied de page
├── assets/
│   ├── css/                    # Styles CSS (depuis front office)
│   ├── js/
│   │   ├── validation.js       # Validation JavaScript
│   │   └── main.js             # Scripts principaux
│   ├── images/                 # Images du site
│   └── fonts/                  # Polices
├── uploads/                    # Dossier pour les images uploadées
├── database/
│   └── autotech.sql           # Script SQL de création de la base
└── README.md                   # Ce fichier
```

## 🚀 Installation

### Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx)
- XAMPP, WAMP ou MAMP (recommandé pour le développement local)

### Étapes d'installation

1. **Cloner ou télécharger le projet**
   ```bash
   # Placer le dossier AutoTech_Integrated dans le répertoire web
   # Pour XAMPP: C:/xampp/htdocs/
   # Pour WAMP: C:/wamp64/www/
   ```

2. **Créer la base de données**
   - Ouvrir phpMyAdmin (http://localhost/phpmyadmin)
   - Importer le fichier `database/autotech.sql`
   - La base de données `autotech_db` sera créée avec toutes les tables

3. **Configurer la connexion**
   - Ouvrir `config/config.php`
   - Vérifier/modifier les paramètres de connexion:
     ```php
     DB_HOST = 'localhost'
     DB_NAME = 'autotech_db'
     DB_USER = 'root'
     DB_PASS = ''
     ```

4. **Créer le dossier uploads**
   ```bash
   mkdir uploads
   chmod 777 uploads  # Sur Linux/Mac
   ```

5. **Accéder au site**
   - Ouvrir le navigateur
   - Aller à: `http://localhost/AutoTech_Integrated/view/public/index.php`

## 👤 Comptes de Test

### Compte Administrateur
- **Email**: admin@autotech.tn
- **Mot de passe**: admin123

### Compte Utilisateur
- **Email**: jean.dupont@email.tn
- **Mot de passe**: test123

## 🔐 Fonctionnalités

### Authentification
- ✅ Inscription avec validation JavaScript
- ✅ Connexion sécurisée
- ✅ Gestion de session
- ✅ Déconnexion

### Gestion des Utilisateurs
- ✅ Modifier le profil
- ✅ Changer le mot de passe
- ✅ Télécharger une photo de profil

### Gestion des Véhicules
- ✅ Ajouter un véhicule
- ✅ Modifier ses véhicules
- ✅ Supprimer ses véhicules
- ✅ Voir tous les véhicules disponibles
- ✅ Voir les détails d'un véhicule avec contact du propriétaire

### Pages Publiques
- ✅ Page d'accueil avec véhicules en vedette
- ✅ Liste complète des voitures
- ✅ Détails d'une voiture
- ✅ À propos d'AutoTech
- ✅ Services proposés
- ✅ Page de contact

## 📝 Validation des Formulaires

Toutes les validations sont effectuées en JavaScript (pas en HTML5) selon les spécifications:

### Inscription
- Nom et prénom: minimum 2 caractères, lettres uniquement
- Email: format valide
- Téléphone: format tunisien (+216 XX XXX XXX)
- Mot de passe: minimum 6 caractères, au moins 1 majuscule, 1 minuscule, 1 chiffre

### Ajout de Véhicule
- Marque et modèle: minimum 2 caractères
- Année: entre 1950 et année actuelle + 1
- Kilométrage: nombre positif, maximum 1,000,000
- Prix journalier: nombre positif, maximum 10,000 DT
- Image: JPG/PNG/GIF, maximum 5MB

## 🎨 Design

Le design combine les éléments des trois dossiers fournis:
- **Front Office**: Template moderne pour le site public (caroussel, animations)
- **Back Office**: Interface admin Kaiadmin pour la gestion
- **AutoTech**: Logique métier et structure MVC

## 🔧 Technologies Utilisées

- **Backend**: PHP 7.4+ avec architecture MVC
- **Base de données**: MySQL avec PDO
- **Frontend**: HTML5, CSS3, JavaScript
- **Frameworks CSS**: Bootstrap 4/5
- **Validation**: JavaScript (pas HTML5 required)
- **Sécurité**: Password hashing, Prepared statements, Session management

## 📂 Base de Données

### Tables Principales

#### `utilisateur`
- Stocke les informations des utilisateurs
- Authentification sécurisée avec mots de passe hashés
- Rôles: utilisateur / admin

#### `vehicule`
- Informations complètes sur les véhicules
- Lié au propriétaire (id_utilisateur)
- Statut de disponibilité

#### `reservation` (future)
- Système de réservation (à implémenter)

## 🌐 Routes Principales

```
/view/public/index.php              - Page d'accueil
/view/public/voitures.php           - Liste des voitures
/view/public/voiture-details.php    - Détails d'une voiture
/view/auth/login.php                - Connexion
/view/auth/signup.php               - Inscription
/view/user/profil.php               - Profil (authentifié)
/view/user/mes-vehicules.php        - Mes véhicules (authentifié)
/view/user/ajouter-vehicule.php     - Ajouter véhicule (authentifié)
```

## 🔒 Sécurité

- Hashage des mots de passe avec `password_hash()`
- Protection contre les injections SQL avec PDO prepared statements
- Protection XSS avec `htmlspecialchars()`
- Vérification des sessions
- Validation côté serveur ET client
- Upload sécurisé des fichiers

## 📱 Responsive

Le site est entièrement responsive et s'adapte à tous les appareils:
- Desktop (1920px+)
- Laptop (1024px+)
- Tablet (768px+)
- Mobile (320px+)

## 🤝 Contributeurs

Ce projet a été réalisé par un groupe de 4 personnes dans le cadre d'un projet web.

## 📄 Licence

Projet académique - Tous droits réservés © 2024 AutoTech

## 🆘 Support

Pour toute question ou problème:
- Email: AutoTech@gmail.tn
- Téléphone: +216 33 856 909
- Adresse: Esprit, Ariana Sogra, Ariana, Tunisie
