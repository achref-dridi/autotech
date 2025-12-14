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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 800;
            color: #667eea !important;
            font-size: 24px;
        }
        .nav-link {
            color: #555 !important;
            font-weight: 500;
            margin: 0 10px;
        }
        .nav-link:hover {
            color: #667eea !important;
        }
        .breadcrumb {
            background: transparent;
            padding: 20px 0;
        }
        .vehicle-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .vehicle-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        .vehicle-title {
            font-size: 32px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        .price-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 24px;
            font-weight: 700;
            display: inline-block;
            margin: 20px 0;
        }
        .spec-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .spec-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        .spec-label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            font-weight: 600;
        }
        .spec-value {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-top: 5px;
        }
        .owner-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            position: sticky;
            top: 100px;
        }
        .owner-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .owner-info {
            margin: 15px 0;
        }
        .owner-info-label {
            font-size: 14px;
            color: #888;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .owner-info-value {
            font-size: 16px;
            color: #1a1a1a;
            font-weight: 500;
        }
        .owner-info-value a {
            color: #667eea;
            text-decoration: none;
        }
        .owner-info-value a:hover {
            text-decoration: underline;
        }
        .contact-btn {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            font-size: 16px;
            margin-top: 10px;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .btn-email {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-email:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-phone {
            background: #28a745;
            color: white;
        }
        .btn-phone:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }
        .description-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-top: 30px;
        }
        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 20px;
        }
        .error-container {
            background: white;
            border-radius: 15px;
            padding: 60px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .footer {
            background: #1a1a1a;
            color: white;
            padding: 40px 0;
            margin-top: 60px;
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="voitures.php">Voitures</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">À propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
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

    <div class="container my-5">
        <?php if (!$vehicule): ?>
            <!-- Error State -->
            <div class="error-container">
                <h2>🚗 Véhicule introuvable</h2>
                <p>Désolé, ce véhicule n'existe plus ou l'identifiant est invalide.</p>
                <a href="voitures.php" class="btn btn-primary btn-lg">Retour aux véhicules</a>
            </div>
        <?php else: ?>
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="voitures.php">Voitures</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></li>
                </ol>
            </nav>

            <div class="row">
                <!-- Left Column - Vehicle Details -->
                <div class="col-lg-8">
                    <!-- Vehicle Image -->
                    <div class="vehicle-card">
                        <img src="<?=
                            !empty($vehicule['image_principale'])
                                ? "../../uploads/" . htmlspecialchars($vehicule['image_principale'])
                                : "../../assets/images/car-1.jpg";
                        ?>" alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>" class="vehicle-image">
                    </div>

                    <!-- Vehicle Information -->
                    <div class="vehicle-card">
                        <h1 class="vehicle-title">
                            <?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>
                        </h1>
                        <p class="text-muted">Année <?= htmlspecialchars($vehicule['annee']) ?></p>

                        <?php if (!empty($vehicule['prix_journalier'])): ?>
                            <div class="price-badge">
                                <?= number_format($vehicule['prix_journalier'], 2) ?> DT / jour
                            </div>
                        <?php endif; ?>

                        <h3 class="section-title mt-4">Caractéristiques techniques</h3>
                        <div class="spec-grid">
                            <div class="spec-item">
                                <div class="spec-label">Année</div>
                                <div class="spec-value"><?= htmlspecialchars($vehicule['annee']) ?></div>
                            </div>
                            <div class="spec-item">
                                <div class="spec-label">Kilométrage</div>
                                <div class="spec-value"><?= number_format((int)$vehicule['kilometrage'], 0, ',', ' ') ?> km</div>
                            </div>
                            <div class="spec-item">
                                <div class="spec-label">Carburant</div>
                                <div class="spec-value"><?= htmlspecialchars($vehicule['carburant']) ?></div>
                            </div>
                            <div class="spec-item">
                                <div class="spec-label">Transmission</div>
                                <div class="spec-value"><?= htmlspecialchars($vehicule['transmission'] ?: 'Non spécifié') ?></div>
                            </div>
                            <div class="spec-item">
                                <div class="spec-label">Couleur</div>
                                <div class="spec-value"><?= htmlspecialchars($vehicule['couleur'] ?: 'Non spécifié') ?></div>
                            </div>
                            <div class="spec-item">
                                <div class="spec-label">Référence</div>
                                <div class="spec-value">#<?= htmlspecialchars($vehicule['id_vehicule']) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <?php if (!empty($vehicule['description'])): ?>
                        <div class="description-section">
                            <h3 class="section-title">Description</h3>
                            <p><?= nl2br(htmlspecialchars($vehicule['description'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column - Owner Contact -->
                <div class="col-lg-4">
                    <div class="owner-card">
                        <div class="owner-header">
                            <h3 class="mb-0">📞 Contact du Propriétaire</h3>
                        </div>

                        <div class="owner-info">
                            <div class="owner-info-label">Nom complet</div>
                            <div class="owner-info-value">
                                <?= htmlspecialchars($vehicule['prenom'] . ' ' . $vehicule['nom']) ?>
                            </div>
                        </div>

                        <div class="owner-info">
                            <div class="owner-info-label">Email</div>
                            <div class="owner-info-value">
                                <a href="mailto:<?= htmlspecialchars($vehicule['email']) ?>">
                                    <?= htmlspecialchars($vehicule['email']) ?>
                                </a>
                            </div>
                        </div>

                        <?php if (!empty($vehicule['telephone'])): ?>
                            <div class="owner-info">
                                <div class="owner-info-label">Téléphone</div>
                                <div class="owner-info-value">
                                    <a href="tel:<?= htmlspecialchars($vehicule['telephone']) ?>">
                                        <?= htmlspecialchars($vehicule['telephone']) ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($vehicule['ville'])): ?>
                            <div class="owner-info">
                                <div class="owner-info-label">Ville</div>
                                <div class="owner-info-value">
                                    <?= htmlspecialchars($vehicule['ville']) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <hr class="my-4">

                        <a href="mailto:<?= htmlspecialchars($vehicule['email']) ?>" class="contact-btn btn-email">
                            ✉️ Contacter par Email
                        </a>

                        <?php if (!empty($vehicule['telephone'])): ?>
                            <a href="tel:<?= htmlspecialchars($vehicule['telephone']) ?>" class="contact-btn btn-phone">
                                📞 Appeler
                            </a>
                        <?php endif; ?>

                        <a href="voitures.php" class="contact-btn btn-secondary mt-3">
                            ← Retour aux voitures
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> AutoTech. Tous droits réservés.</p>
            <p class="mb-0">Email: AutoTech@gmail.tn | Téléphone: +216 33 856 909</p>
            <p class="mb-0">Adresse: Esprit, Ariana Sogra, Ariana, Tunisie</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
