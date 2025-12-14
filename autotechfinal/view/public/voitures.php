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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
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
        .nav-link:hover, .nav-link.active {
            color: #667eea !important;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        .page-header h1 {
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 10px;
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
            width: 100%;
            text-align: center;
        }
        .btn-view:hover {
            color: white;
            transform: translateY(-2px);
        }
        .owner-badge {
            font-size: 12px;
            color: #888;
            margin-top: 10px;
        }
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }
        .footer {
            background: #1a1a1a;
            color: white;
            padding: 40px 0;
            text-align: center;
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
                    <li class="nav-item"><a class="nav-link active" href="voitures.php">Voitures</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#about">À propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#contact">Contact</a></li>
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

    <!-- Page Header -->
    <div class="page-header">
        <div class="container text-center">
            <h1>Toutes nos Voitures</h1>
            <p class="lead">Découvrez tous les véhicules disponibles à la location</p>
        </div>
    </div>

    <!-- Vehicles Grid -->
    <div class="container">
        <?php if (!empty($vehicules)): ?>
            <div class="row">
                <?php foreach ($vehicules as $v): ?>
                    <div class="col-md-6 col-lg-4">
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
                                    <span class="spec-badge">⛽ <?= htmlspecialchars($v['carburant']) ?></span>
                                    <span class="spec-badge">🛣️ <?= number_format($v['kilometrage']) ?> km</span>
                                </div>

                                <?php if (!empty($v['prix_journalier'])): ?>
                                    <p class="fw-bold text-primary mb-2">
                                        <?= number_format($v['prix_journalier'], 2) ?> DT / jour
                                    </p>
                                <?php endif; ?>

                                <p class="owner-badge mb-3">
                                    👤 Propriétaire: <?= htmlspecialchars($v['prenom'] . ' ' . $v['nom']) ?>
                                </p>

                                <a href="voiture-details.php?id=<?= $v['id_vehicule'] ?>" class="btn-view">
                                    Voir les détails et contact
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 64px; margin-bottom: 20px;">🚗</div>
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

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-2">&copy; <?= date('Y') ?> AutoTech. Tous droits réservés.</p>
            <p class="mb-0">Email: AutoTech@gmail.tn | Téléphone: +216 33 856 909</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
