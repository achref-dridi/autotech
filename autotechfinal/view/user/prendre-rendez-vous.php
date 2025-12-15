<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../controller/TechnicienController.php';
require_once __DIR__ . '/../../controller/RendezVousController.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$technicianController = new TechnicienController();
$techniciens = $technicianController->getAllTechniciens();

$message = '';
$messageType = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_technicien = isset($_POST['id_technicien']) ? (int)$_POST['id_technicien'] : 0;
    $date_rdv = isset($_POST['date_rdv']) ? $_POST['date_rdv'] : '';
    $type_intervention = isset($_POST['type_intervention']) ? trim($_POST['type_intervention']) : '';
    $commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : '';

    // Validation
    if (!$id_technicien) {
        $errors[] = 'Veuillez sélectionner un technicien.';
    }
    
    if (!$date_rdv) {
        $errors[] = 'Veuillez sélectionner une date et heure.';
    } else {
        $dateObj = DateTime::createFromFormat('Y-m-d\TH:i', $date_rdv);
        if (!$dateObj) {
            $errors[] = 'Format de date invalide.';
        } else {
            $rdvTime = strtotime($date_rdv);
            $now = time();
            $twoHours = 2 * 3600;
            
            if ($rdvTime <= $now + $twoHours) {
                $errors[] = 'Le rendez-vous doit être fixé au moins 2 heures à l\'avance.';
            }
        }
    }
    
    if (!$type_intervention) {
        $errors[] = 'Veuillez spécifier le type d\'intervention.';
    }

    // Si validation ok
    if (empty($errors)) {
        $rdvController = new RendezVousController();
        
        $rendezVous = new RendezVous($id_technicien, $_SESSION['user_id'], $date_rdv, $type_intervention);
        $rendezVous->setCommentaire($commentaire);

        $result = $rdvController->addRendezVous($rendezVous);
        
        if ($result['success']) {
            header('Location: mes-rendez-vous.php?success=ajoute');
            exit();
        } else {
            $messageType = 'danger';
            $message = $result['message'] ?? 'Erreur lors de la création du rendez-vous.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prendre Rendez-Vous - AutoTech</title>
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
            flex: 1;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-light);
            text-decoration: none;
            margin-bottom: 2rem;
            transition: all 0.3s;
        }

        .back-btn:hover {
            color: var(--primary-color);
            transform: translateX(-4px);
            text-decoration: none;
        }

        .form-container {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2.5rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .form-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .form-subtitle {
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        .alert {
            border: none;
            border-radius: 12px;
            border-left: 4px solid;
            margin-bottom: 2rem;
            padding: 1rem 1.5rem;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.1));
            color: #fca5a5;
            border-left-color: #ef4444;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.1));
            color: #86efac;
            border-left-color: #10b981;
        }

        .form-group label {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group label i {
            color: var(--primary-light);
        }

        .form-control {
            background: rgba(37, 99, 235, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(37, 99, 235, 0.08);
            border-color: var(--primary-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-control option {
            background: var(--card-bg);
            color: var(--text-primary);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .form-text {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .technician-card {
            background: rgba(37, 99, 235, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            margin-bottom: 1rem;
            display: block;
        }

        .technician-card input[type="radio"] {
            cursor: pointer;
            margin-right: 1rem;
        }

        .technician-card input[type="radio"]:checked {
            accent-color: var(--primary-color);
        }

        .technician-card.selected {
            background: rgba(37, 99, 235, 0.15);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .technician-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .technician-details {
            flex: 1;
        }

        .technician-name {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 1.1rem;
        }

        .technician-specialty {
            color: var(--primary-light);
            font-size: 0.9rem;
            margin: 0.25rem 0;
        }

        .technician-contact {
            display: flex;
            gap: 1.5rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .technician-contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .technician-contact-item i {
            color: var(--primary-light);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn-submit {
            flex: 1;
            min-width: 150px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            color: white;
            padding: 1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 12px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        }

        .btn-cancel {
            flex: 1;
            min-width: 150px;
            background: rgba(148, 163, 184, 0.2);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            padding: 1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-cancel:hover {
            background: rgba(148, 163, 184, 0.3);
            border-color: var(--text-secondary);
            color: var(--text-primary);
            text-decoration: none;
            transform: translateY(-2px);
        }

        .required {
            color: #ef4444;
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
            .form-container {
                padding: 1.5rem;
            }

            .form-title {
                font-size: 1.5rem;
            }

            .technician-contact {
                flex-direction: column;
                gap: 0.5rem;
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

    <div class="main-container">
        <a href="mes-rendez-vous.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Retour aux Rendez-Vous
        </a>

        <div class="form-container">
            <h1 class="form-title">
                <i class="fas fa-calendar-plus"></i> Prendre un Rendez-Vous
            </h1>
            <p class="form-subtitle">Sélectionnez un technicien et choisissez une date pour votre intervention</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul style="margin-bottom: 0; padding-left: 1.5rem;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" id="rdvForm">
                <!-- Sélection du Technicien -->
                <div class="form-group">
                    <label for="techniciens">
                        <i class="fas fa-user-tie"></i> Sélectionner un Technicien <span class="required">*</span>
                    </label>
                    <div id="techniciansContainer">
                        <?php if (!empty($techniciens)): ?>
                            <?php foreach ($techniciens as $tech): ?>
                                <label class="technician-card" id="card-<?= $tech['id_technicien'] ?>" style="cursor: pointer;">
                                    <input type="radio" name="id_technicien" value="<?= $tech['id_technicien'] ?>" 
                                           id="tech-<?= $tech['id_technicien'] ?>" 
                                           onchange="updateTechnicianCard(<?= $tech['id_technicien'] ?>)">
                                    <div class="technician-info">
                                        <div class="technician-details">
                                            <div class="technician-name"><?= htmlspecialchars($tech['nom']) ?></div>
                                            <div class="technician-specialty">
                                                <i class="fas fa-tools"></i> <?= htmlspecialchars($tech['specialite']) ?>
                                            </div>
                                            <div class="technician-contact">
                                                <div class="technician-contact-item">
                                                    <i class="fas fa-phone"></i>
                                                    <?= htmlspecialchars($tech['telephone']) ?>
                                                </div>
                                                <div class="technician-contact-item">
                                                    <i class="fas fa-envelope"></i>
                                                    <?= htmlspecialchars($tech['email']) ?>
                                                </div>
                                                <div class="technician-contact-item">
                                                    <i class="fas fa-clock"></i>
                                                    <?= ucfirst($tech['disponibilite']) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="color: var(--primary-light); font-weight: 600;">
                                            <i class="fas fa-check-circle" style="display: none; margin-right: 0.5rem;"></i>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i> Aucun technicien disponible pour le moment.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Date et Heure -->
                <div class="form-group">
                    <label for="date_rdv">
                        <i class="fas fa-calendar-alt"></i> Date et Heure <span class="required">*</span>
                    </label>
                    <input type="datetime-local" class="form-control" id="date_rdv" name="date_rdv" required>
                    <small class="form-text">
                        <i class="fas fa-info-circle"></i> Le rendez-vous doit être fixé au minimum 2 heures à l'avance
                    </small>
                </div>

                <!-- Type d'Intervention -->
                <div class="form-group">
                    <label for="type_intervention">
                        <i class="fas fa-wrench"></i> Type d'Intervention <span class="required">*</span>
                    </label>
                    <select class="form-control" id="type_intervention" name="type_intervention" required>
                        <option value="">-- Sélectionner un type --</option>
                        <option value="Diagnostic moteur">Diagnostic moteur</option>
                        <option value="Vidange et filtre">Vidange et filtre</option>
                        <option value="Réparation frein">Réparation frein</option>
                        <option value="Changement pneus">Changement pneus</option>
                        <option value="Révision générale">Révision générale</option>
                        <option value="Réparation électrique">Réparation électrique</option>
                        <option value="Nettoyage intérieur">Nettoyage intérieur</option>
                        <option value="Autre">Autre (à préciser dans les notes)</option>
                    </select>
                </div>

                <!-- Commentaires -->
                <div class="form-group">
                    <label for="commentaire">
                        <i class="fas fa-clipboard"></i> Notes Supplémentaires
                    </label>
                    <textarea class="form-control" id="commentaire" name="commentaire" 
                              placeholder="Décrivez votre problème ou vos besoins spécifiques..."></textarea>
                    <small class="form-text">Cela aidera le technicien à mieux préparer votre intervention</small>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check"></i> Confirmer le Rendez-Vous
                    </button>
                    <a href="mes-rendez-vous.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
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
    <script>
        function updateTechnicianCard(id) {
            // Remove selection from all cards
            document.querySelectorAll('.technician-card').forEach(card => {
                card.classList.remove('selected');
            });
            // Add selection to clicked card
            const card = document.getElementById('card-' + id);
            if (card) {
                card.classList.add('selected');
            }
        }

        // Set minimum date and time (now + 2 hours)
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            now.setHours(now.getHours() + 2);
            
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            document.getElementById('date_rdv').min = minDateTime;
        });

        document.getElementById('rdvForm').addEventListener('submit', function(e) {
            const dateInput = document.getElementById('date_rdv');
            const selectedTech = document.querySelector('input[name="id_technicien"]:checked');
            const typeInput = document.getElementById('type_intervention');

            if (!selectedTech || !selectedTech.value) {
                e.preventDefault();
                alert('Veuillez sélectionner un technicien');
                return false;
            }

            if (!dateInput.value) {
                e.preventDefault();
                alert('Veuillez sélectionner une date et heure');
                return false;
            }

            if (!typeInput.value) {
                e.preventDefault();
                alert('Veuillez sélectionner un type d\'intervention');
                return false;
            }
        });
    </script>
</body>
</html>