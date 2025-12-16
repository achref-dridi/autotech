<?php
$page_title = "Rendez-vous par Technicien - Administration";
include("partials/admin-header.php");

require_once "../../Controller/RendezVousController.php";
require_once "../../Controller/TechnicienController.php";

$techId = $_GET['id'] ?? null;
if (!$techId || !is_numeric($techId)) {
    header("Location: admin-rdv-list.php");
    exit;
}

$rdvCtrl = new RendezVousController();
$techCtrl = new TechnicienController();

// Handle status updates
if (isset($_GET['update_id']) && isset($_GET['new_status'])) {
    $result = $rdvCtrl->changerStatut($_GET['update_id'], $_GET['new_status']);
    if ($result === true) {
        $messageType = 'success';
        $messageText = 'Statut du rendez-vous mis à jour avec succès!';
    } else {
        $messageType = 'danger';
        $messageText = 'Erreur lors de la mise à jour du statut';
    }
}

$technicien = $techCtrl->getById($techId);
$rendezvous = $rdvCtrl->getByTechnicien($techId);

if (!$technicien) {
    header("Location: /ESSAI/View/backOffice/admin-rdv-list.php");
    exit;
}

// Calculate statistics
$totalRendezVous = count($rendezvous);
$enAttente = 0; 
$confirmes = 0; 
$annules = 0;

foreach ($rendezvous as $rdv) {
    $statut = strtolower(trim($rdv['statut']));
    if (strpos($statut, 'confirme') !== false) $confirmes++;
    elseif (strpos($statut, 'annule') !== false) $annules++;
    else $enAttente++;
}
?>

<section class="dashboard-section py-5 bg-light min-vh-100">
    <div class="container">

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-6 fw-bold text-primary mb-2">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Rendez-vous de 
                            <span class="text-primary">
                                <?= htmlspecialchars(is_object($technicien) ? $technicien->nom : $technicien['nom'] ?? 'Technicien') ?>
                            </span>
                        </h1>
                        <p class="text-muted mb-0">
                            <?= $totalRendezVous ?> rendez-vous trouvés
                        </p>
                    </div>
                    <div>
                        <a href="/ESSAI/View/backOffice/admin-rdv-list.php" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <a href="admin-rdv-add.php?technicien_id=<?= $techId ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Ajouter RDV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        <?php if (isset($messageType)): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show mb-4">
            <?= htmlspecialchars($messageText) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Trier par :</label>
                                <select class="form-select" id="sortSelect">
                                    <option value="date_asc">Date (ancien → récent)</option>
                                    <option value="date_desc">Date (récent → ancien)</option>
                                    <option value="statut">Statut</option>
                                    <option value="type_intervention">Type d'intervention</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="statusFilter" class="form-label fw-bold">Filtrer par statut:</label>
                                <select class="form-select" id="statusFilter">
                                    <option value="all">Tous</option>
                                    <option value="en_attente">En attente</option>
                                    <option value="confirme">Confirmé</option>
                                    <option value="annule">Annulé</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Rechercher :</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Recherche...">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i>Liste des rendez-vous
                </h5>
            </div>

            <div class="card-body p-0">

                <?php if (empty($rendezvous)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Aucun rendez-vous trouvé</h4>
                    </div>

                <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="rdvTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Commentaire</th>
                                <th>Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php foreach($rendezvous as $rdv): 
                            $statusClass = "warning";
                            if (strpos($rdv['statut'], 'confirme') !== false) $statusClass = "success";
                            elseif (strpos($rdv['statut'], 'annule') !== false) $statusClass = "danger";
                        ?>
                        <tr class="rdv-row" data-status="<?= $statusClass ?>">

                            <td class="ps-4"><?= $rdv['id_rdv'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rdv['date_rdv'])) ?></td>
                            <td><?= htmlspecialchars($rdv['type_intervention']) ?></td>
                            <td><?= htmlspecialchars($rdv['commentaire']) ?></td>

                            <td><span class="badge bg-<?= $statusClass ?>"><?= htmlspecialchars($rdv['statut']) ?></span></td>

                            <td class="text-center">

                                <!-- Modifier -->
                                <a href="admin-rdv-edit.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-sm btn-primary me-1">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Confirmer -->
                                <a href="admin-rdv-by-technicien.php?id=<?= $techId ?>&update_id=<?= $rdv['id_rdv'] ?>&new_status=confirme"
                                   class="btn btn-sm btn-success me-1">
                                   <i class="fas fa-check"></i>
                                </a>

                                <!-- Supprimer -->
                                <a href="/ESSAI/View/backOffice/admin-rdv-delete.php?id=<?= $rdv['id_rdv'] ?>&technicien_id=<?= $techId ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Supprimer le rendez-vous #<?= $rdv['id_rdv'] ?> ?');">
                                    <i class="fas fa-trash me-1"></i> 
                                </a>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php endif; ?>

            </div>
        </div>

    </div>
</section>


<!-- JavaScript Filters + Sorting -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const rdvRows = document.querySelectorAll('.rdv-row');

    function filterTable() {
        const statusValue = statusFilter.value;
        const searchValue = searchInput.value.toLowerCase();

        rdvRows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const rowText = row.textContent.toLowerCase();

            let match = true;

            if (statusValue !== 'all') {
                const classMap = {
                    en_attente: "warning",
                    confirme: "success",
                    annule: "danger"
                };
                match = rowStatus === classMap[statusValue];
            }

            if (!rowText.includes(searchValue)) match = false;

            row.style.display = match ? "" : "none";
        });
    }

    statusFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);

    function sortTable() {
        const tbody = document.querySelector("#rdvTable tbody");
        const rows = Array.from(rdvRows);
        const sortValue = sortSelect.value;

        rows.sort((a, b) => {
            if (sortValue === "date_asc" || sortValue === "date_desc") {
                const dateA = new Date(a.querySelector("td:nth-child(2)").innerText);
                const dateB = new Date(b.querySelector("td:nth-child(2)").innerText);
                return sortValue === "date_asc" ? dateA - dateB : dateB - dateA;
            }

            if (sortValue === "statut") {
                return a.getAttribute("data-status").localeCompare(b.getAttribute("data-status"));
            }

            if (sortValue === "type_intervention") {
                return a.querySelector("td:nth-child(3)").innerText.localeCompare(
                    b.querySelector("td:nth-child(3)").innerText
                );
            }
        });

        rows.forEach(r => tbody.appendChild(r));
    }

    sortSelect.addEventListener("change", sortTable);

});
</script>

<?php include("partials/admin-footer.php"); ?>
