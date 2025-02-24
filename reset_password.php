<?php
require_once 'db_connection.php'; // Ensure the path is correct

session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ?");
    $stmt->bind_param('s', $token); // 's' for string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token found, proceed to password reset
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password === $confirm_password) {
                // Update the password in the database
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
                $stmt->bind_param('ss', $hashed_password, $token);
                $stmt->execute();

                echo 'Password has been successfully reset!';
            } else {
                echo 'Passwords do not match!';
            }
        }
    } else {
        echo 'Invalid or expired token!';
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
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
            <form action="reset_password.php" method="POST" class="forgot-password-form">
                <h2 class="title">Reset Your Password</h2>

                <!-- New Password Field -->
                <div class="input-field">
                    <input type="password" name="new_password" placeholder="Enter new password" required />
                </div>

                <!-- Confirm Password Field -->
                <div class="input-field">
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required />
                </div>

                <input type="submit" value="Reset Password" class="btn solid" />

                <div class="back-to-login">
                    <a href="index.php">Back to Login</a>
                </div>
            </form>
        </div>

        <div class="classright">
            <img src="img/pic16.jpg" class="image" alt="Reset Password Illustration" />
        </div>
    </div>
</div>


    <script src="script.js"></script>
</body>
</html>
