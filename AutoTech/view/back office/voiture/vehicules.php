<?php
require_once __DIR__ . '/../../../controller/VehiculeController.php';

$imageName = null;

// Gestion upload
if (isset($_FILES['image_principale']) && $_FILES['image_principale']['error'] === 0) {
    $uploadDir = __DIR__ . '/../../../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = pathinfo($_FILES['image_principale']['name'], PATHINFO_EXTENSION);
    $imageName = time() . "_" . uniqid() . "." . $extension;

    move_uploaded_file($_FILES['image_principale']['tmp_name'], $uploadDir . $imageName);
}

$vehiculeController = new VehiculeController();
$message = "";
$messageType = "success";

// --- GESTION DES ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque       = $_POST['marque'] ?? '';
    $modele       = $_POST['modele'] ?? '';
    $annee        = $_POST['annee'] ?? '';
    $carburant    = $_POST['carburant'] ?? '';
    $kilometrage  = $_POST['kilometrage'] ?? 0;
    $couleur      = $_POST['couleur'] ?? '';
    $transmission = $_POST['transmission'] ?? '';
    $image        = $imageName;

    // Création
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $vehiculeController->createVehicule(
            $marque, $modele, $annee, $carburant, $kilometrage, $couleur, $transmission, $image
        );
        $message = "Véhicule ajouté avec succès.";
    }

    // Mise à jour
    if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['id_vehicule'])) {
        $vehiculeController->updateVehicule(
            $_POST['id_vehicule'],
            $marque, $modele, $annee, $carburant, $kilometrage, $couleur, $transmission, $image
        );
        $message = "Véhicule mis à jour avec succès.";
    }
}

// Suppression
if (isset($_GET['delete'])) {
    $vehiculeController->deleteVehicule($_GET['delete']);
    $message = "Véhicule supprimé avec succès.";
}

// Si on édite, récupérer le véhicule
$vehiculeToEdit = null;
if (isset($_GET['edit'])) {
    $vehiculeToEdit = $vehiculeController->getVehiculeById($_GET['edit']);
}

