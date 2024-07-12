<?php
session_start(); // Ensure session is started

require_once 'Databasehandler.php'; // Include the Databasehandler class

/**
 * The Signup class handles user registration processes.
 * It extends the Databasehandler class to utilize its database connection and methods.
 */
class Signup extends Databasehandler
{
    /**
     * @var string $email User's email address.
     */
    private string $email;

    /**
     * @var string $pwd User's password.
     */
    private string $pwd;

    /**
     * @var array $signup_errors Stores any errors encountered during the signup process.
     */
    private array $signup_errors = [];

    /**
     * Constructor for the Signup class.
     * Initializes the user's email and password, and ensures a database connection.
     *
     * @param string $email User's email address.
     * @param string $pwd User's password.
     */
    public function __construct(string $email, string $pwd)
    {
        parent::__construct(); // Ensure database connection and base table creation
        $this->email = $email;
        $this->pwd = $pwd;
    }

    /**
     * Handles the user signup process, including validation and insertion into the database.
     * Sets session variables for errors or success messages and redirects accordingly.
     */
    public function signupUser(): void
    {
        // Error handlers
        if ($this->isEmptySubmit()) {
            $this->signup_errors[] = 'The fields are empty';
        }
        if ($this->invalidEmail()) {
            $this->signup_errors[] = 'The email is invalid';
        }
        if ($this->emailTaken()) {
            $this->signup_errors[] = 'The email belongs to another user';
        }
        if (!$this->isPasswordComplex($this->pwd)) {
            $this->signup_errors[] = 'The password must be more than 5 characters and include numbers';
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
        header("Location: ../index.php");
        exit();
    }

    /**
     * Checks if the email or password fields are empty.
     *
     * @return bool Returns true if either field is empty, false otherwise.
     */
    private function isEmptySubmit(): bool
    {
        return empty($this->email) || empty($this->pwd);
    }

    /**
     * Validates the email address format.
     *
     * @return bool Returns true if the email is invalid, false otherwise.
     */
    private function invalidEmail(): bool
    {
        return !filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Checks if the provided email is already taken by another user.
     *
     * @return bool Returns true if the email is taken, false otherwise.
     */
    private function emailTaken(): bool
    {
        return $this->checkUser($this->email);
    }

    /**
     * Queries the database to check for the existence of a user with the given email.
     *
     * @param string $email The email address to check.
     * @return bool Returns true if a user with the email exists, false otherwise.
     */
    private function checkUser(string $email): bool
    {
        $query = "SELECT email_address FROM users WHERE email_address = :email";
        $statement = Databasehandler::getInstance()->connect()->prepare($query);

        if (!$statement->execute([':email' => $email])) {
            $_SESSION['error'] = 'database_error'; // Handle database execution errors
            header("Location: ../index.php");
            exit();
        }

        return $statement->rowCount() > 0;
    }

    /**
     * Checks if the password meets complexity requirements.
     *
     * @param string $password The password to check.
     * @return bool Returns true if the password is complex enough, false otherwise.
     */
    private function isPasswordComplex(string $password): bool
    {
        return strlen($password) > 5 && preg_match('/\d/', $password);
    }

    /**
     * Inserts a new user into the database with the provided email and hashed password.
     */
    private function insertUser(): void
    {
        $query = "INSERT INTO users(email_address, pwd) VALUES(:email, :pwd)";
        $statement = Databasehandler::getInstance()->connect()->prepare($query);

        $hashedPwd = password_hash($this->pwd, PASSWORD_DEFAULT);
        $statement->bindParam(':email', $this->email);
        $statement->bindParam(':pwd', $hashedPwd);
        $statement->execute();
    }
}