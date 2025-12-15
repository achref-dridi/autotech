<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$controller = new UtilisateurController();
$token = $_GET['token'] ?? '';
$message = "";
$messageType = "";
$showForm = true;

if (empty($token)) {
    $message = "Lien invalide ou expiré.";
    $messageType = "danger";
    $showForm = false;
} else {
    $user = $controller->getUserByResetToken($token);
    
    if (!$user) {
        $message = "Lien invalide ou expiré.";
        $messageType = "danger";
        $showForm = false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    
    if (empty($password)) {
        $message = "Veuillez entrer un mot de passe.";
        $messageType = "danger";
    } elseif (strlen($password) < 6) {
        $message = "Le mot de passe doit contenir au moins 6 caractères.";
        $messageType = "danger";
    } elseif ($password !== $passwordConfirm) {
        $message = "Les mots de passe ne correspondent pas.";
        $messageType = "danger";
    } else {
        $user = $controller->getUserByResetToken($token);
        
        if ($user) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            
            if ($controller->updatePasswordAndClearToken($user['id_utilisateur'], $hashed)) {
                $message = "Mot de passe réinitialisé avec succès ! Vous pouvez maintenant vous connecter.";
                $messageType = "success";
                $showForm = false;
            } else {
                $message = "Une erreur est survenue. Veuillez réessayer.";
                $messageType = "danger";
            }
        } else {
            $message = "Lien invalide ou expiré.";
            $messageType = "danger";
            $showForm = false;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe - AutoTech</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #1a1a1a;
        }
        .reset-container {
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
        .reset-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
        }
        .reset-card {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        .reset-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .reset-header h2 {
            color: #333;
            margin-bottom: 15px;
            font-weight: 700;
            font-size: 28px;
        }
        .reset-header p {
            color: #666;
            font-size: 14px;
        }
        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 10px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            font-size: 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 16px;
            margin-top: 25px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .login-link {
            text-align: center;
            margin-top: 25px;
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
        .alert-custom {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .alert-success-custom {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger-custom {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .password-requirements {
            background: #f0f0f0;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 13px;
            color: #555;
        }
        .password-requirements ul {
            margin: 8px 0 0 20px;
            padding: 0;
        }
        .password-requirements li {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-header">
                <h2>Nouveau mot de passe</h2>
                <p>Créez un nouveau mot de passe sécurisé pour votre compte</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-custom alert-<?= $messageType ?>-custom">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($messageType === 'success' && !$showForm): ?>
                <div class="login-link" style="margin-top: 30px;">
                    <a href="login.php" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border-radius: 10px; text-decoration: none; display: inline-block;">
                        Se connecter
                    </a>
                </div>
            <?php elseif ($showForm): ?>
                <form method="POST" onsubmit="return validerMotDePasse()">
                    <div class="mb-4">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••" required>
                        <div class="password-requirements">
                            <strong>Critères :</strong>
                            <ul>
                                <li>Minimum 6 caractères</li>
                                <li>Doit contenir lettres et chiffres (recommandé)</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="••••••" required>
                    </div>

                    <button type="submit" name="reset" class="btn btn-submit">
                        Réinitialiser le mot de passe
                    </button>

                    <div class="login-link">
                        <a href="login.php">Retour à la connexion</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script>
        function validerMotDePasse() {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;

            if (password.length < 6) {
                alert('Le mot de passe doit contenir au moins 6 caractères');
                return false;
            }

            if (password !== passwordConfirm) {
                alert('Les mots de passe ne correspondent pas');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
