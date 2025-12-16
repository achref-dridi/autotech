<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/TrajetController.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../model/Trajet.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$trajetController = new TrajetController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trajet = new Trajet(
        $_SESSION['user_id'],
        $_POST['lieu_depart'],
        $_POST['lieu_arrivee'],
        $_POST['date_depart'],
        (int)$_POST['duree_minutes'],
        (float)$_POST['prix'],
        $_POST['description'] ?? '',
        (int)$_POST['places_disponibles']
    );

    $result = $trajetController->addTrajet($trajet);

    if ($result['success']) {
        header('Location: mes-trajets.php?success=cree');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Trajet - AutoTech</title>
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

        .form-container {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            max-width: 700px;
            margin: 0 auto;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
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

        .form-control::placeholder {
            color: var(--text-muted);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .input-group .form-control {
            border-right: none;
        }

        .input-group-append .btn {
            border: 1px solid var(--border-color);
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-light);
        }

        .form-row {
            margin-bottom: 1rem;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 2rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-cancel {
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            color: var(--text-secondary);
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 1rem;
        }

        .btn-cancel:hover {
            border-color: var(--primary-color);
            color: var(--primary-light);
            text-decoration: none;
        }

        .info-box {
            background: rgba(37, 99, 235, 0.1);
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
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

            .form-container {
                padding: 1.5rem;
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
                    <li class="nav-item"><a class="nav-link" href="mes-boutiques.php">Mes Boutiques</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-trajets.php">Mes Trajets</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-plus-circle mr-2"></i> Ajouter un Trajet</h1>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="form-container">
                <div class="info-box">
                    <i class="fas fa-info-circle mr-2"></i>
                    Créez un trajet pour partager votre route avec d'autres utilisateurs. Remplissez les informations ci-dessous.
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label for="lieu_depart">
                            <i class="fas fa-map-pin" style="color: var(--primary-light);"></i>
                            Lieu de départ
                        </label>
                        <input type="text" class="form-control" id="lieu_depart" name="lieu_depart" placeholder="Ex: Tunis" required>
                    </div>

                    <div class="form-group">
                        <label for="lieu_arrivee">
                            <i class="fas fa-map-pin" style="color: var(--primary-light); transform: rotate(180deg);"></i>
                            Lieu d'arrivée
                        </label>
                        <input type="text" class="form-control" id="lieu_arrivee" name="lieu_arrivee" placeholder="Ex: Sfax" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="date_depart">
                                <i class="fas fa-calendar" style="color: var(--primary-light);"></i>
                                Date et heure de départ
                            </label>
                            <input type="datetime-local" class="form-control" id="date_depart" name="date_depart" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="duree_minutes">
                                <i class="fas fa-hourglass" style="color: var(--primary-light);"></i>
                                Durée (minutes)
                            </label>
                            <input type="number" class="form-control" id="duree_minutes" name="duree_minutes" placeholder="Ex: 180" min="1" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="prix">
                                <i class="fas fa-tag" style="color: var(--primary-light);"></i>
                                Prix (DT)
                            </label>
                            <input type="number" class="form-control" id="prix" name="prix" placeholder="Ex: 50" step="0.01" min="0" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="places_disponibles">
                                <i class="fas fa-users" style="color: var(--primary-light);"></i>
                                Places disponibles
                            </label>
                            <input type="number" class="form-control" id="places_disponibles" name="places_disponibles" value="1" min="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">
                            <i class="fas fa-align-left" style="color: var(--primary-light);"></i>
                            Description (optionnel)
                        </label>
                        <textarea class="form-control" id="description" name="description" placeholder="Décrivez votre trajet, conditions spéciales, etc..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check mr-2"></i> Créer le trajet
                    </button>
                    <a href="mes-trajets.php" class="btn-cancel">
                        <i class="fas fa-times mr-2"></i> Annuler
                    </a>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <h3 style="color: var(--text-primary); font-weight: 600; margin-bottom: 1rem;">AutoTech</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Partage de trajets et location de véhicules.</p>
                </div>
            </div>
            <div class="text-center pt-3" style="border-top: 1px solid var(--border-color);">
                <p style="color: var(--text-muted); font-size: 0.9rem;">Copyright &copy; <script>document.write(new Date().getFullYear());</script> AutoTech</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
