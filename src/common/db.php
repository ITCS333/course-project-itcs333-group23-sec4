<?php
/**
 * Database Connection Class
 * This class handles the connection to the MySQL database using PDO.
 * Other PHP files will include this file and call getConnection() to
 * obtain the PDO object.
 */

class Database {

    private $host = "localhost";       
    private $db_name = "course";        
    private $username = "admin";        
    private $password = "password123";  
    private $conn; 

    /**
     * Create and return a PDO database connection
     * 
     * @return PDO
     */
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

            error_log("Database connection error: " . $e->getMessage());

            exit("Database connection failed.");
        }

        return $this->conn;
    }
}
?>
