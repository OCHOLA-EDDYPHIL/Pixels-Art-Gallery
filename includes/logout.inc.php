<?php
// Start a new session or resume the existing one
session_start();
echo "Session started<br>";

// Unset all session variables
session_unset();
echo "Session unset<br>";

// Destroy the session
session_destroy();
echo "Session destroyed<br>";

// Redirect the user to the main page
header("Location: ../main.php");
exit("Redirecting to main.php");