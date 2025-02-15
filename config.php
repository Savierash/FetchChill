<?php
$server = "localhost";
$user = "root"; // default user for XAMPP
$pass = ""; // default password for XAMPP is empty
$db = "fetch_chill_db"; // your database name

// Create a connection
$conn = mysqli_connect($server, $user, $pass, $db);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
