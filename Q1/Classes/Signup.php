<?php

class Signup extends Databasehandler
{
    private $email;
    private $pwd;

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
            header("Location: /index.php");
//            header("Location: /index.php?error=empty_fields");
            die();
        }
        if ($this->invalidEmail()) {
//            header("Location: /index.php?error=invalid_email");
            header("Location: /index.php");
            die();
        }
        if ($this->emailTaken()) {
//            header("Location: /index.php?error=email_taken");
            header("Location: /index.php");
            die();
        }
        // If no errors, signup user
        $this->insertUser();

    }

    private function isEmptySubmit()
    {
        if (isset($this->email) && isset($this->pwd)) {
            return false;
        } else {
            return true;
        }
    }

    private function invalidEmail()
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            return true;
        }
    }

    private function emailTaken()
    {
        if (!$this->checkUser($this->email)) {
            return false;
        } else {
            return true;
        }
    }

    private function checkUser($email)
    {
        $query = "SELECT * FROM users WHERE email_address = :email";
        $statement = $this->connect()->prepare($query);

        if (!$statement->execute(array($email))) {
            $statement = null;
//            header('Location: /index.php?error=statement_failed');
            header("Location: /index.php");
            exit();
        }

        if ($statement->rowCount() > 0) {
            return false;
        } else {
            return true;
        }
    }


}
