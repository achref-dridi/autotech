<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$userController = new UtilisateurController();

// Protection: Vérifier connexion
if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$utilisateur = $userController->getUtilisateurConnecte();
if (!$utilisateur) {
    header('Location: ../auth/login.php');
    exit();
}
$message = '';
$messageType = '';

// ======================
// UPDATE PROFIL
// ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'])
    && $_POST['action'] === 'update_profil'
) {
    $photo_profil = null;

    // Upload photo
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === 0) {
        $uploadDir = __DIR__ . '/../../uploads/profils/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($_FILES['photo_profil']['name'], PATHINFO_EXTENSION);
        $photo_profil = 'profil_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;

        move_uploaded_file(
            $_FILES['photo_profil']['tmp_name'],
            $uploadDir . $photo_profil
        );
    }

    $result = $userController->updateProfil(
        $_SESSION['user_id'],
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['telephone'],
        $_POST['adresse'],
        $_POST['ville'],
        $_POST['code_postal'],
        $photo_profil
    );

    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'danger';

    // Reload user
    $utilisateur = $userController->getUtilisateurConnecte();
}

// ======================
// CHANGE PASSWORD
// ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'])
    && $_POST['action'] === 'change_password'
) {
    $result = $userController->changerMotDePasse(
        $_SESSION['user_id'],
        $_POST['ancien_mot_de_passe'],
        $_POST['nouveau_mot_de_passe']
    );

    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'danger';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - AutoTech</title>
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
            padding: 0.35rem 0.75rem !important;
            font-size: 0.9rem;
            border-radius: 6px;
        }

        .nav-link:hover, .nav-item.active .nav-link {
            color: var(--primary-light) !important;
            background: rgba(37, 99, 235, 0.1);
        }

        .hero-section {
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(30, 41, 59, 0.8)),
                        url('../../images/bg_1.jpg');
            background-size: cover;
            background-position: center;
            padding: 4rem 0 3rem;
            position: relative;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.1));
            color: #10b981;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.1));
            color: #ef4444;
            border-left: 4px solid #ef4444;
        }

        .profile-section {
            padding: 3rem 0;
        }

        .profile-header-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary-color);
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
            margin: 0 auto 1.5rem;
            display: block;
        }

        .profile-initials {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
        }

        .profile-header-card h3 {
            color: var(--text-primary);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .profile-header-card .text-muted {
            color: var(--text-muted) !important;
            font-size: 1rem;
        }

        .form-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .form-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.15);
        }

        .form-card h4 {
            color: var(--text-primary);
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .form-card h4 i {
            color: var(--primary-light);
            margin-right: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
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

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        input[type="file"].form-control {
            padding: 0.5rem;
        }

        input[type="file"].form-control::file-selector-button {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            margin-right: 1rem;
            transition: all 0.3s ease;
        }

        input[type="file"].form-control::file-selector-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary i {
            margin-right: 0.5rem;
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
            .profile-header-card {
                padding: 2rem 1.5rem;
            }

            .profile-avatar,
            .profile-initials {
                width: 100px;
                height: 100px;
                font-size: 2rem;
            }

            .form-card {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
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
                    <li class="nav-item"><a class="nav-link" href="../public/trajets.php">Trajets</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-boutiques.php">Mes Boutiques</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-vehicules.php">Mes Véhicules</a></li>
                    <li class="nav-item"><a class="nav-link" href="mes-trajets.php">Mes Trajets</a></li>
                    <li class="nav-item active"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section"></section>

    <section class="profile-section">
        <div class="container">

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="profile-header-card">
                        <?php
                        $photo = $utilisateur['photo_profil'] ?? '';
                        $photoFile = __DIR__ . '/../../uploads/profils/' . $photo;
                        ?>

                        <?php if (!empty($photo) && file_exists($photoFile)): ?>
                            <img src="../../uploads/profils/<?= htmlspecialchars($photo) ?>" 
                                 alt="Photo de profil"
                                 class="profile-avatar">
                        <?php else: ?>
                            <div class="profile-initials">
                                <?= strtoupper(substr($utilisateur['prenom'],0,1) . substr($utilisateur['nom'],0,1)) ?>
                            </div>
                        <?php endif; ?>

                        <h3><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></h3>
                        <p class="text-muted">
                            <i class="fas fa-envelope mr-2"></i>
                            <?= htmlspecialchars($utilisateur['email']) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-card">
                        <h4>
                            <i class="fas fa-user-edit"></i>
                            Informations personnelles
                        </h4>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update_profil">

                            <div class="form-group">
                                <label>
                                    <i class="fas fa-user mr-1"></i> Nom
                                </label>
                                <input class="form-control" name="nom" required
                                       value="<?= htmlspecialchars($utilisateur['nom']) ?>">
                            </div>

                            <div class="form-group">
                                <label>
                                    <i class="fas fa-user mr-1"></i> Prénom
                                </label>
                                <input class="form-control" name="prenom" required
                                       value="<?= htmlspecialchars($utilisateur['prenom']) ?>">
                            </div>

                            <div class="form-group">
                                <label>
                                    <i class="fas fa-phone mr-1"></i> Téléphone
                                </label>
                                <input class="form-control" name="telephone"
                                       value="<?= htmlspecialchars($utilisateur['telephone'] ?? '') ?>"
                                       placeholder="+216 XX XXX XXX">
                            </div>

                            <div class="form-group">
                                <label>
                                    <i class="fas fa-map-marker-alt mr-1"></i> Adresse
                                </label>
                                <textarea class="form-control" name="adresse" 
                                          placeholder="Votre adresse complète"><?= htmlspecialchars($utilisateur['adresse'] ?? '') ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>
                                    <i class="fas fa-city mr-1"></i> Ville
                                </label>
                                <input class="form-control" name="ville"
                                       value="<?= htmlspecialchars($utilisateur['ville'] ?? '') ?>"
                                       placeholder="Ex: Tunis">
                            </div>

                            <div class="form-group">
                                <label>
                                    <i class="fas fa-mail-bulk mr-1"></i> Code Postal
                                </label>
                                <input class="form-control" name="code_postal"
                                       value="<?= htmlspecialchars($utilisateur['code_postal'] ?? '') ?>"
                                       placeholder="Ex: 1000">
                            </div>

                            <div class="form-group">
                                <label>
                                    <i class="fas fa-camera mr-1"></i> Photo de profil
                                </label>
                                <input type="file" class="form-control" name="photo_profil" 
                                       accept="image/png, image/jpeg, image/jpg, image/webp">
                            </div>

                            <button class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-card">
                        <h4>
                            <i class="fas fa-lock"></i>
                            Changer mot de passe
                        </h4>
                        <form method="POST">
                            <input type="hidden" name="action" value="change_password">

                            <div class="form-group">
                                <label>
                                    <i class="fas fa-key mr-1"></i> Ancien mot de passe
                                </label>
                                <input type="password" class="form-control" name="ancien_mot_de_passe" required
                                       placeholder="Entrez votre ancien mot de passe">
                            </div>

                            <div class="form-group">
                                <label>
                                    <i class="fas fa-key mr-1"></i> Nouveau mot de passe
                                </label>
                                <input type="password" class="form-control" name="nouveau_mot_de_passe" required
                                       placeholder="Entrez votre nouveau mot de passe">
                            </div>

                            <button class="btn btn-primary">
                                <i class="fas fa-check"></i> Changer le mot de passe
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Reporting Section -->
            <?php
            require_once __DIR__ . '/../../controller/SignalementController.php';
            require_once __DIR__ . '/../../controller/VehiculeController.php';
            require_once __DIR__ . '/../../controller/BoutiqueController.php';
            
            $signalementController = new SignalementController();
            $vehiculeController = new VehiculeController();
            $boutiqueController = new BoutiqueController(); 
            
            // Handle Report Submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'nouveau_signalement') {
                $id_objet = null;
                if ($_POST['type_objet'] === 'vehicule') {
                    $id_objet = $_POST['id_objet_vehicule'] ?? null;
                } elseif ($_POST['type_objet'] === 'boutique') {
                    $id_objet = $_POST['id_objet_boutique'] ?? null;
                }

                $data = [
                    'type_objet' => $_POST['type_objet'],
                    'id_objet' => $id_objet,
                    'sujet' => $_POST['sujet'],
                    'description' => $_POST['description']
                ];
                $res = $signalementController->ajouterSignalement($_SESSION['user_id'], $data);
                $message = $res['message'];
                $messageType = $res['success'] ? 'success' : 'danger';
            }
            
            $mesSignalements = $signalementController->getMesSignalements($_SESSION['user_id']);
            $vehicules = $vehiculeController->getAllVehicules(); 
            $boutiques = $boutiqueController->getAllBoutiques();
            ?>
            
            <div class="row mt-5">
                <div class="col-12">
                    <div class="form-card">
                        <h4><i class="fas fa-exclamation-triangle"></i> Mes Signalements</h4>
                        <?php if (empty($mesSignalements)): ?>
                            <p class="text-muted text-center py-4">Aucun signalement effectué.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Sujet</th>
                                            <th>Type</th>
                                            <th>Statut</th>
                                            <th>Réponse Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mesSignalements as $sig): ?>
                                            <tr>
                                                <td><?= date('d/m/Y', strtotime($sig['date_creation'])) ?></td>
                                                <td><?= htmlspecialchars($sig['sujet']) ?></td>
                                                <td>
                                                    <span class="badge badge-secondary"><?= ucfirst($sig['type_objet']) ?></span>
                                                    <?php if($sig['id_objet']): ?> 
                                                        <small class="text-muted">#<?= $sig['id_objet'] ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $badgeClass = 'secondary';
                                                    if ($sig['statut'] === 'traite') $badgeClass = 'success';
                                                    if ($sig['statut'] === 'ignore') $badgeClass = 'danger';
                                                    if ($sig['statut'] === 'en_attente') $badgeClass = 'warning';
                                                    ?>
                                                    <span class="badge badge-<?= $badgeClass ?>"><?= ucfirst(str_replace('_', ' ', $sig['statut'])) ?></span>
                                                </td>
                                                <td>
                                                    <?php if($sig['reponse_admin']): ?>
                                                        <small><?= htmlspecialchars($sig['reponse_admin']) ?></small>
                                                        <?php if($sig['piece_jointe_admin']): ?>
                                                            <br><a href="../../uploads/signalements/<?= htmlspecialchars($sig['piece_jointe_admin']) ?>" target="_blank" class="text-primary"><i class="fas fa-paperclip"></i> Pièce jointe</a>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-card">
                        <h4><i class="fas fa-bullhorn"></i> Signaler un problème</h4>
                        <form method="POST">
                            <input type="hidden" name="action" value="nouveau_signalement">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Type de problème</label>
                                    <select class="form-control" name="type_objet" id="type_objet" required onchange="toggleObjetSelect()">
                                        <option value="autre">Général / Autre</option>
                                        <option value="vehicule">Véhicule</option>
                                        <option value="boutique">Boutique</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <!-- Select for Vehicles -->
                                    <div id="group_vehicule" style="display:none;">
                                        <label>Choisir le Véhicule</label>
                                        <select class="form-control" name="id_objet_vehicule">
                                            <option value="">-- Sélectionner un véhicule --</option>
                                            <?php foreach ($vehicules as $v): ?>
                                                <option value="<?= $v['id_vehicule'] ?>">
                                                    <?= htmlspecialchars($v['marque'] . ' ' . $v['modele'] . ' #' . $v['id_vehicule']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Select for Boutiques -->
                                    <div id="group_boutique" style="display:none;">
                                        <label>Choisir la Boutique</label>
                                        <select class="form-control" name="id_objet_boutique">
                                            <option value="">-- Sélectionner une boutique --</option>
                                            <?php foreach ($boutiques as $b): ?>
                                                <option value="<?= $b['id_boutique'] ?>">
                                                    <?= htmlspecialchars($b['nom_boutique'] . ' #' . $b['id_boutique']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Sujet</label>
                                <select class="form-control" name="sujet" required>
                                    <option value="">Sélectionnez un motif...</option>
                                    <option value="fraude">Fraude / Arnaque</option>
                                    <option value="technique_site">Problème technique (Site)</option>
                                    <option value="comportement">Comportement inapproprié</option>
                                    <option value="vehicule_non_conforme">Véhicule non conforme</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Description détaillée</label>
                                <textarea class="form-control" name="description" rows="4" required placeholder="Expliquez le problème en détail..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-warning text-white">
                                <i class="fas fa-paper-plane"></i> Envoyer le signalement
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
    
    <script>
    function toggleObjetSelect() {
        const type = document.getElementById('type_objet').value;
        const groupVehicule = document.getElementById('group_vehicule');
        const groupBoutique = document.getElementById('group_boutique');
        
        // Hide all first
        groupVehicule.style.display = 'none';
        groupBoutique.style.display = 'none';
        
        if (type === 'vehicule') {
            groupVehicule.style.display = 'block';
        } else if (type === 'boutique') {
            groupBoutique.style.display = 'block';
        }
    }
    
    // Run on load to set initial state
    document.addEventListener('DOMContentLoaded', toggleObjetSelect);
    </script>
    
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
