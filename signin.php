<?php
// Include the database connection
require_once 'db_connection.php'; // Make sure the path is correct

session_start();

function showError($message) {
    return "<p class='error-message'>$message</p>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Basic validation
    if (empty($username) || empty($password)) {
        $error_message = showError("Please fill in both fields.");
    } else {
        // Query to check the user credentials using MySQLi
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param('s', $username); // 's' denotes string type
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $username;
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = showError("Incorrect username or password.");
            }
        } else {
            $error_message = showError("User not found.");
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
            <form action="signin.php" method="POST" class="sign-in-form">
                <h2 class="title">Sign in</h2>
                
                <!-- Displaying error message if it exists -->
                <?php if (isset($error_message)) echo $error_message; ?>

                <div class="input-field">
                    <i class='bx bxs-user'></i>
                    <input type="text" name="username" placeholder="Username" required />
                </div>
                <div class="input-field">
                    <i class='bx bxs-lock-alt'></i>
                    <input type="password" name="password" placeholder="Password" required />
                </div>

                <!-- Forgot Password Link -->
                <div class="forgot-password">
                    <a href="forgot_password.php">Forgot Password?</a>
                </div>

                <input type="submit" value="Login" class="btn solid" />
            </form>
        </div>

        <div class="classright">
            <img src="img/undraw_dog_jfxm.svg" class="image" alt="Dog Illustration" />
        </div>
    </div>
</div>


    <script src="script.js"></script>
</body>
</html>
