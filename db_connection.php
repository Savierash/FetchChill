<?php

$host = 'localhost'; 
$dbname = 'fetch_chill_db'; 
$username = 'admin123'; 
$password = 'admin123'; 

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
