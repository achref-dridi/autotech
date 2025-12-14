<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/VehiculeController.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$vehiculeController = new VehiculeController();
$userController = new UtilisateurController();

$vehicules = $vehiculeController->getAllVehicules();
$vehiculesVedette = array_slice($vehicules, 0, 8);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoTech - Location de Voitures</title>
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
                    <li class="nav-item active"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="voitures.php">Voitures</a></li>
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

    <!-- Hero Section -->
    <div class="hero-wrap ftco-degree-bg" style="background-image: url('../../images/bg_1.jpg');" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text justify-content-start align-items-center justify-content-center">
                <div class="col-lg-8 ftco-animate">
                    <div class="text w-100 text-center mb-md-5 pb-md-5">
                        <h1 class="mb-4">AutoTech</h1>
                        <p style="font-size: 18px;">Connectez-vous, publiez des annonces, réservez des véhicules et partagez vos expériences. Une plateforme sécurisée pour les particuliers et les boutiques partenaires.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Vehicles -->
    <section class="ftco-section ftco-no-pt bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 heading-section text-center ftco-animate mb-5">
                    <span class="subheading">Ce que nous offrons</span>
                    <h2 class="mb-2">Véhicules en Vedette</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="carousel-car owl-carousel">
                        <?php if (!empty($vehiculesVedette)): ?>
                            <?php foreach ($vehiculesVedette as $v): ?>
                                <div class="item">
                                    <div class="car-wrap rounded ftco-animate">
                                        <div class="img rounded d-flex align-items-end" style="background-image: url('<?= 
                                            !empty($v['image_principale']) 
                                                ? '../../uploads/' . htmlspecialchars($v['image_principale']) 
                                                : '../../images/car-1.jpg' 
                                        ?>');"></div>
                                        <div class="text">
                                            <h2 class="mb-0"><a href="voiture-details.php?id=<?= $v['id_vehicule'] ?>"><?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?></a></h2>
                                            <div class="d-flex mb-3">
                                                <span class="cat"><?= htmlspecialchars($v['annee']) ?></span>
                                                <p class="price ml-auto"><?= !empty($v['prix_journalier']) ? number_format($v['prix_journalier'], 2) . ' DT' : 'Prix sur demande' ?> <span>/jour</span></p>
                                            </div>
                                            <p class="d-flex mb-0 d-block">
                                                <a href="voiture-details.php?id=<?= $v['id_vehicule'] ?>" class="btn btn-secondary py-2 ml-1">Détails</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="item">
                                <div class="car-wrap rounded ftco-animate">
                                    <div class="img rounded d-flex align-items-end" style="background-image: url('../../images/car-1.jpg');"></div>
                                    <div class="text">
                                        <h2 class="mb-0"><a href="#">Aucun véhicule disponible</a></h2>
                                        <div class="d-flex mb-3">
                                            <span class="cat">-</span>
                                            <p class="price ml-auto">- <span>/jour</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="ftco-section ftco-about" id="about">
        <div class="container">
            <div class="row no-gutters">
                <div class="col-md-6 p-md-5 img img-2 d-flex justify-content-center align-items-center"
                    style="background-image: url('../../images/about.jpg');">
                </div>
                <div class="col-md-6 wrap-about ftco-animate">
                    <div class="heading-section heading-section-white pl-md-5">
                        <span class="subheading">À propos de nous</span>
                        <h2 class="mb-4">Bienvenue chez AutoTech</h2>
                        <p>Notre plateforme automobile a été développée avec un objectif clair : révolutionner la manière dont les utilisateurs interagissent avec le marché automobile. Que ce soit pour acheter, louer ou publier une offre, tout devient plus intuitif et accessible.</p>
                        <p>Nous réunissons sur un même espace : <br>
