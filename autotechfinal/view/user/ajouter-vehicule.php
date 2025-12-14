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
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload image
    $imageName = null;
    if (isset($_FILES['image_principale']) && $_FILES['image_principale']['error'] === 0) {
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $extension = pathinfo($_FILES['image_principale']['name'], PATHINFO_EXTENSION);
        $imageName = 'vehicule_' . time() . '_' . uniqid() . '.' . $extension;
        move_uploaded_file($_FILES['image_principale']['tmp_name'], $uploadDir . $imageName);
    }
    
    try {
        $vehiculeController->createVehicule(
            $_SESSION['user_id'],
            $_POST['marque'],
            $_POST['modele'],
            $_POST['annee'],
            $_POST['carburant'],
            $_POST['kilometrage'],
            $_POST['couleur'] ?? '',
            $_POST['transmission'] ?? '',
            $_POST['prix_journalier'] ?? 0,
            $_POST['description'] ?? '',
            $imageName
        );
        
        header('Location: mes-vehicules.php?success=ajoute');
        exit();
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Véhicule - AutoTech</title>
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
        .form-container {
            max-width: 800px;
            margin: 40px auto;
        }
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-header h2 {
            font-weight: 700;
            color: #1a1a1a;
        }
        .form-label {
            font-weight: 600;
            color: #555;
        }
        .required-star {
            color: red;
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 40px;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
                    <li class="nav-item"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="form-container">
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <div class="form-header">
                <h2>🚗 Ajouter un Véhicule</h2>
                <p class="text-muted">Remplissez les informations de votre véhicule</p>
            </div>

            <form method="POST" enctype="multipart/form-data" onsubmit="return validerVehicule()">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="marque" class="form-label">Marque <span class="required-star">*</span></label>
                        <input type="text" class="form-control" id="marque" name="marque" placeholder="Ex: Toyota">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="modele" class="form-label">Modèle <span class="required-star">*</span></label>
                        <input type="text" class="form-control" id="modele" name="modele" placeholder="Ex: Corolla">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="annee" class="form-label">Année <span class="required-star">*</span></label>
                        <input type="number" class="form-control" id="annee" name="annee" placeholder="<?= date('Y') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="carburant" class="form-label">Carburant <span class="required-star">*</span></label>
                        <select class="form-select" id="carburant" name="carburant">
                            <option value="">Sélectionner...</option>
                            <option value="Essence">Essence</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Hybride">Hybride</option>
                            <option value="Électrique">Électrique</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="kilometrage" class="form-label">Kilométrage <span class="required-star">*</span></label>
                        <input type="number" class="form-control" id="kilometrage" name="kilometrage" placeholder="0">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="couleur" class="form-label">Couleur</label>
                        <input type="text" class="form-control" id="couleur" name="couleur" placeholder="Ex: Blanc">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="transmission" class="form-label">Transmission</label>
                        <select class="form-select" id="transmission" name="transmission">
                            <option value="">Sélectionner...</option>
                            <option value="Manuelle">Manuelle</option>
                            <option value="Automatique">Automatique</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="prix_journalier" class="form-label">Prix Journalier (DT)</label>
                    <input type="number" step="0.01" class="form-control" id="prix_journalier" name="prix_journalier" placeholder="0.00">
                    <small class="text-muted">Laissez vide si le prix est à négocier</small>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" 
                              placeholder="Décrivez votre véhicule, son état, ses équipements..."></textarea>
                </div>

                <div class="mb-3">
                    <label for="image_principale" class="form-label">Image Principale <span class="required-star">*</span></label>
                    <input type="file" class="form-control" id="image_principale" name="image_principale" accept="image/*">
                    <small class="text-muted">Formats acceptés: JPG, PNG, GIF (Max 5MB)</small>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-submit">Ajouter le Véhicule</button>
                    <a href="mes-vehicules.php" class="btn btn-secondary" style="width: 30%;">Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/validation.js"></script>
</body>
</html>
