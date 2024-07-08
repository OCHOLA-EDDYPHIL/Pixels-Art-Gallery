<?php

// Check if the form was submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input from the form
    $email = $_POST['email']; // User's email
    $pwd = $_POST['pwd']; // User's password

    // Include the necessary PHP classes for database handling and user signup
    require_once "../Classes/Databasehandler.php";
    require_once "../Classes/Signup.php";

    // Create a new Signup object with the user's email and password
    $signup = new Signup($email, $pwd);
    // Execute the signup process
    $signup->signupUser();

}