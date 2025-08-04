<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

redirectIfNotLoggedIn();
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../student/materials.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $material_id = intval($_POST['id']);
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $type = sanitizeInput($_POST['type']);
    $subject = sanitizeInput($_POST['subject']);

    // Verify the material belongs to this teacher
    $stmt = $conn->prepare("SELECT id FROM materials WHERE id = ? AND teacher_id = ?");
    $stmt->bind_param("ii", $material_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        // Update the material
        $update_stmt = $conn->prepare("UPDATE materials SET title = ?, description = ?, type = ?, subject = ? WHERE id = ?");
        $update_stmt->bind_param("ssssi", $title, $description, $type, $subject, $material_id);
        
        if ($update_stmt->execute()) {
            header("Location: materials.php?updated=1");
        } else {
            header("Location: materials.php?error=updatefailed");
        }
    } else {
        header("Location: materials.php?error=notfound");
    }
    exit();
}

header("Location: materials.php");
?>