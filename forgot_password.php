<?php
// Include the database connection
require_once 'db_connection.php'; 

session_start();

function showError($message) {
    return "<p class='error-message'>$message</p>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    if (empty($email)) {
        $error_message = showError("Please enter the admin's email.");
    } else {
        
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?"); 
        $stmt->bind_param('s', $email); // 's' for string
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
           
            $admin = $result->fetch_assoc(); 
            $token = bin2hex(random_bytes(50));

            
            $stmt = $conn->prepare("UPDATE admin SET reset_token = ? WHERE email = ?"); 
            $stmt->bind_param('ss', $token, $email);
            $stmt->execute();

            echo "Password reset token for admin: $token"; 

        } else {
            $error_message = showError("Admin email not found.");
        }

        // Close the statement
        $stmt->close();
    }
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
            <form action="forgot_password.php" method="POST" class="forgot-password-form">
                <h2 class="title">Forgot Password</h2>

                <!-- Displaying error message if it exists -->
                <?php if (isset($error_message)) echo $error_message; ?>

                <div class="input-field">
                    <i class='bx bxs-envelope'></i>
                    <input type="email" name="email" placeholder="Enter your email" required />
                </div>

                <input type="submit" value="Reset Password" class="btn solid" />

                <div class="back-to-login">
                    <a href="index.php">Back to Login</a>
                </div>
            </form>
        </div>

        <div class="classright">
            <img src="img/undraw_forgot-password_odai.svg" class="image" alt="Forgot Password Illustration" />
        </div>
    </div>
</div>


    <script src="script.js"></script>
</body>
</html>
