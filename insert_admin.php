<?php
include 'config.php';  

// Secure password hashing
$password = password_hash('securepassword', PASSWORD_DEFAULT);

// Maghanda ng SQL query
$sql = "INSERT INTO admin (username, password, email, full_name) 
        VALUES ('admin_user', '$password', 'admin@email.com', 'Admin Name')";

// I-execute ang query
if (mysqli_query($conn, $sql)) {
    echo "New admin added successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}

// Isara ang connection
mysqli_close($conn);
?>


