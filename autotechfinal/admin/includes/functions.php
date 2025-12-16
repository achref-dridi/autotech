<?php
require_once __DIR__ . '/../../config/config.php';

function getTableData($table, $pdo) {
    try {
        // Map table names to their ID columns
        $idColumns = [
            'utilisateur' => 'id_utilisateur',
            'vehicule' => 'id_vehicule',
            'boutique' => 'id_boutique',
            'technicien' => 'id_technicien',
            'rendez_vous' => 'id_rdv',
            'reservation' => 'id_reservation',
            'trajet' => 'id_trajet',
            'reservation_trajet' => 'id_reservation_trajet'
        ];
        
        $idColumn = $idColumns[$table] ?? 'id';
        
        // Try to order by date_creation first, then by ID
        $sql = "SELECT * FROM $table ORDER BY date_creation DESC, $idColumn DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        // If date_creation doesn't exist, just order by ID
        try {
            $idColumn = $idColumns[$table] ?? 'id';
            $sql = "SELECT * FROM $table ORDER BY $idColumn DESC";
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e2) {
            error_log("Error in getTableData for table $table: " . $e2->getMessage());
            return [];
        }
    }
}

function deleteRecord($table, $idColumn, $id, $pdo) {
    try {
        $sql = "DELETE FROM $table WHERE $idColumn = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        return false;
    }
}

function getRecordById($table, $idColumn, $id, $pdo) {
    try {
        $sql = "SELECT * FROM $table WHERE $idColumn = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function uploadFile($file, $directory, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return null;
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedTypes)) {
        return null;
    }
    
    $filename = time() . '_' . uniqid() . '.' . $extension;
    $uploadPath = __DIR__ . '/../../uploads/' . $directory . '/' . $filename;
    
    if (!is_dir(dirname($uploadPath))) {
        mkdir(dirname($uploadPath), 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return $filename;
    }
    
    return null;
}

function deleteFile($filename, $directory) {
    if ($filename) {
        $filePath = __DIR__ . '/../../uploads/' . $directory . '/' . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}