✔️ des particuliers souhaitant vendre ou louer leurs véhicules, <br>
✔️ des boutiques partenaires désirant gérer leurs annonces en ligne, <br>
✔️ des conducteurs qui proposent des services de déplacement. <br>
<br>
Grâce à une interface moderne et des fonctionnalités intelligentes, nous rendons l'expérience automobile plus simple, plus rapide et plus sécurisée.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="ftco-section" id="services">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-md-7 text-center heading-section ftco-animate">
                    <span class="subheading">Services</span>
                    <h2 class="mb-3">Nos Derniers Services</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="services services-2 w-100 text-center">
                        <div class="icon d-flex align-items-center justify-content-center"><span
                                class="flaticon-wedding-car"></span></div>
                        <div class="text w-100">
                            <h3 class="heading mb-2">Cérémonie de Mariage</h3>
                            <p>Une petite rivière nommée Duden coule près de leur lieu et l'approvisionne avec les regelialia nécessaires.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="services services-2 w-100 text-center">
                        <div class="icon d-flex align-items-center justify-content-center"><span
                                class="flaticon-transportation"></span></div>
                        <div class="text w-100">
                            <h3 class="heading mb-2">Transfert en Ville</h3>
                            <p>Une petite rivière nommée Duden coule près de leur lieu et l'approvisionne avec les regelialia nécessaires.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="services services-2 w-100 text-center">
                        <div class="icon d-flex align-items-center justify-content-center"><span class="flaticon-car"></span></div>
                        <div class="text w-100">
                            <h3 class="heading mb-2">Transfert Aéroport</h3>
                            <p>Une petite rivière nommée Duden coule près de leur lieu et l'approvisionne avec les regelialia nécessaires.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="services services-2 w-100 text-center">
                        <div class="icon d-flex align-items-center justify-content-center"><span
                                class="flaticon-transportation"></span></div>
                        <div class="text w-100">
                            <h3 class="heading mb-2">Visite de Toute la Ville</h3>
                            <p>Une petite rivière nommée Duden coule près de leur lieu et l'approvisionne avec les regelialia nécessaires.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="ftco-section testimony-section bg-light">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-md-7 text-center heading-section ftco-animate">
                    <span class="subheading">Témoignage</span>
                    <h2 class="mb-3">Clients Satisfaits</h2>
                </div>
            </div>
            <div class="row ftco-animate">
                <div class="col-md-12">
                    <div class="carousel-testimony owl-carousel ftco-owl">
                        <div class="item">
                            <div class="testimony-wrap rounded text-center py-4 pb-5">
                                <div class="user-img mb-2" style="background-image: url('../../images/person_1.jpg')">
                                </div>
                                <div class="text pt-4">
                                    <p class="mb-4">Loin là-bas, derrière les montagnes de mots, loin des pays Vokalia et
                                        Consonantia, vivent les textes aveugles.</p>
                                    <p class="name">Roger Scott</p>
                                    <span class="position">Directeur Marketing</span>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="testimony-wrap rounded text-center py-4 pb-5">
                                <div class="user-img mb-2" style="background-image: url('../../images/person_2.jpg')">
                                </div>
                                <div class="text pt-4">
                                    <p class="mb-4">Loin là-bas, derrière les montagnes de mots, loin des pays Vokalia et
                                        Consonantia, vivent les textes aveugles.</p>
                                    <p class="name">Sophie Martin</p>
                                    <span class="position">Concepteur d'Interface</span>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="testimony-wrap rounded text-center py-4 pb-5">
                                <div class="user-img mb-2" style="background-image: url('../../images/person_3.jpg')">
                                </div>
                                <div class="text pt-4">
                                    <p class="mb-4">Loin là-bas, derrière les montagnes de mots, loin des pays Vokalia et
                                        Consonantia, vivent les textes aveugles.</p>
                                    <p class="name">Marc Durand</p>
                                    <span class="position">Concepteur UI</span>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="testimony-wrap rounded text-center py-4 pb-5">
                                <div class="user-img mb-2" style="background-image: url('../../images/person_4.jpg')">
                                </div>
                                <div class="text pt-4">
                                    <p class="mb-4">Loin là-bas, derrière les montagnes de mots, loin des pays Vokalia et
                                        Consonantia, vivent les textes aveugles.</p>
                                    <p class="name">Julie Bernard</p>
                                    <span class="position">Développeur Web</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="ftco-counter ftco-section img bg-light" id="section-counter">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-3 justify-content-center counter-wrap ftco-animate">
                    <div class="block-18">
                        <div class="text text-border d-flex align-items-center">
                            <strong class="number" data-number="60">0</strong>
                            <span>Années <br>d'Expérience</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 justify-content-center counter-wrap ftco-animate">
                    <div class="block-18">
                        <div class="text text-border d-flex align-items-center">
                            <strong class="number" data-number="1090">0</strong>
                            <span>Total <br>Voitures</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 justify-content-center counter-wrap ftco-animate">
                    <div class="block-18">
                        <div class="text text-border d-flex align-items-center">
                            <strong class="number" data-number="2590">0</strong>
                            <span>Clients <br>Satisfaits</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 justify-content-center counter-wrap ftco-animate">
                    <div class="block-18">
                        <div class="text d-flex align-items-center">
                            <strong class="number" data-number="67">0</strong>
                            <span>Total <br>Agences</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="ftco-footer ftco-bg-dark ftco-section" id="contact">
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
                            <li><a href="#about" class="py-2 d-block">À propos</a></li>
                            <li><a href="#services" class="py-2 d-block">Services</a></li>
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
