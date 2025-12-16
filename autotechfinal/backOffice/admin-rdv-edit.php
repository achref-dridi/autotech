<?php 
$page_title = "Modifier Rendez-vous - Administration";

include __DIR__ . "/partials/admin-header.php";
require_once __DIR__ . "/../../Model/rendezvousDAO.php";

$id_rdv = $_GET['id'] ?? null;

if (!$id_rdv || !is_numeric($id_rdv)) {
    header("Location: /ESSAI/View/backOffice/admin-rdv-by-technicien.php");
    exit;
}

$dao = new RendezVousDAO();
$rdv = $dao->getById($id_rdv);

if (!$rdv) {
    header("Location: admin-rdv-by-technicien.php");
    exit;
}

// Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $result = $dao->update($id_rdv, $_POST);

    if ($result['success']) {
        header("Location: /ESSAI/View/backOffice/admin-rdv-by-technicien.php?id=" . $rdv['id_technicien'] . "&message=Rendez-vous modifié avec succès&type=success");
    } else {
        header("Location: /ESSAI/View/backOffice/admin-rdv-by-technicien.php?id=" . $rdv['id_technicien'] . "&message=Erreur lors de la modification&type=danger");
    }
    exit;
}

// Formatter date pour input
$date_for_form = date('Y-m-d\TH:i', strtotime($rdv['date_rdv']));
?>

<section class="dashboard-section py-5 bg-light min-vh-100">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="card border-0 shadow-sm">

<div class="card-header bg-white py-3">
<h1 class="h4 text-primary mb-0"><i class="fas fa-edit me-2"></i>Modifier le rendez-vous #<?= $id_rdv ?></h1>
</div>

<div class="card-body">

<form method="POST">

<div class="row g-3">

<div class="col-md-6">
<label class="form-label fw-bold">Date et heure *</label>

<!-- ❗ required retiré pour désactiver le contrôle HTML5 -->
<input type="datetime-local"
       id="date_rdv"
       name="date_rdv"
       class="form-control"
       value="<?= $date_for_form ?>"
       min="<?= date('Y-m-d\TH:i') ?>">
</div>

<div class="col-md-6">
<label class="form-label fw-bold">Type d'intervention *</label>
<select name="type_intervention" class="form-select" required>
    <option value="Réparation" <?= $rdv['type_intervention']=='Réparation' ? 'selected':'' ?>>Réparation</option>
    <option value="Maintenance" <?= $rdv['type_intervention']=='Maintenance' ? 'selected':'' ?>>Maintenance</option>
    <option value="Installation" <?= $rdv['type_intervention']=='Installation' ? 'selected':'' ?>>Installation</option>
    <option value="Diagnostic" <?= $rdv['type_intervention']=='Diagnostic' ? 'selected':'' ?>>Diagnostic</option>
    <option value="Autre" <?= $rdv['type_intervention']=='Autre' ? 'selected':'' ?>>Autre</option>
</select>
</div>

<div class="col-12">
<label class="form-label fw-bold">Commentaire</label>
<textarea name="commentaire" class="form-control" rows="4"><?= htmlspecialchars($rdv['commentaire'] ?? '') ?></textarea>
</div>

<input type="hidden" name="id_technicien" value="<?= $rdv['id_technicien'] ?>">

</div>

<div class="d-flex justify-content-between mt-4">
<a href="/ESSAI/View/backOffice/admin-rdv-by-technicien.php?id=<?= $rdv['id_technicien'] ?>" class="btn btn-outline-secondary">
    <i class="fas fa-arrow-left me-2"></i>Annuler
</a>

<button type="submit" class="btn btn-primary">
<i class="fas fa-save me-2"></i>Enregistrer
</button>
</div>

</form>

</div>

</div>

</div>
</div>
</div>
</section>

<!-- ✅ SCRIPT JAVASCRIPT AJOUTÉ À LA FIN (fonctionnel) -->
<script>
document.addEventListener('DOMContentLoaded', () => {

    const dateInput = document.getElementById('date_rdv');
    const form = document.querySelector('form');

    function validate() {

        // Rien choisi
        if (!dateInput.value || dateInput.value.length < 16) {
            alert("Veuillez choisir une date et une heure valides.");
            return false;
        }

        // Chrome : ajouter des secondes sinon Invalid Date
        const selected = new Date(dateInput.value + ":00");
        const now = new Date();

        // Date non valide
        if (isNaN(selected.getTime())) {
            alert("Date invalide.");
            dateInput.value = "";
            return false;
        }

        // Empêcher dates passées
        if (selected < now) {
            alert("Vous ne pouvez pas choisir une date passée.");
            dateInput.value = "";
            return false;
        }

        const hour = selected.getHours();

        // Empêcher hors heures de travail
        if (hour < 10 || hour >= 15) {
            alert("Les rendez-vous doivent être entre 10h et 15h.");
            dateInput.value = "";
            return false;
        }

        return true;
    }

    // Valider quand l'utilisateur change la date
    dateInput.addEventListener("change", validate);

    // Valider avant soumission du formulaire
    form.addEventListener("submit", function(e) {
        if (!validate()) {
            e.preventDefault();
        }
    });

});
</script>


<?php include __DIR__ . "/partials/admin-footer.php"; ?>
