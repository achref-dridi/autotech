<?php
$page_title = "Gestion des Techniciens - Administration";
?>
<link rel="stylesheet" href="/AutoTech/assets/css/admin-rdv-list.css">
<?php
include("partials/admin-header.php");

require_once "../../Controller/RendezVousController.php";
require_once "../../Controller/TechnicienController.php";

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

// Get all technicians using liste() method
$techniciens = $techCtrl->liste();

// Get all rendezvous
$allRendezVous = $rdvCtrl->liste();

// DEBUG: Check what status values you actually have
// echo "<pre>";
// foreach ($allRendezVous as $rdv) {
//     echo "RDV ID: " . $rdv['id_rdv'] . " - Status: " . $rdv['statut'] . "\n";
// }
// echo "</pre>";

// Group rendezvous by technician
$techniciensWithStats = [];
foreach ($allRendezVous as $rdv) {
    $techId = $rdv['id_technicien'];
    
    if (!isset($techniciensWithStats[$techId])) {
        // Get technician info
        $technicien = $techCtrl->getById($techId);
        $techniciensWithStats[$techId] = [
            'technicien' => $technicien,
            'total_rendezvous' => 0,
            'en_attente' => 0,
            'confirmes' => 0,
            'annules' => 0,
            'rendezvous' => []
        ];
    }
    
    // Count by status - FIXED: Trim and lowercase for consistency
    $techniciensWithStats[$techId]['total_rendezvous']++;
    
    $statut = strtolower(trim($rdv['statut']));
    
    // Handle different possible status values
    if (strpos($statut, 'confirme') !== false || $statut === 'confirmé' || $statut === 'confirme') {
        $techniciensWithStats[$techId]['confirmes']++;
    } elseif (strpos($statut, 'annule') !== false || $statut === 'annulé' || $statut === 'annule') {
        $techniciensWithStats[$techId]['annules']++;
    } else {
        // Default to "en attente" for any other status
        $techniciensWithStats[$techId]['en_attente']++;
    }
    
    $techniciensWithStats[$techId]['rendezvous'][] = $rdv;
}

// Also add technicians without appointments
foreach ($techniciens as $technicien) {
    $techId = is_object($technicien) ? $technicien->id_technicien : $technicien['id_technicien'];
    
    if (!isset($techniciensWithStats[$techId])) {
        $techniciensWithStats[$techId] = [
            'technicien' => $technicien,
            'total_rendezvous' => 0,
            'en_attente' => 0,
            'confirmes' => 0,
            'annules' => 0,
            'rendezvous' => []
        ];
    }
}

// Calculate totals with verification
$totalAppointments = 0;
$totalEnAttente = 0;
$totalConfirmes = 0;
$totalAnnules = 0;

foreach ($techniciensWithStats as $techId => $stats) {
    $totalAppointments += $stats['total_rendezvous'];
    $totalEnAttente += $stats['en_attente'];
    $totalConfirmes += $stats['confirmes'];
    $totalAnnules += $stats['annules'];
    
    // DEBUG: Show technician stats
    // $techNom = is_object($stats['technicien']) ? $stats['technicien']->nom : $stats['technicien']['nom'];
    // echo "Technicien: $techNom - Total: {$stats['total_rendezvous']}, En attente: {$stats['en_attente']}, Confirmés: {$stats['confirmes']}, Annulés: {$stats['annules']}<br>";
}

