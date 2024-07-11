<?php

/**
 * Handles database operations, ensuring the database and required tables exist.
 * Utilizes the Singleton pattern to ensure only one instance of the connection is created.
 */
class Databasehandler
{
    private static $instance = null; // Holds the singleton instance
    private $host = "localhost"; // Database host
    private $dbname = "project"; // Database name
    private $username = "root"; // Database username
    private $password = ""; // Database password
    private $pdo; // PDO instance for database connection

    /**
     * Constructor is protected to prevent creating a new instance outside the class.
     * Initializes the database by checking/creating the database and tables.
     */
    protected function __construct()
    {
        $this->checkAndCreateDatabase();
        $this->connectToDb();
        $this->checkAndCreateTable("users", "CREATE TABLE IF NOT EXISTS users (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email_address VARCHAR(50) NOT NULL,
            pwd VARCHAR(255) NOT NULL,
            reg_date TIMESTAMP
        )");
        $this->checkAndCreateTable("photos", "CREATE TABLE IF NOT EXISTS photos (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            caption TEXT,
            user_id INT(6) UNSIGNED,
            FOREIGN KEY (user_id) REFERENCES users(email_address) 
        )");
    }

    /**
     * Checks and creates the database if it does not exist.
     */
    private function checkAndCreateDatabase()
    {
        try {
            $pdo = new PDO("mysql:host=$this->host", $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS $this->dbname");
        } catch (PDOException $e) {
            die("DB creation failed: " . $e->getMessage());
        }
    }

    /**
     * Establishes a connection to the database.
     */
    private function connectToDb()
    {
        try {
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * Checks and creates a table if it does not exist.
     *
     * @param string $tableName The name of the table to check/create.
     * @param string $tableSql The SQL statement to create the table.
     */
    protected function checkAndCreateTable($tableName, $tableSql)
    {
        try {
            $this->pdo->exec($tableSql);
        } catch (PDOException $e) {
            die("Creation of table $tableName failed: " . $e->getMessage());
        }
    }

    /**
     * Returns the singleton instance of the Databasehandler.
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
     * Retrieve a user's ID by their email.
     *
     * @param string $email The user's email.
     * @return int|null The user's ID if found, null otherwise.
     */
    public function getUserIdByEmail($email)
    {
        $sql = "SELECT id FROM users WHERE email_address = :email"; // Corrected column name to email_address
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int)$result['id'] : null;
    }

    /**
     * Provides access to the PDO connection.
     *
     * @return PDO The PDO connection instance.
     */
    public function connect()
    {
        return $this->pdo;
    }
}