<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../controller/VehiculeController.php';
require_once __DIR__ . '/../../controller/ReservationController.php';
require_once __DIR__ . '/../../model/Reservation.php';

$userController = new UtilisateurController();
$vehiculeController = new VehiculeController();
$reservationController = new ReservationController();

// Protection: Vérifier connexion
if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$id_vehicule = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vehicule = null;

if ($id_vehicule > 0) {
    $vehicule = $vehiculeController->getVehiculeById($id_vehicule);
}

$message = '';
$messageType = '';
$errors = [];

// Gérer la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reserver') {
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';

    // Validation
    if (!$date_debut) {
        $errors[] = 'Veuillez spécifier la date de début.';
    }
    if (!$date_fin) {
        $errors[] = 'Veuillez spécifier la date de fin.';
    }

    if ($date_debut && $date_fin) {
        $dateDebut = DateTime::createFromFormat('Y-m-d', $date_debut);
        $dateFin = DateTime::createFromFormat('Y-m-d', $date_fin);

        if (!$dateDebut || !$dateFin) {
            $errors[] = 'Format de date invalide.';
        } else {
            if ($dateFin <= $dateDebut) {
                $errors[] = 'La date de fin doit être après la date de début.';
            }

            $now = new DateTime();
            if ($dateDebut <= $now) {
                $errors[] = 'La date de début doit être dans le futur.';
            }
        }
    }

    // Si validation ok
    if (empty($errors)) {
        $reservation = new Reservation($id_vehicule, $_SESSION['user_id'], $date_debut . ' 00:00:00', $date_fin . ' 23:59:59');
        $result = $reservationController->addReservation($reservation);

        if ($result['success']) {
            header('Location: mes-reservations.php?success=creee');
            exit();
        } else {
            $messageType = 'danger';
            $message = $result['message'];
        }
    }
}

