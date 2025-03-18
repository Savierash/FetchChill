<?php
require_once 'db_connection.php';
session_start();

// Initialize variables to avoid undefined variable warnings
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists and is not expired
    $stmt = $conn->prepare("SELECT * FROM admin WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token is valid, allow password reset
        $user = $result->fetch_assoc();
        $_SESSION['reset_user_id'] = $user['id']; // Store user ID in session for verification
        header("Location: reset_password_form.php"); // Redirect to password reset form
        exit();
    } else {
        $error = "Invalid or expired token.";
    }
    $stmt->close();
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
    <div class="signin-signup">
        <div class="classleft">
            <button class="back-btn" onclick="history.back()">
                <i class='bx bx-arrow-back'></i>
            </button>
            <?php echo $success ?? $error ?? ''; ?>
            <?php if (empty($success)): ?>
                <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST" class="forgot-password-form">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <h2 class="title">Reset Password</h2>
                    <div class="input-fields">
                        <i class='bx bxs-lock-alt'></i>
                        <input type="password" name="password" placeholder="Enter new password" required />
                    </div>
                    <div class="input-fields">
                        <i class='bx bxs-lock-alt'></i>
                        <input type="password" name="confirm_password" placeholder="Confirm new password" required />
                    </div>
                    <input type="submit" value="Reset Password" class="btn solid" />
                </form>
            <?php endif; ?>
        </div>
        <div class="classright">
            <img src="img/undraw_my-password_iyga.svg" class="image" alt="Reset Password Illustration" />
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>

<?php $conn->close(); ?>