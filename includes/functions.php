<?php
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: ../loginandsignup.php");
        exit();
    }
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function redirectBasedOnRole() {
    if (isLoggedIn()) {
        $role = getUserRole();
        
        // Determine the correct paths relative to document root
        $teacherPath = $_SERVER['DOCUMENT_ROOT'] . '/catch-up-corner/teacher/materials.php';
        $studentPath = $_SERVER['DOCUMENT_ROOT'] . '/catch-up-corner/student/materials.php';
        $dashboardPath = $_SERVER['DOCUMENT_ROOT'] . '/catch-up-corner/dashboard.php';
        
        // Check which path exists and is accessible
        if ($role === 'teacher' && file_exists($teacherPath)) {
            header("Location: /catch-up-corner/teacher/materials.php");
        } elseif ($role === 'student' && file_exists($studentPath)) {
            header("Location: /catch-up-corner/student/materials.php");
        } elseif (file_exists($dashboardPath)) {
            header("Location: /catch-up-corner/dashboard.php");
        } else {
            // Ultimate fallback
            header("Location: /catch-up-corner/loginandsignup.php");
        }
        exit();
    }
}
?>