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
$boutique = null;

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: mes-boutiques.php');
    exit();
}

$boutique = $boutiqueController->getBoutiqueById($id);
if (!$boutique || $boutique['id_utilisateur'] != $_SESSION['user_id']) {
    header('Location: mes-boutiques.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom_boutique'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');

    $errors = [];
    if (empty($nom) || strlen($nom) < 3) $errors[] = 'Le nom doit contenir au moins 3 caractères.';
    if (empty($adresse) || strlen($adresse) < 5) $errors[] = 'L\'adresse doit contenir au moins 5 caractères.';
    if (empty($telephone) || strlen($telephone) < 8) $errors[] = 'Le téléphone doit contenir au moins 8 caractères.';

    if (empty($errors)) {
        $boutiqueObj = new Boutique($nom, $adresse, $telephone, $boutique['logo'], $_SESSION['user_id']);
        $result = $boutiqueController->updateBoutique($boutiqueObj, $id, $_FILES['logo'] ?? null);

        if ($result['success']) {
            header('Location: mes-boutiques.php?success=modifiee');
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
    <title>Modifier Boutique - AutoTech</title>
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

        .container { max-width: 600px; margin: 0 auto; padding: 40px 20px; }

        .form-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        .form-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark-bg);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-subtitle {
            color: #64748b;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 12px 15px;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <h1 class="form-title">
                <i class="fas fa-edit"></i>Modifier la Boutique
            </h1>
            <p class="form-subtitle">Mettez à jour les informations de votre boutique</p>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="logo-upload">
                    <label class="form-label">Logo de la boutique</label>
                    <div class="logo-preview" id="logoPreview">
                        <?php if ($boutique['logo']): ?>
                            <img src="/autotechfinal/uploads/logos/<?= htmlspecialchars($boutique['logo']) ?>" alt="logo">
                        <?php else: ?>
                            <i class="fas fa-store"></i>
                        <?php endif; ?>
                    </div>
                    <div class="file-input-wrapper">
                        <input type="file" name="logo" id="logoInput" accept="image/*">
                        <label for="logoInput" class="file-input-label">
                            <i class="fas fa-upload"></i> Changer le logo
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nom_boutique">Nom de la boutique *</label>
                    <input type="text" class="form-control" id="nom_boutique" name="nom_boutique" 
                           placeholder="Ex: AutoTech Paris" required value="<?= htmlspecialchars($boutique['nom_boutique']) ?>">
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse *</label>
                    <input type="text" class="form-control" id="adresse" name="adresse" 
                           placeholder="Ex: 123 Rue de la Paix, 75000 Paris" required value="<?= htmlspecialchars($boutique['adresse']) ?>">
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone *</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone" 
                           placeholder="Ex: 01 23 45 67 89" required value="<?= htmlspecialchars($boutique['telephone']) ?>">
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Enregistrer les modifications
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
