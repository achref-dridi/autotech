# GUIDE D'IMPLÉMENTATION COMPLÈTE - AutoTech

## 📌 Fichiers Déjà Créés ✅

1. ✅ `database/autotech.sql` - Base de données complète
2. ✅ `config/config.php` - Configuration
3. ✅ `model/Utilisateur.php` - Modèle utilisateur
4. ✅ `model/Vehicule.php` - Modèle véhicule  
5. ✅ `controller/UtilisateurController.php` - Contrôleur utilisateur
6. ✅ `controller/VehiculeController.php` - Contrôleur véhicule
7. ✅ `assets/js/validation.js` - Validation JavaScript
8. ✅ `README.md` - Documentation

## 📋 Fichiers à Créer

### 1. COPIER LES ASSETS DU FRONT OFFICE

Copier tous les fichiers du dossier `front office/` vers `AutoTech_Integrated/assets/`:
- `css/` (tous les fichiers CSS)
- `js/` (tous les fichiers JS - ajouter validation.js également)
- `images/` (toutes les images)
- `fonts/` (toutes les polices)

### 2. VIEW/AUTH/LOGIN.PHP

```php
<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$userController = new UtilisateurController();
$message = '';
$messageType = '';

// Si déjà connecté, rediriger
if ($userController->estConnecte()) {
    header('Location: ../public/index.php');
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    
    $result = $userController->connecter($email, $mot_de_passe);
    
    if ($result['success']) {
        header('Location: ../public/index.php');
        exit();
    } else {
        $message = $result['message'];
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - AutoTech</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .login-header p {
            color: #666;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 16px;
            margin-top: 20px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .signup-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2>🚗 AutoTech</h2>
                <p>Connectez-vous à votre compte</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <form method="POST" onsubmit="return validerConnexion()">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="votre@email.com">
                </div>

                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" placeholder="••••••">
                </div>

                <button type="submit" class="btn btn-login">Se connecter</button>

                <div class="signup-link">
                    Pas encore de compte? <a href="signup.php">S'inscrire</a>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/validation.js"></script>
</body>
</html>
```

### 3. VIEW/AUTH/SIGNUP.PHP

Créer un fichier similaire à login.php avec:
- Formulaire d'inscription (nom, prenom, email, telephone, mot_de_passe, confirmer_mot_de_passe)
- Appel à `$userController->inscrire()`
- Validation JavaScript avec `validerInscription()`

### 4. VIEW/AUTH/LOGOUT.PHP

```php
<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$userController = new UtilisateurController();
$userController->deconnecter();

header('Location: ../public/index.php');
exit();
?>
```

### 5. VIEW/INCLUDES/HEADER.PHP

Créer un header avec:
- Navigation (Accueil, À propos, Services, Voitures, Contact)
- Si connecté: afficher "Mon Profil", "Mes Véhicules", "Ajouter Véhicule", "Déconnexion"
- Si non connecté: afficher "Connexion", "Inscription"

### 6. VIEW/INCLUDES/FOOTER.PHP

Copier le footer du fichier `AutoTech/view/front office/index.php` (lignes 345-407)

### 7. VIEW/PUBLIC/INDEX.PHP

Copier le contenu de `AutoTech/view/front office/index.php` et:
- Ajouter `<?php require_once '../includes/header.php'; ?>` au début
- Remplacer les chemins CSS/JS pour pointer vers `../../assets/`
- Garder la logique d'affichage des véhicules
- Ajouter le footer

### 8. VIEW/PUBLIC/VOITURES.PHP

Copier `AutoTech/view/front office/car.php` et adapter les chemins

### 9. VIEW/PUBLIC/VOITURE-DETAILS.PHP

Créer une page affichant:
- Détails complets du véhicule
- Photos
- **INFORMATIONS DU PROPRIÉTAIRE**:
  - Nom et prénom
  - Email
  - Téléphone
  - Ville
- Bouton "Contacter le propriétaire"

### 10. VIEW/USER/PROFIL.PHP

Page pour modifier:
- Informations personnelles
- Photo de profil
- Changer mot de passe
Utiliser `validerProfil()` pour validation

### 11. VIEW/USER/MES-VEHICULES.PHP

Afficher les véhicules de l'utilisateur connecté avec:
- Boutons Modifier / Supprimer
- Lien vers "Ajouter un véhicule"

### 12. VIEW/USER/AJOUTER-VEHICULE.PHP

Formulaire d'ajout de véhicule avec validation `validerVehicule()`:
- Marque, modèle, année
- Carburant, kilométrage
- Couleur, transmission
- Prix journalier
- Description
- Image principale

## 🎨 INTÉGRATION DES STYLES

### Dans chaque page, utiliser cette structure:

```php
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Titre - AutoTech</title>
    
    <!-- Polices Google -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">
    
    <!-- CSS du front office -->
    <link rel="stylesheet" href="../../assets/css/animate.css">
    <link rel="stylesheet" href="../../assets/css/flaticon.css">
    <link rel="stylesheet" href="../../assets/css/icomoon.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    
    <!-- CONTENU ICI -->
    
    <?php require_once '../includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/validation.js"></script>
</body>
</html>
```

## 🔐 PROTECTION DES PAGES

Ajouter en haut des pages utilisateur (profil, mes-vehicules, ajouter-vehicule):

```php
<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$userController = new UtilisateurController();
if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}
?>
```

## 📝 CHECKLIST FINALE

- [ ] Importer autotech.sql dans phpMyAdmin
- [ ] Copier tous les assets (CSS, JS, images, fonts)
- [ ] Créer toutes les pages view/auth/
- [ ] Créer toutes les pages view/user/
- [ ] Créer toutes les pages view/public/
- [ ] Créer header.php et footer.php
- [ ] Tester l'inscription
- [ ] Tester la connexion
- [ ] Tester l'ajout de véhicule
- [ ] Tester l'affichage des voitures avec contact propriétaire
- [ ] Tester la modification du profil
- [ ] Vérifier la validation JavaScript partout

## 🎯 POINTS IMPORTANTS

1. **Tous les chemins doivent être relatifs** depuis le dossier de la page
2. **Toujours utiliser htmlspecialchars()** pour afficher des données
3. **Validation JavaScript (pas HTML5)** - retirer tous les `required` et valider en JS
4. **Session** doit être démarrée dans config.php (déjà fait)
5. **Upload** des images dans le dossier `uploads/` avec vérification de taille et type

## 🚀 POUR TESTER

1. Démarrer XAMPP/WAMP
2. Aller à `http://localhost/AutoTech_Integrated/view/public/index.php`
3. S'inscrire avec un nouveau compte
4. Se connecter
5. Ajouter un véhicule
6. Voir la liste des voitures avec vos infos de contact
7. Modifier votre profil

---

**Bon courage pour la finalisation du projet! 🎉**
