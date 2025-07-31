<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();

if ($_SESSION['role'] !== 'student') {
    header("Location: ../teacher/materials.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h1>
        <p>Catch up on missed materials and stay on track with your studies</p>
    </div>
    <div class="dashboard-content">
        <h2>Available Materials</h2>
        <p>This is the student dashboard</p>
        <a href="../logout.php" class="btn">Logout</a>
    </div>
</body>
</html>