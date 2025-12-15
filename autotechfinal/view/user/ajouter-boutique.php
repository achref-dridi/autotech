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
            --light-bg: #f8fafc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1e293b 100%);
            color: #f1f5f9;
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
            color: #cbd5e1 !important;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem !important;
            border-radius: 6px;
        }

        .nav-link:hover, .nav-item.active .nav-link {
            color: #3b82f6 !important;
            background: rgba(37, 99, 235, 0.1);
        }

        .container { max-width: 600px; margin: 0 auto; padding: 40px 20px; }

        .form-card {
            background: #1e293b;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(37, 99, 235, 0.2);
        }

        .form-title {
            font-size: 28px;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-subtitle {
            color: #cbd5e1;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group label {
            font-weight: 600;
            color: #f1f5f9;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid rgba(37, 99, 235, 0.3);
            padding: 12px 15px;
            background: #0f172a;
            color: #f1f5f9;
            transition: all 0.2s;
        }

        .form-control:focus {
            background: #1e293b;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            color: #f1f5f9;
        }

        .form-control::placeholder {
            color: #64748b;
        }
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
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
            background: var(--primary-color);
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }

        .file-input-label:hover {
            background: var(--primary-dark);
            text-decoration: none;
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 25px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border: none;
            color: white;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .btn-back {
            width: 100%;
            padding: 14px;
            background: #e2e8f0;
            border: none;
            color: #334155;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: #cbd5e1;
            color: #334155;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 25px;
            }

            .form-title {
                font-size: 24px;
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
