<?php
require_once __DIR__ . '/../../controller/VehiculeController.php';

$vehiculeController = new VehiculeController();

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            color: #1a1a1a;
            line-height: 1.6;
        }

        /* Header Navigation */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #667eea;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }

        .nav-links a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #667eea;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 30px;
            font-size: 14px;
            color: #888;
        }

        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Error State */
        .error-container {
            background: white;
            border-radius: 16px;
            padding: 60px;
            text-align: center;
            box-shadow: 0 2px 20px rgba(0,0,0,0.05);
        }

        .error-container h2 {
            font-size: 28px;
            margin-bottom: 16px;
            color: #1a1a1a;
        }

        .error-container p {
            color: #666;
            margin-bottom: 30px;
        }

        /* Main Content Grid */
        .vehicle-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        /* Image Gallery */
        .image-section {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.05);
        }

        .main-image {
            width: 100%;
            height: 400px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .thumbnail {
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.3s;
        }

        .thumbnail:hover {
            opacity: 1;
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Vehicle Info */
        .info-section {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.05);
        }

        .vehicle-title {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 10px;
            color: #1a1a1a;
        }

        .vehicle-subtitle {
            font-size: 16px;
            color: #888;
            margin-bottom: 30px;
        }

        .price-tag {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #0F5ED4 100%);
            color: white;
            font-size: 28px;
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        /* Specs Grid */
        .specs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .spec-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: #0F5ED4;
            border-radius: 10px;
        }

        .spec-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #0F5ED4 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .spec-content {
            flex: 1;
        }

        .spec-label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            font-weight: 600;
        }

        .spec-value {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 16px 32px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #0F5ED4 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #f8f9fa;
        }

        /* Features Section */
        .features-section {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.05);
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1a1a1a;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature-card {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            text-align: center;
        }

        .feature-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .feature-title {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            background: #1a1a1a;
            color: white;
            padding: 40px 20px;
            text-align: center;
            margin-top: 60px;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .vehicle-grid {
                grid-template-columns: 1fr;
            }

            .specs-grid {
                grid-template-columns: 1fr;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .action-buttons {
                flex-direction: column;
            }

            .nav-links {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .features-grid {
                grid-template-columns: 1fr;
            }

            .vehicle-title {
                font-size: 24px;
            }

            .info-section {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <a href="index.php" class="logo">AutoTech</a>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="car.php">Voitures</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
        </div>
    </header>

    <div class="container">
        <?php if (!$vehicule): ?>
            <!-- Error State -->
            <div class="error-container">
                <h2>🚗 Véhicule introuvable</h2>
                <p>Désolé, ce véhicule n'existe plus ou l'identifiant est invalide.</p>
                <a href="car.php" class="btn btn-primary">Retour aux véhicules</a>
            </div>
        <?php else: ?>
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="index.php">Accueil</a>
                <span>/</span>
                <a href="car.php">Voitures</a>
                <span>/</span>
                <span><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></span>
            </div>

            <!-- Main Grid -->
            <div class="vehicle-grid">
                <!-- Image Section -->
                <div class="image-section">
                    <div class="main-image">
                        <img src="<?=
                            !empty($vehicule['image_principale'])
                                ? "../../uploads/" . htmlspecialchars($vehicule['image_principale'])
                                : "images/car-1.jpg";
                        ?>" alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>">
                    </div>
                    <!-- Placeholder pour miniatures si tu as d'autres images -->
                </div>

                <!-- Info Section -->
                <div class="info-section">
                    <h1 class="vehicle-title">
                        <?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>
                    </h1>
                    <p class="vehicle-subtitle">Année <?= htmlspecialchars($vehicule['annee']) ?></p>

                    <!-- Specs Grid -->
                    <div class="specs-grid">
                        <div class="spec-item">
                            <div class="spec-icon">📅</div>
                            <div class="spec-content">
                                <div class="spec-label">Année</div>
                                <div class="spec-value"><?= htmlspecialchars($vehicule['annee']) ?></div>
                            </div>
                        </div>

                        <div class="spec-item">
                            <div class="spec-icon">🛣️</div>
                            <div class="spec-content">
                                <div class="spec-label">Kilométrage</div>
                                <div class="spec-value"><?= number_format((int)$vehicule['kilometrage'], 0, ',', ' ') ?> km</div>
                            </div>
                        </div>

                        <div class="spec-item">
                            <div class="spec-icon">⛽</div>
                            <div class="spec-content">
                                <div class="spec-label">Carburant</div>
                                <div class="spec-value"><?= htmlspecialchars($vehicule['carburant']) ?></div>
                            </div>
                        </div>

                        <div class="spec-item">
                            <div class="spec-icon">⚙️</div>
                            <div class="spec-content">
                                <div class="spec-label">Transmission</div>
                                <div class="spec-value"><?= htmlspecialchars($vehicule['transmission'] ?: 'Non spécifié') ?></div>
                            </div>
                        </div>

                        <div class="spec-item">
                            <div class="spec-icon">🎨</div>
                            <div class="spec-content">
                                <div class="spec-label">Couleur</div>
                                <div class="spec-value"><?= htmlspecialchars($vehicule['couleur'] ?: 'Non spécifié') ?></div>
                            </div>
                        </div>

                        <div class="spec-item">
                            <div class="spec-icon">🔑</div>
                            <div class="spec-content">
                                <div class="spec-label">Référence</div>
                                <div class="spec-value">#<?= htmlspecialchars($vehicule['id_vehicule']) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="#=<?= htmlspecialchars($vehicule['id_vehicule']) ?>" class="btn btn-primary">
                            📅 Réserver maintenant
                        </a>
                        <a href="car.php" class="btn btn-secondary">
                            ← Retour
                        </a>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="features-section">
                <h2 class="section-title">✨ Caractéristiques principales</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🛡️</div>
                        <div class="feature-title">Assurance incluse</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🔧</div>
                        <div class="feature-title">Entretien régulier</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📱</div>
                        <div class="feature-title">Support 24/7</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">✅</div>
                        <div class="feature-title">Véhicule vérifié</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🎯</div>
                        <div class="feature-title">GPS intégré</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💳</div>
                        <div class="feature-title">Paiement flexible</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> AutoTech. Tous droits réservés.</p>
    </footer>
</body>
</html>