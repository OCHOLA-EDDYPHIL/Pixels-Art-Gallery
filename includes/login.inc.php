<?php
// Check if the server request method is POST, indicating form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user's email and password from POST data
    $email = $_POST['email']; // User's email
    $pwd = $_POST['pwd']; // User's password

    // Include the necessary classes for database handling and user login
    require_once "../Classes/Databasehandler.php";
    require_once "../Classes/Login.php";

    // Instantiate a new Login object with user's email and password
    $Login = new Login($email, $pwd);
    // Attempt to log the user in with the provided credentials
    $Login->loginUser();
}