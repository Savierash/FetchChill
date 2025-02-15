<?php
include 'config.php';

if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";
    if (mysqli_query($conn, $query)) {
        echo "Sign up successful! <a href='signin.php'>Sign in here</a>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

ini_set('max_execution_time', 300);  // 300 seconds (5 minutes)

?>
