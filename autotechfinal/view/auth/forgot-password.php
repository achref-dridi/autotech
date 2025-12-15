<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$controller = new UtilisateurController();
$message = "";
$messageType = "";

// Check if PHPMailer is available
$usePhpMailer = file_exists(__DIR__ . '/../../vendor/autoload.php');
if ($usePhpMailer) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $message = "Veuillez entrer votre adresse email.";
        $messageType = "danger";
    } else {
        $user = $controller->getUserByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(50));
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));
            
            if ($controller->saveResetToken($user['id_utilisateur'], $token, $expires)) {
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/reset-password.php?token=" . $token;
                
                $emailSent = false;

                // PHPMailer
                if ($usePhpMailer) {
                    try {
                        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'amiraboubakri34@gmail.com';
                        $mail->Password   = 'xvdh pdze jtts kwlf';
                        $mail->Port       = 587;
                        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->CharSet    = 'UTF-8';

                        $mail->setFrom('no-reply@autotech.com', 'AutoTech');
                        $mail->addAddress($email);
                        $mail->isHTML(true);
                        $mail->Subject = 'Réinitialisation de votre mot de passe - AutoTech';
                        $mail->Body = "
                            <div style='font-family:Arial,sans-serif; max-width:600px; margin:auto; padding:20px; border:1px solid #ddd; border-radius:10px; background:#f9f9f9;'>
                                <h2 style='color:#1a5fb4;'>Bonjour {$user['prenom']},</h2>
                                <p>Vous avez demandé une réinitialisation de mot de passe sur <strong>AutoTech</strong>.</p>
                                <p style='text-align:center; margin:30px 0;'>
                                    <a href='{$reset_link}' style='background:#1a5fb4; color:white; padding:15px 35px; text-decoration:none; border-radius:50px; font-weight:bold; font-size:16px;'>
                                        Réinitialiser mon mot de passe
                                    </a>
                                </p>
                                <p>Ce lien expire dans <strong>1 heure</strong>.</p>
                                <hr>
                                <small style='color:#666;'>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</small>
                            </div>
                        ";
                        $mail->send();
                        $emailSent = true;
                    } catch (Exception $e) {
                        // PHPMailer failed, fallback will be used
                    }
                }

                // Fallback: PHP mail() silently
                if (!$emailSent) {
                    @mail($email, 'Réinitialisation de votre mot de passe - AutoTech', $mail->Body ?? '', "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: no-reply@autotech.com\r\n");
                    $emailSent = true;
                }

                $message = "Si cet email existe dans notre base de données, un lien de réinitialisation vous a été envoyé.";
                $messageType = "success";
            } else {
                $message = "Une erreur est survenue. Veuillez réessayer.";
                $messageType = "danger";
            }
        } else {
            $message = "Si cet email existe dans notre base de données, un lien de réinitialisation vous a été envoyé.";
            $messageType = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - AutoTech</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #1a1a1a;
        }
        .forgot-container {
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
        .forgot-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
        }
        .forgot-card {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        .forgot-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .forgot-header h2 {
            color: #333;
            margin-bottom: 15px;
            font-weight: 700;
            font-size: 28px;
        }
        .forgot-header p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
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
        .btn-reset {
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
        .btn-reset:hover {
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
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-card">
            <div class="forgot-header">
                <h2>Mot de passe oublié ?</h2>
                <p>Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-custom alert-<?= $messageType ?>-custom">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="votre@email.com" required>
                </div>

                <button type="submit" name="submit" class="btn btn-reset">
                    Envoyer le lien de réinitialisation
                </button>

                <div class="login-link">
                    Vous vous souvenez de votre mot de passe ? <a href="login.php">Se connecter</a>
                </div>
                <div class="login-link" style="margin-top: 15px;">
                    Pas encore de compte ? <a href="signup.php">S'inscrire</a>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
</body>
</html>
