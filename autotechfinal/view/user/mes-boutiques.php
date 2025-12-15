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

// Gestion suppression
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
            --light-bg: #f8fafc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: #334155;
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--dark-bg);
            margin: 0;
        }

        .btn-ajouter {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-ajouter:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
            color: white;
            text-decoration: none;
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 30px;
        }

        .boutique-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .boutique-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
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
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .boutique-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--dark-bg);
            margin-bottom: 10px;
            line-height: 1.4;
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

        .boutique-actions {
            display: flex;
            gap: 10px;
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .btn-action {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-modifier {
            background-color: #f59e0b;
            color: white;
        }

        .btn-modifier:hover {
            background-color: #d97706;
            color: white;
            text-decoration: none;
        }

        .btn-supprimer {
            background-color: #ef4444;
            color: white;
        }

        .btn-supprimer:hover {
            background-color: #dc2626;
            color: white;
            text-decoration: none;
        }

        .btn-voitures {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-voitures:hover {
            background-color: var(--primary-dark);
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

        .navbar-top {
            background: white;
            padding: 15px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .nav-links a {
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .nav-links a.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 5px;
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .boutique-grid {
                grid-template-columns: 1fr;
            }

            .nav-links {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar-top">
        <div class="container">
            <div class="nav-links">
                <a href="mes-vehicules.php"><i class="fas fa-car"></i> Mes Véhicules</a>
                <a href="mes-boutiques.php" class="active"><i class="fas fa-store"></i> Mes Boutiques</a>
                <a href="profil.php"><i class="fas fa-user"></i> Profil</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-store" style="margin-right: 10px;"></i>Mes Boutiques</h1>
            <a href="ajouter-boutique.php" class="btn-ajouter">
                <i class="fas fa-plus"></i> Ajouter une boutique
            </a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($mesBoutiques)): ?>
            <div class="empty-state">
                <i class="fas fa-store"></i>
                <h3 style="color: #334155; margin: 15px 0;">Aucune boutique créée</h3>
                <p>Créez votre première boutique pour commencer à ajouter des véhicules dans un espace dédié.</p>
                <a href="ajouter-boutique.php" class="btn-ajouter">
                    <i class="fas fa-plus"></i> Créer une boutique
                </a>
            </div>
        <?php else: ?>
            <div class="boutique-grid">
                <?php foreach ($mesBoutiques as $boutique): ?>
                    <div class="boutique-card">
                        <div class="boutique-logo">
                            <?php if ($boutique['logo']): ?>
                                <img src="/autotechfinal/uploads/logos/<?= htmlspecialchars($boutique['logo']) ?>" alt="<?= htmlspecialchars($boutique['nom_boutique']) ?>">
                            <?php else: ?>
                                <i class="fas fa-store"></i>
                            <?php endif; ?>
                        </div>
                        <div class="boutique-content">
                            <div class="boutique-title"><?= htmlspecialchars($boutique['nom_boutique']) ?></div>
                            <div class="boutique-info">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($boutique['adresse']) ?>
                            </div>
                            <div class="boutique-info">
                                <i class="fas fa-phone"></i>
                                <?= htmlspecialchars($boutique['telephone']) ?>
                            </div>
                            <div class="boutique-actions">
                                <a href="voitures-boutique.php?id=<?= $boutique['id_boutique'] ?>" class="btn-action btn-voitures">
                                    <i class="fas fa-car"></i> Voitures
                                </a>
                                <a href="modifier-boutique.php?id=<?= $boutique['id_boutique'] ?>" class="btn-action btn-modifier">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <a href="mes-boutiques.php?supprimer=<?= $boutique['id_boutique'] ?>" class="btn-action btn-supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette boutique ?');">
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
