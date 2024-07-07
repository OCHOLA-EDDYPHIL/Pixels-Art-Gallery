<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];

    require_once "../Classes/Databasehandler.php";
    require_once "../Classes/Login.php";

    $Login = new Login($email, $pwd);
    $Login->loginUser();


//    header("location: ".$_SERVER['DOCUMENT_ROOT']."../index.php?success");

}