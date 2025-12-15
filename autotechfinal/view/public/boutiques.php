<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/BoutiqueController.php';
require_once __DIR__ . '/../../controller/VehiculeController.php';

$boutiqueController = new BoutiqueController();
$vehiculeController = new VehiculeController();

$boutiques = $boutiqueController->getAllBoutiques();
$id_boutique = $_GET['id'] ?? null;
$vehiculesBoutique = null;
$boutiqueDetails = null;

if ($id_boutique && is_numeric($id_boutique)) {
    $boutiqueDetails = $boutiqueController->getBoutiqueById($id_boutique);
    if ($boutiqueDetails) {
        $vehiculesBoutique = $vehiculeController->getVehiculesByBoutique($id_boutique);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Boutiques - AutoTech</title>
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

        /* Navigation */
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

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(30, 41, 59, 0.8)),
                        linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 6rem 0 4rem;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-section p {
            font-size: 1.1rem;
            color: var(--text-secondary);
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 20px;
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 3rem;
        }

        .section-title i {
            color: var(--primary-light);
            margin-right: 0.5rem;
        }

        /* Back Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-light);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: rgba(37, 99, 235, 0.1);
        }

        .back-button:hover {
            color: white;
            background: var(--primary-color);
            text-decoration: none;
            transform: translateX(-5px);
        }

        /* Boutique Details */
        .boutique-details {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 3rem;
            margin-bottom: 3rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
        }

        .boutique-header {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .boutique-logo-large {
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
        }

        .boutique-logo-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .boutique-header-info {
            flex: 1;
            min-width: 300px;
        }

        .boutique-header-info h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .boutique-info-item {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .boutique-info-item i {
            color: var(--primary-light);
            width: 24px;
            font-size: 1.1rem;
        }

        .vehicles-section-title {
            margin-top: 3rem;
            margin-bottom: 2rem;
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.5rem;
        }

        .vehicles-section-title i {
            color: var(--primary-light);
            margin-right: 0.5rem;
        }

        /* Boutiques Grid */
        .boutiques-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .boutique-card {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid var(--border-color);
        }

        .boutique-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.3);
            border-color: var(--primary-color);
        }

        .boutique-logo {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
            overflow: hidden;
        }

        .boutique-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .boutique-content {
            padding: 1.5rem;
        }

        .boutique-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .boutique-info {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .boutique-info i {
            color: var(--primary-light);
            width: 20px;
        }

        .btn-visit {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            margin-top: 1rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        .btn-visit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            color: white;
            text-decoration: none;
        }

        /* Vehicules Grid */
        .vehicules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .vehicule-card {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .vehicule-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.2);
            border-color: var(--primary-color);
            text-decoration: none;
        }

        .vehicule-image {
            width: 100%;
            height: 160px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            overflow: hidden;
        }

        .vehicule-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .vehicule-info {
            padding: 1rem;
        }

        .vehicule-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
        }

        .vehicule-detail {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .vehicule-detail i {
            color: var(--primary-light);
            width: 16px;
        }

        .vehicule-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-light);
            margin-top: 0.75rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--border-color);
        }

        .empty-state i {
            font-size: 5rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        .empty-state h3 {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .empty-state p {
            font-size: 1.1rem;
            color: var(--text-muted);
        }

        /* Footer */
        footer {
            background: rgba(15, 23, 42, 0.95);
            color: var(--text-secondary);
            padding: 3rem 0 1rem;
            margin-top: 4rem;
            border-top: 1px solid var(--border-color);
        }

        footer h2 {
            color: var(--text-primary);
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        footer img {
            height: 40px;
            filter: brightness(1.1);
        }

        footer p, footer li {
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.8;
        }

        footer a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--primary-light);
        }

        footer ul {
            list-style: none;
            padding: 0;
        }

        footer .icon {
            color: var(--primary-light);
            margin-right: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }

            .boutiques-grid {
                grid-template-columns: 1fr;
            }

            .vehicules-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }

            .boutique-details {
                padding: 2rem 1.5rem;
            }

            .boutique-header {
                flex-direction: column;
            }

            .boutique-logo-large {
                width: 100%;
                height: 200px;
            }

            .section-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
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
                    <li class="nav-item"><a class="nav-link" href="voitures.php">Voitures</a></li>
                    <li class="nav-item active"><a class="nav-link" href="boutiques.php">Boutiques</a></li>
                    <?php
                    require_once __DIR__ . '/../../controller/UtilisateurController.php';
                    $userController = new UtilisateurController();
                    if ($userController->estConnecte()): ?>
                        <li class="nav-item"><a class="nav-link" href="../user/mes-boutiques.php">Mes Boutiques</a></li>
                        <li class="nav-item"><a class="nav-link" href="../user/mes-vehicules.php">Mes Véhicules</a></li>
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

    <!-- HERO -->
    <div class="hero-section">
        <h1><i class="fas fa-store mr-2"></i>Nos Boutiques</h1>
        <p>Explorez les boutiques partenaires et leurs véhicules disponibles</p>
    </div>

    <div class="container">
        <?php if ($vehiculesBoutique !== null): ?>
            <!-- Détails Boutique -->
            <div class="boutique-details">
                <a href="boutiques.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Retour aux boutiques
                </a>

                <div class="boutique-header">
                    <div class="boutique-logo-large">
                        <?php if ($boutiqueDetails['logo']): ?>
                            <img src="../../uploads/logos/<?= htmlspecialchars($boutiqueDetails['logo']) ?>" alt="logo">
                        <?php else: ?>
                            <i class="fas fa-store"></i>
                        <?php endif; ?>
                    </div>
                    <div class="boutique-header-info">
                        <h2><?= htmlspecialchars($boutiqueDetails['nom_boutique']) ?></h2>
                        <div class="boutique-info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars($boutiqueDetails['adresse']) ?></span>
                        </div>
                        <div class="boutique-info-item">
                            <i class="fas fa-phone"></i>
                            <span><?= htmlspecialchars($boutiqueDetails['telephone']) ?></span>
                        </div>
                        <div class="boutique-info-item">
                            <i class="fas fa-user"></i>
                            <span>Propriétaire: <?= htmlspecialchars($boutiqueDetails['proprietaire'] . ' ' . ($boutiqueDetails['prenom'] ?? '')) ?></span>
                        </div>
                    </div>
                </div>

                <?php if (empty($vehiculesBoutique)): ?>
                    <div class="empty-state">
                        <i class="fas fa-car"></i>
                        <h3>Aucun véhicule disponible</h3>
                        <p>Cette boutique n'a pas encore ajouté de véhicules.</p>
                    </div>
                <?php else: ?>
                    <h3 class="vehicles-section-title">
                        <i class="fas fa-car"></i> Véhicules disponibles (<?= count($vehiculesBoutique) ?>)
                    </h3>
                    <div class="vehicules-grid">
                        <?php foreach ($vehiculesBoutique as $vehicule): ?>
                            <a href="voiture-details.php?id=<?= $vehicule['id_vehicule'] ?>" class="vehicule-card">
                                <div class="vehicule-image">
                                    <?php if ($vehicule['image_principale']): ?>
                                        <img src="../../uploads/<?= htmlspecialchars($vehicule['image_principale']) ?>" alt="<?= htmlspecialchars($vehicule['marque']) ?>">
                                    <?php else: ?>
                                        <i class="fas fa-car"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="vehicule-info">
                                    <div class="vehicule-title"><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></div>
                                    <div class="vehicule-detail">
                                        <i class="fas fa-calendar"></i>
                                        <?= $vehicule['annee'] ?>
                                    </div>
                                    <div class="vehicule-detail">
                                        <i class="fas fa-gas-pump"></i>
                                        <?= htmlspecialchars($vehicule['carburant']) ?>
                                    </div>
                                    <div class="vehicule-detail">
                                        <i class="fas fa-tachometer-alt"></i>
                                        <?= number_format($vehicule['kilometrage'], 0, ',', ' ') ?> km
                                    </div>
                                    <?php if ($vehicule['prix_journalier']): ?>
                                        <div class="vehicule-price"><?= number_format($vehicule['prix_journalier'], 2) ?> DT/jour</div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- Liste des Boutiques -->
            <h2 class="section-title">
                <i class="fas fa-store"></i> Toutes les Boutiques
            </h2>

            <?php if (empty($boutiques)): ?>
                <div class="empty-state">
                    <i class="fas fa-store"></i>
                    <h3>Aucune boutique disponible</h3>
                    <p>Les boutiques seront bientôt disponibles.</p>
                </div>
            <?php else: ?>
                <div class="boutiques-grid">
                    <?php foreach ($boutiques as $boutique): ?>
                        <div class="boutique-card" onclick="window.location.href='boutiques.php?id=<?= $boutique['id_boutique'] ?>'">
                            <div class="boutique-logo">
                                <?php if ($boutique['logo']): ?>
                                  <img src="../../uploads/logos/<?= htmlspecialchars($boutique['logo']) ?>" alt="<?= htmlspecialchars($boutique['nom_boutique']) ?>">
                                 <?php else: ?>
                                    <i class="fas fa-store"></i>
                                <?php endif; ?>
                            </div>
                            <div class="boutique-content">
                                <div class="boutique-name"><?= htmlspecialchars($boutique['nom_boutique']) ?></div>
                                <div class="boutique-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($boutique['adresse']) ?>
                                </div>
                                <div class="boutique-info">
                                    <i class="fas fa-phone"></i>
                                    <?= htmlspecialchars($boutique['telephone']) ?>
                                </div>
                                <div class="boutique-info">
                                    <i class="fas fa-user"></i>
                                    <?= htmlspecialchars($boutique['proprietaire'] ?? 'AutoTech') ?>
                                </div>
                                <button class="btn-visit" onclick="event.stopPropagation(); window.location.href='boutiques.php?id=<?= $boutique['id_boutique'] ?>'">
                                    <i class="fas fa-eye"></i> Voir les véhicules
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row mb-5">
                <div class="col-md">
                    <div class="mb-4">
                        <h2><a href="#"><img src="../../images/off_logo.png" alt="logo.png" id="img_logo"></a></h2>
                        <p>Autotech est conçu pour centraliser et simplifier l'expérience automobile dans un environnement digital de pointe.</p>
                    </div>
                </div>
                <div class="col-md">
                    <div class="mb-4">
                        <h2>Vous avez des Questions?</h2>
                        <div class="mb-3">
                            <ul>
                                <li><span class="icon"><i class="fas fa-map-marker-alt"></i></span><span>Esprit, Ariana sogra, Ariana, Tunisie</span></li>
                                <li><a href="#"><span class="icon"><i class="fas fa-phone"></i></span><span>+216 33 856 909</span></a></li>
                                <li><a href="#"><span class="icon"><i class="fas fa-envelope"></i></span><span>AutoTech@gmail.tn</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> Tous droits réservés | AutoTech</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>