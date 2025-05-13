<?php
class Database {
    private $host = "localhost";
    private $db_name = "online_course_enrollment";
    private $username = "root";  
    private $password = "";      
    public $conn;

    // Method for database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

    // Method for SELECT queries
    public function executeSelectQuery($con, $sql) {
        try {
            $stmt = $con->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Method for INSERT, UPDATE, DELETE queries
    public function executeQuery($con, $sql) {
        try {
            $stmt = $con->prepare($sql);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
?>
