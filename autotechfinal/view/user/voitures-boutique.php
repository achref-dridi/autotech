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
            --light-bg: #f8fafc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: #334155;
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }

        .header {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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
        }

        .header-info p {
            margin: 0;
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
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .vehicule-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        }

        .vehicule-image {
            width: 100%;
            height: 200px;
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
            padding: 20px;
        }

        .vehicule-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .vehicule-info {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 3px;
        }

        .vehicule-price {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 10px 0;
        }

        .vehicule-actions {
            display: flex;
            gap: 8px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .btn-action {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
        }

        .btn-edit:hover {
            background: #d97706;
            color: white;
            text-decoration: none;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: white;
            text-decoration: none;
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
                                <img src="/autotechfinal/images/<?= htmlspecialchars($vehicule['image_principale']) ?>" alt="<?= htmlspecialchars($vehicule['marque']) ?>">
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
                                <div class="vehicule-price"><?= number_format($vehicule['prix_journalier'], 2, ',', ' ') ?> €/jour</div>
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
