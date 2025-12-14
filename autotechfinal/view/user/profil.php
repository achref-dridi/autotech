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
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/animate.css">
    <link rel="stylesheet" href="../../assets/css/flaticon.css">
    <link rel="stylesheet" href="../../assets/css/icomoon.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body data-theme="dark">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand" href="../public/index.php"><img src="../../images/off_logo.png" alt="logo.png" id="img_logo"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span> Menu
            </button>
            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="../public/index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/voitures.php">Voitures</a></li>
                    <li class="nav-item active"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-wrap hero-wrap-2 js-fullheight" style="background-image: url('../../images/bg_1.jpg');" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text js-fullheight align-items-end justify-content-start">
                <div class="col-md-9 ftco-animate pb-5">
                    <p class="breadcrumbs"><span class="mr-2"><a href="../public/index.php">Accueil <i class="ion-ios-arrow-forward"></i></a></span> <span>Mon Profil <i class="ion-ios-arrow-forward"></i></span></p>
                    <h1 class="mb-3 bread">Gérez votre profil</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Profile Content -->
    <section class="ftco-section bg-light">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Profile Header -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="bg-white rounded p-4 text-center">
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 36px; font-weight: 700; margin: 0 auto 15px;">
                            <?= strtoupper(substr($utilisateur['prenom'], 0, 1) . substr($utilisateur['nom'], 0, 1)) ?>
                        </div>
                        <h2><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></h2>
                        <p class="text-muted"><?= htmlspecialchars($utilisateur['email']) ?></p>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Update Profile Form -->
                <div class="col-md-6 mb-4">
                    <div class="bg-white rounded p-4">
                        <h3 class="mb-4">📝 Informations personnelles</h3>
                        <form method="POST" enctype="multipart/form-data" onsubmit="return validerProfil()">
                            <input type="hidden" name="action" value="update_profil">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="nom" 
                                           value="<?= htmlspecialchars($utilisateur['nom']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" class="form-control" name="prenom" 
                                           value="<?= htmlspecialchars($utilisateur['prenom']) ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Téléphone</label>
                                <input type="text" class="form-control" name="telephone" 
                                       value="<?= htmlspecialchars($utilisateur['telephone'] ?? '') ?>" 
                                       placeholder="+216 XX XXX XXX">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Adresse</label>
                                <textarea class="form-control" name="adresse" rows="2"><?= htmlspecialchars($utilisateur['adresse'] ?? '') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Ville</label>
                                    <input type="text" class="form-control" name="ville" 
                                           value="<?= htmlspecialchars($utilisateur['ville'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Code Postal</label>
                                    <input type="text" class="form-control" name="code_postal" 
                                           value="<?= htmlspecialchars($utilisateur['code_postal'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Photo de profil</label>
                                <input type="file" class="form-control" name="photo_profil" accept="image/*">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Mettre à jour le profil</button>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="col-md-6 mb-4">
                    <div class="bg-white rounded p-4">
                        <h3 class="mb-4">🔒 Changer le mot de passe</h3>
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

                            <button type="submit" class="btn btn-primary w-100">Changer le mot de passe</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="ftco-footer ftco-bg-dark ftco-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md">
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2"><a href="#" class="logo"><img src="../../images/off_logo.png" alt="logo.png" id="img_logo"></a></h2>
                        <p>Autotech est conçu pour centraliser et simplifier l'expérience automobile dans un environnement digital de pointe.</p>
                    </div>
                </div>
                <div class="col-md">
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2">Vous avez des Questions?</h2>
                        <div class="block-23 mb-3">
                            <ul>
                                <li><span class="icon icon-map-marker"></span><span class="text">Esprit, Ariana sogra, Ariana, Tunisie</span></li>
                                <li><a href="#"><span class="icon icon-phone"></span><span class="text">+216 33 856 909</span></a></li>
                                <li><a href="#"><span class="icon icon-envelope"></span><span class="text">AutoTech@gmail.tn</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> Tous droits réservés | AutoTech</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/jquery-migrate-3.0.1.min.js"></script>
    <script src="../../assets/js/popper.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/jquery.stellar.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/validation.js"></script>
</body>
</html>
