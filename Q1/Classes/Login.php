<?php
        session_start(); // Start session

class Login extends Databasehandler
{
    private $email;
    private $pwd;
    private $login_errors = [];
    public function __construct($email, $pwd)
    {
        $this->email = $email;
        $this->pwd = $pwd;
    }

    public function loginUser()
    {

        if ($this->isEmptySubmit()) {
            $this->login_errors[] = "Email and Password fields cannot be empty"; // You can use this to display an error message on the login page [index.php]
        }

        if (empty($this->login_errors) && !$this->getUser()) {
            $this->login_errors[] = "Invalid email or password";
        }

        if (!empty($this->login_errors)) {
            $_SESSION['login_errors'] = $this->login_errors;
            header("Location: ../index.php");
            exit();
        }

        $_SESSION['email'] = $this->email;
        header("Location: ../home.php");
        exit();
    }

    private function isEmptySubmit()
    {
        return empty($this->email) || empty($this->pwd);
    }

    private function getUser()
    {
        $query = "SELECT pwd FROM users WHERE email_address = :email";
        $statement = $this->connect()->prepare($query);
        $statement->bindParam(':email', $this->email);
        $statement->execute();

        if ($statement->rowCount() > 0) {
            $user = $statement->fetch(PDO::FETCH_ASSOC);
            if (password_verify($this->pwd, $user['pwd'])) {
                return true; // Password matches
            }
        }
        return false; // No user found or password does not match
    }
}