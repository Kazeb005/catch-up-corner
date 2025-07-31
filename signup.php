<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectBasedOnRole();
}

// Error messages
$errorMessages = [
    'emptyfullname' => 'Full name is required',
    'invalidemail' => 'Please enter a valid email address',
    'shortpassword' => 'Password must be at least 6 characters',
    'invalidrole' => 'Please select a valid role',
    'emailtaken' => 'Email already registered',
    'dberror' => 'Database error occurred'
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
    <title>Sign Up - Catch-Up Corner</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h1>Welcome to Catch-Up Corner</h1>
        <p>Access your educational materials and stay connected</p>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form id="signupForm" action="includes/auth.php" method="POST">
            <h2>Sign Up</h2>
            <div class="form-group">
                <label for="fullname">Full Name *</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required
                       value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" placeholder="your.email@example.com" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <span class="error-message" id="emailError"></span>
            </div>
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" placeholder="Create a password (min. 6 characters)" required>
                <span class="error-message" id="passwordError"></span>
            </div>
            <div class="form-group">
                <label for="role">Role *</label>
                <select id="role" name="role" required>
                    <option value="">Select role</option>
                    <option value="student" <?php echo (isset($_POST['role']) && $_POST['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
                    <option value="teacher" <?php echo (isset($_POST['role']) && $_POST['role'] === 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                </select>
            </div>
            <button type="submit" name="signup" class="btn">Create Account</button>
            <p>Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>