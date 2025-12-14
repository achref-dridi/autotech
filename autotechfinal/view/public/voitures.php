<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/VehiculeController.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$vehiculeController = new VehiculeController();
$userController = new UtilisateurController();

$vehicules = $vehiculeController->getAllVehicules();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les Voitures - AutoTech</title>
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

    <!-- Hero Header -->
    <section class="hero-wrap hero-wrap-2 js-fullheight" style="background-image: url('../../images/deal3.webp');" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text js-fullheight align-items-end justify-content-start">
                <div class="col-md-9 ftco-animate pb-5">
                    <p class="breadcrumbs"><span class="mr-2"><a href="index.php">Accueil <i class="ion-ios-arrow-forward"></i></a></span> <span>Voitures <i class="ion-ios-arrow-forward"></i></span></p>
                    <h1 class="mb-3 bread">Réserver ou Acheter votre voiture</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Vehicles Grid -->
    <section class="ftco-section bg-light">
        <div class="container">
            <div class="row">
                <?php if (!empty($vehicules)): ?>
                    <?php foreach ($vehicules as $v): ?>
                        <div class="col-md-4">
                            <div class="car-wrap rounded ftco-animate">
                                <div class="img rounded d-flex align-items-end" style="background-image: url('<?= 
                                    !empty($v['image_principale']) 
                                        ? '../../uploads/' . htmlspecialchars($v['image_principale']) 
                                        : '../../images/car-1.jpg' 
                                ?>');">
                                </div>
                                <div class="text">
                                    <h2 class="mb-0"><a href="voiture-details.php?id=<?= $v['id_vehicule'] ?>"><?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?></a></h2>
                                    <div class="d-flex mb-3">
                                        <span class="cat"><?= htmlspecialchars($v['annee']) ?></span>
                                        <p class="price ml-auto"><?= !empty($v['prix_journalier']) ? number_format($v['prix_journalier'], 2) . ' DT' : 'Prix sur demande' ?> <span>/jour</span></p>
                                    </div>
                                    <p class="d-flex mb-0 d-block">
                                        <a href="voiture-details.php?id=<?= $v['id_vehicule'] ?>" class="btn btn-secondary py-2 ml-1 w-100">Détails</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <h2>Aucun véhicule disponible</h2>
                        <p class="text-muted">Aucun véhicule n'est actuellement disponible sur la plateforme.</p>
                        <?php if ($userController->estConnecte()): ?>
                            <a href="../user/ajouter-vehicule.php" class="btn btn-primary btn-lg mt-3">
                                Ajouter mon véhicule
                            </a>
                        <?php else: ?>
                            <a href="../auth/signup.php" class="btn btn-primary btn-lg mt-3">
                                S'inscrire pour ajouter un véhicule
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
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
