<?php

class Databasehandler{
    private $host = "localhost";
    private $dbname = "SemesterProject";
    private $username = "root";
    private $password = "";

    public function __construct()
    {
        // Constructor is empty because no initialization is needed
    }

    protected function connect()
    {
        try {
            $pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    public function getUserIdByEmail($email) {
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

}