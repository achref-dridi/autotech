<?php
$page_title = "Profil Administratrice - Administration";
?>
<link rel="stylesheet" href="/AutoTech/assets/css/admin-dashboard.css">
<?php
include("partials/admin-header.php");
require_once __DIR__ . "/../../Controller/AdminController.php";

$adminController = new AdminController();
$stats = $adminController->getDashboardStats();
?>

<section class="dashboard-section py-5 bg-light min-vh-100">
    <div class="container">
        <!-- Header -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-6 fw-bold text-primary mb-2">
                        v   <i class="fas fa-user-shield me-3"></i>Profil Administratrice
                        </h1>
                        <p class="text-muted mb-0">Informations et statistiques de votre compte</p>
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
                               <h6 class="card-title text-muted text-uppercase small">En Attente</h6>
                               <h2 class="fw-bold text-warning mb-0"><?= $stats["pending"] ?? 0 ?></h2>
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
                               <h2 class="fw-bold text-success mb-0"><?= $stats["confirmed"] ?? 0 ?></h2>
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
                               <h6 class="card-title text-muted text-uppercase small">Annulés</h6>
                               <h2 class="fw-bold text-danger mb-0"><?= $stats["cancelled"] ?? 0 ?></h2>
                           </div>
                           <div class="icon-wrapper bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px;">
                               <i class="fas fa-times-circle text-danger fa-2x"></i>
                           </div>
                       </div>
                       <div class="mt-3">
                           <small class="text-danger">
                               <i class="fas fa-times me-1"></i>
                               Rendez-vous annulés
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
                               <h2 class="fw-bold text-info mb-0"><?= $stats["techs"] ?? 0 ?></h2>
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

       <!-- Profile Information -->
       <div class="row mb-4">
           <div class="col-12">
               <div class="card border-0 shadow-sm">
                   <div class="card-header bg-white py-3">
                       <h5 class="card-title mb-0 text-primary">
                           <i class="fas fa-user me-2"></i>Informations de l'Administratrice
                       </h5>
                   </div>
                   <div class="card-body">
                       <div class="row">
                           <div class="col-md-6">
                               <p><strong>Nom complet :</strong> <?php echo htmlspecialchars($adminName ?? 'Nom de l\'Administratrice'); ?></p>
                               <p><strong>Email professionnel :</strong> <?php echo htmlspecialchars($adminEmail ?? 'admin@autotech.tn'); ?></p>
                           </div>
                           <div class="col-md-6">
                               <p><strong>Rôle :</strong> Administratrice — Gestion des techniciens & validation des rendez-vous</p>
                               <p><strong>Description :</strong> Responsable de la supervision des techniciens, de la validation des demandes de rendez-vous et du suivi global des interventions.</p>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>

       <!-- Responsibilities -->
       <div class="row mb-4">
           <div class="col-12">
               <div class="card border-0 shadow-sm">
                   <div class="card-header bg-white py-3">
                       <h5 class="card-title mb-0 text-primary">
                           <i class="fas fa-tasks me-2"></i>Responsabilités
                       </h5>
                   </div>
                   <div class="card-body">
                       <ul class="list-group list-group-flush">
                           <li class="list-group-item d-flex align-items-center">
                               <i class="fas fa-check-circle text-success me-3"></i>
                               Valider les demandes de rendez-vous
                           </li>
                           <li class="list-group-item d-flex align-items-center">
                               <i class="fas fa-phone text-primary me-3"></i>
                               Contacter les techniciens assignés
                           </li>
                           <li class="list-group-item d-flex align-items-center">
                               <i class="fas fa-eye text-info me-3"></i>
                               Superviser les interventions
                           </li>
                           <li class="list-group-item d-flex align-items-center">
                               <i class="fas fa-chart-line text-warning me-3"></i>
                               Suivre l'état global du service
                           </li>
                       </ul>
                   </div>
               </div>
           </div>
       </div>

       <!-- Quick Actions -->
       <div class="row">
           <div class="col-12">
               <div class="card border-0 shadow-sm">
                   <div class="card-header bg-white py-3">
                       <h5 class="card-title mb-0 text-primary">
                           <i class="fas fa-bolt me-2"></i>Actions Rapides
                       </h5>
                   </div>
                   <div class="card-body">
                       <div class="row g-3">
                           <div class="col-lg-6">
                               <a href="admin-rdv-list.php" class="card action-card h-100 border-0 text-decoration-none">
                                   <div class="card-body text-center p-4">
                                       <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                            style="width: 70px; height: 70px;">
                                           <i class="fas fa-calendar-alt text-success fa-2x"></i>
                                       </div>
                                       <h6 class="text-success mb-2">Gérer les Rendez-vous</h6>
                                       <p class="text-muted small mb-0">Voir et valider les demandes</p>
                                   </div>
                               </a>
                           </div>
                           <div class="col-lg-6">
                               <a href="admin-techniciens-list.php" class="card action-card h-100 border-0 text-decoration-none">
                                   <div class="card-body text-center p-4">
                                       <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                            style="width: 70px; height: 70px;">
                                           <i class="fas fa-user-cog text-primary fa-2x"></i>
                                       </div>
                                       <h6 class="text-primary mb-2">Gérer les Techniciens</h6>
                                       <p class="text-muted small mb-0">Administrer l'équipe technique</p>
                                   </div>
                               </a>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
</section>

<script src="/AutoTech/assets/js/admin-profile.js"></script>

<?php include("partials/admin-footer.php"); ?>