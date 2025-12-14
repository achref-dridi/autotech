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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../../assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../../assets/css/flaticon.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --secondary-color: #10b981;
            --dark-bg: #0f172a;
            --card-bg: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #334155;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
        }

        .navbar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand img {
            height: 45px;
            filter: brightness(1.1);
        }

        .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem !important;
            border-radius: 6px;
        }

        .nav-link:hover, .nav-item.active .nav-link {
            color: var(--primary-light) !important;
            background: rgba(37, 99, 235, 0.1);
        }

        .hero-wrap {
            background-size: cover;
            background-position: center;
            position: relative;
            height: 600px;
            display: flex;
            align-items: center;
        }

        .hero-wrap .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(37, 99, 235, 0.65));
        }

        .hero-wrap .text {
            position: relative;
            z-index: 1;
        }

        .hero-wrap h1 {
            font-size: 4rem;
            font-weight: 800;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
            margin-bottom: 1.5rem;
        }

        .hero-wrap p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.8;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .ftco-section {
            padding: 5rem 0;
        }

        .bg-light {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1e293b 100%);
        }

        .heading-section {
            margin-bottom: 3rem;
        }

        .subheading {
            color: var(--primary-light);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.9rem;
        }

        .heading-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-top: 0.5rem;
        }

        .car-wrap {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            margin: 10px;
        }

        .car-wrap:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.2);
            border-color: var(--primary-color);
        }

        .car-wrap .img {
            height: 250px;
            background-size: cover;
            background-position: center;
        }

        .car-wrap .text {
            padding: 1.5rem;
        }

        .car-wrap .text h2 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .car-wrap .text h2 a {
            color: var(--text-primary);
            text-decoration: none;
            transition: color 0.3s;
        }

        .car-wrap .text h2 a:hover {
            color: var(--primary-light);
        }

        .car-wrap .cat {
            background: rgba(37, 99, 235, 0.1);
            padding: 0.4rem 1rem;
            border-radius: 8px;
            color: var(--primary-light);
            font-weight: 600;
            font-size: 0.85rem;
        }

        .car-wrap .price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .car-wrap .price span {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            color: white;
            padding: 0.6rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .ftco-about {
            background: var(--card-bg);
        }

        .ftco-about .img {
            background-size: cover;
            background-position: center;
            min-height: 500px;
        }

        .wrap-about {
            padding: 3rem;
        }

        .heading-section-white .subheading {
            color: var(--primary-light);
        }

        .heading-section-white h2 {
            color: var(--text-primary);
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .wrap-about p {
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 1rem;
        }

        .services {
            padding: 2rem 1rem;
            text-align: center;
            background: var(--card-bg);
            border-radius: 12px;
            margin-bottom: 2rem;
            transition: all 0.3s;
            border: 1px solid var(--border-color);
        }

        .services:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.15);
        }

        .services .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }

        .services h3 {
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .services p {
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .testimony-section {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1e293b 100%);
        }

        .testimony-wrap {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 2rem;
            margin: 10px;
        }

        .user-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            margin: 0 auto;
            border: 3px solid var(--primary-color);
        }

        .testimony-wrap .text p {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .testimony-wrap .name {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .testimony-wrap .position {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .ftco-counter {
            background: linear-gradient(rgba(37, 99, 235, 0.9), rgba(30, 64, 175, 0.85)),
                        url('../../images/bg_3.jpg');
            background-size: cover;
            background-attachment: fixed;
            padding: 5rem 0;
        }

        .counter-wrap {
            margin-bottom: 2rem;
        }

        .block-18 .text {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .block-18 .number {
            font-size: 3rem;
            font-weight: 800;
            color: white;
        }

        .block-18 span {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            line-height: 1.4;
        }

        .text-border {
            border-right: 2px solid rgba(255, 255, 255, 0.3);
            padding-right: 2rem;
        }

        footer {
            background: var(--dark-bg);
            padding: 3rem 0 1rem;
            border-top: 1px solid var(--border-color);
        }

        .ftco-heading-2 {
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        footer p, footer a {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        footer a:hover {
            color: var(--primary-light);
        }

        footer ul {
            list-style: none;
            padding: 0;
        }

        footer ul li {
            margin-bottom: 0.5rem;
        }

        .ftco-footer-social li {
            display: inline-block;
            margin-right: 0.5rem;
        }

        .ftco-footer-social a {
            width: 40px;
            height: 40px;
            background: var(--card-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            transition: all 0.3s;
        }

        .ftco-footer-social a:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .hero-wrap h1 {
                font-size: 2.5rem;
            }

            .hero-wrap {
                height: 500px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php"><img src="../../images/off_logo.png" alt="logo.png" id="img_logo"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
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
    <div class="hero-wrap" style="background-image: url('../../images/bg_1.jpg');">
        <div class="overlay"></div>
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-8">
                    <div class="text w-100 text-center">
                        <h1>AutoTech</h1>
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
                <div class="col-md-12 heading-section text-center mb-5">
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
                                    <div class="car-wrap rounded">
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
                                <div class="car-wrap rounded">
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
                <div class="col-md-6 wrap-about">
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
                <div class="col-md-7 text-center heading-section">
                    <span class="subheading">Services</span>
                    <h2 class="mb-3">Nos Derniers Services</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="services w-100">
                        <div class="icon"><span class="flaticon-wedding-car"></span></div>
                        <div class="text w-100">
                            <h3 class="heading mb-2">Cérémonie de Mariage</h3>
                            <p>Une petite rivière nommée Duden coule près de leur lieu et l'approvisionne avec les regelialia nécessaires.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="services w-100">
                        <div class="icon"><span class="flaticon-transportation"></span></div>
                        <div class="text w-100">
                            <h3 class="heading mb-2">Transfert en Ville</h3>
                            <p>Une petite rivière nommée Duden coule près de leur lieu et l'approvisionne avec les regelialia nécessaires.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="services w-100">
                        <div class="icon"><span class="flaticon-car"></span></div>
                        <div class="text w-100">
                            <h3 class="heading mb-2">Transfert Aéroport</h3>
                            <p>Une petite rivière nommée Duden coule près de leur lieu et l'approvisionne avec les regelialia nécessaires.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="services w-100">
                        <div class="icon"><span class="flaticon-transportation"></span></div>
                        <div class="text w-100">
                            <h3 class="heading mb-2">Visite de Toute la Ville</h3>
                            <p>Une petite rivière nammée Duden coule près de leur lieu et l'approvisionne avec les regelialia nécessaires.</p>
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
                <div class="col-md-7 text-center heading-section">
                    <span class="subheading">Témoignage</span>
                    <h2 class="mb-3">Clients Satisfaits</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="carousel-testimony owl-carousel">
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
    <section class="ftco-counter ftco-section img" id="section-counter">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-3 justify-content-center counter-wrap">
                    <div class="block-18">
                        <div class="text text-border d-flex align-items-center">
                            <strong class="number" data-number="60">0</strong>
                            <span>Années <br>d'Expérience</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 justify-content-center counter-wrap">
                    <div class="block-18">
                        <div class="text text-border d-flex align-items-center">
                            <strong class="number" data-number="1090">0</strong>
                            <span>Total <br>Voitures</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 justify-content-center counter-wrap">
                    <div class="block-18">
                        <div class="text text-border d-flex align-items-center">
                            <strong class="number" data-number="2590">0</strong>
                            <span>Clients <br>Satisfaits</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 justify-content-center counter-wrap">
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
    <footer class="ftco-footer ftco-section" id="contact">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md">
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2"><img src="../../images/off_logo.png" alt="logo.png" style="height: 40px;"></h2>
                        <p>Autotech est conçu pour centraliser et simplifier l'expérience automobile dans un environnement digital de pointe, répondant à la demande croissante d'efficacité et de transparence.</p>
                        <ul class="ftco-footer-social list-unstyled mt-5">
                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
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
