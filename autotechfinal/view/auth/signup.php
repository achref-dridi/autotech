<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$userController = new UtilisateurController();
$message = '';
$messageType = '';

if ($userController->estConnecte()) {
    header('Location: ../public/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $captcha = trim($_POST['captcha'] ?? '');
    
    // Verify CAPTCHA
    if (!isset($_SESSION['captcha_code']) || $captcha !== $_SESSION['captcha_code']) {
        $message = 'Code CAPTCHA incorrect.';
        $messageType = 'danger';
    } else {
        $result = $userController->inscrire($nom, $prenom, $email, $mot_de_passe, $telephone);
        
        if ($result['success']) {
            $loginResult = $userController->connecter($email, $mot_de_passe);
            header('Location: ../public/index.php');
            exit();
        } else {
            $message = $result['message'];
            $messageType = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - AutoTech</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #1a1a1a;
        }
        .signup-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('../../images/bg_3.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            padding: 40px 20px;
        }
        .signup-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
        }
        .signup-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        .signup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .signup-header img {
            width: 120px;
            margin-bottom: 20px;
        }
        .signup-header h2 {
            color: #333;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .signup-header p {
            color: #666;
        }
        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-signup {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 16px;
            margin-top: 20px;
            transition: all 0.3s;
        }
        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .login-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .required-star {
            color: red;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-header">
                <img src="../../images/off_logo.png" alt="AutoTech Logo">
                <p>Créez votre compte</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" onsubmit="return validerInscription()">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom <span class="required-star">*</span></label>
                        <input type="text" class="form-control" id="nom" name="nom" placeholder="Dupont">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label">Prénom <span class="required-star">*</span></label>
                        <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Jean">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="required-star">*</span></label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="votre@email.com">
                </div>

                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" placeholder="+216 XX XXX XXX">
                    <small class="text-muted">Format: +216 XX XXX XXX</small>
                </div>

                <div class="mb-3">
                    <label for="mot_de_passe" class="form-label">Mot de passe <span class="required-star">*</span></label>
                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" placeholder="••••••">
                    <small class="text-muted">Min. 6 caractères avec majuscule, minuscule et chiffre</small>
                </div>

                <div class="mb-3">
                    <label for="confirmer_mot_de_passe" class="form-label">Confirmer le mot de passe <span class="required-star">*</span></label>
                    <input type="password" class="form-control" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" placeholder="••••••">
                </div>

                <div class="mb-3">
                    <label for="captcha" class="form-label">Vérification anti-robot <span class="required-star">*</span></label>
                    <div class="mb-2" style="display: flex; align-items: center; gap: 10px;">
                        <img src="captcha.php" alt="CAPTCHA" id="captcha-image" style="border-radius: 8px; border: 1px solid #e0e0e0;">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="reloadCaptcha()" title="Recharger le CAPTCHA" style="padding: 8px 12px;">🔄</button>
                    </div>
                    <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Entrez le code ci-dessus" required>
                </div>

                <button type="submit" class="btn btn-signup">S'inscrire</button>

                <div class="login-link">
                    Vous avez déjà un compte? <a href="login.php">Se connecter</a>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/validation.js"></script>
    <script>
        function reloadCaptcha() {
            document.getElementById('captcha-image').src = 'captcha.php?' + new Date().getTime();
            document.getElementById('captcha').value = '';
            document.getElementById('captcha').focus();
        }

        function validerInscription() {
            const nom = document.getElementById('nom').value.trim();
            const prenom = document.getElementById('prenom').value.trim();
            const email = document.getElementById('email').value.trim();
            const motDePasse = document.getElementById('mot_de_passe').value;
            const confirmerMotDePasse = document.getElementById('confirmer_mot_de_passe').value;
            const captcha = document.getElementById('captcha').value.trim();

            if (!nom || nom.length < 2) {
                alert('Le nom doit contenir au moins 2 caractères');
                return false;
            }
            if (!prenom || prenom.length < 2) {
                alert('Le prénom doit contenir au moins 2 caractères');
                return false;
            }
            if (!email || !email.includes('@')) {
                alert('Veuillez entrer un email valide');
                return false;
            }
            if (!motDePasse || motDePasse.length < 6) {
                alert('Le mot de passe doit contenir au moins 6 caractères');
                return false;
            }
            if (motDePasse !== confirmerMotDePasse) {
                alert('Les mots de passe ne correspondent pas');
                return false;
            }
            if (!captcha) {
                alert('Veuillez entrer le code CAPTCHA');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
