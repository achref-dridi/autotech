<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../controller/RendezVousController.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$rdvController = new RendezVousController();
$mesRendezVous = $rdvController->getRendezVousByUtilisateur($_SESSION['user_id']);

// Handle cancellation
if (isset($_GET['annuler']) && is_numeric($_GET['annuler'])) {
    $id = (int)$_GET['annuler'];
    $rdv = $rdvController->getRendezVousById($id);
    
    if ($rdv && $rdv['id_utilisateur'] == $_SESSION['user_id']) {
        $result = $rdvController->deleteRendezVous($id);
        if ($result['success']) {
            header('Location: mes-rendez-vous.php?success=supprime');
            exit();
        }
    }
}

$message = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'ajoute') {
        $message = 'Rendez-vous créé avec succès!';
    } elseif ($_GET['success'] === 'supprime') {
        $message = 'Rendez-vous annulé.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Rendez-Vous - AutoTech</title>
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
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        .navbar .container {
            max-width: 1140px;
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

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            flex: 1;
        }

        .section-header {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .section-header p {
            color: var(--text-muted);
            margin: 0;
        }

        .btn-add {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            color: white;
            text-decoration: none;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.1));
            color: #10b981;
            border: none;
            border-left: 4px solid #10b981;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .rdv-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .rdv-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.3);
            border-color: var(--primary-color);
        }

        .rdv-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .rdv-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .rdv-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-en-attente {
            background: rgba(251, 146, 60, 0.2);
            color: #fb923c;
        }

        .status-confirme {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .status-annule {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .rdv-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .rdv-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: rgba(37, 99, 235, 0.05);
            border-radius: 8px;
        }

        .rdv-item i {
            color: var(--primary-light);
            font-size: 1.2rem;
        }

        .rdv-item-content {
            display: flex;
            flex-direction: column;
        }

        .rdv-item-label {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .rdv-item-value {
            font-weight: 600;
            color: var(--text-primary);
        }

        .rdv-commentaire {
            background: rgba(37, 99, 235, 0.05);
            border-left: 3px solid var(--primary-color);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .rdv-commentaire p {
            margin: 0;
            color: var(--text-secondary);
        }

        .rdv-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.75rem 1.25rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .btn-edit {
            background: rgba(251, 146, 60, 0.2);
            color: #fb923c;
            border: 1px solid rgba(251, 146, 60, 0.3);
        }

        .btn-edit:hover {
            background: rgba(251, 146, 60, 0.3);
            color: #fb923c;
            text-decoration: none;
            border-color: #fb923c;
        }

        .btn-cancel {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-cancel:hover {
            background: rgba(239, 68, 68, 0.3);
            color: #ef4444;
            text-decoration: none;
            border-color: #ef4444;
        }

        .empty-state {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 5rem 2rem;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .empty-state-icon {
            font-size: 5rem;
            margin-bottom: 2rem;
            color: var(--text-muted);
        }

        .empty-state h2 {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        footer {
            background: rgba(15, 23, 42, 0.95);
            color: var(--text-secondary);
            padding: 3rem 0 1rem;
            margin-top: 4rem;
            border-top: 1px solid var(--border-color);
        }

        footer .container {
            max-width: 1140px;
        }

        footer h2 {
            color: var(--text-primary);
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        footer img {
            height: 40px;
            filter: brightness(1.1);
        }

        footer p, footer li {
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.8;
        }

        footer a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--primary-light);
        }

        footer ul {
            list-style: none;
            padding: 0;
        }

        footer .icon {
            color: var(--primary-light);
            margin-right: 0.5rem;
        }

        @media (max-width: 768px) {
            .section-header {
                padding: 1.5rem;
            }

            .section-header h2 {
                font-size: 1.5rem;
            }

            .rdv-card {
                padding: 1rem;
            }

            .rdv-title {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../public/index.php">
                <img src="../../images/off_logo.png" alt="AutoTech">
            </a>
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
                        <li class="nav-item active">
                            <a class="nav-link" href="prendre-rendez-vous.php">Rendez-Vous</a>
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

    <div class="main-container">
        <div class="section-header">
            <div>
                <h2>Mes Rendez-Vous</h2>
                <p>Gérez vos rendez-vous avec nos techniciens</p>
            </div>
            <a href="prendre-rendez-vous.php" class="btn-add">
                <i class="fas fa-plus"></i> Nouveau Rendez-Vous
            </a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if (empty($mesRendezVous)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h2>Aucun rendez-vous</h2>
                <p>Vous n'avez pas encore de rendez-vous. Cliquez sur le bouton ci-dessus pour en créer un.</p>
                <a href="prendre-rendez-vous.php" class="btn-add">
                    <i class="fas fa-plus"></i> Prendre Rendez-Vous
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($mesRendezVous as $rdv): ?>
                <div class="rdv-card">
                    <div class="rdv-header">
                        <div>
                            <h3 class="rdv-title">
                                <i class="fas fa-wrench"></i> <?= htmlspecialchars($rdv['type_intervention']) ?>
                            </h3>
                            <p style="color: var(--text-muted); margin-top: 0.25rem;">
                                <i class="fas fa-user"></i> Technicien: <strong><?= htmlspecialchars($rdv['technicien_nom']) ?></strong>
                            </p>
                        </div>
                        <span class="rdv-status status-<?= strtolower($rdv['statut']) ?>">
                            <i class="fas fa-circle"></i>
                            <?= ucfirst($rdv['statut']) ?>
                        </span>
                    </div>

                    <div class="rdv-info">
                        <div class="rdv-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div class="rdv-item-content">
                                <span class="rdv-item-label">Date & Heure</span>
                                <span class="rdv-item-value"><?= date('d/m/Y à H:i', strtotime($rdv['date_rdv'])) ?></span>
                            </div>
                        </div>
                        <div class="rdv-item">
                            <i class="fas fa-stethoscope"></i>
                            <div class="rdv-item-content">
                                <span class="rdv-item-label">Spécialité</span>
                                <span class="rdv-item-value"><?= htmlspecialchars($rdv['specialite']) ?></span>
                            </div>
                        </div>
                        <div class="rdv-item">
                            <i class="fas fa-phone"></i>
                            <div class="rdv-item-content">
                                <span class="rdv-item-label">Téléphone</span>
                                <span class="rdv-item-value"><?= htmlspecialchars($rdv['telephone']) ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($rdv['commentaire']): ?>
                        <div class="rdv-commentaire">
                            <p><strong>Notes:</strong> <?= htmlspecialchars($rdv['commentaire']) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="rdv-actions">
                        <a href="modifier-rendez-vous.php?id=<?= $rdv['id_rdv'] ?>" class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="mes-rendez-vous.php?annuler=<?= $rdv['id_rdv'] ?>" class="btn-action btn-cancel" 
                           onclick="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous?');">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <footer>
        <div class="container">
            <div class="row mb-5">
                <div class="col-md">
                    <div class="mb-4">
                        <h2><a href="#"><img src="../../images/off_logo.png" alt="logo.png" id="img_logo"></a></h2>
                        <p>Autotech est conçu pour centraliser et simplifier l'expérience automobile dans un environnement digital de pointe.</p>
                    </div>
                </div>
                <div class="col-md">
                    <div class="mb-4">
                        <h2>Vous avez des Questions?</h2>
                        <div class="mb-3">
                            <ul>
                                <li><span class="icon"><i class="fas fa-map-marker-alt"></i></span><span>Esprit, Ariana sogra, Ariana, Tunisie</span></li>
                                <li><a href="#"><span class="icon"><i class="fas fa-phone"></i></span><span>+216 33 856 909</span></a></li>
                                <li><a href="#"><span class="icon"><i class="fas fa-envelope"></i></span><span>AutoTech@gmail.tn</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> Tous droits réservés | AutoTech</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>