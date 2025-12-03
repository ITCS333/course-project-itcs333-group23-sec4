<?php

class Database {

    private $host = "localhost";
    private $db_name = "course";
    private $username = "admin";
    private $password = "password123";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Database connection failed"]);
            exit;
        }

        return $this->conn;
    }
}
