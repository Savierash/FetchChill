<?php
include 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Insert into database
    $query = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";
    
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "Message sent successfully!";
        } else {
            echo "Error: Unable to send message.";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Could not prepare statement.";
    }

    mysqli_close($conn);
}
?>
