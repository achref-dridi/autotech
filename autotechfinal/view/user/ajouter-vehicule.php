<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../controller/VehiculeController.php';

$userController = new UtilisateurController();
if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$vehiculeController = new VehiculeController();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload image
    $imageName = null;
    if (isset($_FILES['image_principale']) && $_FILES['image_principale']['error'] === 0) {
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $extension = pathinfo($_FILES['image_principale']['name'], PATHINFO_EXTENSION);
        $imageName = 'vehicule_' . time() . '_' . uniqid() . '.' . $extension;
        move_uploaded_file($_FILES['image_principale']['tmp_name'], $uploadDir . $imageName);
    }
    
    try {
        $vehiculeController->createVehicule(
            $_SESSION['user_id'],
            $_POST['marque'],
            $_POST['modele'],
            $_POST['annee'],
            $_POST['carburant'],
            $_POST['kilometrage'],
            $_POST['couleur'] ?? '',
            $_POST['transmission'] ?? '',
            $_POST['prix_journalier'] ?? 0,
            $_POST['description'] ?? '',
            $imageName
        );
        
        header('Location: mes-vehicules.php?success=ajoute');
        exit();
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Véhicule - AutoTech</title>
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
                    <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item active"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-wrap hero-wrap-2 js-fullheight" style="background-image: url('../../images/bg_3.jpg');" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text js-fullheight align-items-end justify-content-start">
                <div class="col-md-9 ftco-animate pb-5">
                    <p class="breadcrumbs"><span class="mr-2"><a href="../public/index.php">Accueil <i class="ion-ios-arrow-forward"></i></a></span> <span class="mr-2"><a href="mes-vehicules.php">Mes Véhicules <i class="ion-ios-arrow-forward"></i></a></span> <span>Ajouter un véhicule <i class="ion-ios-arrow-forward"></i></span></p>
                    <h1 class="mb-3 bread">Ajouter un véhicule</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <section class="ftco-section bg-light">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="bg-white rounded p-5">
                        <div class="text-center mb-4">
                            <h2 class="mb-2">Ajouter un Véhicule</h2>
                            <p class="text-muted">Remplissez les informations de votre véhicule</p>
                        </div>

                        <form method="POST" enctype="multipart/form-data" onsubmit="return validerVehicule()">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Marque <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="marque" name="marque" placeholder="Ex: Toyota" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Modèle <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="modele" name="modele" placeholder="Ex: Corolla" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Année <span style="color: red;">*</span></label>
                                    <input type="number" class="form-control" id="annee" name="annee" placeholder="<?= date('Y') ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Carburant <span style="color: red;">*</span></label>
                                    <select class="form-control" id="carburant" name="carburant" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="Essence">Essence</option>
                                        <option value="Diesel">Diesel</option>
                                        <option value="Hybride">Hybride</option>
                                        <option value="Électrique">Électrique</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Kilométrage <span style="color: red;">*</span></label>
                                    <input type="number" class="form-control" id="kilometrage" name="kilometrage" placeholder="0" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Couleur</label>
                                    <input type="text" class="form-control" id="couleur" name="couleur" placeholder="Ex: Blanc">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Transmission</label>
                                    <select class="form-control" id="transmission" name="transmission">
                                        <option value="">Sélectionner...</option>
                                        <option value="Manuelle">Manuelle</option>
                                        <option value="Automatique">Automatique</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Prix Journalier (DT)</label>
                                <input type="number" step="0.01" class="form-control" id="prix_journalier" name="prix_journalier" placeholder="0.00">
                                <small class="text-muted">Laissez vide si le prix est à négocier</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Décrivez votre véhicule, son état, ses équipements..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Image Principale <span style="color: red;">*</span></label>
                                <input type="file" class="form-control" id="image_principale" name="image_principale" accept="image/*">
                                <small class="text-muted">Formats acceptés: JPG, PNG, GIF (Max 5MB)</small>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-8">
                                    <button type="submit" class="btn btn-primary btn-block py-3" style="font-size: 16px; font-weight: 600;">
                                        <span class="icon-check" style="margin-right: 10px;"></span>Ajouter le Véhicule
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <a href="mes-vehicules.php" class="btn btn-secondary btn-block py-3" style="font-size: 16px;">
                                        <span class="icon-x" style="margin-right: 10px;"></span>Annuler
                                    </a>
                                </div>
                            </div>
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
