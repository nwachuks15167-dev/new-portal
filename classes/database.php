<?php
class Database {

    // Connection details
    private $host     = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "newportal";

    // Holds the connection
    protected $conn;

    // Runs automatically when object is created
    public function __construct() {
        $this->conn = mysqli_connect(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        } 
    }

}
?>