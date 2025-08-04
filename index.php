<?php
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectBasedOnRole();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catch-Up Corner</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<main>
        <div class="hero">
        <h1>Welcome to Catch-Up Corner</h1>
        <p>Access educational materials and stay connected with your studies</p>
        <div class="cta-buttons">
            <a href="loginandsignup.php?action=login" class="btn">Login</a>
            <a href="loginandsignup.php" class="btn btn-outline">Sign Up</a>
    </div>
    
</main>
<footer class="footer">
    <p>© Catch-Up-Corner 2025</p>
</footer>

</body>
</html>