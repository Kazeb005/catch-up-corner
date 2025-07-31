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
    // Validate inputs
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $type = sanitizeInput($_POST['type']);
    $subject = sanitizeInput($_POST['subject']);
    
    // File upload handling
    $targetDir = "../uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = basename($_FILES["file"]["name"]);
    $targetFile = $targetDir . uniqid() . '_' . $fileName;
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $fileSize = $_FILES["file"]["size"];
    
    // Check file size (20MB max)
    if ($fileSize > 20000000) {
        header("Location: materials.php?error=filetoolarge");
        exit();
    }
    
    // Allow certain file formats
    $allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'mov', 'avi'];
    if (!in_array($fileType, $allowedTypes)) {
        header("Location: materials.php?error=invalidfiletype");
        exit();
    }
    
    // Upload file
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO materials (teacher_id, title, description, type, subject, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssi", $_SESSION['user_id'], $title, $description, $type, $subject, $targetFile, $fileType, $fileSize);
        
        if ($stmt->execute()) {
            header("Location: materials.php?success=1");
            exit();
        } else {
            // Delete the uploaded file if DB insert failed
            unlink($targetFile);
            header("Location: materials.php?error=dberror");
            exit();
        }
    } else {
        header("Location: materials.php?error=uploaderror");
        exit();
    }
}

header("Location: materials.php");
exit();
?>