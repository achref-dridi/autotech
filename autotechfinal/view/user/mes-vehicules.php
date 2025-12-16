<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../controller/VehiculeController.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$vehiculeController = new VehiculeController();
$mesVehicules = $vehiculeController->getVehiculesByUtilisateur($_SESSION['user_id']);

// Gestion suppression
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    if ($vehiculeController->estProprietaire($id, $_SESSION['user_id'])) {
        $vehiculeController->deleteVehicule($id);
        header('Location: mes-vehicules.php?success=supprime');
        exit();
    }
}

$message = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'supprime') {
        $message = 'Véhicule supprimé avec succès.';
    } elseif ($_GET['success'] === 'ajoute') {
        $message = 'Véhicule ajouté avec succès.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Véhicules - AutoTech</title>
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
            font-size: 0.9rem;
            border-radius: 6px;
        }

        .nav-link:hover, .nav-item.active .nav-link {
            color: var(--primary-light) !important;
            background: rgba(37, 99, 235, 0.1);
        }

        .hero-section {
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(30, 41, 59, 0.8)),
                        url('../../images/bg_2.jpg');
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

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.1));
            color: #10b981;
            border: none;
            border-left: 4px solid #10b981;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .section-header p {
            color: var(--text-muted);
            margin: 0;
        }

        .btn-add {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            color: white;
            text-decoration: none;
        }

        .empty-state {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 5rem 2rem;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .empty-state-icon {
            font-size: 5rem;
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

        .vehicle-card {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
            margin-bottom: 2rem;
        }

        .vehicle-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.3);
            border-color: var(--primary-color);
        }

        .vehicle-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }

        .vehicle-body {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .vehicle-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .vehicle-specs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .spec-badge {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-light);
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .vehicle-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .vehicle-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: auto;
        }

        .btn-view {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-light);
            border: 1px solid var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            flex: 1;
            text-align: center;
            text-decoration: none;
        }

        .btn-view:hover {
            background: var(--primary-color);
            color: white;
            text-decoration: none;
        }

        .btn-edit {
            background: rgba(251, 146, 60, 0.1);
            color: #fb923c;
            border: 1px solid #fb923c;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            flex: 1;
            text-align: center;
            text-decoration: none;
        }

        .btn-edit:hover {
            background: #fb923c;
            color: white;
            text-decoration: none;
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid #ef4444;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            flex: 1;
            text-align: center;
            text-decoration: none;
        }

        .btn-delete:hover {
            background: #ef4444;
            color: white;
            text-decoration: none;
        }

        footer {
            background: var(--dark-bg);
            padding: 3rem 0 1rem;
            margin-top: 4rem;
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

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .vehicle-image {
                height: 180px;
            }

            .vehicle-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../public/index.php">
                <img src="../../images/off_logo.png" alt="logo.png" id="img_logo">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="../public/index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/voitures.php">Voitures</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/boutiques.php">Boutiques</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/trajets.php">Trajets</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-boutiques.php">Mes Boutiques</a></li>
                    <li class="nav-item active"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-trajets.php">Mes Trajets</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="breadcrumbs">
                <a href="../public/index.php">Accueil</a> <i class="fas fa-chevron-right"></i>
                <span>Mes Véhicules</span>
            </div>
            <h1 class="hero-title">Gérez vos véhicules</h1>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="section-header">
                <div>
                    <h2><i class="fas fa-car mr-2"></i> Mes Véhicules</h2>
                    <p>Gérez vos véhicules disponibles à la location</p>
                </div>
                <a href="ajouter-vehicule.php" class="btn-add">
                    <i class="fas fa-plus-circle"></i> Ajouter un véhicule
                </a>
            </div>

            <?php if (empty($mesVehicules)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🚗</div>
                    <h2>Aucun véhicule</h2>
                    <p>Vous n'avez pas encore ajouté de véhicule.</p>
                    <a href="ajouter-vehicule.php" class="btn-add">
                        <i class="fas fa-plus-circle"></i> Ajouter mon premier véhicule
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($mesVehicules as $v): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="vehicle-card">
                                <img src="<?= 
                                    !empty($v['image_principale']) 
                                        ? '../../uploads/' . htmlspecialchars($v['image_principale']) 
                                        : '../../images/car-1.jpg' 
                                ?>" alt="<?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>" class="vehicle-image">
                                
                                <div class="vehicle-body">
                                    <h5 class="vehicle-title">
                                        <?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>
                                    </h5>
                                    
                                    <div class="vehicle-specs">
                                        <span class="spec-badge">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            <?= htmlspecialchars($v['annee']) ?>
                                        </span>
                                        <span class="spec-badge">
                                            <i class="fas fa-gas-pump mr-1"></i>
                                            <?= htmlspecialchars($v['carburant']) ?>
                                        </span>
                                        <span class="spec-badge">
                                            <i class="fas fa-road mr-1"></i>
                                            <?= number_format($v['kilometrage']) ?> km
                                        </span>
                                    </div>

                                    <?php if (!empty($v['prix_journalier'])): ?>
                                        <div class="vehicle-price">
                                            <?= number_format($v['prix_journalier'], 2) ?> DT <span style="font-size: 0.8rem; color: var(--text-muted);">/ jour</span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="vehicle-actions">
                                        <a href="../public/voiture-details.php?id=<?= $v['id_vehicule'] ?>" 
                                           class="btn-view">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                        <a href="modifier-vehicule.php?id=<?= $v['id_vehicule'] ?>" 
                                           class="btn-edit">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <a href="mes-vehicules.php?supprimer=<?= $v['id_vehicule'] ?>" 
                                           class="btn-delete"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule?')">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Section Rendez-Vous -->
            <div class="section-header" style="margin-top: 3rem;">
                <div>
                    <h2><i class="fas fa-calendar-check mr-2"></i> Rendez-Vous</h2>
                    <p>Gérez vos rendez-vous de maintenance et services</p>
                </div>
                <a href="prendre-rendez-vous.php" class="btn-add">
                    <i class="fas fa-plus-circle"></i> Prendre un rendez-vous
                </a>
            </div>

            <div class="row">
                <div class="col-12">
                    <a href="mes-rendez-vous.php" class="btn" style="background: rgba(37, 99, 235, 0.1); color: var(--primary-light); border: 1px solid var(--primary-color); padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: inline-block;">
                        <i class="fas fa-list mr-2"></i> Voir mes rendez-vous actifs
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2">
                            <img src="../../images/off_logo.png" alt="logo.png" style="height: 40px;">
                        </h2>
                        <p>Autotech est conçu pour centraliser et simplifier l'expérience automobile dans un environnement digital de pointe.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2">Vous avez des Questions?</h2>
                        <p><i class="fas fa-map-marker-alt mr-2"></i> Esprit, Ariana sogra, Ariana, Tunisie</p>
                        <p><i class="fas fa-phone mr-2"></i> <a href="tel:+21633856909">+216 33 856 909</a></p>
                        <p><i class="fas fa-envelope mr-2"></i> <a href="mailto:AutoTech@gmail.tn">AutoTech@gmail.tn</a></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>Copyright &copy; <script>document.write(new Date().getFullYear());</script> Tous droits réservés | AutoTech</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>