<?php
// db_connection.php - Database Connection File
require_once 'db_connection.php';
session_start();

function showError($message) {
    return "<p class='error-message'>$message</p>";
}

// Forgot Password Logic for Admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    if (empty($email)) {
        echo showError("Please enter the admin's email.");
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?"); 
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc(); 
            $token = bin2hex(random_bytes(50));
            $hashed_token = password_hash($token, PASSWORD_DEFAULT);
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            
            $stmt = $conn->prepare("UPDATE admin SET reset_token = ?, reset_token_expires = ? WHERE email = ?"); 
            $stmt->bind_param('sss', $hashed_token, $expires_at, $email);
            $stmt->execute();
            
            
            $reset_link = "https://yourwebsite.com/reset_password.php?token=$token";
            echo "Password reset link for admin: $reset_link"; 
        } else {
            echo showError("Admin email not found.");
        }
        $stmt->close();
    }
}

// Reset Password Logic for Admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token'])) {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo showError("Passwords do not match!");
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE reset_token_expires > NOW()"); 
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($admin = $result->fetch_assoc()) { 
            if (password_verify($token, $admin['reset_token'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE admin SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE email = ?"); 
                $stmt->bind_param('ss', $hashed_password, $admin['email']);
                $stmt->execute();
                
                echo "Admin password successfully reset!";
                exit();
            }
        }
        echo showError("Invalid or expired token!");
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css" />
    <title>Fetch & Chill</title>
</head>
<body>
<div class="container">
    <div class="signin-signup">
        <div class="classleft">

        <!-- Back Button -->
        <button class="back-btn" onclick="history.back()">
            <i class='bx bx-arrow-back'></i>
        </button>
            
        <form action="reset_password.php" method="POST" class="forgot-password-form">
            
            

                <h2 class="title">Forgot Password</h2>

                <!-- New Password Field -->
                <div class="input-fields">
                    <i class='bx bxs-lock-alt'></i>
                    <input type="password" name="new_password" placeholder="Enter new password" required />
                </div>

                <!-- Confirm Password Field -->
                <div class="input-fields">
                    <i class='bx bxs-lock-alt'></i>
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required />
                </div>

                <input type="submit" value="Reset Password" class="btn solid" />

            </form>
        </div>

        <div class="classright">
            <img src="img/undraw_my-password_iyga.svg" class="image" alt="Reset Password Illustration" />
        </div>
    </div>
</div>


    <script src="script.js"></script>
</body>
</html>
