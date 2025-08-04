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
    <title>contact</title>
</head>
<body>
    <?php include 'nav.php'; ?>
    <main>
         <div class="dashboard-content about">
        <div class="dashboard-content-head">

            <div class="dashboard-content-intro description">
                <h3>Get in Touch</h3>
                <p>Have questions about Catch-Up Corner? We're here to help! Reach out to us and we'll get back to you as soon as possible.</p>
            </div>
    <div class="material-part">
        <div class="material-container ">
            <div class="material-card">
                <h3>Email us</h3>
                <p>Send us an email anytime</p>
                <p>support@catchupcorner.edu</p>
            </div>

            <div class="material-card">
                <h3>Call Us</h3>
                <p>Mon-Fri, 9am-5pm Gmt+2</p>
                <p>+(250)781234567 </p>
            </div>
        </div>
    </div>
    </main>
<footer class="footer">
    <p>© Catch-Up-Corner 2025</p>
</footer>
</body>
</html>