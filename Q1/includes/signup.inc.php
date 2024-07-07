<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];

    require_once "../Classes/Databasehandler.php";
    require_once "../Classes/Signup.php";

    $signup = new Signup($email, $pwd);
    $signup->signupUser();

//    header("location: ".$_SERVER['DOCUMENT_ROOT']."../index.php?success");

}