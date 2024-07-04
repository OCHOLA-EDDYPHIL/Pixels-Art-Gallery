<?php
        session_start(); // Ensure session is started

class Signup extends Databasehandler
{
    private $email;
    private $pwd;
    private $signup_errors = [];
    public function __construct($email, $pwd)
    {
        $this->email = $email;
        $this->pwd = $pwd;
    }

    private function insertUser()
    {
        $query = "INSERT INTO users(email_address, pwd) VALUES(:email, :pwd)";
        $statement = $this->connect()->prepare($query);

        $hashedPwd = password_hash($this->pwd, PASSWORD_DEFAULT);
        $statement->bindParam(':email', $this->email);
        $statement->bindParam(':pwd', $hashedPwd);
        $statement->execute();
    }

    public function signupUser()
    {
        // Error handlers
        if ($this->isEmptySubmit()) {
            $this->signup_errors[] = 'empty_fields';
        }
        if ($this->invalidEmail()) {
            $this->signup_errors[] = 'invalid_email';
        }
        if ($this->emailTaken()) {
            $this->signup_errors[] = 'email_taken';
        }
        if (!empty($this->signup_errors)) {
            $_SESSION['signup_errors'] = $this->signup_errors;
            header("Location: ../index.php");
            exit();
        }
        // If no errors, signup user
        $this->insertUser();
        // Optionally, set a success message or similar
        $_SESSION['signup_success'] = 'true';
        header("Location: ../home.php");
        exit();
    }

    private function isEmptySubmit()
    {
        return empty($this->email) || empty($this->pwd);
    }

    private function invalidEmail()
    {
        return !filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    private function emailTaken()
    {
        return $this->checkUser($this->email);
    }

    private function checkUser($email)
    {
        $query = "SELECT * FROM users WHERE email_address = :email";
        $statement = $this->connect()->prepare($query);

        if (!$statement->execute([':email' => $email])) {
            $_SESSION['error'] = 'database_error'; // Handle database execution errors
            header("Location: /index.php");
            exit();
        }

        return $statement->rowCount() > 0;
    }
}