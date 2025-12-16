<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Gestion des Rendez-vous - Administration AutoTech</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

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
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
    
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="assets/css/plugin/datatables/datatables.min.css">
    <style>
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .action-btn {
            min-width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .filter-card {
            transition: all 0.3s ease;
        }
        .filter-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .appointment-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .appointment-card:hover {
            transform: translateX(5px);
        }
        .badge-new {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        .calendar-icon {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar (identique au dashboard) -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="index.html" class="logo">
                        <img src="assets/img/kaiadmin/off_logo.png" alt="navbar brand" class="navbar-brand" height="20" />
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
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">
                        <li class="nav-item">
                            <a href="index.html">
                                <i class="fas fa-tachometer-alt"></i>
                                <p>Tableau de Bord</p>
                            </a>
                        </li>
                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Administration</h4>
                        </li>
                        <li class="nav-item">
                            <a href="admin-techniciens-list.php">
                                <i class="fas fa-user-cog"></i>
                                <p>Techniciens</p>
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="admin-rdv-list.php">
                                <i class="fas fa-calendar-alt"></i>
                                <p>Rendez-vous</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin-profile.php">
                                <i class="fas fa-user-shield"></i>
                                <p>Profil Admin</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../frontOffice/index.php">
                                <i class="fas fa-eye"></i>
                                <p>Voir le Site</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="main-panel">
            <!-- Main Header (identique au dashboard) -->
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
                            <img src="assets/img/off_logo.png" alt="navbar brand" class="navbar-brand" height="20" />
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
                </div>
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="submit" class="btn btn-search pe-1">
                                        <i class="fa fa-search search-icon"></i>
                                    </button>
                                </div>
                                <input type="text" placeholder="Rechercher un rendez-vous..." class="form-control" id="globalSearch" />
                            </div>
                        </nav>
                        <!-- Identique au dashboard pour les notifications et profil -->
                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <!-- ... (identique au dashboard) ... -->
                        </ul>
                    </div>
                </nav>
            </div>

            <div class="container">
                <div class="page-inner">
                    <!-- Page Header -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h1 class="display-6 fw-bold text-primary mb-2">
                                        <i class="fas fa-calendar-alt me-3"></i>Gestion des Rendez-vous
                                    </h1>
                                    <p class="text-muted mb-0">Gérez toutes les demandes de rendez-vous des boutiques</p>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-primary" id="refreshBtn">
                                        <i class="fas fa-sync-alt me-2"></i>Actualiser
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card stat-card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title text-muted text-uppercase small">Total RDV</h6>
                                            <h2 class="fw-bold text-primary mb-0" id="totalRdv">0</h2>
                                        </div>
                                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-calendar-check text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">Toutes les demandes</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card stat-card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title text-muted text-uppercase small">En Attente</h6>
                                            <h2 class="fw-bold text-warning mb-0" id="pendingRdv">0</h2>
                                        </div>
                                        <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-clock text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-warning">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            En attente de validation
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card stat-card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title text-muted text-uppercase small">Confirmés</h6>
                                            <h2 class="fw-bold text-success mb-0" id="confirmedRdv">0</h2>
                                        </div>
                                        <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-success">
                                            <i class="fas fa-thumbs-up me-1"></i>
                                            Rendez-vous validés
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card stat-card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title text-muted text-uppercase small">Aujourd'hui</h6>
                                            <h2 class="fw-bold text-info mb-0" id="todayRdv">0</h2>
                                        </div>
                                        <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-calendar-day text-info"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-info">
                                            <i class="fas fa-calendar me-1"></i>
                                            RDV du jour
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="mb-3 text-muted">
                                        <i class="fas fa-filter me-2"></i>Filtres
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-lg-3 col-md-6">
                                            <select class="form-select" id="statusFilter">
                                                <option value="">Tous les statuts</option>
                                                <option value="en_attente">En attente</option>
                                                <option value="confirme">Confirmé</option>
                                                <option value="annule">Annulé</option>
                                                <option value="termine">Terminé</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <input type="date" class="form-control" id="dateFilter" />
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <select class="form-select" id="technicianFilter">
                                                <option value="">Tous les techniciens</option>
                                                <!-- Techniciens seront chargés dynamiquement -->
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <button class="btn btn-outline-secondary w-100" id="resetFilters">
                                                <i class="fas fa-redo me-2"></i>Réinitialiser
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 text-primary">
                                        <i class="fas fa-list me-2"></i>Liste des Rendez-vous
                                    </h5>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-download me-2"></i>Exporter
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" id="exportCSV"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                                            <li><a class="dropdown-item" href="#" id="exportPDF"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                                            <li><a class="dropdown-item" href="#" id="exportExcel"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="appointmentsTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Boutique</th>
                                                    <th>Technicien</th>
                                                    <th>Date & Heure</th>
                                                    <th>Service</th>
                                                    <th>Statut</th>
                                                    <th>Créé le</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="appointmentsBody">
                                                <!-- Les rendez-vous seront chargés ici -->
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-5">
                                                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                                        Chargement des rendez-vous...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div class="text-muted small" id="tableInfo">
                                            Affichage de 0 à 0 sur 0 entrées
                                        </div>
                                        <nav>
                                            <ul class="pagination pagination-sm mb-0" id="pagination">
                                                <!-- La pagination sera générée dynamiquement -->
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour les détails du rendez-vous -->
                    <div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Détails du Rendez-vous</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" id="modalBody">
                                    <!-- Les détails seront chargés ici -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <button type="button" class="btn btn-primary" id="saveChangesBtn">Enregistrer les modifications</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid d-flex justify-content-center">
                    <div class="copyright">
                        2025, AutoTech - Système de gestion des rendez-vous
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Datatables -->
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Sweet Alert -->
    <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="assets/js/kaiadmin.min.js"></script>

    <script>
        // Variables globales
        let currentPage = 1;
        const itemsPerPage = 10;
        let allAppointments = [];
        let filteredAppointments = [];
        let technicians = [];

        // Fonction pour formater la date
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;
            
            return date.toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Fonction pour obtenir le badge du statut
        function getStatusBadge(status) {
            const statusMap = {
                'en_attente': { class: 'bg-warning', text: 'En attente' },
                'confirme': { class: 'bg-success', text: 'Confirmé' },
                'annule': { class: 'bg-danger', text: 'Annulé' },
                'termine': { class: 'bg-info', text: 'Terminé' }
            };
            
            const statusInfo = statusMap[status] || { class: 'bg-secondary', text: status };
            return `<span class="badge ${statusInfo.class} status-badge">${statusInfo.text}</span>`;
        }

        // Fonction pour charger les statistiques
        async function loadStatistics() {
            try {
                const response = await fetch('simple_stats.php');
                const stats = await response.json();
                
                const total = stats.pending + stats.confirmed + stats.cancelled;
                document.getElementById('totalRdv').textContent = total;
                document.getElementById('pendingRdv').textContent = stats.pending;
                document.getElementById('confirmedRdv').textContent = stats.confirmed;
                
                // Charger les RDV du jour
                const todayResponse = await fetch('getTodayAppointments.php');
                const todayStats = await todayResponse.json();
                document.getElementById('todayRdv').textContent = todayStats.count || 0;
                
            } catch (error) {
                console.error('Erreur lors du chargement des statistiques:', error);
            }
        }

        // Fonction pour charger les techniciens
        async function loadTechnicians() {
            try {
                const response = await fetch('getTechnicians.php');
                technicians = await response.json();
                
                const select = document.getElementById('technicianFilter');
                select.innerHTML = '<option value="">Tous les techniciens</option>';
                
                technicians.forEach(tech => {
                    const option = document.createElement('option');
                    option.value = tech.id_technicien;
                    option.textContent = tech.nom + ' ' + tech.prenom;
                    select.appendChild(option);
                });
                
            } catch (error) {
                console.error('Erreur lors du chargement des techniciens:', error);
            }
        }

        // Fonction pour charger tous les rendez-vous
        async function loadAllAppointments() {
            try {
                const response = await fetch('getAllAppointments.php');
                allAppointments = await response.json();
                filteredAppointments = [...allAppointments];
                renderTable();
                updatePagination();
                loadStatistics();
                
            } catch (error) {
                console.error('Erreur lors du chargement des rendez-vous:', error);
                document.getElementById('appointmentsBody').innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-danger py-5">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Erreur lors du chargement des données
                        </td>
                    </tr>
                `;
            }
        }

        // Fonction pour filtrer les rendez-vous
        function filterAppointments() {
            const status = document.getElementById('statusFilter').value;
            const date = document.getElementById('dateFilter').value;
            const technicianId = document.getElementById('technicianFilter').value;
            const searchTerm = document.getElementById('globalSearch').value.toLowerCase();
            
            filteredAppointments = allAppointments.filter(appointment => {
                // Filtre par statut
                if (status && appointment.statut !== status) return false;
                
                // Filtre par date
                if (date) {
                    const appointmentDate = new Date(appointment.date_rdv).toISOString().split('T')[0];
                    if (appointmentDate !== date) return false;
                }
                
                // Filtre par technicien
                if (technicianId && appointment.technicien_id != technicianId) return false;
                
                // Recherche globale
                if (searchTerm) {
                    const searchStr = `
                        ${appointment.id_rdv}
                        ${appointment.boutique_nom || ''}
                        ${appointment.technicien_nom || ''}
                        ${appointment.type_intervention || ''}
                        ${appointment.statut || ''}
                    `.toLowerCase();
                    
                    if (!searchStr.includes(searchTerm)) return false;
                }
                
                return true;
            });
            
            currentPage = 1;
            renderTable();
            updatePagination();
        }

        // Fonction pour afficher les rendez-vous dans le tableau
        function renderTable() {
            const tbody = document.getElementById('appointmentsBody');
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const pageAppointments = filteredAppointments.slice(startIndex, endIndex);
            
            if (pageAppointments.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fas fa-calendar-times me-2"></i>
                            Aucun rendez-vous trouvé
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = '';
            
            pageAppointments.forEach(appointment => {
                const row = document.createElement('tr');
                row.className = 'appointment-card';
                row.style.borderLeftColor = appointment.statut === 'en_attente' ? '#ffc107' : 
                                          appointment.statut === 'confirme' ? '#198754' : 
                                          appointment.statut === 'annule' ? '#dc3545' : '#6c757d';
                
                const createdDate = new Date(appointment.date_creation);
                const isNew = (Date.now() - createdDate.getTime()) < 24 * 60 * 60 * 1000; // Moins de 24h
                
                row.innerHTML = `
                    <td>
                        <strong>#${appointment.id_rdv}</strong>
                        ${isNew ? '<span class="badge bg-danger badge-new ms-2">Nouveau</span>' : ''}
                    </td>
                    <td>
                        <div class="fw-bold">${appointment.boutique_nom || 'N/A'}</div>
                        <small class="text-muted">${appointment.boutique_email || ''}</small>
                    </td>
                    <td>
                        ${appointment.technicien_nom ? `
                            <div>${appointment.technicien_nom}</div>
                            <small class="text-muted">${appointment.technicien_specialite || ''}</small>
                        ` : '<span class="text-muted">Non assigné</span>'}
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt calendar-icon me-2 text-primary"></i>
                            <div>
                                <div>${formatDate(appointment.date_rdv).split(' à ')[0]}</div>
                                <small class="text-muted">${formatDate(appointment.date_rdv).split(' à ')[1] || ''}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">${appointment.type_intervention || 'Non spécifié'}</span>
                    </td>
                    <td>${getStatusBadge(appointment.statut)}</td>
                    <td>
                        <small class="text-muted">${formatDate(appointment.date_creation)}</small>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary action-btn me-1 view-btn" 
                                data-id="${appointment.id_rdv}"
                                title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${appointment.statut === 'en_attente' ? `
                            <button class="btn btn-sm btn-outline-success action-btn me-1 confirm-btn" 
                                    data-id="${appointment.id_rdv}"
                                    title="Confirmer">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger action-btn cancel-btn" 
                                    data-id="${appointment.id_rdv}"
                                    title="Annuler">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : ''}
                    </td>
                `;
                
                tbody.appendChild(row);
            });
            
            // Mettre à jour les informations du tableau
            const totalItems = filteredAppointments.length;
            const start = totalItems > 0 ? startIndex + 1 : 0;
            const end = Math.min(endIndex, totalItems);
            document.getElementById('tableInfo').textContent = 
                `Affichage de ${start} à ${end} sur ${totalItems} entrées`;
        }

        // Fonction pour mettre à jour la pagination
        function updatePagination() {
            const totalPages = Math.ceil(filteredAppointments.length / itemsPerPage);
            const pagination = document.getElementById('pagination');
            
            pagination.innerHTML = '';
            
            // Bouton précédent
            const prevItem = document.createElement('li');
            prevItem.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevItem.innerHTML = `
                <a class="page-link" href="#" data-page="${currentPage - 1}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            `;
            pagination.appendChild(prevItem);
            
            // Numéros de page
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            
            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                const pageItem = document.createElement('li');
                pageItem.className = `page-item ${i === currentPage ? 'active' : ''}`;
                pageItem.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                pagination.appendChild(pageItem);
            }
            
            // Bouton suivant
            const nextItem = document.createElement('li');
            nextItem.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextItem.innerHTML = `
                <a class="page-link" href="#" data-page="${currentPage + 1}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            `;
            pagination.appendChild(nextItem);
        }

        // Fonction pour charger les détails d'un rendez-vous dans le modal
        async function loadAppointmentDetails(id) {
            try {
                const response = await fetch(`getAppointmentDetails.php?id=${id}`);
                const appointment = await response.json();
                
                const modalBody = document.getElementById('modalBody');
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informations de la boutique</h6>
                            <p><strong>Nom :</strong> ${appointment.boutique_nom || 'N/A'}</p>
                            <p><strong>Email :</strong> ${appointment.boutique_email || 'N/A'}</p>
                            <p><strong>Téléphone :</strong> ${appointment.boutique_telephone || 'N/A'}</p>
                            <p><strong>Adresse :</strong> ${appointment.boutique_adresse || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Détails du rendez-vous</h6>
                            <p><strong>ID :</strong> #${appointment.id_rdv}</p>
                            <p><strong>Date :</strong> ${formatDate(appointment.date_rdv)}</p>
                            <p><strong>Service :</strong> ${appointment.type_intervention || 'N/A'}</p>
                            <p><strong>Statut :</strong> ${getStatusBadge(appointment.statut)}</p>
                            <p><strong>Créé le :</strong> ${formatDate(appointment.date_creation)}</p>
                        </div>
                    </div>
                    
                    ${appointment.description ? `
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Description</h6>
                                <div class="bg-light p-3 rounded">
                                    ${appointment.description}
                                </div>
                            </div>
                        </div>
                    ` : ''}
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Assigner un technicien</h6>
                            <select class="form-select" id="assignTechnician">
                                <option value="">Sélectionner un technicien</option>
                                ${technicians.map(tech => `
                                    <option value="${tech.id_technicien}" ${tech.id_technicien == appointment.technicien_id ? 'selected' : ''}>
                                        ${tech.nom} ${tech.prenom} - ${tech.specialite || 'Sans spécialité'}
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Changer le statut</h6>
                            <select class="form-select" id="changeStatus">
                                <option value="en_attente" ${appointment.statut === 'en_attente' ? 'selected' : ''}>En attente</option>
                                <option value="confirme" ${appointment.statut === 'confirme' ? 'selected' : ''}>Confirmé</option>
                                <option value="annule" ${appointment.statut === 'annule' ? 'selected' : ''}>Annulé</option>
                                <option value="termine" ${appointment.statut === 'termine' ? 'selected' : ''}>Terminé</option>
                            </select>
                        </div>
                    </div>
                `;
                
                // Sauvegarder l'ID du rendez-vous dans le bouton de sauvegarde
                document.getElementById('saveChangesBtn').dataset.id = id;
                
            } catch (error) {
                console.error('Erreur lors du chargement des détails:', error);
                document.getElementById('modalBody').innerHTML = `
                    <div class="alert alert-danger">
                        Erreur lors du chargement des détails du rendez-vous
                    </div>
                `;
            }
        }

        // Fonction pour mettre à jour un rendez-vous
        async function updateAppointment(id, updates) {
            try {
                const response = await fetch('updateAppointment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_rdv: id,
                        ...updates
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    swal({
                        title: 'Succès!',
                        text: result.message,
                        icon: 'success',
                        button: 'OK'
                    }).then(() => {
                        // Recharger les données
                        loadAllAppointments();
                        $('#appointmentModal').modal('hide');
                    });
                } else {
                    swal({
                        title: 'Erreur!',
                        text: result.message,
                        icon: 'error',
                        button: 'OK'
                    });
                }
                
            } catch (error) {
                console.error('Erreur lors de la mise à jour:', error);
                swal({
                    title: 'Erreur!',
                    text: 'Une erreur est survenue lors de la mise à jour',
                    icon: 'error',
                    button: 'OK'
                });
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Charger les données initiales
            loadTechnicians();
            loadAllAppointments();
            
            // Événements de filtrage
            document.getElementById('statusFilter').addEventListener('change', filterAppointments);
            document.getElementById('dateFilter').addEventListener('change', filterAppointments);
            document.getElementById('technicianFilter').addEventListener('change', filterAppointments);
            document.getElementById('globalSearch').addEventListener('input', filterAppointments);
            document.getElementById('resetFilters').addEventListener('click', function() {
                document.getElementById('statusFilter').value = '';
                document.getElementById('dateFilter').value = '';
                document.getElementById('technicianFilter').value = '';
                document.getElementById('globalSearch').value = '';
                filterAppointments();
            });
            
            // Bouton d'actualisation
            document.getElementById('refreshBtn').addEventListener('click', function() {
                loadAllAppointments();
                loadTechnicians();
                swal({
                    title: 'Actualisation',
                    text: 'Les données ont été actualisées',
                    icon: 'success',
                    timer: 1000,
                    buttons: false
                });
            });
            
            // Pagination
            document.getElementById('pagination').addEventListener('click', function(e) {
                e.preventDefault();
                if (e.target.tagName === 'A') {
                    const page = parseInt(e.target.dataset.page);
                    if (page && page !== currentPage) {
                        currentPage = page;
                        renderTable();
                        updatePagination();
                    }
                }
            });
            
            // Délégation d'événements pour les boutons d'action
            document.addEventListener('click', function(e) {
                // Bouton de visualisation
                if (e.target.closest('.view-btn')) {
                    const id = e.target.closest('.view-btn').dataset.id;
                    loadAppointmentDetails(id);
                    $('#appointmentModal').modal('show');
                }
                
                // Bouton de confirmation
                if (e.target.closest('.confirm-btn')) {
                    const id = e.target.closest('.confirm-btn').dataset.id;
                    swal({
                        title: 'Confirmer le rendez-vous?',
                        text: 'Voulez-vous vraiment confirmer ce rendez-vous?',
                        icon: 'warning',
                        buttons: ['Annuler', 'Confirmer'],
                        dangerMode: true
                    }).then((confirm) => {
                        if (confirm) {
                            updateAppointment(id, { statut: 'confirme' });
                        }
                    });
                }
                
                // Bouton d'annulation
                if (e.target.closest('.cancel-btn')) {
                    const id = e.target.closest('.cancel-btn').dataset.id;
                    swal({
                        title: 'Annuler le rendez-vous?',
                        text: 'Voulez-vous vraiment annuler ce rendez-vous?',
                        icon: 'warning',
                        buttons: ['Annuler', 'Confirmer l\'annulation'],
                        dangerMode: true
                    }).then((confirm) => {
                        if (confirm) {
                            updateAppointment(id, { statut: 'annule' });
                        }
                    });
                }
            });
            
            // Sauvegarder les modifications dans le modal
            document.getElementById('saveChangesBtn').addEventListener('click', function() {
                const id = this.dataset.id;
                const technicianId = document.getElementById('assignTechnician').value;
                const status = document.getElementById('changeStatus').value;
                
                const updates = {};
                if (technicianId) updates.technicien_id = technicianId;
                if (status) updates.statut = status;
                
                if (Object.keys(updates).length > 0) {
                    updateAppointment(id, updates);
                } else {
                    swal({
                        title: 'Aucune modification',
                        text: 'Aucune modification n\'a été effectuée',
                        icon: 'info',
                        button: 'OK'
                    });
                }
            });
            
            // Exporter les données
            document.getElementById('exportCSV').addEventListener('click', function(e) {
                e.preventDefault();
                swal({
                    title: 'Export CSV',
                    text: 'Fonctionnalité d\'export CSV à implémenter',
                    icon: 'info',
                    button: 'OK'
                });
            });
            
            document.getElementById('exportPDF').addEventListener('click', function(e) {
                e.preventDefault();
                swal({
                    title: 'Export PDF',
                    text: 'Fonctionnalité d\'export PDF à implémenter',
                    icon: 'info',
                    button: 'OK'
                });
            });
            
            document.getElementById('exportExcel').addEventListener('click', function(e) {
                e.preventDefault();
                swal({
                    title: 'Export Excel',
                    text: 'Fonctionnalité d\'export Excel à implémenter',
                    icon: 'info',
                    button: 'OK'
                });
            });
        });
    </script>
</body>
</html>