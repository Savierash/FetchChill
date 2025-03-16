<?php
session_start();
include 'pet_connection.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id']; 

   
    $sql = "DELETE FROM admin WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $userId); 
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "User deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting user: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Error preparing SQL statement: " . $conn->error;
    }

   
    header("Location: dashboard.php");
    exit();
}
?>