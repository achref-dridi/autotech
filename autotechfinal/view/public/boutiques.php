<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/BoutiqueController.php';
require_once __DIR__ . '/../../controller/VehiculeController.php';

$boutiqueController = new BoutiqueController();
$vehiculeController = new VehiculeController();

$boutiques = $boutiqueController->getAllBoutiques();
$id_boutique = $_GET['id'] ?? null;
$vehiculesBoutique = null;
$boutiqueDtails = null;

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
            --light-bg: #f8fafc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: #334155;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 15px 0;
        }

        .navbar-brand img {
            height: 45px;
        }

        .nav-link {
            color: #64748b !important;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .hero-section p {
            font-size: 16px;
            opacity: 0.9;
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }

        .section-title {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 40px;
        }

        .boutiques-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .boutique-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .boutique-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        .boutique-logo {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            overflow: hidden;
        }

        .boutique-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .boutique-content {
            padding: 20px;
        }

        .boutique-name {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .boutique-info {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .boutique-info i {
            color: var(--primary-color);
            width: 16px;
        }

        .btn-visit {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 15px;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.2s;
        }

        .btn-visit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
            color: white;
            text-decoration: none;
        }

        .boutique-details {
            background: white;
            border-radius: 12px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.2s;
        }

        .back-button:hover {
            color: var(--primary-dark);
            text-decoration: none;
            transform: translateX(-5px);
        }

        .vehicules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .vehicule-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .vehicule-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        .vehicule-image {
            width: 100%;
            height: 150px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            overflow: hidden;
        }

        .vehicule-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .vehicule-info {
            padding: 15px;
        }

        .vehicule-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .vehicule-detail {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 3px;
        }

        .vehicule-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-color);
            margin-top: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
        }

        .empty-state i {
            font-size: 48px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .empty-state p {
            font-size: 16px;
            color: #94a3b8;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .boutiques-grid {
                grid-template-columns: 1fr;
            }

            .vehicules-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .hero-section h1 {
                font-size: 24px;
            }

            .section-title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="../../images/off_logo.png" alt="AutoTech">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="voitures.php">Voitures</a></li>
                    <li class="nav-item"><a class="nav-link" href="boutiques.php">Boutiques</a></li>
                    <li class="nav-item"><a class="nav-link" href="../user/mes-vehicules.php">Mon Espace</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <h1>Nos Boutiques</h1>
        <p>Explorez les boutiques partenaires et leurs véhicules disponibles</p>
    </div>

    <div class="container">
        <?php if ($vehiculesBoutique !== null): ?>
            <!-- Détails Boutique -->
            <div class="boutique-details">
                <a href="boutiques.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Retour aux boutiques
                </a>

                <div style="display: flex; gap: 30px; align-items: flex-start; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <div style="width: 250px; height: 250px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 60px; overflow: hidden;">
                            <?php if ($boutiqueDetails['logo']): ?>
                                <img src="/autotechfinal/uploads/logos/<?= htmlspecialchars($boutiqueDetails['logo']) ?>" alt="logo" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <i class="fas fa-store"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 250px;">
                        <h2 style="font-size: 32px; font-weight: 700; color: #0f172a; margin-bottom: 20px;">
                            <?= htmlspecialchars($boutiqueDetails['nom_boutique']) ?>
                        </h2>
                        <div style="font-size: 16px; color: #64748b; line-height: 1.8;">
                            <p><i class="fas fa-map-marker-alt" style="color: var(--primary-color); width: 20px;"></i> <?= htmlspecialchars($boutiqueDetails['adresse']) ?></p>
                            <p><i class="fas fa-phone" style="color: var(--primary-color); width: 20px;"></i> <?= htmlspecialchars($boutiqueDetails['telephone']) ?></p>
                            <p><i class="fas fa-user" style="color: var(--primary-color); width: 20px;"></i> Propriétaire: <?= htmlspecialchars($boutiqueDetails['proprietaire'] . ' ' . ($boutiqueDetails['prenom'] ?? '')) ?></p>
                        </div>
                    </div>
                </div>

                <?php if (empty($vehiculesBoutique)): ?>
                    <div class="empty-state" style="margin-top: 40px;">
                        <i class="fas fa-car"></i>
                        <h3 style="color: #334155; margin: 15px 0;">Aucun véhicule disponible</h3>
                        <p>Cette boutique n'a pas encore ajouté de véhicules.</p>
                    </div>
                <?php else: ?>
                    <h3 style="margin-top: 40px; margin-bottom: 20px; color: #0f172a; font-weight: 700;">
                        <i class="fas fa-car"></i> Véhicules disponibles (<?= count($vehiculesBoutique) ?>)
                    </h3>
                    <div class="vehicules-grid">
                        <?php foreach ($vehiculesBoutique as $vehicule): ?>
                            <a href="voiture-details.php?id=<?= $vehicule['id_vehicule'] ?>" style="text-decoration: none; color: inherit;">
                                <div class="vehicule-card">
                                    <div class="vehicule-image">
                                        <?php if ($vehicule['image_principale']): ?>
                                            <img src="/autotechfinal/images/<?= htmlspecialchars($vehicule['image_principale']) ?>" alt="<?= htmlspecialchars($vehicule['marque']) ?>">
                                        <?php else: ?>
                                            <i class="fas fa-car"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="vehicule-info">
                                        <div class="vehicule-title"><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></div>
                                        <div class="vehicule-detail"><i class="fas fa-calendar"></i> <?= $vehicule['annee'] ?></div>
                                        <div class="vehicule-detail"><i class="fas fa-gas-pump"></i> <?= htmlspecialchars($vehicule['carburant']) ?></div>
                                        <div class="vehicule-detail"><i class="fas fa-tachometer-alt"></i> <?= number_format($vehicule['kilometrage'], 0, ',', ' ') ?> km</div>
                                        <?php if ($vehicule['prix_journalier']): ?>
                                            <div class="vehicule-price"><?= number_format($vehicule['prix_journalier'], 2, ',', ' ') ?> €/jour</div>
                                        <?php endif; ?>
                                    </div>
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
                    <h3 style="color: #334155; margin: 15px 0;">Aucune boutique disponible</h3>
                    <p>Les boutiques seront bientôt disponibles.</p>
                </div>
            <?php else: ?>
                <div class="boutiques-grid">
                    <?php foreach ($boutiques as $boutique): ?>
                        <div class="boutique-card" onclick="window.location.href='boutiques.php?id=<?= $boutique['id_boutique'] ?>'">
                            <div class="boutique-logo">
                                <?php if ($boutique['logo']): ?>
                                    <img src="/autotechfinal/uploads/logos/<?= htmlspecialchars($boutique['logo']) ?>" alt="<?= htmlspecialchars($boutique['nom_boutique']) ?>">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
