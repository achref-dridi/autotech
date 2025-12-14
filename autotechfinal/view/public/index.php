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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 800;
            color: #667eea !important;
            font-size: 28px;
        }
        .nav-link {
            color: #555 !important;
            font-weight: 500;
            margin: 0 10px;
        }
        .nav-link:hover {
            color: #667eea !important;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
        }
        .hero-section p {
            font-size: 20px;
            max-width: 700px;
            margin: 0 auto;
        }
        .section-title {
            text-align: center;
            margin: 60px 0 40px;
        }
        .section-title h2 {
            font-size: 36px;
            font-weight: 700;
            color: #1a1a1a;
        }
        .section-title p {
            color: #888;
            font-size: 18px;
        }
        .car-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 30px;
        }
        .car-card:hover {
            transform: translateY(-10px);
        }
        .car-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .car-body {
            padding: 20px;
        }
        .car-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        .car-specs {
            display: flex;
            gap: 10px;
            margin: 15px 0;
            flex-wrap: wrap;
        }
        .spec-badge {
            background: #f0f0f0;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
        }
        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }
        .btn-view:hover {
            color: white;
            transform: translateY(-2px);
        }
        .about-section {
            background: #f8f9fa;
            padding: 60px 0;
        }
        .footer {
            background: #1a1a1a;
            color: white;
            padding: 40px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">🚗 AutoTech</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="voitures.php">Voitures</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">À propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
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

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1>AutoTech</h1>
            <p>Connectez-vous, publiez des annonces, réservez des véhicules et partagez vos expériences. Une plateforme sécurisée pour les particuliers et les boutiques partenaires.</p>
        </div>
    </div>

    <!-- Featured Vehicles -->
    <div class="container">
        <div class="section-title">
            <h2>Véhicules en Vedette</h2>
            <p>Découvrez notre sélection de véhicules disponibles</p>
        </div>

        <div class="row">
            <?php if (!empty($vehiculesVedette)): ?>
                <?php foreach ($vehiculesVedette as $v): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="car-card">
                            <img src="<?= 
                                !empty($v['image_principale']) 
                                    ? '../../uploads/' . htmlspecialchars($v['image_principale']) 
                                    : '../../assets/images/car-1.jpg' 
                            ?>" alt="<?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>" class="car-image">
                            
                            <div class="car-body">
                                <h5 class="car-title">
                                    <?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>
                                </h5>
                                
                                <div class="car-specs">
                                    <span class="spec-badge">📅 <?= htmlspecialchars($v['annee']) ?></span>
                                    <span class="spec-badge">🛣️ <?= number_format($v['kilometrage']) ?> km</span>
                                </div>

                                <?php if (!empty($v['prix_journalier'])): ?>
                                    <p class="fw-bold text-primary mb-3">
                                        <?= number_format($v['prix_journalier'], 2) ?> DT / jour
                                    </p>
                                <?php endif; ?>

                                <a href="voiture-details.php?id=<?= $v['id_vehicule'] ?>" class="btn-view w-100 text-center">
                                    Voir les détails
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Aucun véhicule disponible pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center my-5">
            <a href="voitures.php" class="btn btn-lg btn-primary">Voir tous les véhicules</a>
        </div>
    </div>

    <!-- About Section -->
    <div class="about-section" id="about">
        <div class="container">
            <div class="section-title">
                <h2>À propos d'AutoTech</h2>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p style="font-size: 16px; line-height: 1.8;">
                        Notre plateforme automobile a été développée avec un objectif clair : révolutionner la manière dont les utilisateurs interagissent avec le marché automobile. Que ce soit pour acheter, louer ou publier une offre, tout devient plus intuitif et accessible.
                    </p>
                    <p style="font-size: 16px; line-height: 1.8;">
                        Nous réunissons sur un même espace :<br>
                        ✔️ des particuliers souhaitant vendre ou louer leurs véhicules,<br>
                        ✔️ des boutiques partenaires désirant gérer leurs annonces en ligne,<br>
                        ✔️ des conducteurs qui proposent des services de déplacement.<br>
                    </p>
                </div>
                <div class="col-md-6 text-center">
                    <h3 style="font-size: 64px;">🚗</h3>
                    <p class="lead">Une plateforme sécurisée et moderne</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div class="container my-5" id="services">
        <div class="section-title">
            <h2>Nos Services</h2>
        </div>
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div style="font-size: 48px; margin-bottom: 15px;">🚙</div>
                <h5>Location de Voitures</h5>
                <p class="text-muted">Large choix de véhicules</p>
            </div>
            <div class="col-md-3 mb-4">
                <div style="font-size: 48px; margin-bottom: 15px;">✅</div>
                <h5>Véhicules Vérifiés</h5>
                <p class="text-muted">Contrôle qualité garantie</p>
            </div>
            <div class="col-md-3 mb-4">
                <div style="font-size: 48px; margin-bottom: 15px;">📱</div>
                <h5>Support 24/7</h5>
                <p class="text-muted">Assistance disponible</p>
            </div>
            <div class="col-md-3 mb-4">
                <div style="font-size: 48px; margin-bottom: 15px;">💳</div>
                <h5>Paiement Sécurisé</h5>
                <p class="text-muted">Transactions protégées</p>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="about-section" id="contact">
        <div class="container">
            <div class="section-title">
                <h2>Contactez-nous</h2>
            </div>
            <div class="row text-center">
                <div class="col-md-4">
                    <div style="font-size: 32px; margin-bottom: 10px;">📍</div>
                    <h5>Adresse</h5>
                    <p>Esprit, Ariana Sogra<br>Ariana, Tunisie</p>
                </div>
                <div class="col-md-4">
                    <div style="font-size: 32px; margin-bottom: 10px;">📞</div>
                    <h5>Téléphone</h5>
                    <p>+216 33 856 909</p>
                </div>
                <div class="col-md-4">
                    <div style="font-size: 32px; margin-bottom: 10px;">✉️</div>
                    <h5>Email</h5>
                    <p>AutoTech@gmail.tn</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-2">&copy; <?= date('Y') ?> AutoTech. Tous droits réservés.</p>
            <p class="mb-0">Plateforme de location de véhicules en Tunisie</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
