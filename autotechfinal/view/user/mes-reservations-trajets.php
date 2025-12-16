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

// Handle cancellation
if (isset($_GET['annuler']) && is_numeric($_GET['annuler'])) {
    $resId = (int)$_GET['annuler'];
    $trajetController->cancelReservation($resId, $_SESSION['user_id']);
    header('Location: mes-reservations-trajets.php?success=annulee');
    exit();
}

$mesReservations = $trajetController->getReservationsTrajet($_SESSION['user_id']);

$message = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'creee') {
        $message = 'Réservation créée avec succès.';
    } elseif ($_GET['success'] === 'annulee') {
        $message = 'Réservation annulée avec succès.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations de Trajets - AutoTech</title>
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

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #10b981;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .reservation-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .reservation-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .route-info h3 {
            color: var(--text-primary);
            margin: 0 0 0.5rem 0;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.en-attente {
            background: rgba(249, 115, 22, 0.2);
            color: #fb923c;
        }

        .status-badge.confirmee {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .status-badge.annulee {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .reservation-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
            padding: 1.5rem 0;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            color: var(--text-primary);
            font-size: 1.1rem;
            font-weight: 600;
        }

        .conducteur-info {
            background: rgba(37, 99, 235, 0.1);
            padding: 1rem;
            border-radius: 12px;
            margin: 1rem 0;
            border-left: 4px solid var(--primary-color);
        }

        .conducteur-info p {
            margin: 0.3rem 0;
            font-size: 0.9rem;
        }

        .conducteur-name {
            color: var(--text-primary);
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.4);
            text-decoration: none;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.3);
            text-decoration: none;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        footer {
            background: var(--dark-bg);
            padding: 3rem 0 1rem;
            margin-top: 4rem;
            border-top: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }

            .reservation-details {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
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
                    <li class="nav-item active"><a class="nav-link" href="mes-reservations-trajets.php">Mes Rés. Trajets</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-ticket-alt mr-2"></i> Mes Réservations de Trajets</h1>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert-success">
                    <i class="fas fa-check-circle mr-2"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($mesReservations)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-map-location-dot"></i>
                    </div>
                    <h3>Aucune réservation</h3>
                    <p>Vous n'avez pas encore réservé de trajet. Découvrez les trajets disponibles.</p>
                    <a href="../public/trajets.php" class="btn btn-primary">
                        <i class="fas fa-road mr-2"></i> Découvrir les trajets
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($mesReservations as $res): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="reservation-card">
                                <div class="reservation-header">
                                    <div class="route-info">
                                        <h3>
                                            <i class="fas fa-map-pin" style="color: var(--primary-light); margin-right: 0.5rem;"></i>
                                            <?= htmlspecialchars($res['lieu_depart']) ?> → <?= htmlspecialchars($res['lieu_arrivee']) ?>
                                        </h3>
                                    </div>
                                    <span class="status-badge <?= strtolower(str_replace(' ', '-', $res['statut'])) ?>">
                                        <?= htmlspecialchars($res['statut']) ?>
                                    </span>
                                </div>

                                <div class="reservation-details">
                                    <div class="detail-item">
                                        <div class="detail-label">Départ</div>
                                        <div class="detail-value"><?= date('d/m H:i', strtotime($res['date_depart'])) ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Durée</div>
                                        <div class="detail-value"><?= $res['duree_minutes'] ?> min</div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Places</div>
                                        <div class="detail-value"><?= $res['nombre_places'] ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Prix</div>
                                        <div class="detail-value" style="color: var(--primary-light);"><?= number_format($res['prix'], 2) ?> DT</div>
                                    </div>
                                </div>

                                <div class="conducteur-info">
                                    <p class="conducteur-name">
                                        <i class="fas fa-user-circle mr-1"></i>
                                        <?= htmlspecialchars($res['prenom'] . ' ' . $res['nom']) ?>
                                    </p>
                                    <p><i class="fas fa-phone mr-1"></i> <?= htmlspecialchars($res['telephone'] ?? 'N/A') ?></p>
                                    <p><i class="fas fa-envelope mr-1"></i> <?= htmlspecialchars($res['email']) ?></p>
                                </div>

                                <div class="action-buttons">
                                    <?php if ($res['statut'] !== 'annulee'): ?>
                                        <a href="?annuler=<?= $res['id_reservation_trajet'] ?>" 
                                           class="btn btn-danger"
                                           onclick="return confirm('Êtes-vous sûr de vouloir annuler?');">
                                            <i class="fas fa-times"></i> Annuler
                                        </a>
                                    <?php endif; ?>
                                    <a href="../public/trajets.php" class="btn btn-primary">
                                        <i class="fas fa-arrow-left"></i> Découvrir plus
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
</body>
</html>
