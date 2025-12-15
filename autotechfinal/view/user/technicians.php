<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../controller/TechnicienController.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$technicianController = new TechnicienController();
$techniciens = $technicianController->getAllTechniciens();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Techniciens - AutoTech</title>
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .section-header {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 3rem;
            border: 1px solid var(--border-color);
            text-align: center;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .section-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin: 0;
        }

        .grid-technicians {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .technician-card {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .technician-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.3);
            border-color: var(--primary-color);
        }

        .tech-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .tech-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
        }

        .tech-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }

        .tech-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            color: white;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-actif {
            background: rgba(16, 185, 129, 0.3);
            color: #86efac;
        }

        .tech-body {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .tech-specialty {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .tech-specialty i {
            color: var(--primary-light);
            font-size: 1.2rem;
        }

        .tech-specialty-text {
            font-size: 1rem;
            color: var(--text-secondary);
        }

        .tech-contact {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            flex: 1;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .contact-item i {
            color: var(--primary-light);
            width: 20px;
            text-align: center;
        }

        .contact-item a {
            color: var(--primary-light);
            text-decoration: none;
            transition: all 0.2s;
        }

        .contact-item a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .btn-book {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            width: 100%;
        }

        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            text-decoration: none;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--border-color);
        }

        .empty-state i {
            font-size: 5rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        .empty-state h3 {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--text-muted);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
        }

        .btn-primary-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../public/index.php"><img src="../../images/off_logo.png" alt="AutoTech"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../public/index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../public/voitures.php">Voitures</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../public/boutiques.php">Boutiques</a>
                    </li>
                    <?php if ($userController->estConnecte()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="mes-boutiques.php">Mes Boutiques</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="mes-rendez-vous.php">Rendez-Vous</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profil.php">Mon Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../controller/UtilisateurController.php?action=deconnexion">Déconnexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-user-tie"></i> Nos Techniciens</h2>
            <p>Rencontrez nos experts et réservez votre rendez-vous</p>
        </div>

        <div class="action-buttons">
            <a href="mes-rendez-vous.php" class="btn-action btn-primary-action">
                <i class="fas fa-calendar-alt"></i> Mes Rendez-Vous
            </a>
            <a href="prendre-rendez-vous.php" class="btn-action btn-primary-action">
                <i class="fas fa-plus"></i> Prendre Rendez-Vous
            </a>
        </div>

        <?php if (!empty($techniciens)): ?>
            <div class="grid-technicians">
                <?php foreach ($techniciens as $tech): ?>
                    <div class="technician-card">
                        <div class="tech-header">
                            <div class="tech-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="tech-name"><?= htmlspecialchars($tech['nom']) ?></div>
                            <span class="tech-status status-<?= strtolower($tech['disponibilite']) ?>">
                                <i class="fas fa-circle"></i> <?= ucfirst($tech['disponibilite']) ?>
                            </span>
                        </div>

                        <div class="tech-body">
                            <div class="tech-specialty">
                                <i class="fas fa-tools"></i>
                                <span class="tech-specialty-text"><?= htmlspecialchars($tech['specialite']) ?></span>
                            </div>

                            <div class="tech-contact">
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <a href="tel:<?= htmlspecialchars($tech['telephone']) ?>">
                                        <?= htmlspecialchars($tech['telephone']) ?>
                                    </a>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <a href="mailto:<?= htmlspecialchars($tech['email']) ?>">
                                        <?= htmlspecialchars($tech['email']) ?>
                                    </a>
                                </div>
                            </div>

                            <a href="prendre-rendez-vous.php" class="btn-book">
                                <i class="fas fa-calendar-plus"></i> Réserver
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-user-tie"></i>
                <h3>Aucun Technicien Disponible</h3>
                <p>Les techniciens seront disponibles bientôt. Veuillez revenir plus tard.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
