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
        }
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .vehicle-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        .vehicle-card:hover {
            transform: translateY(-5px);
        }
        .vehicle-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .vehicle-body {
            padding: 20px;
        }
        .vehicle-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        .vehicle-specs {
            display: flex;
            gap: 15px;
            margin: 15px 0;
            flex-wrap: wrap;
        }
        .spec-badge {
            background: #f0f0f0;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            color: #555;
        }
        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 10px;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="../public/index.php">🚗 AutoTech</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../public/index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/voitures.php">Voitures</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link active" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show mt-4">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">Mes Véhicules</h1>
                    <p class="text-muted mb-0">Gérez vos véhicules disponibles à la location</p>
                </div>
                <a href="ajouter-vehicule.php" class="btn btn-add">
                    + Ajouter un véhicule
                </a>
            </div>
        </div>

        <?php if (empty($mesVehicules)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🚗</div>
                <h2>Aucun véhicule</h2>
                <p class="text-muted">Vous n'avez pas encore ajouté de véhicule.</p>
                <a href="ajouter-vehicule.php" class="btn btn-add mt-3">Ajouter mon premier véhicule</a>
            </div>
        <?php else: ?>
            <div class="row g-4 mb-5">
                <?php foreach ($mesVehicules as $v): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="vehicle-card">
                            <img src="<?= 
                                !empty($v['image_principale']) 
                                    ? '../../uploads/' . htmlspecialchars($v['image_principale']) 
                                    : '../../assets/images/car-1.jpg' 
                            ?>" alt="<?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>" class="vehicle-image">
                            
                            <div class="vehicle-body">
                                <h5 class="vehicle-title">
                                    <?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>
                                </h5>
                                
                                <div class="vehicle-specs">
                                    <span class="spec-badge">📅 <?= htmlspecialchars($v['annee']) ?></span>
                                    <span class="spec-badge">⛽ <?= htmlspecialchars($v['carburant']) ?></span>
                                    <span class="spec-badge">🛣️ <?= number_format($v['kilometrage']) ?> km</span>
                                </div>

                                <?php if (!empty($v['prix_journalier'])): ?>
                                    <p class="fw-bold text-primary mb-3">
                                        <?= number_format($v['prix_journalier'], 2) ?> DT / jour
                                    </p>
                                <?php endif; ?>

                                <div class="d-flex gap-2">
                                    <a href="../public/voiture-details.php?id=<?= $v['id_vehicule'] ?>" 
                                       class="btn btn-sm btn-outline-primary flex-fill">
                                        Voir
                                    </a>
                                    <a href="modifier-vehicule.php?id=<?= $v['id_vehicule'] ?>" 
                                       class="btn btn-sm btn-warning flex-fill">
                                        Modifier
                                    </a>
                                    <a href="mes-vehicules.php?supprimer=<?= $v['id_vehicule'] ?>" 
                                       class="btn btn-sm btn-danger flex-fill"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule?')">
                                        Supprimer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
