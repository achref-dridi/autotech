<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';
require_once __DIR__ . '/../../controller/TechnicienController.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$pdo = Config::getConnexion();
$tablesExist = false;
$technicianCount = 0;
$message = '';

try {
    // Check if tables exist
    $result = $pdo->query("SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'autotech_db' AND (TABLE_NAME = 'technicien' OR TABLE_NAME = 'rendez_vous')");
    $tableCheck = $result->fetch();
    $tablesExist = $tableCheck['count'] >= 2;
    
    if ($tablesExist) {
        // Count technicians
        $techResult = $pdo->query("SELECT COUNT(*) as count FROM technicien");
        $techData = $techResult->fetch();
        $technicianCount = $techData['count'];
    }
} catch (Exception $e) {
    $message = 'Erreur lors de la vérification des tables';
}

// Handle form submission for inserting sample technicians
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'insert_sample_technicians') {
        try {
            $sql = "INSERT INTO technicien (nom, specialite, telephone, email, disponibilite) VALUES
                    ('Mohamed Ali', 'Diagnostic moteur', '98765432', 'ali.tech@autotech.tn', 'actif'),
                    ('Khaled Ben Salah', 'Réparation freins', '98765433', 'khaled.tech@autotech.tn', 'actif'),
                    ('Fatima Ezzahra', 'Électricité automobile', '98765434', 'fatima.tech@autotech.tn', 'actif'),
                    ('Nabil Jebali', 'Changement pneus', '98765435', 'nabil.tech@autotech.tn', 'actif'),
                    ('Amel Kareem', 'Révision générale', '98765436', 'amel.tech@autotech.tn', 'actif')";
            $pdo->exec($sql);
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> 5 techniciens exemples ont été insérés avec succès!</div>';
            $technicianCount = 5;
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Erreur: ' . $e->getMessage() . '</div>';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Rendez-Vous - AutoTech</title>
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .section-header {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .section-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .status-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(37, 99, 235, 0.05);
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .status-item:last-child {
            margin-bottom: 0;
        }

        .status-icon {
            font-size: 1.5rem;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .status-icon.success {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .status-icon.warning {
            background: rgba(251, 146, 60, 0.2);
            color: #fb923c;
        }

        .status-icon.error {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .status-text {
            flex: 1;
        }

        .status-label {
            font-weight: 600;
            color: var(--text-primary);
        }

        .status-info {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .setup-steps {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }

        .step {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .step:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .step-number {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            font-size: 1.1rem;
        }

        .step-content h3 {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .step-content p {
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }

        .code-block {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 0.5rem;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            color: var(--primary-light);
        }

        .alert {
            border: none;
            border-radius: 12px;
            border-left: 4px solid;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #86efac;
            border-left-color: #10b981;
        }

        .alert-warning {
            background: rgba(251, 146, 60, 0.1);
            color: #fed7aa;
            border-left-color: #fb923c;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border-left-color: #ef4444;
        }

        .btn-section {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            text-decoration: none;
            color: white;
        }

        .btn-secondary-custom {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-secondary);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-secondary-custom:hover {
            border-color: var(--text-secondary);
            color: var(--text-primary);
            text-decoration: none;
        }

        .checklist {
            list-style: none;
            padding: 0;
        }

        .checklist li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .checklist li i {
            color: var(--primary-light);
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
                    <?php if ($userController->estConnecte()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="mes-rendez-vous.php">Mes Rendez-Vous</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="prendre-rendez-vous.php">Prendre RDV</a>
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
            <h2><i class="fas fa-cog"></i> Configuration du Système Rendez-Vous</h2>
        </div>

        <?php echo $message; ?>

        <!-- Status Check -->
        <div class="status-card">
            <h3 style="margin-bottom: 1.5rem; color: var(--text-primary); font-weight: 700;">
                <i class="fas fa-heartbeat"></i> État du Système
            </h3>
            
            <div class="status-item">
                <div class="status-icon <?= $tablesExist ? 'success' : 'error' ?>">
                    <i class="fas fa-<?= $tablesExist ? 'check' : 'times' ?>-circle"></i>
                </div>
                <div class="status-text">
                    <div class="status-label">Tables de Base de Données</div>
                    <div class="status-info">
                        <?= $tablesExist ? '✓ Tables créées et opérationnelles' : '✗ Tables non trouvées - Configuration nécessaire' ?>
                    </div>
                </div>
            </div>

            <div class="status-item">
                <div class="status-icon <?= $technicianCount > 0 ? 'success' : 'warning' ?>">
                    <i class="fas fa-<?= $technicianCount > 0 ? 'check' : 'exclamation' ?>-circle"></i>
                </div>
                <div class="status-text">
                    <div class="status-label">Techniciens</div>
                    <div class="status-info">
                        <?= $technicianCount > 0 ? $technicianCount . ' technicien(s) disponible(s)' : 'Aucun technicien - Veuillez en ajouter' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Setup Instructions -->
        <div class="setup-steps">
            <h3 style="margin-bottom: 2rem; color: var(--text-primary); font-weight: 700;">
                <i class="fas fa-list"></i> Étapes d'Installation
            </h3>

            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Créer les Tables de Base de Données</h3>
                    <p>Exécutez les requêtes SQL suivantes dans votre gestionnaire de base de données (phpMyAdmin, MySQL Workbench, etc.):</p>
                    <div class="code-block">
CREATE TABLE IF NOT EXISTS technicien (
    id_technicien INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    specialite VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(255),
    disponibilite VARCHAR(50) DEFAULT 'actif',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rendez_vous (
    id_rdv INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_technicien INT UNSIGNED NOT NULL,
    id_utilisateur INT UNSIGNED NOT NULL,
    date_rdv DATETIME NOT NULL,
    type_intervention VARCHAR(255) NOT NULL,
    commentaire TEXT,
    statut VARCHAR(50) DEFAULT 'en attente',
    google_event_id VARCHAR(255),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rdv_technicien FOREIGN KEY (id_technicien) REFERENCES technicien(id_technicien) ON DELETE CASCADE,
    CONSTRAINT fk_rdv_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_technicien (id_technicien),
    INDEX idx_utilisateur (id_utilisateur),
    INDEX idx_date (date_rdv),
    UNIQUE KEY unique_google_event (google_event_id)
) ENGINE=InnoDB;
                    </div>
                </div>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Ajouter des Techniciens</h3>
                    <p>Vous pouvez utiliser les données exemples ou ajouter vos propres techniciens:</p>
                    <form method="POST" style="margin-top: 1rem;">
                        <input type="hidden" name="action" value="insert_sample_technicians">
                        <button type="submit" class="btn-primary-custom" onclick="return confirm('Cela insérera 5 techniciens exemples. Continuer?');">
                            <i class="fas fa-plus"></i> Insérer les Techniciens Exemples
                        </button>
                    </form>
                    <p style="margin-top: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                        <i class="fas fa-info-circle"></i> Cela ajoutera 5 techniciens de test. Vous pouvez les modifier plus tard.
                    </p>
                </div>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Fonctionnalités Disponibles</h3>
                    <p>Une fois configuré, vous pouvez:</p>
                    <ul class="checklist">
                        <li><i class="fas fa-check"></i> Voir la liste de tous les techniciens</li>
                        <li><i class="fas fa-check"></i> Prendre un rendez-vous avec un technicien</li>
                        <li><i class="fas fa-check"></i> Modifier vos rendez-vous</li>
                        <li><i class="fas fa-check"></i> Annuler vos rendez-vous</li>
                        <li><i class="fas fa-check"></i> Voir l'historique de vos rendez-vous</li>
                        <li><i class="fas fa-check"></i> Synchronisation automatique avec Google Calendar (optionnel)</li>
                    </ul>
                </div>
            </div>

            <div class="step">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h3>Configuration Google Calendar (Optionnel)</h3>
                    <p>Pour synchroniser les rendez-vous avec Google Calendar:</p>
                    <ol style="color: var(--text-muted);">
                        <li>Créer un projet dans Google Cloud Console</li>
                        <li>Activer l'API Google Calendar</li>
                        <li>Créer un compte de service</li>
                        <li>Télécharger le fichier JSON des identifiants</li>
                        <li>Placer le fichier dans <code style="color: var(--primary-light);">config/google-credentials.json</code></li>
                    </ol>
                    <p style="margin-top: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                        <i class="fas fa-info-circle"></i> Le système fonctionne sans Google Calendar. Cette étape est optionnelle.
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="btn-section">
            <a href="prendre-rendez-vous.php" class="btn-primary-custom">
                <i class="fas fa-calendar-plus"></i> Prendre un Rendez-Vous
            </a>
            <a href="mes-rendez-vous.php" class="btn-primary-custom">
                <i class="fas fa-calendar-alt"></i> Mes Rendez-Vous
            </a>
            <a href="technicians.php" class="btn-secondary-custom">
                <i class="fas fa-user-tie"></i> Voir les Techniciens
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
