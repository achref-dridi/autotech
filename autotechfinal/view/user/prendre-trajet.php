<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/TrajetController.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$trajetController = new TrajetController();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: ../public/trajets.php');
    exit();
}

$trajet = $trajetController->getTrajetById($id);

if (!$trajet || $trajet['id_utilisateur'] == $_SESSION['user_id']) {
    header('Location: ../public/trajets.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $trajetController->reserverTrajet(
        $id,
        $_SESSION['user_id'],
        (int)$_POST['nombre_places']
    );

    if ($result['success']) {
        header('Location: mes-reservations-trajets.php?success=creee');
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
    <title>Réserver Trajet - AutoTech</title>
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
            padding: 0.75rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand img {
            height: 40px;
        }

        .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.35rem 0.75rem !important;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .nav-link:hover, .nav-item.active .nav-link {
            color: var(--primary-light) !important;
            background: rgba(37, 99, 235, 0.1);
        }

        .hero-section {
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(30, 41, 59, 0.8)),
                        linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 5rem 0 3rem;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .content-section {
            padding: 4rem 0;
        }

        .container-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card h2 {
            color: var(--text-primary);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .route-display {
            background: rgba(37, 99, 235, 0.1);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }

        .route-display h3 {
            color: var(--primary-light);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .lieux {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--text-secondary);
            margin: 0.75rem 0;
        }

        .detail-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            background: rgba(15, 23, 42, 0.5);
            padding: 1rem;
            border-radius: 8px;
        }

        .detail-label {
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
        }

        .conducteur-card {
            background: rgba(37, 99, 235, 0.1);
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-light);
        }

        .conducteur-name {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .conducteur-info {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin: 0.3rem 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.7);
            border-color: var(--primary-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #ef4444;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .btn-secondary {
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            color: var(--text-secondary);
            margin-top: 1rem;
        }

        .btn-secondary:hover {
            border-color: var(--primary-color);
            color: var(--primary-light);
            text-decoration: none;
        }

        footer {
            background: var(--dark-bg);
            padding: 3rem 0 1rem;
            margin-top: 4rem;
            border-top: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            .container-wrapper {
                grid-template-columns: 1fr;
            }

            .hero-section h1 {
                font-size: 2rem;
            }

            .detail-box {
                grid-template-columns: 1fr;
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
                    <li class="nav-item"><a class="nav-link" href="../public/trajets.php">Trajets</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-reservations-trajets.php">Mes Réservations</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-ticket-alt mr-2"></i> Réserver un Trajet</h1>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="container-wrapper">
                <div class="card">
                    <h2><i class="fas fa-map-location-dot" style="color: var(--primary-light);"></i> Informations du Trajet</h2>

                    <div class="route-display">
                        <h3><?= htmlspecialchars($trajet['lieu_depart']) ?> → <?= htmlspecialchars($trajet['lieu_arrivee']) ?></h3>
                        <div class="lieux">
                            <span>De: <?= htmlspecialchars($trajet['lieu_depart']) ?></span>
                            <i class="fas fa-arrow-right" style="color: var(--primary-light);"></i>
                            <span>À: <?= htmlspecialchars($trajet['lieu_arrivee']) ?></span>
                        </div>
                    </div>

                    <div class="detail-box">
                        <div class="detail-item">
                            <div class="detail-label">Départ</div>
                            <div class="detail-value"><?= date('d/m H:i', strtotime($trajet['date_depart'])) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Durée</div>
                            <div class="detail-value"><?= $trajet['duree_minutes'] ?> min</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Prix</div>
                            <div class="detail-value" style="color: var(--primary-light);"><?= number_format($trajet['prix'], 2) ?> DT</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Places Dispo</div>
                            <div class="detail-value"><?= $trajet['places_disponibles'] ?></div>
                        </div>
                    </div>

                    <?php if (!empty($trajet['description'])): ?>
                        <div style="background: rgba(15, 23, 42, 0.5); padding: 1rem; border-radius: 8px; color: var(--text-secondary); font-size: 0.9rem;">
                            <strong style="color: var(--text-primary);">Description:</strong><br>
                            <?= htmlspecialchars($trajet['description']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <h2><i class="fas fa-user-check" style="color: var(--primary-light);"></i> Conductor</h2>

                    <div class="conducteur-card">
                        <div class="conducteur-name">
                            <i class="fas fa-user-circle mr-1" style="color: var(--primary-light);"></i>
                            <?= htmlspecialchars($trajet['prenom'] . ' ' . $trajet['nom']) ?>
                        </div>
                        <div class="conducteur-info">
                            <i class="fas fa-envelope mr-1"></i> <?= htmlspecialchars($trajet['email']) ?>
                        </div>
                        <?php if (!empty($trajet['telephone'])): ?>
                            <div class="conducteur-info">
                                <i class="fas fa-phone mr-1"></i> <?= htmlspecialchars($trajet['telephone']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <form method="POST">
                        <?php if (isset($error)): ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="nombre_places">
                                <i class="fas fa-chair" style="color: var(--primary-light);"></i>
                                Nombre de places
                            </label>
                            <input type="number" class="form-control" id="nombre_places" name="nombre_places" value="1" min="1" max="<?= $trajet['places_disponibles'] ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle"></i> Confirmer la réservation
                        </button>
                        <a href="../public/trajets.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="text-center pt-3" style="border-top: 1px solid var(--border-color);">
                <p style="color: var(--text-muted); font-size: 0.9rem;">Copyright &copy; <script>document.write(new Date().getFullYear());</script> AutoTech</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Replace template syntax with PHP
        document.querySelector('.route-display h3').innerHTML = '<?= htmlspecialchars($trajet['lieu_depart']) ?> → <?= htmlspecialchars($trajet['lieu_arrivee']) ?>';
        document.querySelectorAll('[data-temp]').forEach(el => el.remove());
    </script>
</body>
</html>
