<?php
// Include the database connection
require_once 'db_connection.php'; // Ensure the path is correct

session_start();

function showError($message) {
    return "<p class='error-message'>$message</p>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Basic validation
    if (empty($email)) {
        $error_message = showError("Please enter your email.");
    } else {
        // Query to check if email exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param('s', $email); // 's' for string
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email exists, process password reset logic here
            // You might want to generate a token, send an email, etc.
            // For example:
            $user = $result->fetch_assoc();
            $token = bin2hex(random_bytes(50)); // Generate a random token

            // Save the token in the database for password reset (you need a 'reset_token' column in the table)
            $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
            $stmt->bind_param('ss', $token, $email);
            $stmt->execute();

            // You can then send an email to the user with the reset link containing the token.
            // For now, just echo the token for testing.
            echo "Password reset token: $token"; // Remove this after testing

        } else {
            $error_message = showError("Email not found.");
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
            <img src="img/pic15.jpg" class="image" alt="Forgot Password Illustration" />
        </div>
    </div>
</div>


    <script src="script.js"></script>
</body>
</html>
