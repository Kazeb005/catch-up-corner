<?php
require_once 'includes/functions.php';
session_start();
redirectIfNotLoggedIn();

// Fallback dashboard if role-specific pages don't work
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Catch-Up Corner</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h1>
    <p>Your role: <?php echo htmlspecialchars($_SESSION['role']); ?></p>
    <p>Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    
    <h2>Debug Information</h2>
    <pre><?php print_r($_SESSION); ?></pre>
    
    <p><a href="logout.php">Logout</a></p>
</body>
</html>