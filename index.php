<?php
// Fetch error messages from session if available
session_start();
$error = [
    'signin' => $_SESSION['signin_error'] ?? ''
];

// Function to display the error message if available
function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}
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
    <div class="forms-container">
        <!-- Sign-in Form -->
        <div class="signin-signup" id="login-form">
            <div class="classleft">
                <form action="signin.php" method="POST" class="sign-in-form">
                    <h2 class="title">Sign in</h2>

                    <!--error message-->
                    <?= showError($error['signin']) ?>

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
                <img src="img/undraw_cat_lqdj.svg" class="image" alt="Dog Illustration" />
            </div>
        </div>
    </div>
</div>


    <script src="script.js"></script>
</body>
</html>
