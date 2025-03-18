<?php
require_once 'db_connection.php';
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function showMessage($message, $type = 'danger') {
    return "<div class='alert alert-$type'>" . htmlspecialchars($message) . "</div>";
}

function sendResetEmail($email, $token) {
    $reset_link = "http://localhost/FetchChill/reset_password.php?token=" . urlencode($token);
    $subject = "Password Reset Request";
    $message = "Click this link to reset your password: $reset_link\nLink expires in 1 hour.";
    $headers = "From: your_email@gmail.com\r\n" .
               "Reply-To: your_email@gmail.com\r\n" .
               "X-Mailer: PHP/" . phpversion();

    $result = mail($email, $subject, $message, $headers);
    if (!$result) {
        error_log("Failed to send email to $email");
    }
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Security error: Invalid token");
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = showMessage("Please enter a valid email address.");
        } else {
            $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $token = bin2hex(random_bytes(50));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $update_stmt = $conn->prepare("UPDATE admin SET reset_token = ?, token_expiry = ? WHERE email = ?");
                $update_stmt->bind_param('sss', $token, $expiry, $email);
                
                if (!$update_stmt->execute()) {
                    throw new Exception("Database update failed: " . $update_stmt->error);
                }
                if (!sendResetEmail($email, $token)) {
                    throw new Exception("Failed to send reset email");
                }
                $success = showMessage("Reset link sent to your email.", "success");
                
                $update_stmt->close();
            } else {
                $error = showMessage("Email not found in our system.");
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        $error = showMessage("Error: " . $e->getMessage());
    }
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
            <form action="forgot_password.php" method="POST" class="forgot-password-form">
                <h2 class="title">Forgot Password</h2>
                <?php echo $success ?? $error ?? ''; ?>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="input-field">
                    <i class='bx bxs-envelope'></i>
                    <input type="email" name="email" placeholder="Enter your email" required />
                </div>
                <input type="submit" value="Reset Password" class="btn solid" />
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

<?php $conn->close(); ?>