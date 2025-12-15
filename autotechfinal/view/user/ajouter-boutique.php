<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../controller/BoutiqueController.php';
require_once __DIR__ . '/../../model/Boutique.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$boutiqueController = new BoutiqueController();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom_boutique'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');

    $errors = [];
    if (empty($nom) || strlen($nom) < 3) $errors[] = 'Le nom doit contenir au moins 3 caractères.';
    if (empty($adresse) || strlen($adresse) < 5) $errors[] = 'L\'adresse doit contenir au moins 5 caractères.';
    if (empty($telephone) || strlen($telephone) < 8) $errors[] = 'Le téléphone doit contenir au moins 8 caractères.';

    if (empty($errors)) {
        $boutique = new Boutique($nom, $adresse, $telephone, "", $_SESSION['user_id']);
        $result = $boutiqueController->addBoutique($boutique, $_FILES['logo'] ?? null);

        if ($result['success']) {
            header('Location: mes-boutiques.php?success=ajoutee');
            exit();
        } else {
            $message = $result['message'];
            $messageType = 'danger';
        }
    } else {
        $message = implode('<br>', $errors);
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Boutique - AutoTech</title>
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

        .navbar .container {
            max-width: 1140px;
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

        .main-container { 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 40px 20px; 
        }

        .form-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .form-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.15);
        }

        .form-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-subtitle {
            color: var(--text-muted);
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 12px 15px;
            font-size: 14px;
            background: rgba(15, 23, 42, 0.5);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.7);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            color: var(--text-primary);
            outline: none;
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .logo-upload {
            margin-bottom: 25px;
        }

        .logo-preview {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .logo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        .file-input-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            text-decoration: none;
            color: white;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 25px;
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

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border: none;
            color: white;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-back {
            width: 100%;
            padding: 14px;
            background: rgba(148, 163, 184, 0.2);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: rgba(148, 163, 184, 0.3);
            border-color: var(--text-secondary);
            color: var(--text-primary);
            text-decoration: none;
            transform: translateY(-2px);
        }

        footer {
            background: rgba(15, 23, 42, 0.95);
            color: var(--text-secondary);
            padding: 3rem 0 1rem;
            margin-top: 4rem;
            border-top: 1px solid var(--border-color);
        }

        footer .container {
            max-width: 1140px;
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
            .form-card {
                padding: 25px;
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

    <div class="main-container">
        <div class="form-card">
            <h1 class="form-title">
                <i class="fas fa-store"></i>Ajouter une Boutique
            </h1>
            <p class="form-subtitle">Créez votre espace de vente dédié</p>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="logo-upload">
                    <label class="form-label">Logo de la boutique</label>
                    <div class="logo-preview" id="logoPreview">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="file-input-wrapper">
                        <input type="file" name="logo" id="logoInput" accept="image/*">
                        <label for="logoInput" class="file-input-label">
                            <i class="fas fa-upload"></i> Télécharger un logo
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nom_boutique">Nom de la boutique *</label>
                    <input type="text" class="form-control" id="nom_boutique" name="nom_boutique" 
                           placeholder="Ex: AutoTech Paris" required value="<?= htmlspecialchars($_POST['nom_boutique'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse *</label>
                    <input type="text" class="form-control" id="adresse" name="adresse" 
                           placeholder="Ex: 123 Rue de la Paix, 75000 Paris" required value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone *</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone" 
                           placeholder="Ex: 01 23 45 67 89" required value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Créer la boutique
                </button>
                <a href="mes-boutiques.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </form>
        </div>
    </div>

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

    <script>
        document.getElementById('logoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('logoPreview');
                    preview.innerHTML = '<img src="' + event.target.result + '" alt="preview">';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>