<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectBasedOnRole();
}

// Handle errors
$errorMessages = [
    'invalidemail' => 'Please enter a valid email address',
    'shortpassword' => 'Password must be at least 6 characters',
    'invalidcredentials' => 'Invalid email or password'
];

$error = isset($_GET['error']) && isset($errorMessages[$_GET['error']]) 
    ? $errorMessages[$_GET['error']] 
    : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Catch-Up Corner</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h1>Welcome to Catch-Up Corner</h1>
        <p>Access your educational materials and stay connected</p>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form id="loginForm" action="includes/auth.php" method="POST">
            <h2>Login</h2>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="your.email@example.com" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <span class="error-message" id="emailError"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <span class="error-message" id="passwordError"></span>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </form>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>