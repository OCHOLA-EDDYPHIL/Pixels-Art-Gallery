<?php

class Databasehandler
{
    private static $instance = null;
    private $host = "localhost";
    private $dbname = "SemesterProject";
    private $username = "root";
    private $password = "";
    private $pdo;

    protected function __construct()
    {
        try {
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Databasehandler();
        }
        return self::$instance;
    }

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

    public function connect()
    {
        return $this->pdo;
    }
}