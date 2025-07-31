<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

redirectIfNotLoggedIn();
if ($_SESSION['role'] !== 'student') {
    header("Location: ../teacher/materials.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$material_id = intval($_GET['id']);

// Get material details
$stmt = $conn->prepare("SELECT * FROM materials WHERE id = ?");
$stmt->bind_param("i", $material_id);
$stmt->execute();
$material = $stmt->get_result()->fetch_assoc();

if (!$material) {
    header("Location: materials.php?error=notfound");
    exit();
}

// Update download count
$update_stmt = $conn->prepare("UPDATE materials SET download_count = download_count + 1 WHERE id = ?");
$update_stmt->bind_param("i", $material_id);
$update_stmt->execute();

// Track download in database (optional)
$track_stmt = $conn->prepare("INSERT INTO downloads (material_id, student_id) VALUES (?, ?)");
$track_stmt->bind_param("ii", $material_id, $_SESSION['user_id']);
$track_stmt->execute();

// Send file to browser
if (file_exists($material['file_path'])) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($material['file_path']).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($material['file_path']));
    flush();
    readfile($material['file_path']);
    exit();
} else {
    header("Location: materials.php?error=filenotfound");
    exit();
}
?>