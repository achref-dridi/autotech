<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../controller/ReservationController.php';
require_once __DIR__ . '/../../controller/VehiculeController.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$reservationController = new ReservationController();
$vehiculeController = new VehiculeController();
$mesReservations = $reservationController->getReservationsByUser($_SESSION['user_id']);

// Gestion annulation
if (isset($_GET['annuler']) && is_numeric($_GET['annuler'])) {
    $id = (int)$_GET['annuler'];
    $result = $reservationController->cancelReservation($id, $_SESSION['user_id']);
    if ($result['success']) {
        header('Location: mes-reservations.php?success=annulee');
        exit();
    }
}

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
    <title>Mes Réservations - AutoTech</title>
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

        .hero-section {
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(30, 41, 59, 0.8)),
                        linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 6rem 0 4rem;
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

        .reservation-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .reservation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.2);
        }

        .reservation-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .vehicle-info h3 {
            color: var(--text-primary);
            margin: 0 0 0.5rem 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .vehicle-info p {
            color: var(--text-muted);
            margin: 0;
            font-size: 0.9rem;
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
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
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            color: var(--text-primary);
            font-size: 1.1rem;
            font-weight: 600;
        }

        .price-highlight {
            color: var(--primary-light);
            font-size: 1.3rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
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
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.3);
            color: #ef4444;
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

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #10b981;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        footer {
            background: var(--dark-bg);
            padding: 3rem 0 1rem;
            margin-top: 4rem;
            border-top: 1px solid var(--border-color);
        }

        .footer-heading {
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        footer p, footer a {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        footer a:hover {
            color: var(--primary-light);
        }

        footer ul {
            list-style: none;
            padding: 0;
        }

        footer ul li {
            margin-bottom: 0.5rem;
        }

        .social-icons a {
            display: inline-flex;
            width: 40px;
            height: 40px;
            background: var(--card-bg);
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
            color: var(--text-secondary);
        }

        .social-icons a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
            color: white;
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
                    <?php if ($userController->estConnecte()): ?>
                        <li class="nav-item"><a class="nav-link" href="mes-boutiques.php">Mes Boutiques</a></li>
                        <li class="nav-item"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                        <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                        <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="../auth/login.php">Connexion</a></li>
                        <li class="nav-item"><a class="nav-link" href="../auth/signup.php">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <h1>Mes Réservations</h1>
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
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3>Aucune réservation</h3>
                    <p>Vous n'avez pas encore de réservations. Découvrez nos véhicules disponibles.</p>
                    <a href="../public/voitures.php" class="btn btn-primary">
                        <i class="fas fa-car mr-2"></i> Découvrir les véhicules
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($mesReservations as $reservation): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="reservation-card">
                                <div class="reservation-header">
                                    <div class="vehicle-info">
                                        <h3><?= htmlspecialchars($reservation['marque'] . ' ' . $reservation['modele']) ?></h3>
                                        <p><?= htmlspecialchars($reservation['annee'] ?? 'N/A') ?></p>
                                    </div>
                                    <span class="status-badge <?= strtolower(str_replace(' ', '-', $reservation['statut'])) ?>">
                                        <?= htmlspecialchars($reservation['statut']) ?>
                                    </span>
                                </div>

                                <div class="reservation-details">
                                    <div class="detail-item">
                                        <div class="detail-label">Début</div>
                                        <div class="detail-value"><?= date('d/m/Y', strtotime($reservation['date_debut'])) ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Fin</div>
                                        <div class="detail-value"><?= date('d/m/Y', strtotime($reservation['date_fin'])) ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Prix Total</div>
                                        <div class="detail-value price-highlight"><?= number_format($reservation['prix_total'], 2) ?> DT</div>
                                    </div>
                                </div>

                                <div class="action-buttons">
                                    <a href="../public/voiture-details.php?id=<?= $reservation['id_vehicule'] ?>" class="btn btn-primary">
                                        <i class="fas fa-eye"></i> Voir détails
                                    </a>
                                    <?php if ($reservation['statut'] !== 'annulée'): ?>
                                        <a href="mes-reservations.php?annuler=<?= $reservation['id_reservation'] ?>" 
                                           class="btn btn-danger"
                                           onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                                            <i class="fas fa-times"></i> Annuler
                                        </a>
                                    <?php endif; ?>
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
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <h3 class="footer-heading"><img src="../../images/off_logo.png" alt="logo.png" style="height: 40px;"></h3>
                    <p>Autotech est conçu pour centraliser et simplifier l'expérience automobile dans un environnement digital de pointe, répondant à la demande croissante d'efficacité et de transparence.</p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h4 class="footer-heading">Informations</h4>
                    <ul>
                        <li><a href="#">À propos</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">Termes et Conditions</a></li>
                        <li><a href="#">Garantie du Meilleur Prix</a></li>
                        <li><a href="#">Politique de Confidentialité</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h4 class="footer-heading">Support Client</h4>
                    <ul>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Option de Paiement</a></li>
                        <li><a href="#">Conseils de Réservation</a></li>
                        <li><a href="#">Comment ça marche</a></li>
                        <li><a href="#">Nous Contacter</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h4 class="footer-heading">Vous avez des Questions?</h4>
                    <p><i class="fas fa-map-marker-alt mr-2"></i> Esprit, Ariana sogra, Ariana, Tunisie</p>
                    <p><i class="fas fa-phone mr-2"></i> <a href="tel:+21633856909">+216 33 856 909</a></p>
                    <p><i class="fas fa-envelope mr-2"></i> <a href="mailto:AutoTech@gmail.tn">AutoTech@gmail.tn</a></p>
                </div>
            </div>
            <div class="text-center pt-3" style="border-top: 1px solid var(--border-color);">
                <p>Copyright &copy; <script>document.write(new Date().getFullYear());</script> Tous droits réservés | AutoTech</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