// Verify totals match
$calculatedTotal = $totalEnAttente + $totalConfirmes + $totalAnnules;
if ($calculatedTotal != $totalAppointments) {
    // DEBUG: Show mismatch
    // echo "<div class='alert alert-warning'>Warning: Total mismatch! Appointments: $totalAppointments, Sum of statuses: $calculatedTotal</div>";
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
                            <i class="fas fa-users-cog me-3"></i>Statistiques par Technicien
                        </h1>
                        <p class="text-muted mb-0">Vue d'ensemble des rendez-vous par technicien</p>
                    </div>
                    <div class="text-end">
                        <a href="admin-rdv-add.php" class="btn btn-primary me-2">
                            <i class="fas fa-plus me-1"></i>Nouveau RDV
                        </a>
                        <span class="badge bg-primary fs-6">
                            <?= count($techniciens) ?> techniciens
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        <?php if (isset($messageType) && isset($messageText)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                    <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-2"></i>
                    <?= htmlspecialchars($messageText) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Statistics Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-users text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold"><?= count($techniciens) ?></h5>
                                        <small class="text-muted">Techniciens</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-clock text-warning fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold"><?= $totalEnAttente ?></h5>
                                        <small class="text-muted">En attente</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-check-circle text-success fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold"><?= $totalConfirmes ?></h5>
                                        <small class="text-muted">Confirmés</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-wrapper bg-danger bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-times-circle text-danger fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold"><?= $totalAnnules ?></h5>
                                        <small class="text-muted">Annulés</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Debug info (optional, remove in production) -->
                        <div class="mt-3 small text-muted text-center">
                            Total rendez-vous: <?= $totalAppointments ?> | 
                            Somme des statuts: <?= $calculatedTotal ?>
                            <?php if ($calculatedTotal != $totalAppointments): ?>
                                <span class="text-danger ms-2">
                                    <i class="fas fa-exclamation-triangle"></i> Incohérence détectée
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <label for="searchInput" class="form-label fw-bold">Rechercher un technicien:</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Rechercher par nom, spécialité...">
                            </div>
                            <div class="col-md-3">
                                <label for="sortBy" class="form-label fw-bold">Trier par:</label>
                                <select class="form-select" id="sortBy">
                                    <option value="nom">Nom (A-Z)</option>
                                    <option value="total_rdv">Total RDV (descendant)</option>
                                    <option value="en_attente">En attente (descendant)</option>
                                    <option value="confirmes">Confirmés (descendant)</option>
                                    <option value="annules">Annulés (descendant)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">&nbsp;</label>
                                <div class="d-grid">
                                    <button class="btn btn-outline-secondary" id="resetFilters">
                                        <i class="fas fa-redo me-2"></i>Réinitialiser
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technicians Statistics Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-primary">
                                <i class="fas fa-chart-bar me-2"></i>Statistiques par Technicien
                            </h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-info" onclick="exportToExcel()">
                                    <i class="fas fa-file-excel me-1"></i>Exporter
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="printTable()">
                                    <i class="fas fa-print me-1"></i>Imprimer
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($techniciensWithStats)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Aucun technicien trouvé</h4>
                                <p class="text-muted">Aucun technicien n'est enregistré dans le système.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="techTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Technicien</th>
                                            <th>Spécialité</th>
                                            <th class="text-center">Total RDV</th>
                                            <th class="text-center">En attente</th>
                                            <th class="text-center">Confirmés</th>
                                            <th class="text-center">Annulés</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($techniciensWithStats as $techId => $item): 
                                            $tech = $item['technicien'];
                                            // Handle both object and array
                                            $techNom = is_object($tech) ? ($tech->nom ?? 'Inconnu') : ($tech['nom'] ?? 'Inconnu');
                                            $specialite = is_object($tech) ? ($tech->specialite ?? 'Non spécifié') : ($tech['specialite'] ?? 'Non spécifié');
                                            $telephone = is_object($tech) ? ($tech->telephone ?? '') : ($tech['telephone'] ?? '');
                                            $email = is_object($tech) ? ($tech->email ?? '') : ($tech['email'] ?? '');
                                            
                                            // Get pending appointments for this technician - FIXED FILTER
                                            $pendingAppointments = array_filter($item['rendezvous'], function($rdv) {
                                                $statut = strtolower(trim($rdv['statut']));
                                                return !(strpos($statut, 'confirme') !== false || strpos($statut, 'annule') !== false);
                                            });
                                        ?>
                                        <tr class="tech-row" data-name="<?= strtolower(htmlspecialchars($techNom)) ?>" 
                                            data-specialite="<?= strtolower(htmlspecialchars($specialite)) ?>"
                                            data-total="<?= $item['total_rendezvous'] ?>"
                                            data-pending="<?= $item['en_attente'] ?>"
                                            data-confirmed="<?= $item['confirmes'] ?>"
                                            data-cancelled="<?= $item['annules'] ?>">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user-cog text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <strong><?= htmlspecialchars($techNom) ?></strong>
                                                        <?php if (!empty($telephone)): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars($telephone) ?></small>
                                                        <?php endif; ?>
                                                        <?php if (!empty($email)): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars($email) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?= htmlspecialchars($specialite) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="display-5 fw-bold text-primary">
                                                    <?= $item['total_rendezvous'] ?>
                                                </div>
                                                <small class="text-muted">rendez-vous</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="display-5 fw-bold text-warning">
                                                    <?= $item['en_attente'] ?>
                                                </div>
                                                <small class="text-muted">en attente</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="display-5 fw-bold text-success">
                                                    <?= $item['confirmes'] ?>
                                                </div>
                                                <small class="text-muted">confirmés</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="display-5 fw-bold text-danger">
                                                    <?= $item['annules'] ?>
                                                </div>
                                                <small class="text-muted">annulés</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group-vertical btn-group-sm" role="group">
                                                    <!-- View All Appointments Button -->
                                                    <a href="admin-rdv-by-technicien.php?id=<?= $techId ?>" 
                                                       class="btn btn-primary mb-1">
                                                        <i class="fas fa-eye me-1"></i>Tous les RDV
                                                    </a>
                                                    
                                                    <!-- Add Appointment Button -->
                                                    <a href="admin-rdv-add.php?technicien_id=<?= $techId ?>" 
                                                       class="btn btn-success mb-1">
                                                        <i class="fas fa-plus me-1"></i>Ajouter RDV
                                                    </a>
                                                    
                                                    <!-- Quick Actions for Pending Appointments -->
                                                    <?php if (!empty($pendingAppointments)): ?>
                                                    <div class="dropdown">
                                                        <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-clock me-1"></i>RDV en attente
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><h6 class="dropdown-header"><?= count($pendingAppointments) ?> RDV en attente</h6></li>
                                                            <?php 
                                                            $counter = 0;
                                                            foreach($pendingAppointments as $pendingRdv): 
                                                                $counter++;
                                                                if ($counter > 5) {
                                                                    echo '<li><a class="dropdown-item text-primary" href="admin-rdv-by-technicien.php?id=' . $techId . '"><i class="fas fa-ellipsis-h me-2"></i>Voir plus...</a></li>';
                                                                    break;
                                                                }
                                                            ?>
                                                            <li>
                                                                <div class="dropdown-item">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <strong>RDV #<?= $pendingRdv['id_rdv'] ?></strong><br>
                                                                            <small class="text-muted">
                                                                                <?= date('d/m H:i', strtotime($pendingRdv['date_rdv'])) ?><br>
                                                                                <?= htmlspecialchars(substr($pendingRdv['type_intervention'], 0, 30)) ?>...
                                                                            </small>
                                                                        </div>
                                                                        <div class="btn-group btn-group-sm">
                                                                            <a href="admin-rdv-list.php?update_id=<?= $pendingRdv['id_rdv'] ?>&new_status=confirme" 
                                                                               class="btn btn-sm btn-outline-success" 
                                                                               title="Confirmer"
                                                                               onclick="return confirm('Confirmer le RDV #<?= $pendingRdv['id_rdv'] ?> ?')">
                                                                                <i class="fas fa-check"></i>
                                                                            </a>
                                                                            <a href="admin-rdv-list.php?update_id=<?= $pendingRdv['id_rdv'] ?>&new_status=annule" 
                                                                               class="btn btn-sm btn-outline-danger" 
                                                                               title="Refuser"
                                                                               onclick="return confirm('Refuser le RDV #<?= $pendingRdv['id_rdv'] ?> ?')">
                                                                                <i class="fas fa-times"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Table Footer -->
                    <?php if (!empty($techniciensWithStats)): ?>
                    <div class="card-footer bg-light py-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    Affichage de <strong id="rowCount"><?= count($techniciensWithStats) ?></strong> techniciens
                                    <span class="ms-3">
                                        <i class="fas fa-clock text-warning me-1"></i><?= $totalEnAttente ?> en attente
                                    </span>
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="dashboard.php" class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fas fa-arrow-left me-1"></i>Tableau de bord
                                </a>
                                <a href="admin-rdv-list-detailed.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-list me-1"></i>Vue détaillée
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript remains the same -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const sortBy = document.getElementById('sortBy');
    const resetFilters = document.getElementById('resetFilters');
    const techRows = document.querySelectorAll('.tech-row');

    // Function to filter table
    function filterAndSortTable() {
        const searchValue = searchInput.value.toLowerCase();
        const sortValue = sortBy.value;
        
        let filteredRows = Array.from(techRows);
        
        // Filter by search
        if (searchValue) {
            filteredRows = filteredRows.filter(row => {
                const name = row.getAttribute('data-name');
                const specialite = row.getAttribute('data-specialite');
                return name.includes(searchValue) || specialite.includes(searchValue);
            });
        }
        
        // Sort rows
        filteredRows.sort((a, b) => {
            switch(sortValue) {
                case 'nom':
                    const nameA = a.getAttribute('data-name');
                    const nameB = b.getAttribute('data-name');
                    return nameA.localeCompare(nameB);
                    
                case 'total_rdv':
                    const totalA = parseInt(a.getAttribute('data-total'));
                    const totalB = parseInt(b.getAttribute('data-total'));
                    return totalB - totalA;
                    
                case 'en_attente':
                    const enAttenteA = parseInt(a.getAttribute('data-pending'));
                    const enAttenteB = parseInt(b.getAttribute('data-pending'));
                    return enAttenteB - enAttenteA;
                    
                case 'confirmes':
                    const confirmesA = parseInt(a.getAttribute('data-confirmed'));
                    const confirmesB = parseInt(b.getAttribute('data-confirmed'));
                    return confirmesB - confirmesA;
                    
                case 'annules':
                    const annulesA = parseInt(a.getAttribute('data-cancelled'));
                    const annulesB = parseInt(b.getAttribute('data-cancelled'));
                    return annulesB - annulesA;
                    
                default:
                    return 0;
            }
        });
        
        // Hide all rows first
        techRows.forEach(row => row.style.display = 'none');
        
        // Show filtered and sorted rows
        filteredRows.forEach(row => row.style.display = '');
        
        // Update counter
        updateRowCount(filteredRows.length);
    }

    searchInput.addEventListener('input', filterAndSortTable);
    sortBy.addEventListener('change', filterAndSortTable);

    resetFilters.addEventListener('click', function() {
        searchInput.value = '';
        sortBy.value = 'nom';
        filterAndSortTable();
    });

    function updateRowCount(count) {
        const counterElement = document.getElementById('rowCount');
        if (counterElement) {
            counterElement.textContent = count;
        }
    }
    
    // Add confirmation for all status change links
    document.querySelectorAll('a[href*="new_status="]').forEach(link => {
        link.addEventListener('click', function(e) {
            const isConfirm = this.getAttribute('href').includes('confirme');
            const rdvId = this.getAttribute('href').match(/update_id=(\d+)/)[1];
            const action = isConfirm ? 'confirmer' : 'refuser';
            
            if (!confirm(`Voulez-vous vraiment ${action} le rendez-vous #${rdvId} ?`)) {
                e.preventDefault();
            }
        });
    });
    
    // Initialize filter
    filterAndSortTable();
});

function confirmDelete(techId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce technicien ?\nCette action supprimera également tous ses rendez-vous.')) {
        window.location.href = `admin-technicien-delete.php?id=${techId}`;
    }
}

function exportToExcel() {
    // Simple Excel export
    const table = document.getElementById('techTable');
    const html = table.outerHTML;
    const blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'techniciens-statistiques.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function printTable() {
    const printContent = document.getElementById('techTable').outerHTML;
    const originalContent = document.body.innerHTML;
    document.body.innerHTML = `
        <html>
            <head>
                <title>Statistiques Techniciens</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .text-center { text-align: center; }
                    .fw-bold { font-weight: bold; }
                </style>
            </head>
            <body>
                <h2>Statistiques par Technicien</h2>
                <p>Date d'impression: ${new Date().toLocaleDateString()}</p>
                ${printContent}
            </body>
        </html>
    `;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}
</script>

<?php include("partials/admin-footer.php"); ?>