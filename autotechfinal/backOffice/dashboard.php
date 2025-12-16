<?php
$page_title = "Tableau de Bord - Administration";
?>
<link rel="stylesheet" href="/AUTO/backOffice/css/admin-dashboard.css">
<?php
include("partials/admin-header.php");

require_once "../../Controller/RendezVousController.php";
require_once "../../Controller/TechnicienController.php";

$rdvController = new RendezVousController();
$techController = new TechnicienController();

$rendezvous = $rdvController->liste();
$techniciens = $techController->liste();
$stats = $rdvController->getStatistics();

$totalRendezVous = $stats['total'] ?? 0;
$rdvEnAttente = $stats['en_attente'] ?? 0;
$rdvConfirmes = $stats['confirmes'] ?? 0;
$rdvAnnules = $stats['annules'] ?? 0;
$totalTechniciens = count($techniciens);
?>

<section class="dashboard-section py-5 bg-light min-vh-100">
    <div class="container">
        <!-- Header -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-6 fw-bold text-primary mb-2">
                            <i class="fas fa-tachometer-alt me-3"></i>Tableau de Bord
                        </h1>
                        <p class="text-muted mb-0">Bienvenue dans votre espace d'administration AutoTech</p>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Dernière connexion</small>
                        <strong><?= date('d/m/Y à H:i') ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-5">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted text-uppercase small">Total Rendez-vous</h6>
                                <h2 class="fw-bold text-primary mb-0"><?= $totalRendezVous ?></h2>
                            </div>
                            <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-calendar-check text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-success">
                                <i class="fas fa-chart-line me-1"></i>
                                Toutes les demandes
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted text-uppercase small">En Attente</h6>
                                <h2 class="fw-bold text-warning mb-0"><?= $rdvEnAttente ?></h2>
                            </div>
                            <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-clock text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-warning">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Nécessitent validation
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted text-uppercase small">Confirmés</h6>
                                <h2 class="fw-bold text-success mb-0"><?= $rdvConfirmes ?></h2>
                            </div>
                            <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-check-circle text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-success">
                                <i class="fas fa-thumbs-up me-1"></i>
                                Rendez-vous validés
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted text-uppercase small">Techniciens</h6>
                                <h2 class="fw-bold text-info mb-0"><?= $totalTechniciens ?></h2>
                            </div>
                            <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-users text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-info">
                                <i class="fas fa-user-cog me-1"></i>
                                Experts disponibles
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fas fa-bolt me-2"></i>Actions Rapides
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <a href="../BackOffice/admin-techniciens-list.php" class="card action-card h-100 border-0 text-decoration-none">
                                    <div class="card-body text-center p-4">
                                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                             style="width: 70px; height: 70px;">
                                            <i class="fas fa-user-cog text-primary fa-2x"></i>
                                        </div>
                                        <h6 class="text-primary mb-2">Gérer les Techniciens</h6>
                                        <p class="text-muted small mb-0">Voir et modifier la liste des experts</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <a href="admin-rdv-list.php" class="card action-card h-100 border-0 text-decoration-none position-relative">
                                    <div class="card-body text-center p-4">
                                        <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                             style="width: 70px; height: 70px;">
                                            <i class="fas fa-calendar-alt text-success fa-2x"></i>
                                        </div>
                                        <h6 class="text-success mb-2">Rendez-vous</h6>
                                        <p class="text-muted small mb-0">Gérer les demandes des boutiques</p>
                                        <?php if ($rdvEnAttente > 0): ?>
                                        <span class="badge bg-warning position-absolute top-0 start-100 translate-middle">
                                            <?= $rdvEnAttente ?> nouveau<?= $rdvEnAttente > 1 ? 'x' : '' ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <a href="admin-profile.php" class="card action-card h-100 border-0 text-decoration-none">
                                    <div class="card-body text-center p-4">
                                        <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                             style="width: 70px; height: 70px;">
                                            <i class="fas fa-user-shield text-info fa-2x"></i>
                                        </div>
                                        <h6 class="text-info mb-2">Profil Admin</h6>
                                        <p class="text-muted small mb-0">Modifier vos informations</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <a href="../frontOffice/index.php" class="card action-card h-100 border-0 text-decoration-none">
                                    <div class="card-body text-center p-4">
                                        <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                             style="width: 70px; height: 70px;">
                                            <i class="fas fa-eye text-warning fa-2x"></i>
                                        </div>
                                        <h6 class="text-warning mb-2">Voir le Site</h6>
                                        <p class="text-muted small mb-0">Accéder à l'interface publique</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fas fa-history me-2"></i>Activité Récente
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $recentRdv = $rdvController->getRecent(5);
                        if (empty($recentRdv)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune activité récente</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Technicien</th>
                                            <th>Date</th>
                                            <th>Service</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($recentRdv as $rdv): ?>
                                        <tr>
                                            <td><strong>#<?= $rdv['id_rdv'] ?? '' ?></strong></td>
                                            <td><?= htmlspecialchars($rdv['technicien_nom'] ?? 'Inconnu') ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($rdv['date_rdv'] ?? '')) ?></td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?= htmlspecialchars($rdv['type_intervention'] ?? 'Non spécifié') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = match($rdv['statut'] ?? 'en_attente') {
                                                    'confirme' => 'success',
                                                    'annule' => 'danger',
                                                    default => 'warning'
                                                };
                                                $statusText = match($rdv['statut'] ?? 'en_attente') {
                                                    'confirme' => 'Confirmé',
                                                    'annule' => 'Annulé',
                                                    default => 'En attente'
                                                };
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>">
                                                    <?= $statusText ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="admin-rdv-list.php" class="btn btn-outline-primary btn-sm">
                                    Voir tous les rendez-vous
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fas fa-chart-pie me-2"></i>Statistiques
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Répartition des Rendez-vous</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Confirmés</span>
                                <span class="fw-bold text-success"><?= $rdvConfirmes ?></span>
                            </div>
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: <?= $totalRendezVous > 0 ? ($rdvConfirmes/$totalRendezVous)*100 : 0 ?>%"></div>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>En attente</span>
                                <span class="fw-bold text-warning"><?= $rdvEnAttente ?></span>
                            </div>
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: <?= $totalRendezVous > 0 ? ($rdvEnAttente/$totalRendezVous)*100 : 0 ?>%"></div>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Annulés</span>
                                <span class="fw-bold text-danger"><?= $rdvAnnules ?></span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: <?= $totalRendezVous > 0 ? ($rdvAnnules/$totalRendezVous)*100 : 0 ?>%"></div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <div class="bg-light rounded p-3">
                                <h4 class="text-primary mb-1"><?= $totalTechniciens ?></h4>
                                <small class="text-muted">Techniciens actifs</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include("partials/admin-footer.php"); ?>