<?php
// Get the root directory (two levels up from admin/partials)
$rootDir = dirname(dirname(__DIR__));
require_once $rootDir . '/config/config.php';
require_once $rootDir . '/controller/UtilisateurController.php';

$userController = new UtilisateurController();

// Check if user is logged in and is admin
if (!$userController->estConnecte() || ($_SESSION['user_email'] ?? '') !== 'admin@autotech.tn') {
    header('Location: ../view/auth/login.php');
    exit();
}

$currentUser = $userController->getUtilisateurConnecte();
$pageTitle = $pageTitle ?? 'Tableau de Bord - Administration AutoTech';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../images/off_logo.png" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
</head>
<body>
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.php" class="logo">
              <img
                src="../images/off_logo.png"
                alt="navbar brand"
                class="navbar-brand"
                height="40"
              />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <a href="index.php" class="collapsed" aria-expanded="false">
                  <i class="fas fa-tachometer-alt"></i>
                  <p>Tableau de Bord</p>
                </a>
              </li>
              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Gestion</h4>
              </li>
              <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'utilisateurs.php' ? 'active' : '' ?>">
                <a href="utilisateurs.php">
                  <i class="fas fa-users"></i>
                  <p>Utilisateurs</p>
                </a>
              </li>
              <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'vehicules.php' ? 'active' : '' ?>">
                <a href="vehicules.php">
                  <i class="fas fa-car"></i>
                  <p>Véhicules</p>
                </a>
              </li>
              <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'boutiques.php' ? 'active' : '' ?>">
                <a href="boutiques.php">
                  <i class="fas fa-store"></i>
                  <p>Boutiques</p>
                </a>
              </li>
              <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'techniciens.php' ? 'active' : '' ?>">
                <a href="techniciens.php">
                  <i class="fas fa-user-cog"></i>
                  <p>Techniciens</p>
                </a>
              </li>
              <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'rendez-vous.php' ? 'active' : '' ?>">
                <a href="rendez-vous.php">
                  <i class="fas fa-calendar-alt"></i>
                  <p>Rendez-vous</p>
                </a>
              </li>
              <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'active' : '' ?>">
                <a href="reservations.php">
                  <i class="fas fa-calendar-check"></i>
                  <p>Réservations</p>
                </a>
              </li>
              <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'trajets.php' ? 'active' : '' ?>">
                <a href="trajets.php">
                  <i class="fas fa-route"></i>
                  <p>Trajets</p>
                </a>
              </li>
              <!-- Link removed -->
              <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'signalements.php' ? 'active' : '' ?>">
                <a href="signalements.php">
                  <i class="fas fa-exclamation-triangle"></i>
                  <p>Signalements</p>
                </a>
              </li>
              <li class="nav-section">
                <span class="sidebar-mini-icon">

                  <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Navigation</h4>
              </li>
              <li class="nav-item">
                        <a href="../view/public/index.php" target="_blank">
                  <i class="fas fa-eye"></i>
                  <p>Voir le Site</p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
              <a href="index.php" class="logo">
                <img
                  src="../images/off_logo.png"
                  alt="navbar brand"
                  class="navbar-brand"
                  height="30"
                />
              </a>
              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                  <i class="gg-menu-left"></i>
                </button>
              </div>
              <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
              </button>
            </div>
            <!-- End Logo Header -->
          </div>
          <!-- Navbar Header -->
          <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
            <div class="container-fluid">
              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                      <img
                        src="<?= !empty($currentUser['photo_profil']) ? '../uploads/profils/' . htmlspecialchars($currentUser['photo_profil']) : 'assets/img/profile.jpg' ?>"
                        alt="..."
                        class="avatar-img rounded-circle"
                      />
                    </div>
                    <span class="profile-username">
                      <span class="op-7">Salut,</span>
                      <span class="fw-bold"><?= htmlspecialchars($currentUser['prenom'] ?? 'Admin') ?></span>
                    </span>
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                            <img
                              src="<?= !empty($currentUser['photo_profil']) ? '../uploads/profils/' . htmlspecialchars($currentUser['photo_profil']) : 'assets/img/profile.jpg' ?>"
                              alt="image profile"
                              class="avatar-img rounded"
                            />
                          </div>
                          <div class="u-text">
                            <h4><?= htmlspecialchars(($currentUser['prenom'] ?? '') . ' ' . ($currentUser['nom'] ?? '')) ?></h4>
                            <p class="text-muted"><?= htmlspecialchars($currentUser['email'] ?? '') ?></p>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="../view/auth/logout.php">
                          <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                        </a>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>

        <div class="container">
          <div class="page-inner">

