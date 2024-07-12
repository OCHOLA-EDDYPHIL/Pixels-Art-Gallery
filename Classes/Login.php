<?php
// Starts a new session or resumes an existing session
session_start();

/**
 * The Login class extends the Databasehandler class to provide user authentication functionality.
 */
class Login extends Databasehandler
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
     * @var array $login_errors Stores any login errors encountered during the login process.
     */
    private array $login_errors = [];

    /**
     * Constructor for the Login class.
     *
     * @param string $email User's email address.
     * @param string $pwd User's password.
     */
    public function __construct(string $email, string $pwd)
    {
        $this->email = $email;
        $this->pwd = $pwd;
    }

    /**
     * Attempts to log the user in.
     *
     * Validates the user's credentials, sets session variables upon successful login,
     * and redirects to the appropriate page based on the outcome of the login attempt.
     */
    public function loginUser(): void
    {
        // Checks if the email and password fields were submitted empty
        if ($this->isEmptySubmit()) {
            $this->login_errors[] = "Email and Password fields cannot be empty";
        }

        // If there are no errors and the user cannot be authenticated, add an error message
        if (empty($this->login_errors) && !$this->getUser()) {
            $this->login_errors[] = "Invalid email or password";
        }

        // If there are any login errors, store them in the session and redirect to the login page
        if (!empty($this->login_errors)) {
            $_SESSION['login_errors'] = $this->login_errors;
            header("Location: ../index.php");
            exit();
        }

        // On successful login, set the user's email in the session and redirect to the main page
        $_SESSION['email'] = $this->email;
        header("Location: ../main.php");
        exit();
    }

    /**
     * Checks if the email or password fields are empty.
     *
     * @return bool Returns true if either the email or password field is empty, false otherwise.
     */
    private function isEmptySubmit(): bool
    {
        return empty($this->email) || empty($this->pwd);
    }

    /**
     * Authenticates the user by checking the provided credentials against the database.
     *
     * @return bool Returns true if the user exists and the password matches, false otherwise.
     */
    private function getUser(): bool
    {
        // Prepare and execute the query to find the user by email
        $query = "SELECT pwd FROM users WHERE email_address = :email";
        $statement = Databasehandler::getInstance()->connect()->prepare($query);
        $statement->bindParam(':email', $this->email);
        $statement->execute();

        // If a user is found, verify the password
        if ($statement->rowCount() > 0) {
            $user = $statement->fetch(PDO::FETCH_ASSOC);
            if (password_verify($this->pwd, $user['pwd'])) {
                return true; // Password matches
            }
        }
        return false; // No user found or password does not match
    }
}