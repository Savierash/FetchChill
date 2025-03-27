<?php
class PetDatabase {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $host = "localhost";
        $username = "root";
        $password = "";
        $dbname = "fetch_chill_db";

        $this->conn = new mysqli($host, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new PetDatabase();
        }
        return self::$instance->conn;
    }
}
?>