// Récupérer les réservations existantes pour le calendrier
$reservations = [];
if ($vehicule) {
    $reservations = $reservationController->getReservationsByVehicle($id_vehicule);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver un Véhicule - AutoTech</title>
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
                        url('../../images/bg_3.jpg');
            background-size: cover;
            background-position: center;
            padding: 4rem 0 3rem;
            position: relative;
        }

        .breadcrumbs {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .breadcrumbs a {
            color: var(--primary-light);
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumbs a:hover {
            color: var(--primary-color);
        }

        .breadcrumbs i {
            font-size: 0.7rem;
            margin: 0 0.5rem;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .reservation-section {
            padding: 3rem 0;
        }

        .reservation-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .vehicle-info-card {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(16, 185, 129, 0.1));
            border-radius: 12px;
            padding: 1.5rem;
            border-left: 4px solid var(--primary-color);
            margin-bottom: 2rem;
        }

        .vehicle-info-card h5 {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .vehicle-info-card p {
            color: var(--text-secondary);
            margin: 0.25rem 0;
        }

        .form-group label {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.7);
            border-color: var(--primary-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            color: white;
            text-decoration: none;
        }

        .btn-back {
            background: rgba(37, 99, 235, 0.1);
            border: 1px solid var(--primary-color);
            color: var(--primary-light);
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .btn-back:hover {
            background: var(--primary-color);
            color: white;
            text-decoration: none;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.1));
            color: #ef4444;
            border-left: 4px solid #ef4444;
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(37, 99, 235, 0.1));
            color: var(--primary-light);
            border-left: 4px solid var(--primary-color);
        }

        .error-list {
            list-style: none;
            padding: 0;
        }

        .error-list li {
            padding: 0.5rem 0;
            color: #ef4444;
        }

        .error-list li:before {
            content: "✗ ";
            font-weight: bold;
            margin-right: 0.5rem;
        }

        .price-info {
            background: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--secondary-color);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .price-info p {
            color: var(--text-secondary);
            margin: 0.5rem 0;
        }

        .price-total {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--secondary-color);
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

        @media (max-width: 768px) {
            .reservation-card {
                padding: 1.5rem;
            }

            .hero-title {
                font-size: 1.8rem;
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
                    <li class="nav-item"><a class="nav-link" href="mes-reservations.php">Mes Réservations</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="breadcrumbs">
                <a href="../public/index.php">Accueil</a> <i class="fas fa-chevron-right"></i>
                <a href="../public/voitures.php">Voitures</a> <i class="fas fa-chevron-right"></i>
                <span>Réservation</span>
            </div>
            <h1 class="hero-title">Réserver un Véhicule</h1>
        </div>
    </section>

    <section class="reservation-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <?php if (!$vehicule): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            Véhicule introuvable.
                        </div>
                        <a href="../public/voitures.php" class="btn-back">
                            <i class="fas fa-arrow-left mr-2"></i> Retour aux véhicules
                        </a>
                    <?php else: ?>
                        <a href="../public/voiture-details.php?id=<?= $vehicule['id_vehicule'] ?>" class="btn-back">
                            <i class="fas fa-arrow-left mr-2"></i> Retour aux détails
                        </a>

                        <div class="vehicle-info-card">
                            <h5><i class="fas fa-car mr-2"></i> <?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></h5>
                            <p><strong>Année:</strong> <?= htmlspecialchars($vehicule['annee']) ?></p>
                            <p><strong>Carburant:</strong> <?= htmlspecialchars($vehicule['carburant']) ?></p>
                            <p><strong>Prix journalier:</strong> <span class="price-total"><?= number_format($vehicule['prix_journalier'], 2) ?> DT</span></p>
                        </div>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Erreurs:</strong>
                                <ul class="error-list mt-2">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($message): ?>
                            <div class="alert alert-<?= $messageType ?>">
                                <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i>
                                <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>

                        <div class="reservation-card">
                            <h4><i class="fas fa-calendar-alt mr-2"></i> Détails de la Réservation</h4>
                            <form method="POST">
                                <input type="hidden" name="action" value="reserver">

                                <div class="form-group">
                                    <label for="date_debut">
                                        <i class="fas fa-calendar-check mr-1"></i> Date de début
                                    </label>
                                    <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                                </div>

                                <div class="form-group">
                                    <label for="date_fin">
                                        <i class="fas fa-calendar-times mr-1"></i> Date de fin
                                    </label>
                                    <input type="date" class="form-control" id="date_fin" name="date_fin" required>
                                </div>

                                <div class="price-info">
                                    <p><strong>Calcul du prix:</strong></p>
                                    <p>Nombre de jours: <span id="days">0</span> jours</p>
                                    <p>Prix unitaire: <?= number_format($vehicule['prix_journalier'], 2) ?> DT/jour</p>
                                    <p class="price-total">Montant total: <span id="total">0</span> DT</p>
                                </div>

                                <button type="submit" class="btn-submit mt-3">
                                    <i class="fas fa-check mr-2"></i> Confirmer la Réservation
                                </button>
                            </form>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Information:</strong> Votre réservation sera à l'état "en attente" en attendant la confirmation du propriétaire du véhicule.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <h3 class="footer-heading"><img src="../../images/off_logo.png" alt="logo.png" style="height: 40px;"></h3>
                    <p>Autotech est conçu pour centraliser et simplifier l'expérience automobile dans un environnement digital de pointe.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h4 class="footer-heading">Informations</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="#">À propos</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">Termes et Conditions</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h4 class="footer-heading">Support Client</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Conseils de Réservation</a></li>
                        <li><a href="#">Nous Contacter</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h4 class="footer-heading">Contact</h4>
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
    <script>
        const prixJournalier = <?= $vehicule['prix_journalier'] ?>;

        document.getElementById('date_debut').addEventListener('change', calculatePrice);
        document.getElementById('date_fin').addEventListener('change', calculatePrice);

        function calculatePrice() {
            const dateDebut = new Date(document.getElementById('date_debut').value);
            const dateFin = new Date(document.getElementById('date_fin').value);

            if (dateDebut && dateFin && dateFin > dateDebut) {
                const diffTime = Math.abs(dateFin - dateDebut);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                const total = diffDays * prixJournalier;

                document.getElementById('days').textContent = diffDays;
                document.getElementById('total').textContent = total.toFixed(2);
            } else {
                document.getElementById('days').textContent = '0';
                document.getElementById('total').textContent = '0';
            }
        }
    </script>
</body>
</html>
