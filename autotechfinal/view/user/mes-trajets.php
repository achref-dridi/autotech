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

// Handle deletion
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    if ($trajetController->estProprietaire($id, $_SESSION['user_id'])) {
        $trajetController->deleteTrajet($id);
        header('Location: mes-trajets.php?success=supprime');
        exit();
    }
}

// Handle reservation approval/rejection
if (isset($_GET['confirmer']) && is_numeric($_GET['confirmer'])) {
    $resId = (int)$_GET['confirmer'];
    $trajetId = (int)$_GET['trajet_id'];
    $trajetController->confirmReservation($resId, $trajetId);
    header('Location: mes-trajets.php?success=confirmee');
    exit();
}

if (isset($_GET['rejeter']) && is_numeric($_GET['rejeter'])) {
    $resId = (int)$_GET['rejeter'];
    $trajetId = (int)$_GET['trajet_id'];
    $trajetController->rejectReservation($resId, $trajetId);
    header('Location: mes-trajets.php?success=rejetee');
    exit();
}

$mesTrajets = $trajetController->getTrajetsByUtilisateur($_SESSION['user_id']);

$message = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'cree') {
        $message = 'Trajet créé avec succès.';
    } elseif ($_GET['success'] === 'supprime') {
        $message = 'Trajet supprimé avec succès.';
    } elseif ($_GET['success'] === 'modifie') {
        $message = 'Trajet modifié avec succès.';
    } elseif ($_GET['success'] === 'confirmee') {
        $message = 'Réservation confirmée.';
    } elseif ($_GET['success'] === 'rejetee') {
        $message = 'Réservation rejetée.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Trajets - AutoTech</title>
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
            filter: brightness(1.1);
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

        .action-buttons-header {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
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

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #10b981;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .trajet-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .trajet-route {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .route-info h3 {
            color: var(--text-primary);
            margin: 0 0 0.5rem 0;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .route-details {
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

        .reservations-section {
            background: rgba(37, 99, 235, 0.05);
            border: 1px solid rgba(37, 99, 235, 0.2);
            padding: 1.5rem;
            border-radius: 12px;
            margin-top: 1.5rem;
        }

        .reservations-title {
            color: var(--primary-light);
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .reservation-item {
            background: rgba(15, 23, 42, 0.5);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 3px solid var(--primary-color);
        }

        .reservation-item:last-child {
            margin-bottom: 0;
        }

        .reservation-name {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .reservation-status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .status-attente {
            background: rgba(249, 115, 22, 0.2);
            color: #fb923c;
        }

        .status-confirmee {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .status-rejetee {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .res-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .res-actions a {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            text-decoration: none;
        }

        .btn-confirm {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }

        .btn-confirm:hover {
            background: rgba(16, 185, 129, 0.3);
            color: #10b981;
        }

        .btn-reject {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        .btn-reject:hover {
            background: rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }

        .trajet-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .btn-edit {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--primary-light);
            text-decoration: none;
        }

        .btn-edit:hover {
            border-color: var(--primary-color);
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

            .trajet-route {
                flex-direction: column;
                align-items: flex-start;
            }

            .route-details {
                grid-template-columns: 1fr;
            }

            .action-buttons-header {
                flex-direction: column;
            }

            .action-buttons-header .btn {
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
                    <li class="nav-item"><a class="nav-link" href="../public/voitures.php">Voitures</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/boutiques.php">Boutiques</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/trajets.php">Trajets</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item active"><a class="nav-link" href="mes-trajets.php">Mes Trajets</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-road mr-2"></i> Mes Trajets</h1>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert-success">
                    <i class="fas fa-check-circle mr-2"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="action-buttons-header">
                <a href="ajouter-trajet.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Nouveau trajet
                </a>
            </div>

            <?php if (empty($mesTrajets)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-road"></i>
                    </div>
                    <h3>Aucun trajet</h3>
                    <p>Vous n'avez pas encore créé de trajet. Commencez à partager votre route maintenant!</p>
                    <a href="ajouter-trajet.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-2"></i> Créer un trajet
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($mesTrajets as $trajet): ?>
                    <div class="trajet-card">
                        <div class="trajet-route">
                            <div class="route-info">
                                <h3>
                                    <i class="fas fa-map-pin" style="color: var(--primary-light); margin-right: 0.5rem;"></i>
                                    <?= htmlspecialchars($trajet['lieu_depart']) ?> → <?= htmlspecialchars($trajet['lieu_arrivee']) ?>
                                </h3>
                            </div>
                            <div class="route-price" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); padding: 0.8rem 1.5rem; border-radius: 10px; color: white; font-weight: 600; text-align: center;">
                                <div style="font-size: 1.5rem;"><?= number_format($trajet['prix'], 2) ?></div>
                                <div style="font-size: 0.8rem; opacity: 0.9;">DT</div>
                            </div>
                        </div>

                        <div class="route-details">
                            <div class="detail-item">
                                <div class="detail-label">Départ</div>
                                <div class="detail-value"><?= date('d/m H:i', strtotime($trajet['date_depart'])) ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Durée</div>
                                <div class="detail-value"><?= $trajet['duree_minutes'] ?> min</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Places</div>
                                <div class="detail-value"><?= $trajet['places_disponibles'] ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Statut</div>
                                <div class="detail-value" style="text-transform: capitalize; color: var(--primary-light);">
                                    <?= htmlspecialchars($trajet['statut']) ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($trajet['description'])): ?>
                            <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 1rem 0 0 0; font-style: italic;">
                                "<?= htmlspecialchars(substr($trajet['description'], 0, 200)) ?><?php if (strlen($trajet['description']) > 200): ?>...<?php endif; ?>"
                            </p>
                        <?php endif; ?>

                        <?php 
                        $reservations = $trajetController->getReservationsByTrajet($trajet['id_trajet']);
                        if (!empty($reservations)): 
                        ?>
                            <div class="reservations-section">
                                <div class="reservations-title">
                                    <i class="fas fa-users mr-1"></i> Réservations (<?= count($reservations) ?>)
                                </div>
                                <?php foreach ($reservations as $res): ?>
                                    <div class="reservation-item">
                                        <div class="reservation-name">
                                            <i class="fas fa-user-circle mr-1" style="color: var(--primary-light);"></i>
                                            <?= htmlspecialchars($res['prenom'] . ' ' . $res['nom']) ?>
                                        </div>
                                        <div>
                                            <span class="reservation-status status-<?= strtolower(str_replace(' ', '-', $res['statut'])) ?>">
                                                <?= htmlspecialchars(ucfirst($res['statut'])) ?>
                                            </span>
                                        </div>
                                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.75rem;">
                                            <i class="fas fa-envelope mr-1"></i> <?= htmlspecialchars($res['email']) ?>
                                            <?php if (!empty($res['telephone'])): ?>
                                                | <i class="fas fa-phone mr-1"></i> <?= htmlspecialchars($res['telephone']) ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($res['statut'] === 'en attente'): ?>
                                            <div class="res-actions">
                                                <a href="?confirmer=<?= $res['id_reservation_trajet'] ?>&trajet_id=<?= $trajet['id_trajet'] ?>" class="btn btn-confirm">
                                                    <i class="fas fa-check"></i> Confirmer
                                                </a>
                                                <a href="?rejeter=<?= $res['id_reservation_trajet'] ?>&trajet_id=<?= $trajet['id_trajet'] ?>" class="btn btn-reject">
                                                    <i class="fas fa-times"></i> Rejeter
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="trajet-actions">
                            <a href="modifier-trajet.php?id=<?= $trajet['id_trajet'] ?>" class="btn btn-edit">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="?supprimer=<?= $trajet['id_trajet'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?')">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
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
