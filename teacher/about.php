<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

redirectIfNotLoggedIn();
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../student/materials.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>about</title>
</head>
<body>
    <?php include './nav.php'; ?>
    <main>
                <div class="dashboard-content about">
        <div class="dashboard-content-head">

            <div class="dashboard-content-intro description">
                <h3>About Catch-Up Corner</h3>
                <p>We're dedicated to ensuring no student falls behind.</p>
                <p> platform bridges the gap between classroom learning and independent study, making education accessible and continuous.</p>
            </div>
    <div class="material-part">
        <div class="material-container ">
            <div class="material-card">
                <h3>Student-Centered</h3>
                <p>Designed with students in mind, providing easy access to missed materials and resources.</p>
            </div>

            <div class="material-card">
                <h3>Teacher-Friendly</h3>
                <p>Simple tools for educators to upload and organize materials for absent students.</p>
            </div>
            <div class="material-card">
                <h3>Always Available</h3>
                <p>24/7 access to learning materials, ensuring education never stops.</p>
            </div>
        </div>
    </div>
    </main>
<footer class="footer">
    <p>© Catch-Up-Corner 2025</p>
</footer>
</body>
</html>