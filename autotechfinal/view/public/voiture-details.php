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
                        url('../../images/bg_3.jpg');
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

        .vehicle-image-card {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
        }

        .vehicle-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            display: block;
        }

        .vehicle-title-section {
            padding: 2rem;
            text-align: center;
            background: linear-gradient(to bottom, var(--card-bg), rgba(30, 41, 59, 0.5));
        }

        .vehicle-brand {
            color: var(--primary-light);
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 0.5rem;
        }

        .vehicle-model {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .specs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .spec-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .spec-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .spec-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
        }

        .spec-card:hover::before {
            transform: scaleX(1);
        }

        .spec-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .spec-label {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .spec-value {
            color: var(--text-primary);
            font-size: 1.3rem;
            font-weight: 600;
        }

        .custom-tabs {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid var(--border-color);
        }

        .nav-pills {
            background: rgba(15, 23, 42, 0.5);
            padding: 0.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .nav-pills .nav-link {
            border-radius: 8px;
            color: var(--text-secondary);
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
        }

        .nav-pills .nav-link:hover:not(.active) {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-light);
        }

        .tab-content {
            color: var(--text-secondary);
            line-height: 1.8;
        }

        .tab-content h4 {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border-color);
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(51, 65, 85, 0.5);
        }

        .feature-list li:last-child {
            border-bottom: none;
        }

        .feature-list strong {
            color: var(--primary-light);
            display: inline-block;
            min-width: 150px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            padding: 0.75rem 1.5rem;
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

        .btn-success {
            background: linear-gradient(135deg, var(--success), #059669);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-secondary {
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            color: var(--text-secondary);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            border-color: var(--primary-color);
            color: var(--primary-light);
            background: rgba(37, 99, 235, 0.1);
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        .contact-info p {
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(51, 65, 85, 0.5);
        }

        .contact-info a {
            color: var(--primary-light);
            text-decoration: none;
            transition: color 0.3s;
        }

        .contact-info a:hover {
            color: var(--primary-color);
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

        .error-section {
            text-align: center;
            padding: 4rem 0;
        }

        .error-icon {
            font-size: 5rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }

            .vehicle-model {
                font-size: 1.8rem;
            }

            .specs-grid {
                grid-template-columns: 1fr;
            }

            .nav-pills .nav-link {
                padding: 0.75rem 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="../../images/off_logo.png" alt="logo.png" id="img_logo">
            </a>
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
                    <?php if ($userController->estConnecte()): ?>
                        <li class="nav-item"><a class="nav-link" href="../user/mes-boutiques.php">Mes Boutiques</a></li>
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

    <?php if (!$vehicule): ?>
        <section class="hero-section">
            <div class="container">
                <div class="breadcrumbs">
                    <a href="index.php">Accueil</a> <i class="fas fa-chevron-right"></i>
                    <span>Véhicule</span>
                </div>
                <h1 class="hero-title">Véhicule introuvable</h1>
            </div>
        </section>
        
        <section class="error-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <div class="error-icon">🚗</div>
                        <h2>Véhicule introuvable</h2>
                        <p class="lead">Désolé, ce véhicule n'existe plus ou l'identifiant est invalide.</p>
                        <a href="voitures.php" class="btn btn-primary btn-lg mt-4">Retour aux véhicules</a>
                    </div>
                </div>
            </div>
        </section>
    <?php else: ?>
        <section class="hero-section">
            <div class="container">
                <div class="breadcrumbs">
                    <a href="index.php">Accueil</a> <i class="fas fa-chevron-right"></i>
                    <a href="voitures.php">Voitures</a> <i class="fas fa-chevron-right"></i>
                    <span>Détails</span>
                </div>
                <h1 class="hero-title">Détails du Véhicule</h1>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <div class="vehicle-image-card">
                    <img src="<?= !empty($vehicule['image_principale']) ? "../../uploads/vehicule/" . htmlspecialchars($vehicule['image_principale']) : "../../images/car-1.jpg"; ?>" 
                         alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>" 
                         class="vehicle-image">
                    <div class="vehicle-title-section">
                        <div class="vehicle-brand"><?= htmlspecialchars($vehicule['marque']) ?></div>
                        <h2 class="vehicle-model"><?= htmlspecialchars($vehicule['modele']) ?></h2>
                    </div>
                </div>

                <div class="specs-grid">
                    <div class="spec-card">
                        <div class="spec-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <div class="spec-label">Kilométrage</div>
                        <div class="spec-value"><?= number_format((int)$vehicule['kilometrage'], 0, ',', ' ') ?> km</div>
                    </div>
                    <div class="spec-card">
                        <div class="spec-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="spec-label">Transmission</div>
                        <div class="spec-value"><?= htmlspecialchars($vehicule['transmission'] ?: 'Non spécifié') ?></div>
                    </div>
                    <div class="spec-card">
                        <div class="spec-icon">
                            <i class="fas fa-gas-pump"></i>
                        </div>
                        <div class="spec-label">Carburant</div>
                        <div class="spec-value"><?= htmlspecialchars($vehicule['carburant']) ?></div>
                    </div>
                    <div class="spec-card">
                        <div class="spec-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="spec-label">Année</div>
                        <div class="spec-value"><?= htmlspecialchars($vehicule['annee']) ?></div>
                    </div>
                </div>

                <div class="custom-tabs">
                    <ul class="nav nav-pills justify-content-center" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-description-tab" data-toggle="pill" href="#pills-description" role="tab">
                                <i class="fas fa-info-circle mr-2"></i> Description
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab">
                                <i class="fas fa-address-card mr-2"></i> Contact
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-4" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-description" role="tabpanel" aria-labelledby="pills-description-tab">
                            <?php if (!empty($vehicule['description'])): ?>
                                <p><?= nl2br(htmlspecialchars($vehicule['description'])) ?></p>
                            <?php else: ?>
                                <p>Aucune description disponible pour ce véhicule.</p>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <h4><i class="fas fa-list-ul mr-2"></i> Caractéristiques supplémentaires</h4>
                                <ul class="feature-list">
                                    <li><strong>Couleur:</strong> <?= htmlspecialchars($vehicule['couleur'] ?: 'Non spécifié') ?></li>
                                    <li><strong>Prix journalier:</strong> <?= !empty($vehicule['prix_journalier']) ? number_format($vehicule['prix_journalier'], 2) . ' DT' : 'Prix sur demande' ?></li>
                                    <li><strong>Référence:</strong> #<?= htmlspecialchars($vehicule['id_vehicule']) ?></li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4><i class="fas fa-user mr-2"></i> Informations du Propriétaire</h4>
                                    <div class="contact-info">
                                        <p><strong>Nom complet:</strong> <?= htmlspecialchars($vehicule['prenom'] . ' ' . $vehicule['nom']) ?></p>
                                        <p><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($vehicule['email']) ?>"><?= htmlspecialchars($vehicule['email']) ?></a></p>
                                        <?php if (!empty($vehicule['telephone'])): ?>
                                            <p><strong>Téléphone:</strong> <a href="tel:<?= htmlspecialchars($vehicule['telephone']) ?>"><?= htmlspecialchars($vehicule['telephone']) ?></a></p>
                                        <?php endif; ?>
                                        <?php if (!empty($vehicule['ville'])): ?>
                                            <p><strong>Ville:</strong> <?= htmlspecialchars($vehicule['ville']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h4><i class="fas fa-phone-square-alt mr-2"></i> Actions</h4>
                                    <a href="mailto:<?= htmlspecialchars($vehicule['email']) ?>" class="btn btn-primary btn-block mb-3">
                                        <i class="fas fa-envelope mr-2"></i> Contacter par Email
                                    </a>
                                    <?php if (!empty($vehicule['telephone'])): ?>
                                        <a href="tel:<?= htmlspecialchars($vehicule['telephone']) ?>" class="btn btn-success btn-block mb-3">
                                            <i class="fas fa-phone mr-2"></i> Appeler
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($userController->estConnecte() && $vehicule['id_utilisateur'] != $_SESSION['user_id']): ?>
                                        <a href="../user/prendre-reservation.php?id=<?= $vehicule['id_vehicule'] ?>" class="btn btn-warning btn-block mb-3">
                                            <i class="fas fa-calendar-check mr-2"></i> Réserver
                                        </a>
                                    <?php elseif (!$userController->estConnecte()): ?>
                                        <a href="../auth/login.php" class="btn btn-warning btn-block mb-3">
                                            <i class="fas fa-calendar-check mr-2"></i> Réserver
                                        </a>
                                    <?php endif; ?>
                                    <a href="voitures.php" class="btn btn-secondary btn-block">
                                        <i class="fas fa-arrow-left mr-2"></i> Retour aux voitures
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
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