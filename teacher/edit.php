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

// Get material details
$material_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM materials WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $material_id, $_SESSION['user_id']);
$stmt->execute();
$material = $stmt->get_result()->fetch_assoc();

if (!$material) {
    header("Location: materials.php?error=notfound");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $type = sanitizeInput($_POST['type']);
    $subject = sanitizeInput($_POST['subject']);
    
    $stmt = $conn->prepare("UPDATE materials SET title = ?, description = ?, type = ?, subject = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $description, $type, $subject, $material_id);
    
    if ($stmt->execute()) {
        header("Location: materials.php?updated=1");
        exit();
    } else {
        header("Location: edit.php?id=$material_id&error=dberror");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Material - Catch-Up Corner</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>Edit Material</h1>
        <a href="materials.php" class="btn">Back to Materials</a>
    </div>

    <div class="dashboard-content">
        <form method="POST">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($material['title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($material['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="type">Type *</label>
                <select id="type" name="type" required>
                    <option value="notes" <?php echo $material['type'] === 'notes' ? 'selected' : ''; ?>>Notes</option>
                    <option value="quiz" <?php echo $material['type'] === 'quiz' ? 'selected' : ''; ?>>Quiz</option>
                    <option value="assignment" <?php echo $material['type'] === 'assignment' ? 'selected' : ''; ?>>Assignment</option>
                    <option value="video" <?php echo $material['type'] === 'video' ? 'selected' : ''; ?>>Video</option>
                    <option value="other" <?php echo $material['type'] === 'other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="subject">Subject *</label>
                <select id="subject" name="subject" required>
                    <option value="biology" <?php echo $material['subject'] === 'biology' ? 'selected' : ''; ?>>Biology</option>
                    <option value="mathematics" <?php echo $material['subject'] === 'mathematics' ? 'selected' : ''; ?>>Mathematics</option>
                    <option value="physics" <?php echo $material['subject'] === 'physics' ? 'selected' : ''; ?>>Physics</option>
                    <option value="chemistry" <?php echo $material['subject'] === 'chemistry' ? 'selected' : ''; ?>>Chemistry</option>
                    <option value="history" <?php echo $material['subject'] === 'history' ? 'selected' : ''; ?>>History</option>
                    <option value="english" <?php echo $material['subject'] === 'english' ? 'selected' : ''; ?>>English</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Current File:</label>
                <p><?php echo basename($material['file_path']); ?> (<?php echo round($material['file_size'] / 1024 / 1024, 2); ?> MB)</p>
            </div>
            
            <button type="submit" class="btn">Update Material</button>
        </form>
    </div>
</body>
</html>