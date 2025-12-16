<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/TrajetController.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$trajetController = new TrajetController();
$userController = new UtilisateurController();

$trajets = $trajetController->getAllTrajets();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trajets Disponibles - AutoTech</title>
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

        .trajet-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .trajet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.2);
        }

        .trajet-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .trajet-route {
            flex: 1;
            min-width: 200px;
        }

        .trajet-route h3 {
            color: var(--text-primary);
            margin: 0 0 0.5rem 0;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .route-arrow {
            color: var(--primary-light);
            font-size: 1rem;
            margin: 0 0.5rem;
        }

        .lieu {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .trajet-prix {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            padding: 1rem 1.5rem;
            border-radius: 12px;
            text-align: center;
            color: white;
        }

        .prix-value {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .prix-label {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .trajet-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
            padding: 1rem 0;
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
            font-size: 1rem;
            font-weight: 600;
        }

        .conducteur-info {
            background: rgba(37, 99, 235, 0.1);
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
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

        .description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 1rem 0;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
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

        .btn-secondary {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }

        .btn-secondary:hover {
            border-color: var(--primary-color);
            color: var(--primary-light);
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

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }

            .nav-link {
                padding: 0.3rem 0.5rem !important;
                font-size: 0.85rem;
            }

            .trajet-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="../../images/off_logo.png" alt="logo.png" id="img_logo">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="voitures.php">Voitures</a></li>
                    <li class="nav-item"><a class="nav-link" href="boutiques.php">Boutiques</a></li>
                    <li class="nav-item active"><a class="nav-link" href="trajets.php">Trajets</a></li>
                    <?php if ($userController->estConnecte()): ?>
                        <li class="nav-item"><a class="nav-link" href="../user/mes-vehicules.php">Mes Véhicules</a></li>
                        <li class="nav-item"><a class="nav-link" href="../user/mes-trajets.php">Mes Trajets</a></li>
                        <li class="nav-item"><a class="nav-link" href="../user/profil.php">Mon Profil</a></li>
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
            <h1><i class="fas fa-road mr-2"></i> Trajets Disponibles</h1>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <?php if (empty($trajets)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-map-location-dot"></i>
                    </div>
                    <h3>Aucun trajet disponible</h3>
                    <p>Il n'y a pas de trajets disponibles pour le moment.</p>
                    <?php if ($userController->estConnecte()): ?>
                        <a href="../user/ajouter-trajet.php" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i> Proposer un trajet
                        </a>
                    <?php else: ?>
                        <a href="../auth/login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt mr-2"></i> Connexion
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($trajets as $trajet): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="trajet-card">
                                <div class="trajet-header">
                                    <div class="trajet-route">
                                        <h3>
                                            <i class="fas fa-map-pin" style="color: var(--primary-light); margin-right: 0.5rem;"></i>
                                            <?= htmlspecialchars(substr($trajet['lieu_depart'], 0, 12)) ?>
                                            <span class="route-arrow">→</span>
                                            <?= htmlspecialchars(substr($trajet['lieu_arrivee'], 0, 12)) ?>
                                        </h3>
                                        <p class="lieu"><i class="fas fa-info-circle mr-1" style="color: var(--text-muted);"></i>
                                            <?= htmlspecialchars($trajet['lieu_depart'] . ' → ' . $trajet['lieu_arrivee']) ?>
                                        </p>
                                    </div>
                                    <div class="trajet-prix">
                                        <div class="prix-value"><?= number_format($trajet['prix'], 2) ?></div>
                                        <div class="prix-label">DT</div>
                                    </div>
                                </div>

                                <div class="conducteur-info">
                                    <p class="conducteur-name">
                                        <i class="fas fa-user-circle mr-1"></i>
                                        <?= htmlspecialchars($trajet['prenom'] . ' ' . $trajet['nom']) ?>
                                    </p>
                                    <p><i class="fas fa-phone mr-1"></i> <?= htmlspecialchars($trajet['telephone'] ?? 'N/A') ?></p>
                                    <p><i class="fas fa-envelope mr-1"></i> <?= htmlspecialchars($trajet['email']) ?></p>
                                </div>

                                <div class="trajet-details">
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
                                </div>

                                <?php if (!empty($trajet['description'])): ?>
                                    <div class="description">
                                        <i class="fas fa-quote-left mr-1" style="opacity: 0.5;"></i>
                                        <?= htmlspecialchars(substr($trajet['description'], 0, 100)) ?>
                                        <?php if (strlen($trajet['description']) > 100): ?>...<?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="action-buttons">
                                    <?php if ($userController->estConnecte() && $trajet['id_utilisateur'] != $_SESSION['user_id']): ?>
                                        <a href="../user/prendre-trajet.php?id=<?= $trajet['id_trajet'] ?>" class="btn btn-primary">
                                            <i class="fas fa-check-circle"></i> Réserver
                                        </a>
                                    <?php elseif (!$userController->estConnecte()): ?>
                                        <a href="../auth/login.php" class="btn btn-primary">
                                            <i class="fas fa-sign-in-alt"></i> Se connecter
                                        </a>
                                    <?php else: ?>
                                        <span class="btn btn-secondary" style="cursor: default;">
                                            <i class="fas fa-lock"></i> Votre trajet
                                        </span>
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
                    <h3 class="footer-heading"><img src="../../images/off_logo.png" alt="logo" style="height: 40px;"></h3>
                    <p>Autotech est conçu pour centraliser et simplifier l'expérience automobile.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h4 class="footer-heading">Informations</h4>
                    <ul>
                        <li><a href="#">À propos</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">Termes</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h4 class="footer-heading">Support</h4>
                    <ul>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Aide</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h4 class="footer-heading">Contact</h4>
                    <p><i class="fas fa-phone mr-2"></i> +216 33 856 909</p>
                    <p><i class="fas fa-envelope mr-2"></i> AutoTech@gmail.tn</p>
                </div>
            </div>
            <div class="text-center pt-3" style="border-top: 1px solid var(--border-color);">
                <p>Copyright &copy; <script>document.write(new Date().getFullYear());</script> AutoTech</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
