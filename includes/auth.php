<?php
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Login
    if (isset($_POST['login'])) {
        $email = sanitizeInput($_POST['email']);
        $password = sanitizeInput($_POST['password']);
        
        // Validate inputs
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: loginandsignup.php?action=login&error=invalidemail");
            exit();
        }
        
        if (strlen($password) < 6) {
            header("Location: loginandsignup.php?action=login&error=shortpassword");
            exit();
        }
        
        // Check user credentials
        $stmt = $conn->prepare("SELECT id, fullname, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();
                
                // Redirect based on role
                if ($user['role'] === 'teacher') {
                    header("Location: teacher/materials.php");
                } else {
                    header("Location: student/materials.php");
                }
                exit();
            }
        }
        
        // Login failed
        header("Location: loginandsignup.php?action=login&error=invalidcredentials");
        exit();
    }
    
    // Handle Signup
    if (isset($_POST['signup'])) {
        $fullname = sanitizeInput($_POST['fullname']);
        $email = sanitizeInput($_POST['email']);
        $password = sanitizeInput($_POST['password']);
        $role = sanitizeInput($_POST['role']);
        
        // Validate inputs
        if (empty($fullname)) {
            header("Location: loginandsignup.php?error=emptyfullname");
            exit();
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: loginandsignup.php?error=invalidemail");
            exit();
        }
        
        if (strlen($password) < 6) {
            header("Location: loginandsignup.php?error=shortpassword");
            exit();
        }
        
        if (!in_array($role, ['student', 'teacher'])) {
            header("Location: loginandsignup.php?error=invalidrole");
            exit();
        }
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            header("Location: loginandsignup.php?error=emailtaken");
            exit();
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullname, $email, $hashedPassword, $role);
        
        if ($stmt->execute()) {
            // Get the new user's ID
            $user_id = $stmt->insert_id;
            
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
            $_SESSION['last_activity'] = time();
            
            // Redirect based on role
            if ($role === 'teacher') {
                header("Location: teacher/materials.php");
            } else {
                header("Location: student/materials.php");
            }
            exit();
        } else {
            header("Location: loginandsignup.php?error=dberror");
            exit();
        }
    }
}

// If not a POST request or no action specified
header("Location: loginandsignup.php");
exit();
?>