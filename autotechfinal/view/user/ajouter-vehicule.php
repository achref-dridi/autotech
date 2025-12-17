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
            padding: 0.5rem 1rem !important;
            border-radius: 6px;
        }

        .nav-link:hover, .nav-item.active .nav-link {
            color: var(--primary-light) !important;
            background: rgba(37, 99, 235, 0.1);
        }

        .hero-section {
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(30, 41, 59, 0.8)),
                        url('../../images/bg_3.jpg');
            background-size: cover;
            background-position: center;
            padding: 4rem 0 3rem;
            position: relative;
        }

        .hero-section .breadcrumbs {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .hero-section .breadcrumbs a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .hero-section .breadcrumbs a:hover {
            color: var(--primary-light);
        }

        .hero-section h1 {
            color: var(--text-primary);
            font-size: 2.5rem;
            font-weight: 700;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.1));
            color: #10b981;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.1));
            color: #ef4444;
            border-left: 4px solid #ef4444;
        }

        .form-section {
            padding: 3rem 0;
        }

        .form-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .form-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.15);
        }

        .form-card .text-center h2 {
            color: var(--text-primary);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .form-card .text-center p {
            color: var(--text-muted);
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .form-label {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-label span {
            color: #ef4444;
        }

        .form-control, .form-control:focus {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.7);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        select.form-control {
            cursor: pointer;
        }

        select.form-control option {
            background: var(--dark-bg);
            color: var(--text-primary);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        input[type="file"].form-control {
            padding: 0.5rem;
        }

        input[type="file"].form-control::file-selector-button {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            margin-right: 1rem;
            transition: all 0.3s ease;
        }

        input[type="file"].form-control::file-selector-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        }

        small.text-muted {
            color: var(--text-muted) !important;
            font-size: 0.85rem;
            display: block;
            margin-top: 0.25rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            color: white;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: rgba(148, 163, 184, 0.2);
            border: 1px solid var(--border-color);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            color: var(--text-secondary);
        }

        .btn-secondary:hover {
            background: rgba(148, 163, 184, 0.3);
            border-color: var(--text-secondary);
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        .btn-block {
            width: 100%;
        }

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

        @media (max-width: 768px) {
            .hero-section {
                padding: 3rem 0 2rem;
            }

            .hero-section h1 {
                font-size: 1.8rem;
            }

            .form-card {
                padding: 2rem 1.5rem;
            }

            .form-card .text-center h2 {
                font-size: 1.5rem;
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
                    <li class="nav-item"><a class="nav-link" href="mes-boutiques.php">Mes Boutiques</a></li>
                    <li class="nav-item active"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <p class="breadcrumbs">
                        <span class="mr-2"><a href="../public/index.php">Accueil <i class="fas fa-chevron-right"></i></a></span> 
                        <span class="mr-2"><a href="mes-vehicules.php">Mes Véhicules <i class="fas fa-chevron-right"></i></a></span> 
                        <span>Ajouter un véhicule</span>
                    </p>
                    <h1>Ajouter un véhicule</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="form-section">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="form-card">
                        <div class="text-center mb-4">
                            <h2>Ajouter un Véhicule</h2>
                            <p>Remplissez les informations de votre véhicule</p>
                        </div>

                        <form method="POST" enctype="multipart/form-data" onsubmit="return validerVehicule()">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-car mr-1"></i> Marque <span>*</span>
                                    </label>
                                    <input type="text" class="form-control" id="marque" name="marque" placeholder="Ex: Toyota" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-tag mr-1"></i> Modèle <span>*</span>
                                    </label>
                                    <input type="text" class="form-control" id="modele" name="modele" placeholder="Ex: Corolla" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-calendar mr-1"></i> Année <span>*</span>
                                    </label>
                                    <input type="number" class="form-control" id="annee" name="annee" placeholder="<?= date('Y') ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-gas-pump mr-1"></i> Carburant <span>*</span>
                                    </label>
                                    <select class="form-control" id="carburant" name="carburant" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="Essence">Essence</option>
                                        <option value="Diesel">Diesel</option>
                                        <option value="Hybride">Hybride</option>
                                        <option value="Électrique">Électrique</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-tachometer-alt mr-1"></i> Kilométrage <span>*</span>
                                    </label>
                                    <input type="number" class="form-control" id="kilometrage" name="kilometrage" placeholder="0" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-palette mr-1"></i> Couleur
                                    </label>
                                    <input type="text" class="form-control" id="couleur" name="couleur" placeholder="Ex: Blanc">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-cog mr-1"></i> Transmission
                                    </label>
                                    <select class="form-control" id="transmission" name="transmission">
                                        <option value="">Sélectionner...</option>
                                        <option value="Manuelle">Manuelle</option>
                                        <option value="Automatique">Automatique</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-coins mr-1"></i> Prix Journalier (DT)
                                </label>
                                <input type="number" step="0.01" class="form-control" id="prix_journalier" name="prix_journalier" placeholder="0.00">
                                <small class="text-muted">Laissez vide si le prix est à négocier</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-align-left mr-1"></i> Description
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Décrivez votre véhicule, son état, ses équipements..."></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-image mr-1"></i> Image Principale <span>*</span>
                                </label>
                                <input type="file" class="form-control" id="image_principale" name="image_principale" accept="image/*">
                                <small class="text-muted">Formats acceptés: JPG, PNG, GIF (Max 5MB)</small>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-8 mb-3">
                                    <button type="submit" class="btn btn-primary btn-block py-3">
                                        <i class="fas fa-check mr-2"></i>Ajouter le Véhicule
                                    </button>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="mes-vehicules.php" class="btn btn-secondary btn-block py-3">
                                        <i class="fas fa-times mr-2"></i>Annuler
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
    <script src="../../assets/js/validation.js"></script>
</body>
</html>