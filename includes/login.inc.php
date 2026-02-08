<?php
require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/csrf.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }

    $email = $_POST['email'] ?? '';
    $pwd = $_POST['pwd'] ?? '';

    require_once "../Classes/Databasehandler.php";
    require_once "../Classes/Login.php";

    $Login = new Login($email, $pwd);
    $Login->loginUser();
} else {
    http_response_code(405);
    exit('Method not allowed');
}
