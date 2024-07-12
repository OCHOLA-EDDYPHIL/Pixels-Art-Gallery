<?php

/**
 * Handles database operations, ensuring the database and required tables exist.
 * Utilizes the Singleton pattern to ensure only one instance of the connection is created.
 *
 * The Singleton pattern is used here to prevent multiple instances of the database connection
 * from being created. This is important for efficiency and to avoid potential conflicts or
 * excessive resource usage. By using a single instance, all database operations are funneled
 * through the same connection, ensuring consistency and reliability.
 *
 * PDO (PHP Data Objects) is used for the database connection because it provides a uniform method
 * for accessing various databases. It supports multiple database drivers, offering flexibility
 * and a consistent API for database interactions. PDO also offers prepared statements, which
 * help prevent SQL injection attacks, making it a secure choice for database operations.
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
     *
     * The constructor is protected to enforce the Singleton pattern, ensuring that
     * instances can only be created within the class itself. This method initializes
     * the database connection and checks for the existence of required tables, creating
     * them if they do not exist. This setup process ensures that the application has a
     * solid foundation to perform its database operations.
     */
    protected function __construct()
    {
        $this->checkAndCreateDatabase();
        $this->connectToDb();
        $this->checkAndCreateTable("users", "CREATE TABLE IF NOT EXISTS users (
            id INT(6) UNSIGNED AUTO_INCREMENT,
            email_address VARCHAR(50) NOT NULL UNIQUE,
            pwd VARCHAR(255) NOT NULL,
            reg_date TIMESTAMP,
            PRIMARY KEY (id)
        )");
        $this->checkAndCreateTable("photos", "CREATE TABLE IF NOT EXISTS photos (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            caption TEXT,
            user_id VARCHAR(50) NOT NULL,
            reg_date TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(email_address)
        )");
        $this->checkAndCreateTable("urls", "CREATE TABLE IF NOT EXISTS urls (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            long_url VARCHAR(2048) NOT NULL,
            short_code VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    /**
     * Checks and creates the database if it does not exist.
     *
     * This method attempts to connect to the MySQL server and create the database
     * if it does not already exist. It uses PDO to execute a raw SQL command that
     * creates the database. This is a crucial step in ensuring that the application
     * has a database to work with before attempting to connect to it or create tables.
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
     *
     * This method creates a new PDO instance to connect to the database using the
     * credentials and database name provided. It sets the error mode to exception
     * mode, which means that PDO will throw exceptions on errors, allowing for better
     * error handling. This connection is essential for performing all subsequent
     * database operations.
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
     *
     * This method uses PDO to execute a SQL statement that creates a table if it does
     * not already exist. This ensures that the application's required tables are available
     * for storing and retrieving data. The method takes the name of the table and the SQL
     * statement as parameters, providing flexibility to create various tables as needed.
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
     *
     * This static method checks if an instance of the Databasehandler class already exists.
     * If not, it creates one and stores it in a static property. This ensures that there is
     * only ever one instance of the Databasehandler, adhering to the Singleton pattern. This
     * instance can then be used throughout the application to perform database operations.
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
     *
     * This method uses a prepared statement to query the database for a user's ID based on
     * their email address. Prepared statements are used here for security, to prevent SQL
     * injection attacks. If the user is found, their ID is returned; otherwise, null is returned.
     * This method is useful for operations that require the user's ID, such as linking records
     * in related tables.
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
     * Provides access to the PDO connection.
     *
     * @return PDO The PDO connection instance.
     *
     * This method returns the PDO instance used for the database connection. It allows
     * other parts of the application to use the established connection for executing
     * queries and other database operations. This is part of the encapsulation provided
     * by the Databasehandler class, centralizing the database connection logic.
     */
    public function connect()
    {
        return $this->pdo;
    }
}