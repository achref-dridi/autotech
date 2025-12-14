<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$userController = new UtilisateurController();

// Protection: Vérifier connexion
if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$utilisateur = $userController->getUtilisateurConnecte();
$message = '';
$messageType = '';

// Traitement formulaire profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profil') {
    $photo_profil = null;
    
    // Traiter upload photo si nécessaire
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === 0) {
        $uploadDir = __DIR__ . '/../../uploads/profils/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $extension = pathinfo($_FILES['photo_profil']['name'], PATHINFO_EXTENSION);
        $photo_profil = 'profil_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
        move_uploaded_file($_FILES['photo_profil']['tmp_name'], $uploadDir . $photo_profil);
    }
    
    $result = $userController->updateProfil(
        $_SESSION['user_id'],
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['telephone'],
        $_POST['adresse'],
        $_POST['ville'],
        $_POST['code_postal'],
        $photo_profil
    );
    
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'danger';
    $utilisateur = $userController->getUtilisateurConnecte(); // Recharger
}

// Traitement changement mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $result = $userController->changerMotDePasse(
        $_SESSION['user_id'],
        $_POST['ancien_mot_de_passe'],
        $_POST['nouveau_mot_de_passe']
    );
    
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'danger';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - AutoTech</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 800;
            color: #667eea !important;
        }
        .profile-container {
            max-width: 900px;
            margin: 40px auto;
        }
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        .profile-header {
            text-align: center;
            padding: 30px 0;
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 30px;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: 700;
            margin: 0 auto 15px;
        }
        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1a1a1a;
        }
        .form-label {
            font-weight: 600;
            color: #555;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="../public/index.php">🚗 AutoTech</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../public/index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/voitures.php">Voitures</a></li>
                    <li class="nav-item"><a class="nav-link active" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="profile-container">
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Profile Header -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?= strtoupper(substr($utilisateur['prenom'], 0, 1) . substr($utilisateur['nom'], 0, 1)) ?>
                </div>
                <h2><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></h2>
                <p class="text-muted"><?= htmlspecialchars($utilisateur['email']) ?></p>
            </div>

            <!-- Update Profile Form -->
            <h3 class="section-title">📝 Informations personnelles</h3>
            <form method="POST" enctype="multipart/form-data" onsubmit="return validerProfil()">
                <input type="hidden" name="action" value="update_profil">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" 
                               value="<?= htmlspecialchars($utilisateur['nom']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" 
                               value="<?= htmlspecialchars($utilisateur['prenom']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" 
                           value="<?= htmlspecialchars($utilisateur['telephone'] ?? '') ?>" 
                           placeholder="+216 XX XXX XXX">
                </div>

                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <textarea class="form-control" id="adresse" name="adresse" rows="2"><?= htmlspecialchars($utilisateur['adresse'] ?? '') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="ville" class="form-label">Ville</label>
                        <input type="text" class="form-control" id="ville" name="ville" 
                               value="<?= htmlspecialchars($utilisateur['ville'] ?? '') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="code_postal" class="form-label">Code Postal</label>
                        <input type="text" class="form-control" id="code_postal" name="code_postal" 
                               value="<?= htmlspecialchars($utilisateur['code_postal'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="photo_profil" class="form-label">Photo de profil</label>
                    <input type="file" class="form-control" id="photo_profil" name="photo_profil" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="profile-card">
            <h3 class="section-title">🔒 Changer le mot de passe</h3>
            <form method="POST">
                <input type="hidden" name="action" value="change_password">
                
                <div class="mb-3">
                    <label class="form-label">Ancien mot de passe</label>
                    <input type="password" class="form-control" name="ancien_mot_de_passe">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control" name="nouveau_mot_de_passe">
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" class="form-control" name="confirmer_nouveau_mot_de_passe">
                </div>

                <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/validation.js"></script>
</body>
</html>
