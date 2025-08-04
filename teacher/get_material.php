<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

redirectIfNotLoggedIn();
if ($_SESSION['role'] !== 'teacher') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $material_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM materials WHERE id = ? AND teacher_id = ?");
    $stmt->bind_param("ii", $material_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Material not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>