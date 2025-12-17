<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../controller/BoutiqueController.php';
require_once __DIR__ . '/../../controller/VehiculeController.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$boutiqueController = new BoutiqueController();
$vehiculeController = new VehiculeController();

$id_boutique = $_GET['id'] ?? null;
if (!$id_boutique || !is_numeric($id_boutique)) {
    header('Location: mes-boutiques.php');
    exit();
}

$boutique = $boutiqueController->getBoutiqueById($id_boutique);
if (!$boutique || $boutique['id_utilisateur'] != $_SESSION['user_id']) {
    header('Location: mes-boutiques.php');
    exit();
}

$vehicules = $vehiculeController->getVehiculesByBoutique($id_boutique);
$vehicleCount = $vehiculeController->countVehiculesByBoutique($id_boutique);

// Gestion suppression
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $id_vehicule = (int)$_GET['supprimer'];
    $vehicule = $vehiculeController->getVehiculeById($id_vehicule);
    
    if ($vehicule && $vehicule['id_utilisateur'] == $_SESSION['user_id'] && $vehicule['id_boutique'] == $id_boutique) {
        $vehiculeController->deleteVehicule($id_vehicule);
        header("Location: voitures-boutique.php?id=$id_boutique&success=supprime");
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
    <title><?= htmlspecialchars($boutique['nom_boutique']) ?> - Mes Véhicules</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --dark-bg: #0f172a;
            --card-bg: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --light-bg: #f8fafc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1e293b 100%);
            color: var(--text-primary);
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
            padding: 0.5rem 1rem !important;
            border-radius: 6px;
        }

        .nav-link:hover, .nav-item.active .nav-link {
            color: var(--primary-light) !important;
            background: rgba(37, 99, 235, 0.1);
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }

        .header {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            border: 1px solid rgba(37, 99, 235, 0.2);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-info h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: var(--text-primary);
        }

        .header-info p {
            margin: 0;
            color: var(--text-secondary);
        }
            color: #64748b;
            font-size: 14px;
        }

        .btn-ajouter {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-ajouter:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
            color: white;
            text-decoration: none;
        }

        .vehicules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .vehicule-card {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(37, 99, 235, 0.2);
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .vehicule-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.3);
            border-color: var(--primary-color);
        }

        .vehicule-image {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            overflow: hidden;
        }

        .vehicule-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .vehicule-content {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .vehicule-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .vehicule-info {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .vehicule-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-light);
            margin: 0.75rem 0;
        }

        .vehicule-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid rgba(37, 99, 235, 0.2);
        }

        .btn-action {
            flex: 1;
            padding: 0.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-edit {
            background: rgba(251, 146, 60, 0.2);
            color: #fb923c;
            border: 1px solid rgba(251, 146, 60, 0.3);
        }

        .btn-edit:hover {
            background: rgba(251, 146, 60, 0.3);
            color: #fb923c;
            text-decoration: none;
            border-color: #fb923c;
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.3);
            color: #ef4444;
            text-decoration: none;
            border-color: #ef4444;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid rgba(37, 99, 235, 0.2);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .empty-state p {
            font-size: 1rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 25px;
        }

        .breadcrumb {
            background: white;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        @media (max-width: 768px) {
            .vehicules-grid {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../public/index.php"><img src="../../images/off_logo.png" alt="logo.png" id="img_logo"></a>
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

    <div class="container">
        <div class="breadcrumb">
            <a href="mes-boutiques.php"><i class="fas fa-store"></i> Mes Boutiques</a> /
            <span><?= htmlspecialchars($boutique['nom_boutique']) ?></span>
        </div>

        <div class="header">
            <div class="header-content">
                <div class="header-info">
                    <h1><?= htmlspecialchars($boutique['nom_boutique']) ?></h1>
                    <p><i class="fas fa-car"></i> <?= $vehicleCount['total'] ?? 0 ?> véhicule(s)</p>
                </div>
                <a href="ajouter-vehicule-boutique.php?id_boutique=<?= $id_boutique ?>" class="btn-ajouter">
                    <i class="fas fa-plus"></i> Ajouter un véhicule
                </a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($vehicules)): ?>
            <div class="empty-state">
                <i class="fas fa-car"></i>
                <h3 style="color: #334155; margin: 15px 0;">Aucun véhicule ajouté</h3>
                <p>Commencez par ajouter un véhicule à cette boutique.</p>
                <a href="ajouter-vehicule-boutique.php?id_boutique=<?= $id_boutique ?>" class="btn-ajouter">
                    <i class="fas fa-plus"></i> Ajouter un véhicule
                </a>
            </div>
        <?php else: ?>
            <div class="vehicules-grid">
                <?php foreach ($vehicules as $vehicule): ?>
                    <div class="vehicule-card">
                        <div class="vehicule-image">
                            <?php if ($vehicule['image_principale']): ?>
                                <img src="../../uploads/vehicule/<?= htmlspecialchars($vehicule['image_principale']) ?>" alt="<?= htmlspecialchars($vehicule['marque']) ?>">
                            <?php else: ?>
                                <i class="fas fa-car"></i>
                            <?php endif; ?>
                        </div>
                        <div class="vehicule-content">
                            <div class="vehicule-title"><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></div>
                            <div class="vehicule-info">
                                <i class="fas fa-calendar"></i> <?= $vehicule['annee'] ?>
                            </div>
                            <div class="vehicule-info">
                                <i class="fas fa-gas-pump"></i> <?= htmlspecialchars($vehicule['carburant']) ?>
                            </div>
                            <div class="vehicule-info">
                                <i class="fas fa-tachometer-alt"></i> <?= number_format($vehicule['kilometrage'], 0, ',', ' ') ?> km
                            </div>
                            <?php if ($vehicule['prix_journalier']): ?>
                                <div class="vehicule-price"><?= number_format($vehicule['prix_journalier'], 2, ',', ' ') ?> DT/jour</div>
                            <?php endif; ?>
                            <div class="vehicule-actions">
                                <a href="modifier-vehicule.php?id=<?= $vehicule['id_vehicule'] ?>" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <a href="voitures-boutique.php?id=<?= $id_boutique ?>&supprimer=<?= $vehicule['id_vehicule'] ?>" 
                                   class="btn-action btn-delete" 
                                   onclick="return confirm('Êtes-vous sûr ?');">
                                    <i class="fas fa-trash"></i> Supprimer
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
