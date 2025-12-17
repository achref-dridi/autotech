<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/TrajetController.php';
require_once __DIR__ . '/../../controller/PropositionController.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$idTrajet = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$trajetController = new TrajetController();
$propController = new PropositionController();

$trajet = $trajetController->getTrajetById($idTrajet);

// Verify ownership
if (!$trajet || $trajet['id_utilisateur'] != $_SESSION['user_id']) {
    header('Location: mes-trajets.php');
    exit();
}

$propositions = $propController->getPropositionsByTrajet($idTrajet);

// Handle accept
if (isset($_GET['accept']) && is_numeric($_GET['accept'])) {
    $propId = (int)$_GET['accept'];
    $propController->acceptProposition($propId);
    header("Location: voir-propositions.php?id=$idTrajet&success=acceptee");
    exit();
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $propId = (int)$_GET['delete'];
    $propController->deleteProposition($propId);
    header("Location: voir-propositions.php?id=$idTrajet&success=supprimee");
    exit();
}
// Handle reject
if (isset($_GET['reject']) && is_numeric($_GET['reject'])) {
    $propId = (int)$_GET['reject'];
    $propController->rejectProposition($propId);
    header("Location: voir-propositions.php?id=$idTrajet&success=refusee");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propositions - AutoTech</title>
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

        .navbar-brand img { height: 40px; filter: brightness(1.1); }
        .nav-link { color: var(--text-secondary) !important; transition: all 0.3s ease; }
        .nav-link:hover { color: var(--primary-light) !important; }

        .container-content { padding: 4rem 0; }
        
        .prop-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .driver-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .driver-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-light);
        }

        .driver-details h5 { margin: 0; color: var(--text-primary); font-weight: 600; }
        .driver-details p { margin: 0; color: var(--text-secondary); font-size: 0.9rem; }

        .prop-details {
            text-align: right;
            border-left: 1px solid var(--border-color);
            padding-left: 1.5rem;
        }

        .price-tag {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-light);
        }

        .message-box {
            background: rgba(15, 23, 42, 0.5);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            width: 100%;
            font-style: italic;
            color: var(--text-secondary);
        }

        .btn-accept {
            background: #10b981;
            color: white;
            border: none;
        }
        .btn-delete {
            background: #ef4444;
            color: white;
            border: none;
        }
        .btn-reject {
            background: #f59e0b; /* Amber/Orange for reject */
            color: white;
            border: none;
        }
            color: white;
            border: none;
        }

        @media (max-width: 768px) {
            .prop-details { border-left: none; padding-left: 0; text-align: left; width: 100%; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../public/index.php"><img src="../../images/off_logo.png" alt="logo"></a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="mes-trajets.php">Retour à mes trajets</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container container-content">
        <h2 class="mb-4">Propositions pour votre trajet</h2>
        <p class="text-muted mb-5">
            <?= htmlspecialchars($trajet['lieu_depart']) ?> <i class="fas fa-arrow-right mx-2"></i> <?= htmlspecialchars($trajet['lieu_arrivee']) ?>
        </p>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Action effectuée avec succès.</div>
        <?php endif; ?>

        <?php if (empty($propositions)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Aucune proposition pour le moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($propositions as $prop): ?>
                <div class="prop-card">
                    <div class="driver-info">
                        <img src="<?= !empty($prop['photo_profil']) ? '../../uploads/profils/' . htmlspecialchars($prop['photo_profil']) : '../../images/default-avatar.png' ?>" alt="Driver" class="driver-img">
                        <div class="driver-details">
                            <h5><?= htmlspecialchars($prop['prenom'] . ' ' . $prop['nom']) ?></h5>
                            <p><i class="fas fa-star text-warning"></i> Conducteur</p>
                            <?php if (!empty($prop['marque'])): ?>
                                <p class="text-muted"><small><i class="fas fa-car"></i> <?= htmlspecialchars($prop['marque'] . ' ' . $prop['modele']) ?></small></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="prop-details">
                        <div class="price-tag"><?= number_format($prop['prix'], 2) ?> DT</div>
                        <span class="badge badge-<?= $prop['statut'] === 'acceptee' ? 'success' : ($prop['statut'] === 'refusee' ? 'danger' : 'secondary') ?>">
                            <?= ucfirst($prop['statut']) ?>
                        </span>
                    </div>

                    <div class="message-box">
                        <i class="fas fa-quote-left mr-2 opacity-50"></i>
                        <?= !empty($prop['message']) ? nl2br(htmlspecialchars($prop['message'])) : 'Aucun message.' ?>
                    </div>

                    <div class="w-100 d-flex justify-content-end mt-3 gap-2">
                        <a href="?accept=<?= $prop['id_proposition'] ?>&id=<?= $idTrajet ?>" class="btn btn-accept btn-sm mr-2 confirmation-btn">
                            <i class="fas fa-check"></i> Accepter
                        </a>
                        <a href="?reject=<?= $prop['id_proposition'] ?>&id=<?= $idTrajet ?>" class="btn btn-reject btn-sm mr-2 confirmation-btn" onclick="return confirm('Voulez-vous vraiment refuser cette proposition?')">
                            <i class="fas fa-times"></i> Rejeter
                        </a>
                        <!-- As per user request: "people who can transport him make a proposition... he can only delete the propositions"
                             Actually user said: "the admin can modify delete add trajets and there's a button for every trajet where if you click it it takes u to the propositions for that trajet where he can only delete the propositions".
                             Wait, that was for ADMIN. 
                             For USER: "user posts a trajet ... people ... make a proposition".
                             Implies user sees them. Can passenger accept? Probably. I'll include accept.
                        -->
                        <!-- Actually, user said "admin ... can only delete propositions". 
                             For normal user, usually they accept or contact. 
                             I'll add a contact button (mailto/tel) if accepted? 
                             For now, Accept/Delete is standard. -->
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
