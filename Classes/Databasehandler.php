<?php

/**
 * Class Databasehandler
 *
 * This class is responsible for handling database connections and operations.
 * It implements the Singleton pattern to ensure that only one instance of the database connection exists throughout the application.
 */
class Databasehandler
{
    /**
     * @var Databasehandler|null The single instance of the Databasehandler class.
     */
    private static $instance = null;

    /**
     * @var string Database host.
     */
    private $host = "localhost";

    /**
     * @var string Database name.
     */
    private $dbname = "SemesterProject";

    /**
     * @var string Database user.
     */
    private $username = "root";

    /**
     * @var string Database password.
     */
    private $password = "";

    /**
     * @var PDO The PDO instance for database connection.
     */
    private $pdo;

    /**
     * The constructor is protected to prevent creating a new instance outside of the class.
     * It initializes the PDO connection to the database.
     */
    protected function __construct()
    {
        try {
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * Returns the singleton instance of the Databasehandler class.
     *
     * @return Databasehandler The singleton instance.
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Databasehandler();
        }
        return self::$instance;
    }

    /**
     * Retrieves the email address of a user from the database.
     *
     * @param string $email The email address to search for.
     * @return string|null The email address if found, null otherwise.
     */
    public function getUserEmail($email)
    {
        $sql = "SELECT email_address FROM users WHERE email_address = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindValue(1, $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result['email_address'];
        } else {
            return null;
        }
    }

    /**
     * Provides the PDO connection instance.
     *
     * @return PDO The PDO connection instance.
     */
    public function connect()
    {
        return $this->pdo;
    }
}