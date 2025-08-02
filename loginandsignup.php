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
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .auth-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
        }
        .auth-header {
            text-align: center;
            padding: 30px 20px 20px;
            background: linear-gradient(135deg, #4a6fa5, #166088);
            color: white;
        }
        .auth-header h1 {
            margin: 0;
            font-size: 28px;
        }
        .auth-header p {
            margin: 5px 0 0;
            opacity: 0.9;
        }
        .auth-tabs {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
        }
        .auth-tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            background: #f8fafc;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .auth-tab.active {
            background: white;
            color: #166088;
            border-bottom: 2px solid #166088;
        }
        .auth-form {
            padding: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4a5568;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 16px;
            transition: border 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: #166088;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #166088;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #134e75;
        }
        .error-message {
            color: #e53e3e;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        .form-active {
            display: block;
        }
        .form-hidden {
            display: none;
        }
    </style>
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
                
                <button type="submit" class="btn">Login</button>
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
                
                <button type="submit" class="btn">Create Account</button>
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