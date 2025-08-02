<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = array();


session_destroy();


header("Location: loginandsignup.php");
exit();
?>