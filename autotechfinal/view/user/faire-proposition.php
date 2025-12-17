<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/TrajetController.php';
require_once __DIR__ . '/../../controller/PropositionController.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$idTrajet = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$trajetController = new TrajetController();
$propController = new PropositionController();

$trajet = $trajetController->getTrajetById($idTrajet);

if (!$trajet) {
    header('Location: ../public/trajets.php');
    exit();
}

// Check if user is trying to propose to themselves
if ($trajet['id_utilisateur'] == $_SESSION['user_id']) {
    die("Vous ne pouvez pas faire une proposition sur votre propre demande.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proposition = new Proposition(
        $idTrajet,
        $_SESSION['user_id'],
        (float)$_POST['prix'],
        $_POST['message'] ?? ''
    );
    
    $result = $propController->addProposition($proposition);
    
    if ($result['success']) {
        header('Location: ../public/trajets.php?success=proposition_envoyee'); // Redirect to public list or my propositions
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faire une proposition - AutoTech</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --dark-bg: #0f172a;
            --card-bg: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --border-color: #334155;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1e293b 100%);
            color: var(--text-primary);
            min-height: 100vh;
        }
        .form-container {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 2rem;
            max-width: 600px;
            margin: 4rem auto;
            border: 1px solid var(--border-color);
        }
        .form-control {
            background: rgba(15, 23, 42, 0.5);
            border-color: var(--border-color);
            color: white;
        }
        .btn-submit {
            background: var(--primary-color);
            color: white;
            width: 100%;
            padding: 0.8rem;
            border-radius: 8px;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h3 class="mb-4 text-center">Faire une proposition</h3>
            <div class="alert alert-info">
                <strong>Demande :</strong> <?= htmlspecialchars($trajet['lieu_depart']) ?> -> <?= htmlspecialchars($trajet['lieu_arrivee']) ?><br>
                <strong>Budget du passager :</strong> <?= number_format($trajet['budget'], 2) ?> DT
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Votre offre de prix (DT)</label>
                    <input type="number" name="prix" class="form-control" step="0.01" required value="<?= $trajet['budget'] ?>"> <!-- Default to user budget -->
                </div>
                <div class="form-group">
                    <label>Message (Détails véhicule, horaire...)</label>
                    <textarea name="message" class="form-control" rows="4" placeholder="Bonjour, je peux vous déposer... J'ai un grand coffre..."></textarea>
                </div>
                <button type="submit" class="btn-submit">Envoyer la proposition</button>
                <a href="../public/trajets.php" class="btn btn-link text-white d-block text-center mt-3">Annuler</a>
            </form>
        </div>
    </div>
</body>
</html>
