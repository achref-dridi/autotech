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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$trajet = null;

if ($id > 0) {
    $trajet = $trajetController->getTrajetById($id);
    if (!$trajet || $trajet['id_utilisateur'] != $_SESSION['user_id']) {
        header('Location: mes-trajets.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trajetObj = new Trajet(
        $_SESSION['user_id'],
        $_POST['lieu_depart'],
        $_POST['lieu_arrivee'],
        $_POST['date_depart'],
        (int)$_POST['duree_minutes'],
        (float)$_POST['budget'],
        $_POST['description'] ?? '',
        (int)$_POST['places_demandees']
    );

    $result = $trajetController->updateTrajet($id, $trajetObj);

    if ($result['success']) {
        header('Location: mes-trajets.php?success=modifie');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier ma demande - AutoTech</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
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

        /* Map Modal Styles */
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
        }
        .leaflet-container {
            font-family: 'Poppins', sans-serif;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: stretch;
            width: 100%;
        }
        
        .input-group .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .btn-map {
            background: var(--primary-light);
            color: white;
            border: none;
            padding: 0 15px;
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-map:hover {
            background: var(--primary-color);
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

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
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
            <h1><i class="fas fa-edit mr-2"></i> Modifier ma demande</h1>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <?php if ($trajet): ?>
                <div class="form-container">
                    <form method="POST">
                        <div class="form-group">
                            <label for="lieu_depart">
                                <i class="fas fa-map-pin" style="color: var(--primary-light);"></i>
                                Lieu de départ
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="lieu_depart" name="lieu_depart" value="<?= htmlspecialchars($trajet['lieu_depart']) ?>" required>
                                <button type="button" class="btn-map" onclick="openMap('lieu_depart')">
                                    <i class="fas fa-map-marked-alt"></i> Carte
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="lieu_arrivee">
                                <i class="fas fa-map-pin" style="color: var(--primary-light); transform: rotate(180deg);"></i>
                                Lieu d'arrivée
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="lieu_arrivee" name="lieu_arrivee" value="<?= htmlspecialchars($trajet['lieu_arrivee']) ?>" required>
                                <button type="button" class="btn-map" onclick="openMap('lieu_arrivee')">
                                    <i class="fas fa-map-marked-alt"></i> Carte
                                </button>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="date_depart">
                                    <i class="fas fa-calendar" style="color: var(--primary-light);"></i>
                                    Date et heure
                                </label>
                                <input type="datetime-local" class="form-control" id="date_depart" name="date_depart" value="<?= date('Y-m-d\TH:i', strtotime($trajet['date_depart'])) ?>" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="duree_minutes">
                                    <i class="fas fa-hourglass" style="color: var(--primary-light);"></i>
                                    Durée (min)
                                </label>
                                <input type="number" class="form-control" id="duree_minutes" name="duree_minutes" value="<?= $trajet['duree_minutes'] ?>" min="1" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="budget">
                                    <i class="fas fa-coins" style="color: var(--primary-light);"></i>
                                    Budget (DT)
                                </label>
                                <input type="number" class="form-control" id="budget" name="budget" value="<?= $trajet['budget'] ?>" step="0.01" min="0" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="places_demandees">
                                    <i class="fas fa-users" style="color: var(--primary-light);"></i>
                                    Places demandées
                                </label>
                                <input type="number" class="form-control" id="places_demandees" name="places_demandees" value="<?= $trajet['places_demandees'] ?>" min="1" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">
                                <i class="fas fa-align-left" style="color: var(--primary-light);"></i>
                                Description
                            </label>
                            <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($trajet['description']) ?></textarea>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                        </button>
                        <a href="mes-trajets.php" class="btn-cancel">
                            <i class="fas fa-times mr-2"></i> Annuler
                        </a>
                    </form>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 4rem 0;">
                    <p style="color: var(--text-muted); margin-bottom: 2rem;">Trajet introuvable</p>
                    <a href="mes-trajets.php" style="color: var(--primary-light); text-decoration: none;">Retour à mes trajets</a>
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

    <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="background: var(--card-bg); color: var(--text-primary);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title">Choisir un emplacement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: var(--text-primary);">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="map"></div>
                    <p class="text-muted mt-2"><small>Cliquez sur la carte pour sélectionner une position.</small></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        let map;
        let marker;
        let currentInputId;

        function initMap() {
            // Centered on Tunisia
            map = L.map('map').setView([33.8869, 9.5375], 6);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            map.on('click', function(e) {
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker(e.latlng).addTo(map);
                getAddress(e.latlng.lat, e.latlng.lng);
            });
        }

        function openMap(inputId) {
            currentInputId = inputId;
            $('#mapModal').modal('show');
            setTimeout(function() {
                map.invalidateSize();
            }, 500);
        }

        function getAddress(lat, lng) {
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.display_name) {
                        document.getElementById(currentInputId).value = data.display_name;
                        $('#mapModal').modal('hide');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        $(document).ready(function() {
            initMap();
        });
    </script>
</body>
</html>
