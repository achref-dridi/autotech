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

// Vérifier si un ID est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: mes-vehicules.php');
    exit();
}

$id_vehicule = (int)$_GET['id'];

// Vérifier que l'utilisateur est propriétaire
if (!$vehiculeController->estProprietaire($id_vehicule, $_SESSION['user_id'])) {
    header('Location: mes-vehicules.php');
    exit();
}

// Récupérer les données du véhicule
$vehicule = $vehiculeController->getVehiculeById($id_vehicule);
if (!$vehicule) {
    header('Location: mes-vehicules.php');
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload image si nouvelle image
    $imageName = null;
    if (isset($_FILES['image_principale']) && $_FILES['image_principale']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $extension = strtolower(pathinfo($_FILES['image_principale']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($extension, $allowedExtensions)) {
            $imageName = 'vehicule_' . time() . '_' . uniqid() . '.' . $extension;
            
            if (move_uploaded_file($_FILES['image_principale']['tmp_name'], $uploadDir . $imageName)) {
                // Supprimer l'ancienne image si elle existe
                if (!empty($vehicule['image_principale']) && file_exists($uploadDir . $vehicule['image_principale'])) {
                    unlink($uploadDir . $vehicule['image_principale']);
                }
            } else {
                $imageName = null;
            }
        }
    }
    
    try {
        $vehiculeController->updateVehicule(
            $id_vehicule,
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
        
        header('Location: mes-vehicules.php?success=modifie');
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
    <title>Modifier un Véhicule - AutoTech</title>
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
                    <p class="breadcrumbs"><span class="mr-2"><a href="../public/index.php">Accueil <i class="ion-ios-arrow-forward"></i></a></span> <span class="mr-2"><a href="mes-vehicules.php">Mes Véhicules <i class="ion-ios-arrow-forward"></i></a></span> <span>Modifier un véhicule <i class="ion-ios-arrow-forward"></i></span></p>
                    <h1 class="mb-3 bread">Modifier un véhicule</h1>
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
                            <h2 class="mb-2">Modifier le Véhicule</h2>
                            <p class="text-muted">Mettez à jour les informations de votre véhicule</p>
                        </div>

                        <form method="POST" enctype="multipart/form-data" onsubmit="return validerVehicule()">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Marque <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="marque" name="marque" 
                                           value="<?= htmlspecialchars($vehicule['marque']) ?>" 
                                           placeholder="Ex: Toyota" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Modèle <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="modele" name="modele" 
                                           value="<?= htmlspecialchars($vehicule['modele']) ?>" 
                                           placeholder="Ex: Corolla" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Année <span style="color: red;">*</span></label>
                                    <input type="number" class="form-control" id="annee" name="annee" 
                                           value="<?= htmlspecialchars($vehicule['annee']) ?>" 
                                           placeholder="<?= date('Y') ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Carburant <span style="color: red;">*</span></label>
                                    <select class="form-control" id="carburant" name="carburant" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="Essence" <?= $vehicule['carburant'] === 'Essence' ? 'selected' : '' ?>>Essence</option>
                                        <option value="Diesel" <?= $vehicule['carburant'] === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                                        <option value="Hybride" <?= $vehicule['carburant'] === 'Hybride' ? 'selected' : '' ?>>Hybride</option>
                                        <option value="Électrique" <?= $vehicule['carburant'] === 'Électrique' ? 'selected' : '' ?>>Électrique</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Kilométrage <span style="color: red;">*</span></label>
                                    <input type="number" class="form-control" id="kilometrage" name="kilometrage" 
                                           value="<?= htmlspecialchars($vehicule['kilometrage']) ?>" 
                                           placeholder="0" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Couleur</label>
                                    <input type="text" class="form-control" id="couleur" name="couleur" 
                                           value="<?= htmlspecialchars($vehicule['couleur'] ?? '') ?>" 
                                           placeholder="Ex: Blanc">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Transmission</label>
                                    <select class="form-control" id="transmission" name="transmission">
                                        <option value="">Sélectionner...</option>
                                        <option value="Manuelle" <?= ($vehicule['transmission'] ?? '') === 'Manuelle' ? 'selected' : '' ?>>Manuelle</option>
                                        <option value="Automatique" <?= ($vehicule['transmission'] ?? '') === 'Automatique' ? 'selected' : '' ?>>Automatique</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Prix Journalier (DT)</label>
                                <input type="number" step="0.01" class="form-control" id="prix_journalier" name="prix_journalier" 
                                       value="<?= htmlspecialchars($vehicule['prix_journalier'] ?? '') ?>" 
                                       placeholder="0.00">
                                <small class="text-muted">Laissez vide si le prix est à négocier</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Décrivez votre véhicule, son état, ses équipements..."><?= htmlspecialchars($vehicule['description'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Image Principale</label>
                                <?php if (!empty($vehicule['image_principale'])): ?>
                                    <div class="mb-2">
                                        <img src="../../uploads/<?= htmlspecialchars($vehicule['image_principale']) ?>" 
                                             alt="Image actuelle" 
                                             style="max-width: 200px; max-height: 150px; border-radius: 10px; object-fit: cover;">
                                        <p class="text-muted small mt-1">Image actuelle</p>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="image_principale" name="image_principale" accept="image/*">
                                <small class="text-muted">Formats acceptés: JPG, PNG, GIF (Max 5MB) - Laissez vide pour conserver l'image actuelle</small>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-8">
                                    <button type="submit" class="btn btn-primary btn-block py-3" style="font-size: 16px; font-weight: 600;">
                                        <span class="icon-check" style="margin-right: 10px;"></span>Enregistrer les Modifications
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