// Récupérer tous les véhicules
$vehicules = $vehiculeController->getAllVehicules();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des véhicules - AutoTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
   <style>
    :root {
        /* Palette Kaiadmin */
        --primary-color: #177dff;
        --primary-dark: #1263d1;
        --success-color: #31ce36;
        --warning-color: #ffad46;
        --danger-color: #f25961;
        --sidebar-bg: #1a2035;

        --card-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.08),
                       0 1px 2px -1px rgba(15, 23, 42, 0.06);
        --card-shadow-hover: 0 10px 15px -3px rgba(15, 23, 42, 0.1),
                             0 4px 6px -4px rgba(15, 23, 42, 0.1);
    }

    body {
        background: #f1f5f9; /* même esprit que le tableau de bord */
        min-height: 100vh;
        font-family: 'Public Sans', system-ui, -apple-system, BlinkMacSystemFont,
            "Segoe UI", sans-serif;
        color: #0f172a;
    }

    .main-container {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.18);
        margin: 2rem auto;
        padding: 0;
        overflow: hidden;
    }

    .page-header {
        background: #ffffff;
        color: #0f172a;
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
    }

    .page-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header h1 i {
        color: var(--primary-color);
        font-size: 1.6rem;
    }

    .page-header .btn-back {
        background: var(--primary-color);
        border: none;
        color: #ffffff;
        padding: 0.5rem 1.25rem;
        border-radius: 999px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(23, 125, 255, 0.4);
    }

    .page-header .btn-back:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(23, 125, 255, 0.45);
    }

    .content-wrapper {
        padding: 2rem;
        background: #f8fafc;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        transition: all 0.2s ease;
        margin-bottom: 1.5rem;
        overflow: hidden;
        background: #ffffff;
    }

    .card:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-1px);
    }

    .card-header {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
        font-weight: 600;
        font-size: 1rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-header i {
        color: var(--primary-color);
    }

    .form-label {
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 0.4rem;
        font-size: 0.9rem;
    }

    .form-label i {
        color: var(--primary-color);
    }

    .form-control,
    .form-select {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0.55rem 0.85rem;
        transition: all 0.15s ease;
        font-size: 0.95rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(23, 125, 255, 0.18);
    }

    .btn {
        border-radius: 8px;
        padding: 0.55rem 1.1rem;
        font-weight: 500;
        transition: all 0.15s ease;
        border: none;
        font-size: 0.95rem;
    }

    .btn-primary {
        background: var(--primary-color);
        box-shadow: 0 3px 8px rgba(23, 125, 255, 0.3);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 5px 12px rgba(23, 125, 255, 0.4);
    }

    .btn-warning {
        background: var(--warning-color);
        color: #1f2933;
    }

    .btn-warning:hover {
        filter: brightness(0.95);
    }

    .btn-danger {
        background: var(--danger-color);
    }

    .btn-danger:hover {
        filter: brightness(0.95);
    }

    .btn-success {
        background: var(--success-color);
    }

    .btn-success:hover {
        filter: brightness(0.95);
    }

    .btn-sm {
        padding: 0.3rem 0.65rem;
        font-size: 0.85rem;
        border-radius: 999px;
    }

    .table {
        margin-bottom: 0;
        font-size: 0.9rem;
    }

    .table thead {
        background: #f9fafb;
    }

    .table thead th {
        border-bottom: 2px solid #e5e7eb;
        color: #6b7280;
        font-weight: 600;
        padding: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 0.78rem;
    }

    .table tbody tr {
        transition: all 0.15s ease;
    }

    .table tbody tr:hover {
        background-color: #f9fafb;
    }

    .table tbody td {
        vertical-align: middle;
        padding: 0.85rem;
        color: #111827;
    }

    .vehicle-image {
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        object-fit: cover;
    }

    .vehicle-image:hover {
        transform: scale(1.35);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.25);
        cursor: zoom-in;
    }

    .alert {
        border: none;
        border-radius: 10px;
        padding: 0.8rem 1.2rem;
        margin-bottom: 1.5rem;
        animation: slideDown 0.25s ease;
        font-size: 0.9rem;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-success {
        background: linear-gradient(135deg, var(--success-color), #22c55e);
        color: #f9fafb;
    }

    .alert-danger {
        background: linear-gradient(135deg, var(--danger-color), #dc2626);
        color: #f9fafb;
    }

    .badge {
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        font-weight: 500;
        font-size: 0.78rem;
    }

    .stats-badge {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #ffffff;
        padding: 0.55rem 1.1rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 1.2rem;
        font-weight: 500;
        font-size: 0.9rem;
        box-shadow: 0 4px 10px rgba(23, 125, 255, 0.4);
    }

    .stats-badge i {
        font-size: 1.1rem;
    }

    .no-image-placeholder {
        width: 80px;
        height: 80px;
        background: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        color: #9ca3af;
        border: 1px dashed #cbd5e1;
    }

    .no-image-placeholder i {
        font-size: 1.4rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.4rem;
    }

    .file-input-wrapper input[type="file"] {
        border-radius: 8px;
        border: 1px dashed #cbd5e1;
        padding: 0.5rem;
        background: #f9fafb;
    }

    .file-input-wrapper input[type="file"]:hover {
        background: #f3f4f6;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 0.75rem;
            text-align: center;
        }

        .content-wrapper {
            padding: 1.25rem;
        }

        .action-buttons {
            flex-direction: row;
            flex-wrap: wrap;
        }

        .main-container {
            margin: 1rem auto;
        }
    }
</style>

</head>
<body>

<div class="container main-container">
    <div class="page-header">
        <h1>
            <i class="bi bi-car-front-fill"></i>
            Gestion des véhicules
        </h1>
        <a href="../index.php" class="btn btn-back">
            <i class="bi bi-arrow-left"></i> Retour au dashboard
        </a>
    </div>

    <div class="content-wrapper">
        <!-- Zone erreurs JS -->
        <div id="errorBox"></div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <i class="bi bi-<?= $messageType === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats-badge">
            <i class="bi bi-car-front"></i>
            <?= count($vehicules) ?> véhicule<?= count($vehicules) > 1 ? 's' : '' ?> au total
        </div>

        <!-- FORMULAIRE AJOUT / EDIT -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-<?= $vehiculeToEdit ? 'pencil-square' : 'plus-circle' ?>"></i>
                <?= $vehiculeToEdit ? "Modifier le véhicule #".$vehiculeToEdit['id_vehicule'] : "Ajouter un nouveau véhicule" ?>
            </div>
            <div class="card-body">
                <form id="vehiculeForm" method="POST" enctype="multipart/form-data">
                    <?php if ($vehiculeToEdit): ?>
                        <input type="hidden" name="id_vehicule" value="<?= htmlspecialchars($vehiculeToEdit['id_vehicule']) ?>">
                        <input type="hidden" name="action" value="update">
                    <?php else: ?>
                        <input type="hidden" name="action" value="create">
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-tag-fill text-primary"></i> Marque *
                            </label>
                            <input type="text" name="marque" class="form-control" placeholder="Ex: Toyota"
                                   value="<?= htmlspecialchars($vehiculeToEdit['marque'] ?? '') ?>" >
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-car-front text-primary"></i> Modèle *
                            </label>
                            <input type="text" name="modele" class="form-control" placeholder="Ex: Corolla"
                                   value="<?= htmlspecialchars($vehiculeToEdit['modele'] ?? '') ?>" >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-calendar-event text-primary"></i> Année *
                            </label>
                            <input type="number" name="annee" class="form-control" placeholder="<?= date('Y') ?>"
                                   value="<?= htmlspecialchars($vehiculeToEdit['annee'] ?? '') ?>" >
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-fuel-pump text-primary"></i> Carburant *
                            </label>
                            <select name="carburant" class="form-select" >
                                <option value="">Sélectionner...</option>
                                <option value="Essence" <?= ($vehiculeToEdit['carburant'] ?? '') === 'Essence' ? 'selected' : '' ?>>Essence</option>
                                <option value="Diesel" <?= ($vehiculeToEdit['carburant'] ?? '') === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                                <option value="Hybride" <?= ($vehiculeToEdit['carburant'] ?? '') === 'Hybride' ? 'selected' : '' ?>>Hybride</option>
                                <option value="Électrique" <?= ($vehiculeToEdit['carburant'] ?? '') === 'Électrique' ? 'selected' : '' ?>>Électrique</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-speedometer2 text-primary"></i> Kilométrage *
                            </label>
                            <input type="number" name="kilometrage" class="form-control" placeholder="0"
                                   value="<?= htmlspecialchars($vehiculeToEdit['kilometrage'] ?? '') ?>" >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-palette-fill text-primary"></i> Couleur
                            </label>
                            <input type="text" name="couleur" class="form-control" placeholder="Ex: Blanc"
                                   value="<?= htmlspecialchars($vehiculeToEdit['couleur'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-gear-fill text-primary"></i> Transmission
                            </label>
                            <select name="transmission" class="form-select">
                                <option value="">Sélectionner...</option>
                                <option value="Manuelle" <?= ($vehiculeToEdit['transmission'] ?? '') === 'Manuelle' ? 'selected' : '' ?>>Manuelle</option>
                                <option value="Automatique" <?= ($vehiculeToEdit['transmission'] ?? '') === 'Automatique' ? 'selected' : '' ?>>Automatique</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                <i class="bi bi-image-fill text-primary"></i> Image principale
                            </label>
                            <div class="file-input-wrapper">
                                <input type="file" name="image_principale" id="imageInput" class="form-control" accept="image/*">
                            </div>
                            <small class="text-muted">Formats acceptés: JPG, PNG, GIF (Max 5MB)</small>
                        </div>
                    </div>

                    <div class="mt-4 action-buttons">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-<?= $vehiculeToEdit ? 'save' : 'plus-circle' ?>"></i>
                            <?= $vehiculeToEdit ? "Mettre à jour" : "Ajouter le véhicule" ?>
                        </button>
                        <?php if ($vehiculeToEdit): ?>
                            <a href="vehicules.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Annuler
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABLEAU DES VEHICULES -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-ul"></i> Liste des véhicules
            </div>
            <div class="card-body table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Véhicule</th>
                        <th>Année</th>
                        <th>Carburant</th>
                        <th>Kilométrage</th>
                        <th>Transmission</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($vehicules): ?>
                        <?php foreach ($vehicules as $v): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($v['id_vehicule']) ?></strong></td>

                                <td>
                                    <?php if (!empty($v['image_principale'])): ?>
                                        <img src="/AutoTech/uploads/<?= htmlspecialchars($v['image_principale']) ?>"
                                             alt="<?= htmlspecialchars($v['marque'].' '.$v['modele']) ?>"
                                             class="vehicle-image"
                                             width="80" height="80">
                                    <?php else: ?>
                                        <div class="no-image-placeholder">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <strong><?= htmlspecialchars($v['marque']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($v['modele']) ?></small>
                                    <?php if (!empty($v['couleur'])): ?>
                                        <br><span class="badge bg-secondary"><?= htmlspecialchars($v['couleur']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($v['annee']) ?></td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <i class="bi bi-fuel-pump"></i>
                                        <?= htmlspecialchars($v['carburant']) ?>
                                    </span>
                                </td>
                                <td>
                                    <i class="bi bi-speedometer2"></i>
                                    <?= number_format($v['kilometrage'], 0, ',', ' ') ?> km
                                </td>
                                <td>
                                    <?php if (!empty($v['transmission'])): ?>
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-gear"></i>
                                            <?= htmlspecialchars($v['transmission']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="vehicules.php?edit=<?= $v['id_vehicule'] ?>"
                                           class="btn btn-sm btn-warning"
                                           title="Modifier">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="vehicules.php?delete=<?= $v['id_vehicule'] ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce véhicule ?\n\n<?= htmlspecialchars($v['marque'].' '.$v['modele']) ?>');"
                                           title="Supprimer">
                                            <i class="bi bi-trash3"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.5;"></i>
                                <p class="mt-3">Aucun véhicule pour le moment.</p>
                                <small>Commencez par ajouter votre premier véhicule ci-dessus.</small>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Validation du formulaire
document.getElementById('vehiculeForm').addEventListener('submit', function (e) {
    const errors = [];
    const form = e.target;

    const marque = form.marque.value.trim();
    const modele = form.modele.value.trim();
    const annee = form.annee.value.trim();
    const carburant = form.carburant.value.trim();
    const kilometrage = form.kilometrage.value.trim();

    if (marque === '') {
        errors.push("La marque est obligatoire.");
    }

    if (modele === '') {
        errors.push("Le modèle est obligatoire.");
    }

    if (annee === '') {
        errors.push("L'année est obligatoire.");
    } else if (isNaN(annee) || parseInt(annee) < 1950 || parseInt(annee) > new Date().getFullYear() + 1) {
        errors.push(`L'année doit être entre 1950 et ${new Date().getFullYear() + 1}.`);
    }

    if (carburant === '') {
        errors.push("Le type de carburant est obligatoire.");
    }

    if (kilometrage === '') {
        errors.push("Le kilométrage est obligatoire.");
    } else if (isNaN(kilometrage) || parseInt(kilometrage) < 0) {
        errors.push("Le kilométrage doit être un nombre positif.");
    }

    const errorBox = document.getElementById('errorBox');
    errorBox.innerHTML = '';

    if (errors.length > 0) {
        e.preventDefault();
        let html = '<div class="alert alert-danger"><strong><i class="bi bi-exclamation-triangle-fill"></i> Erreurs de validation:</strong><ul class="mb-0 mt-2">';
        errors.forEach(err => {
            html += '<li>' + err + '</li>';
        });
        html += '</ul></div>';
        errorBox.innerHTML = html;
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
});

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        if (!alert.querySelector('ul')) { // Don't auto-hide validation errors
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }
    });
}, 5000);

// Image preview
document.getElementById('imageInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileSize = file.size / 1024 / 1024; // in MB
        if (fileSize > 5) {
            alert('⚠️ Le fichier est trop volumineux. Taille maximum: 5MB');
            e.target.value = '';
        }
    }
});
</script>

</body>
</html>