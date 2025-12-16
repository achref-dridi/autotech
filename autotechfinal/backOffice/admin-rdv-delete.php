<?php
// ===============================
//  Charger le DAO
// ===============================
require_once __DIR__ . "/../../Model/rendezvousDAO.php";

// ===============================
//  Vérifier ID
// ===============================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /ESSAI/View/backOffice/admin-rdv-by-technicien.php?error=id_invalide");
    exit;
}

$id_rdv = (int) $_GET['id'];

// ===============================
//  Supprimer via DAO
// ===============================
$dao = new RendezVousDAO();
$result = $dao->delete($id_rdv);

// ===============================
//  Redirection selon résultat
// ===============================
if ($result['success'] && $result['rows_affected'] > 0) {
    header("Location: /ESSAI/View/backOffice/admin-rdv-by-technicien.php?delete=ok");
} else {
    header("Location: /ESSAI/View/backOffice/admin-rdv-by-technicien.php?delete=fail");
}

exit;
?>
