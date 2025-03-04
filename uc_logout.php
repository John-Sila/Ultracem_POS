<?php
session_start();

// Unset the specific session variable
// unset($_SESSION['username']);
// unset($_SESSION['actualName']);
// unset($_SESSION['permissions']);
// unset($_SESSION['db']);
session_unset(); // Unsets all session variables
session_destroy(); // Destroys the session
// Optionally destroy the entire session if needed
// session_destroy(); // Uncomment if you want to destroy the entire session

// Redirect to the login page
header('Location: login.php');
exit();




?>
