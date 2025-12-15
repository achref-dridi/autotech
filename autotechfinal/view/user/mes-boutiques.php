<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../controller/BoutiqueController.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$boutiqueController = new BoutiqueController();
$mesBoutiques = $boutiqueController->getBoutiquesByUser($_SESSION['user_id']);

if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $boutique = $boutiqueController->getBoutiqueById($id);
    
    if ($boutique && $boutique['id_utilisateur'] == $_SESSION['user_id']) {
        $result = $boutiqueController->deleteBoutique($id, $_SESSION['user_id']);
        header('Location: mes-boutiques.php?success=supprimee');
        exit();
    }
}

$message = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'supprimee') {
        $message = 'Boutique supprimée avec succès.';
    } elseif ($_GET['success'] === 'ajoutee') {
        $message = 'Boutique ajoutée avec succès.';
    } elseif ($_GET['success'] === 'modifiee') {
        $message = 'Boutique modifiée avec succès.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Boutiques - AutoTech</title>
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

        /* Alert Messages */
        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.1));
            color: #10b981;
            border: none;
            border-left: 4px solid #10b981;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Section Header */
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

        /* Empty State */
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

        .empty-state h3 {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .boutique-card {
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
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .boutique-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .boutique-info {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .boutique-info i {
            color: var(--primary-light);
            width: 20px;
        }

        .boutique-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .btn-action {
            flex: 1;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            border: none;
        }

        .btn-voitures {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-light);
            border: 1px solid var(--primary-color);
        }

        .btn-voitures:hover {
            background: var(--primary-color);
            color: white;
            text-decoration: none;
        }

        .btn-modifier {
            background: rgba(251, 146, 60, 0.1);
            color: #fb923c;
            border: 1px solid #fb923c;
        }

        .btn-modifier:hover {
            background: #fb923c;
            color: white;
            text-decoration: none;
        }

        .btn-supprimer {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid #ef4444;
        }

        .btn-supprimer:hover {
            background: #ef4444;
            color: white;
            text-decoration: none;
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
            .hero-title {
                font-size: 2rem;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .boutique-logo {
                height: 180px;
            }

            .boutique-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
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
                    <li class="nav-item active"><a class="nav-link" href="mes-boutiques.php">Mes Boutiques</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero-section">
        <div class="container">
            <div class="breadcrumbs">
                <a href="../public/index.php">Accueil</a> <i class="fas fa-chevron-right"></i>
                <span>Mes Boutiques</span>
            </div>
            <h1 class="hero-title">Gérez vos boutiques</h1>
        </div>
    </section>

    <!-- CONTENT -->
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
                    <h2><i class="fas fa-store mr-2"></i> Mes Boutiques</h2>
                    <p>Gérez vos boutiques et leurs véhicules</p>
                </div>
                <a href="ajouter-boutique.php" class="btn-add">
                    <i class="fas fa-plus-circle"></i> Ajouter une boutique
                </a>
            </div>

            <?php if (empty($mesBoutiques)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🏪</div>
                    <h3>Aucune boutique créée</h3>
                    <p>Créez votre première boutique pour commencer à ajouter des véhicules dans un espace dédié.</p>
                    <a href="ajouter-boutique.php" class="btn-add">
                        <i class="fas fa-plus-circle"></i> Créer une boutique
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($mesBoutiques as $boutique): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="boutique-card">
                                <div class="boutique-logo">
                                    <?php if ($boutique['logo']): ?>
<img src="../../uploads/logos/<?= htmlspecialchars($boutique['logo']) ?>" 
     alt="<?= htmlspecialchars($boutique['nom_boutique']) ?>">

                                    <?php else: ?>
                                        <i class="fas fa-store"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="boutique-content">
                                    <div class="boutique-title">
                                        <?= htmlspecialchars($boutique['nom_boutique']) ?>
                                    </div>
                                    <div class="boutique-info">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= htmlspecialchars($boutique['adresse']) ?>
                                    </div>
                                    <div class="boutique-info">
                                        <i class="fas fa-phone"></i>
                                        <?= htmlspecialchars($boutique['telephone']) ?>
                                    </div>
                                    <div class="boutique-actions">
                                        <a href="voitures-boutique.php?id=<?= $boutique['id_boutique'] ?>" 
                                           class="btn-action btn-voitures">
                                            <i class="fas fa-car"></i> Voitures
                                        </a>
                                        <a href="modifier-boutique.php?id=<?= $boutique['id_boutique'] ?>" 
                                           class="btn-action btn-modifier">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <a href="mes-boutiques.php?supprimer=<?= $boutique['id_boutique'] ?>" 
                                           class="btn-action btn-supprimer" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette boutique ?');">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

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