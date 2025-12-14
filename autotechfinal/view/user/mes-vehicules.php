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
$mesVehicules = $vehiculeController->getVehiculesByUtilisateur($_SESSION['user_id']);

// Gestion suppression
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    if ($vehiculeController->estProprietaire($id, $_SESSION['user_id'])) {
        $vehiculeController->deleteVehicule($id);
        header('Location: mes-vehicules.php?success=supprime');
        exit();
    }
}

$message = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'supprime') {
        $message = 'Véhicule supprimé avec succès.';
    } elseif ($_GET['success'] === 'ajoute') {
        $message = 'Véhicule ajouté avec succès.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Véhicules - AutoTech</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/animate.css">
    <link rel="stylesheet" href="../../assets/css/flaticon.css">
    <link rel="stylesheet" href="../../assets/css/icomoon.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .vehicle-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            background: #fff;
            transition: transform 0.3s;
        }
        .vehicle-card:hover {
            transform: translateY(-5px);
        }
        .vehicle-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
        }
        .vehicle-body {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .vehicle-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .vehicle-specs {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 10px;
        }
        .spec-badge {
            background: #f0f0f0;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        .d-flex.gap-2 a {
            text-align: center;
        }
        @media (max-width: 768px) {
            .vehicle-image {
                height: 150px;
            }
        }
    </style>
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
    <section class="hero-wrap hero-wrap-2 js-fullheight" style="background-image: url('../../images/bg_2.jpg');" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text js-fullheight align-items-end justify-content-start">
                <div class="col-md-9 ftco-animate pb-5">
                    <p class="breadcrumbs"><span class="mr-2"><a href="../public/index.php">Accueil <i class="ion-ios-arrow-forward"></i></a></span> <span>Mes Véhicules <i class="ion-ios-arrow-forward"></i></span></p>
                    <h1 class="mb-3 bread">Gérez vos véhicules</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="ftco-section bg-light">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2>Mes Véhicules</h2>
                            <p class="text-muted">Gérez vos véhicules disponibles à la location</p>
                        </div>
                        <a href="ajouter-vehicule.php" class="btn btn-primary">
                            <span class="icon-plus"></span> Ajouter un véhicule
                        </a>
                    </div>
                </div>
            </div>

            <?php if (empty($mesVehicules)): ?>
                <div class="empty-state text-center">
                    <div class="empty-state-icon" style="font-size: 48px;">🚗</div>
                    <h2>Aucun véhicule</h2>
                    <p class="text-muted">Vous n'avez pas encore ajouté de véhicule.</p>
                    <a href="ajouter-vehicule.php" class="btn btn-add mt-3">Ajouter mon premier véhicule</a>
                </div>
            <?php else: ?>
                <div class="row g-4 mb-5">
                    <?php foreach ($mesVehicules as $v): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="vehicle-card">
                                <img src="<?= 
                                    !empty($v['image_principale']) 
                                        ? '../../uploads/' . htmlspecialchars($v['image_principale']) 
                                        : '../../assets/images/car-1.jpg' 
                                ?>" alt="<?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>" class="vehicle-image">
                                
                                <div class="vehicle-body">
                                    <h5 class="vehicle-title">
                                        <?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>
                                    </h5>
                                    
                                    <div class="vehicle-specs">
                                        <span class="spec-badge">📅 <?= htmlspecialchars($v['annee']) ?></span>
                                        <span class="spec-badge">⛽ <?= htmlspecialchars($v['carburant']) ?></span>
                                        <span class="spec-badge">🛣️ <?= number_format($v['kilometrage']) ?> km</span>
                                    </div>

                                    <?php if (!empty($v['prix_journalier'])): ?>
                                        <p class="fw-bold text-primary mb-3">
                                            <?= number_format($v['prix_journalier'], 2) ?> DT / jour
                                        </p>
                                    <?php endif; ?>

                                    <div class="d-flex gap-2">
                                        <a href="../public/voiture-details.php?id=<?= $v['id_vehicule'] ?>" 
                                           class="btn btn-sm btn-outline-primary flex-fill">
                                            Voir
                                        </a>
                                        <a href="modifier-vehicule.php?id=<?= $v['id_vehicule'] ?>" 
                                           class="btn btn-sm btn-warning flex-fill">
                                            Modifier
                                        </a>
                                        <a href="mes-vehicules.php?supprimer=<?= $v['id_vehicule'] ?>" 
                                           class="btn btn-sm btn-danger flex-fill"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule?')">
                                            Supprimer
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
</body>
</html>
