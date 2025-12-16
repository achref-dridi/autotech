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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --accent-orange: #f97316;
            --success: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1e293b 100%);
            color: var(--text-primary);
            min-height: 100vh;
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
            padding: 0.35rem 0.75rem !important;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .nav-link:hover, .nav-item.active .nav-link {
            color: var(--primary-light) !important;
            background: rgba(37, 99, 235, 0.1);
        }

        .hero-section {
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(30, 41, 59, 0.8)),
                        url('../../images/deal3.webp');
            background-size: cover;
            background-position: center;
            padding: 6rem 0 4rem;
            position: relative;
        }

        .breadcrumbs {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .breadcrumbs a {
            color: var(--primary-light);
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumbs a:hover {
            color: var(--primary-color);
        }

        .breadcrumbs i {
            font-size: 0.7rem;
            margin: 0 0.5rem;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .vehicles-section {
            padding: 4rem 0;
        }

        .vehicle-card {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .vehicle-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.3);
            border-color: var(--primary-color);
        }

        .vehicle-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
            position: relative;
        }

        .vehicle-image-container {
            position: relative;
            overflow: hidden;
            height: 250px;
            background-size: cover;
            background-position: center;
        }

        .vehicle-image-container::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to top, rgba(30, 41, 59, 0.9), transparent);
            pointer-events: none;
        }

        .vehicle-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .vehicle-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            text-decoration: none;
            display: block;
            transition: color 0.3s;
        }

        .vehicle-title:hover {
            color: var(--primary-light);
            text-decoration: none;
        }

        .vehicle-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .vehicle-year {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(37, 99, 235, 0.1);
            padding: 0.4rem 1rem;
            border-radius: 8px;
            color: var(--primary-light);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .vehicle-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .vehicle-price span {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        .btn-details {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-align: center;
            display: block;
            text-decoration: none;
            margin-top: auto;
        }

        .btn-details:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            color: white;
            text-decoration: none;
        }

        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--border-color);
        }

        .empty-icon {
            font-size: 5rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        .empty-state h2 {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        footer {
            background: var(--dark-bg);
            padding: 3rem 0 1rem;
            margin-top: 4rem;
            border-top: 1px solid var(--border-color);
        }

        .footer-heading {
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

        .social-icons a {
            display: inline-flex;
            width: 40px;
            height: 40px;
            background: var(--card-bg);
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
            color: var(--text-secondary);
        }

        .social-icons a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
            color: white;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }

            .vehicle-card {
                margin-bottom: 1.5rem;
            }

            .vehicle-price {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php"><img src="../../images/off_logo.png" alt="logo.png" id="img_logo"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item active"><a class="nav-link" href="voitures.php">Voitures</a></li>
                    <li class="nav-item"><a class="nav-link" href="boutiques.php">Boutiques</a></li>
                    <li class="nav-item"><a class="nav-link" href="trajets.php">Trajets</a></li>
                    <?php
                    require_once __DIR__ . '/../../controller/UtilisateurController.php';
                    $userController = new UtilisateurController();
                    if ($userController->estConnecte()): ?>
                        <li class="nav-item"><a class="nav-link" href="../user/mes-vehicules.php">Mes Véhicules</a></li>
                        <li class="nav-item"><a class="nav-link" href="../user/mes-trajets.php">Mes Trajets</a></li>
                        <li class="nav-item"><a class="nav-link" href="../user/profil.php">Mon Profil</a></li>
                        <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="../auth/login.php">Connexion</a></li>
                        <li class="nav-item"><a class="nav-link" href="../auth/signup.php">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="breadcrumbs">
                <a href="index.php">Accueil</a> <i class="fas fa-chevron-right"></i>
                <span>Voitures</span>
            </div>
            <h1 class="hero-title">Réserver votre voiture</h1>
        </div>
    </section>

    <section class="vehicles-section">
        <div class="container">
            <?php if (!empty($vehicules)): ?>
                <div class="row">
                    <?php foreach ($vehicules as $v): ?>
                        <div class="col-md-4">
                            <div class="vehicle-card">
                                <div class="vehicle-image-container" style="background-image: url('<?= 
                                    !empty($v['image_principale']) 
                                        ? '../../uploads/' . htmlspecialchars($v['image_principale']) 
                                        : '../../images/car-1.jpg' 
                                ?>');">
                                </div>
                                <div class="vehicle-content">
                                    <a href="voiture-details.php?id=<?= $v['id_vehicule'] ?>" class="vehicle-title">
                                        <?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>
                                    </a>
                                    <div class="vehicle-info">
                                        <span class="vehicle-year">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?= htmlspecialchars($v['annee']) ?>
                                        </span>
                                        <div class="vehicle-price">
                                            <?= !empty($v['prix_journalier']) ? number_format($v['prix_journalier'], 2) . ' DT' : 'Prix sur demande' ?>
                                            <?php if (!empty($v['prix_journalier'])): ?>
                                                <span>/jour</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <a href="voiture-details.php?id=<?= $v['id_vehicule'] ?>" class="btn-details">
                                        <i class="fas fa-info-circle mr-2"></i> Voir les détails
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h2>Aucun véhicule disponible</h2>
                    <p>Aucun véhicule n'est actuellement disponible sur la plateforme.</p>
                    <?php if ($userController->estConnecte()): ?>
                        <a href="../user/ajouter-vehicule.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus-circle mr-2"></i> Ajouter mon véhicule
                        </a>
                    <?php else: ?>
                        <a href="../auth/signup.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus mr-2"></i> S'inscrire pour ajouter un véhicule
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Section Mes Réservations -->
    <?php if ($userController->estConnecte()): ?>
    <section class="py-5" style="background: rgba(37, 99, 235, 0.05);">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2><i class="fas fa-calendar-check mr-2"></i> Mes Réservations</h2>
                    <p>Accédez à vos réservations de véhicules</p>
                </div>
                <a href="../user/mes-reservations.php" class="btn-add">
                    <i class="fas fa-list mr-1"></i> Voir mes réservations
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer>
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <h3 class="footer-heading"><img src="../../images/off_logo.png" alt="logo.png" style="height: 40px;"></h3>
                    <p>Autotech est conçu pour centraliser et simplifier l'expérience automobile dans un environnement digital de pointe, répondant à la demande croissante d'efficacité et de transparence.</p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h4 class="footer-heading">Informations</h4>
                    <ul>
                        <li><a href="#">À propos</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">Termes et Conditions</a></li>
                        <li><a href="#">Garantie du Meilleur Prix</a></li>
                        <li><a href="#">Politique de Confidentialité</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h4 class="footer-heading">Support Client</h4>
                    <ul>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Option de Paiement</a></li>
                        <li><a href="#">Conseils de Réservation</a></li>
                        <li><a href="#">Comment ça marche</a></li>
                        <li><a href="#">Nous Contacter</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h4 class="footer-heading">Vous avez des Questions?</h4>
                    <p><i class="fas fa-map-marker-alt mr-2"></i> Esprit, Ariana sogra, Ariana, Tunisie</p>
                    <p><i class="fas fa-phone mr-2"></i> <a href="tel:+21633856909">+216 33 856 909</a></p>
                    <p><i class="fas fa-envelope mr-2"></i> <a href="mailto:AutoTech@gmail.tn">AutoTech@gmail.tn</a></p>
                </div>
            </div>
            <div class="text-center pt-3" style="border-top: 1px solid var(--border-color);">
                <p>Copyright &copy; <script>document.write(new Date().getFullYear());</script> Tous droits réservés | AutoTech</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>