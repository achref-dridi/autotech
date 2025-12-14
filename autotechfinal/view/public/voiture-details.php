<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/VehiculeController.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$vehiculeController = new VehiculeController();
$userController = new UtilisateurController();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$vehicule = null;
if ($id > 0) {
    $vehicule = $vehiculeController->getVehiculeById($id);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $vehicule ? htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) : 'Véhicule' ?> - AutoTech</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../../assets/css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/animate.css">
    <link rel="stylesheet" href="../../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../../assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../../assets/css/magnific-popup.css">
    <link rel="stylesheet" href="../../assets/css/aos.css">
    <link rel="stylesheet" href="../../assets/css/ionicons.min.css">
    <link rel="stylesheet" href="../../assets/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="../../assets/css/jquery.timepicker.css">
    <link rel="stylesheet" href="../../assets/css/flaticon.css">
    <link rel="stylesheet" href="../../assets/css/icomoon.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body data-theme="dark">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php"><img src="../../images/off_logo.png" alt="logo.png" id="img_logo"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span> Menu
            </button>
            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item active"><a class="nav-link" href="voitures.php">Voitures</a></li>
                    <?php if ($userController->estConnecte()): ?>
                        <li class="nav-item"><a class="nav-link" href="../user/profil.php">Mon Profil</a></li>
                        <li class="nav-item"><a class="nav-link" href="../user/mes-vehicules.php">Mes Véhicules</a></li>
                        <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="../auth/login.php">Connexion</a></li>
                        <li class="nav-item"><a class="nav-link" href="../auth/signup.php">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- END nav -->

    <?php if (!$vehicule): ?>
        <!-- Error State Hero -->
        <section class="hero-wrap hero-wrap-2 js-fullheight" style="background-image: url('../../images/bg_3.jpg');" data-stellar-background-ratio="0.5">
            <div class="overlay"></div>
            <div class="container">
                <div class="row no-gutters slider-text js-fullheight align-items-end justify-content-start">
                    <div class="col-md-9 ftco-animate pb-5">
                        <p class="breadcrumbs"><span class="mr-2"><a href="index.php">Accueil <i class="ion-ios-arrow-forward"></i></a></span> <span>Véhicule <i class="ion-ios-arrow-forward"></i></span></p>
                        <h1 class="mb-3 bread">Véhicule introuvable</h1>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="ftco-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <h2>🚗 Véhicule introuvable</h2>
                        <p class="lead">Désolé, ce véhicule n'existe plus ou l'identifiant est invalide.</p>
                        <a href="voitures.php" class="btn btn-primary btn-lg mt-4">Retour aux véhicules</a>
                    </div>
                </div>
            </div>
        </section>
    <?php else: ?>
        <!-- Hero Section -->
        <section class="hero-wrap hero-wrap-2 js-fullheight" style="background-image: url('../../images/bg_3.jpg');" data-stellar-background-ratio="0.5">
            <div class="overlay"></div>
            <div class="container">
                <div class="row no-gutters slider-text js-fullheight align-items-end justify-content-start">
                    <div class="col-md-9 ftco-animate pb-5">
                        <p class="breadcrumbs">
                            <span class="mr-2"><a href="index.php">Accueil <i class="ion-ios-arrow-forward"></i></a></span>
                            <span class="mr-2"><a href="voitures.php">Voitures <i class="ion-ios-arrow-forward"></i></a></span>
                            <span>Détails <i class="ion-ios-arrow-forward"></i></span>
                        </p>
                        <h1 class="mb-3 bread">Détails du Véhicule</h1>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vehicle Details -->
        <section class="ftco-section ftco-car-details">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="car-details">
                            <div class="img rounded" style="background-image: url('<?=
                                !empty($vehicule['image_principale'])
                                    ? "../../uploads/" . htmlspecialchars($vehicule['image_principale'])
                                    : "../../images/car-1.jpg";
                            ?>');"></div>
                            <div class="text text-center">
                                <span class="subheading"><?= htmlspecialchars($vehicule['marque']) ?></span>
                                <h2><?= htmlspecialchars($vehicule['modele']) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Specs -->
                <div class="row">
                    <div class="col-md d-flex align-self-stretch ftco-animate">
                        <div class="media block-6 services">
                            <div class="media-body py-md-4">
                                <div class="d-flex mb-3 align-items-center">
                                    <div class="icon d-flex align-items-center justify-content-center"><span class="flaticon-dashboard"></span></div>
                                    <div class="text">
                                        <h3 class="heading mb-0 pl-3">
                                            Kilométrage
                                            <span><?= number_format((int)$vehicule['kilometrage'], 0, ',', ' ') ?> km</span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>      
                    </div>
                    <div class="col-md d-flex align-self-stretch ftco-animate">
                        <div class="media block-6 services">
                            <div class="media-body py-md-4">
                                <div class="d-flex mb-3 align-items-center">
                                    <div class="icon d-flex align-items-center justify-content-center"><span class="flaticon-pistons"></span></div>
                                    <div class="text">
                                        <h3 class="heading mb-0 pl-3">
                                            Transmission
                                            <span><?= htmlspecialchars($vehicule['transmission'] ?: 'Non spécifié') ?></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>      
                    </div>
                    <div class="col-md d-flex align-self-stretch ftco-animate">
                        <div class="media block-6 services">
                            <div class="media-body py-md-4">
                                <div class="d-flex mb-3 align-items-center">
                                    <div class="icon d-flex align-items-center justify-content-center"><span class="flaticon-diesel"></span></div>
                                    <div class="text">
                                        <h3 class="heading mb-0 pl-3">
                                            Carburant
                                            <span><?= htmlspecialchars($vehicule['carburant']) ?></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>      
                    </div>
                    <div class="col-md d-flex align-self-stretch ftco-animate">
                        <div class="media block-6 services">
                            <div class="media-body py-md-4">
                                <div class="d-flex mb-3 align-items-center">
                                    <div class="icon d-flex align-items-center justify-content-center"><span class="flaticon-car"></span></div>
                                    <div class="text">
                                        <h3 class="heading mb-0 pl-3">
                                            Année
                                            <span><?= htmlspecialchars($vehicule['annee']) ?></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>      
                    </div>
                </div>

                <!-- Details Tabs -->
                <div class="row">
                    <div class="col-md-12 pills">
                        <div class="bd-example bd-example-tabs">
                            <div class="d-flex justify-content-center">
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-description-tab" data-toggle="pill" href="#pills-description" role="tab" aria-controls="pills-description" aria-expanded="true">Description</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-expanded="true">Contact</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content" id="pills-tabContent">
                                <!-- Description Tab -->
                                <div class="tab-pane fade show active" id="pills-description" role="tabpanel" aria-labelledby="pills-description-tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php if (!empty($vehicule['description'])): ?>
                                                <p><?= nl2br(htmlspecialchars($vehicule['description'])) ?></p>
                                            <?php else: ?>
                                                <p>Aucune description disponible pour ce véhicule.</p>
                                            <?php endif; ?>
                                            
                                            <div class="mt-4">
                                                <h4>Caractéristiques supplémentaires</h4>
                                                <ul class="list-unstyled">
                                                    <li><strong>Couleur:</strong> <?= htmlspecialchars($vehicule['couleur'] ?: 'Non spécifié') ?></li>
                                                    <li><strong>Prix journalier:</strong> <?= !empty($vehicule['prix_journalier']) ? number_format($vehicule['prix_journalier'], 2) . ' DT' : 'Prix sur demande' ?></li>
                                                    <li><strong>Référence:</strong> #<?= htmlspecialchars($vehicule['id_vehicule']) ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Tab -->
                                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4>Informations du Propriétaire</h4>
                                            <p><strong>Nom complet:</strong> <?= htmlspecialchars($vehicule['prenom'] . ' ' . $vehicule['nom']) ?></p>
                                            <p><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($vehicule['email']) ?>"><?= htmlspecialchars($vehicule['email']) ?></a></p>
                                            <?php if (!empty($vehicule['telephone'])): ?>
                                                <p><strong>Téléphone:</strong> <a href="tel:<?= htmlspecialchars($vehicule['telephone']) ?>"><?= htmlspecialchars($vehicule['telephone']) ?></a></p>
                                            <?php endif; ?>
                                            <?php if (!empty($vehicule['ville'])): ?>
                                                <p><strong>Ville:</strong> <?= htmlspecialchars($vehicule['ville']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <h4>Actions</h4>
                                            <a href="mailto:<?= htmlspecialchars($vehicule['email']) ?>" class="btn btn-primary btn-block mb-2">✉️ Contacter par Email</a>
                                            <?php if (!empty($vehicule['telephone'])): ?>
                                                <a href="tel:<?= htmlspecialchars($vehicule['telephone']) ?>" class="btn btn-success btn-block mb-2">📞 Appeler</a>
                                            <?php endif; ?>
                                            <a href="voitures.php" class="btn btn-secondary btn-block">← Retour aux voitures</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="ftco-footer ftco-bg-dark ftco-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md">
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2"><a href="#" class="logo"><img src="../../images/off_logo.png" alt="logo.png" id="img_logo"></a></h2>
                        <p>Autotech est conçu pour centraliser et simplifier l'expérience automobile dans un environnement digital de pointe, répondant à la demande croissante d'efficacité et de transparence.</p>
                        <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
                            <li class="ftco-animate"><a href="#"><span class="icon-twitter"></span></a></li>
                            <li class="ftco-animate"><a href="#"><span class="icon-facebook"></span></a></li>
                            <li class="ftco-animate"><a href="#"><span class="icon-instagram"></span></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md">
                    <div class="ftco-footer-widget mb-4 ml-md-5">
                        <h2 class="ftco-heading-2">Informations</h2>
                        <ul class="list-unstyled">
                            <li><a href="#" class="py-2 d-block">À propos</a></li>
                            <li><a href="#" class="py-2 d-block">Services</a></li>
                            <li><a href="#" class="py-2 d-block">Termes et Conditions</a></li>
                            <li><a href="#" class="py-2 d-block">Garantie du Meilleur Prix</a></li>
                            <li><a href="#" class="py-2 d-block">Politique de Confidentialité et Cookies</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md">
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2">Support Client</h2>
                        <ul class="list-unstyled">
                            <li><a href="#" class="py-2 d-block">FAQ</a></li>
                            <li><a href="#" class="py-2 d-block">Option de Paiement</a></li>
                            <li><a href="#" class="py-2 d-block">Conseils de Réservation</a></li>
                            <li><a href="#" class="py-2 d-block">Comment ça marche</a></li>
                            <li><a href="#" class="py-2 d-block">Nous Contacter</a></li>
                        </ul>
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
                    <p>Copyright &copy;
                        <script>document.write(new Date().getFullYear());</script> Tous droits réservés | AutoTech
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- loader -->
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
        <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
        <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00" />
    </svg></div>

    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/jquery-migrate-3.0.1.min.js"></script>
    <script src="../../assets/js/popper.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/jquery.easing.1.3.js"></script>
    <script src="../../assets/js/jquery.waypoints.min.js"></script>
    <script src="../../assets/js/jquery.stellar.min.js"></script>
    <script src="../../assets/js/owl.carousel.min.js"></script>
    <script src="../../assets/js/jquery.magnific-popup.min.js"></script>
    <script src="../../assets/js/aos.js"></script>
    <script src="../../assets/js/jquery.animateNumber.min.js"></script>
    <script src="../../assets/js/bootstrap-datepicker.js"></script>
    <script src="../../assets/js/jquery.timepicker.min.js"></script>
    <script src="../../assets/js/scrollax.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
    <script src="../../assets/js/google-map.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>
