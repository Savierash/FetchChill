<?php
require_once 'db_connection.php'; 

session_start();

function showError($message) {
    return "<p class='error-message'>$message</p>";
}

$error_message = '';  

// Default admin credentials
$default_username = 'admin123';
$default_password = 'admin123';  


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $default_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    
    if ($admin['password'] == $default_password) {
        $_SESSION['username'] = $admin['username'];
        $_SESSION['role'] = 'admin'; 
        
        
        header("Location: homepage.php");
        exit();
    } else {
        $error_message = showError("Incorrect password for the default admin.");
    }
} else {
    $error_message = showError("No admin account found in the database.");
}

$stmt->close();
$conn->close();

echo $error_message; 
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
                
                <!-- Display error message if set -->
                <?php if (!empty($error_message)) echo $error_message; ?>

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
