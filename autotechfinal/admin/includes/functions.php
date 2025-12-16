<?php
require_once __DIR__ . '/../../config/config.php';

function getTableData($table, $pdo) {
    try {
        $sql = "SELECT * FROM $table ORDER BY date_creation DESC, id_" . substr($table, 0, -1) . " DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
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

