<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if coming from index with login intent
$show_login = isset($_GET['action']) && $_GET['action'] === 'login';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/auth.php';
    exit();
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
    <div class="auth-container">
        <div class="auth-header">
            <h1>Welcome to Catch-Up Corner</h1>
            <p>Access your educational materials and stay connected</p>
        </div>

        <div class="auth-tabs">
            <button id="loginTab" class="auth-tab <?php echo $show_login ? 'active' : ''; ?>">Login</button>
            <button id="signupTab" class="auth-tab <?php echo !$show_login ? 'active' : ''; ?>">Sign Up</button>
        </div>

        <div class="auth-forms">
            <!-- Login Form -->
            <form id="loginForm" method="POST" action="loginandsignup.php"
                class="<?php echo $show_login ? 'form-active' : 'form-hidden'; ?>">
                <input type="hidden" name="login" value="1">

                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" name="email" class="form-control"
                        placeholder="your.email@example.com" required>
                    <span class="error-message" id="loginEmailError"></span>
                </div>

                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="password" class="form-control"
                        placeholder="Your password" required>
                    <span class="error-message" id="loginPasswordError"></span>
                </div>

                <button type="submit" class="form-btn">Login</button>
            </form>

            <!-- Sign Up Form -->
            <form id="signupForm" method="POST" action="loginandsignup.php"
                class="<?php echo !$show_login ? 'form-active' : 'form-hidden'; ?>">
                <input type="hidden" name="signup" value="1">

                <div class="form-group">
                    <label for="signupFullname">Full Name</label>
                    <input type="text" id="signupFullname" name="fullname" class="form-control"
                        placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label for="signupEmail">Email</label>
                    <input type="email" id="signupEmail" name="email" class="form-control"
                        placeholder="your.email@example.com" required>
                    <span class="error-message" id="signupEmailError"></span>
                </div>

                <div class="form-group">
                    <label for="signupPassword">Password</label>
                    <input type="password" id="signupPassword" name="password" class="form-control"
                        placeholder="Create a password (min. 6 characters)" required>
                    <span class="error-message" id="signupPasswordError"></span>
                </div>

                <div class="form-group">
                    <label for="signupRole">Role</label>
                    <select id="signupRole" name="role" class="form-control" required>
                        <option value="">Select role</option>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                </div>

                <button type="submit" class="form-btn">Create Account</button>
            </form>
        </div>
    </div>

    <script>
        // Tab switching
        const loginTab = document.getElementById('loginTab');
        const signupTab = document.getElementById('signupTab');
        const loginForm = document.getElementById('loginForm');
        const signupForm = document.getElementById('signupForm');

        loginTab.addEventListener('click', () => {
            loginTab.classList.add('active');
            signupTab.classList.remove('active');
            loginForm.classList.add('form-active');
            loginForm.classList.remove('form-hidden');
            signupForm.classList.add('form-hidden');
            signupForm.classList.remove('form-active');
        });

        signupTab.addEventListener('click', () => {
            signupTab.classList.add('active');
            loginTab.classList.remove('active');
            signupForm.classList.add('form-active');
            signupForm.classList.remove('form-hidden');
            loginForm.classList.add('form-hidden');
            loginForm.classList.remove('form-active');
        });

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            let valid = true;
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            // Reset errors
            document.getElementById('loginEmailError').style.display = 'none';
            document.getElementById('loginPasswordError').style.display = 'none';

            // Validate email
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('loginEmailError').textContent = 'Please enter a valid email address';
                document.getElementById('loginEmailError').style.display = 'block';
                valid = false;
            }

            // Validate password
            if (password.length < 6) {
                document.getElementById('loginPasswordError').textContent = 'Password must be at least 6 characters';
                document.getElementById('loginPasswordError').style.display = 'block';
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });

        document.getElementById('signupForm').addEventListener('submit', function(e) {
            let valid = true;
            const email = document.getElementById('signupEmail').value;
            const password = document.getElementById('signupPassword').value;

            // Reset errors
            document.getElementById('signupEmailError').style.display = 'none';
            document.getElementById('signupPasswordError').style.display = 'none';

            // Validate email
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('signupEmailError').textContent = 'Please enter a valid email address';
                document.getElementById('signupEmailError').style.display = 'block';
                valid = false;
            }

            // Validate password
            if (password.length < 6) {
                document.getElementById('signupPasswordError').textContent = 'Password must be at least 6 characters';
                document.getElementById('signupPasswordError').style.display = 'block';
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>