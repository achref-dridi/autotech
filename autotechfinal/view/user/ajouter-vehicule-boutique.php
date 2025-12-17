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
$message = '';
$messageType = '';

$id_boutique = $_GET['id_boutique'] ?? null;
if (!$id_boutique || !is_numeric($id_boutique)) {
    header('Location: mes-boutiques.php');
    exit();
}

$boutique = $boutiqueController->getBoutiqueById($id_boutique);
if (!$boutique || $boutique['id_utilisateur'] != $_SESSION['user_id']) {
    header('Location: mes-boutiques.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload image
    $imageName = null;
    if (isset($_FILES['image_principale']) && $_FILES['image_principale']['error'] === 0) {
        $uploadDir = __DIR__ . '/../../uploads/vehicule/';
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
            $imageName,
            $id_boutique
        );
        
        header("Location: voitures-boutique.php?id=$id_boutique&success=ajoute");
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
    <title>Ajouter un Véhicule - <?= htmlspecialchars($boutique['nom_boutique']) ?></title>
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
            transition: color 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover, .nav-item.active .nav-link {
            color: var(--primary-light) !important;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        .breadcrumb-nav {
            background: transparent;
            padding: 15px 0;
            margin-bottom: 30px;
            color: var(--text-secondary);
        }

        .breadcrumb-nav a {
            color: var(--primary-light);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-nav a:hover {
            text-decoration: underline;
        }

        .form-container {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
        }

        .form-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--text-primary);
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-light);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group label {
            color: var(--text-secondary);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 12px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .form-control:focus {
            background: rgba(30, 41, 59, 0.8);
            border-color: var(--primary-light);
            color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .image-upload-box {
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: rgba(30, 41, 59, 0.3);
        }

        .image-upload-box:hover {
            border-color: var(--primary-light);
            background: rgba(59, 130, 246, 0.1);
        }

        .image-upload-box i {
            font-size: 40px;
            color: var(--primary-light);
            margin-bottom: 15px;
        }

        .image-upload-box p {
            color: var(--text-secondary);
            margin: 0;
        }

        .image-preview {
            max-width: 200px;
            margin: 20px auto;
            border-radius: 8px;
            overflow: hidden;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            display: block;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px;
            margin-bottom: 30px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #6ee7b7;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
        }

        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            flex: 1;
        }

        .btn-secondary:hover {
            background: rgba(59, 130, 246, 0.1);
            border-color: var(--primary-light);
            color: var(--primary-light);
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-container {
                padding: 20px;
            }

            .form-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../public/index.php">
                <img src="../../images/off_logo.png" alt="logo" id="img_logo">
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
                    <li class="nav-item"><a class="nav-link" href="mes-boutiques.php">Mes Boutiques</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="breadcrumb-nav">
            <a href="mes-boutiques.php"><i class="fas fa-store"></i> Mes Boutiques</a> >
            <a href="voitures-boutique.php?id=<?= $id_boutique ?>"><?= htmlspecialchars($boutique['nom_boutique']) ?></a> >
            <span>Ajouter un Véhicule</span>
        </div>

        <div class="form-container">
            <h1 class="form-title">
                <i class="fas fa-car-plus"></i> Ajouter un Véhicule à <?= htmlspecialchars($boutique['nom_boutique']) ?>
            </h1>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>" role="alert">
                    <i class="fas fa-<?= $messageType === 'danger' ? 'exclamation-circle' : 'check-circle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <!-- Section Information Générale -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-info-circle"></i> Informations Générales
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="marque">Marque *</label>
                            <input type="text" class="form-control" id="marque" name="marque" 
                                   placeholder="Ex: Toyota" required>
                        </div>
                        <div class="form-group">
                            <label for="modele">Modèle *</label>
                            <input type="text" class="form-control" id="modele" name="modele" 
                                   placeholder="Ex: Corolla" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="annee">Année *</label>
                            <input type="number" class="form-control" id="annee" name="annee" 
                                   placeholder="2023" min="1990" max="2099" required>
                        </div>
                        <div class="form-group">
                            <label for="couleur">Couleur</label>
                            <input type="text" class="form-control" id="couleur" name="couleur" 
                                   placeholder="Ex: Blanc">
                        </div>
                    </div>
                </div>

                <!-- Section Spécifications -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-wrench"></i> Spécifications Techniques
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="carburant">Carburant *</label>
                            <select class="form-control" id="carburant" name="carburant" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="Essence">Essence</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Hybride">Hybride</option>
                                <option value="Électrique">Électrique</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="transmission">Transmission</label>
                            <select class="form-control" id="transmission" name="transmission">
                                <option value="">-- Sélectionner --</option>
                                <option value="Manuelle">Manuelle</option>
                                <option value="Automatique">Automatique</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="kilometrage">Kilométrage *</label>
                            <input type="number" class="form-control" id="kilometrage" name="kilometrage" 
                                   placeholder="Ex: 50000" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="prix_journalier">Prix journalier (DT)</label>
                            <input type="number" class="form-control" id="prix_journalier" name="prix_journalier" 
                                   placeholder="Ex: 45.99" step="0.01" min="0">
                        </div>
                    </div>
                </div>

                <!-- Section Description -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-align-left"></i> Description
                    </h3>
                    <div class="form-group">
                        <label for="description">Description du véhicule</label>
                        <textarea class="form-control" id="description" name="description" 
                                  placeholder="Décrivez l'état, les équipements, les accessoires..."></textarea>
                    </div>
                </div>

                <!-- Section Image -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-image"></i> Photo Principale
                    </h3>
                    <div class="image-upload-box" onclick="document.getElementById('image_principale').click();">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Cliquez pour télécharger une image</p>
                        <small style="color: var(--text-muted);">PNG, JPG ou GIF (max 5MB)</small>
                    </div>
                    <input type="file" id="image_principale" name="image_principale" accept="image/*" 
                           style="display: none;" onchange="previewImage(this)">
                    <div id="imagePreview"></div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Ajouter le Véhicule
                    </button>
                    <a href="voitures-boutique.php?id=<?= $id_boutique ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<div class="image-preview"><img src="' + e.target.result + '" alt="preview"></div>';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
