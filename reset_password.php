<?php
require_once 'db_connection.php';
header("Content-Type: application/json");

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request method", 405);
    }

    // Get JSON data from the request body
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['token']) || empty($data['password']) || empty($data['confirm_password'])) {
        throw new Exception("Please provide token, password, and confirm password", 400);
    }

    $token = trim($data['token']);
    $password = trim($data['password']);
    $confirm_password = trim($data['confirm_password']);

    // Validate password requirements
    if ($password !== $confirm_password) {
        throw new Exception("Passwords do not match", 400);
    }
    if (strlen($password) < 8) {
        throw new Exception("Password must be at least 8 characters long", 400);
    }

    // Check if token is valid and not expired
    $stmt = $conn->prepare("SELECT id FROM admin WHERE reset_token = ? AND token_expiry > NOW()");
    if ($stmt === false) {
        throw new Exception("Database prepare failed: " . $conn->error, 500);
    }
    $stmt->bind_param('s', $token);
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed: " . $stmt->error, 500);
    }
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Invalid or expired token", 404);
    }

    $user = $result->fetch_assoc();
    $user_id = $user['id'];

    // Hash the new password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update password and clear reset token
    $update_stmt = $conn->prepare("UPDATE admin SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
    if ($update_stmt === false) {
        throw new Exception("Database prepare failed: " . $conn->error, 500);
    }
    $update_stmt->bind_param('si', $hashed_password, $user_id);
    if (!$update_stmt->execute()) {
        throw new Exception("Failed to reset password: " . $update_stmt->error, 500);
    }

    $response['success'] = true;
    $response['message'] = "Password reset successfully! You can now log in.";

    $stmt->close();
    $update_stmt->close();

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code($e->getCode() ?: 500);
}

$conn->close();
echo json_encode($response);
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>Fetch & Chill - Reset Password</title>
</head>

<style>
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap");

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body,
input {
  font-family: "Poppins", sans-serif;
}

body {
  position: relative;
  overflow: hidden; /* Prevent scrolling */
}

.container {
  display: flex;
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: #ffffff;
  justify-content: center;
  align-items: center;
  animation: fadeIn 1s ease-out;
}

/*back button*/
.back-btn {
  position: absolute;
  top: 20px;
  left: 20px;
  font-size: 24px;
  color: #333;
  cursor: pointer;
  border: none;
  background: none;
}

.forms-container {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.signin-signup {
  position: relative;
  display: flex;
  width: 90%;
  max-width: 1200px;
  height: 90%;
  background: #fff;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
  border-radius: 10px;
  overflow: hidden;
  opacity: 0;
  animation: slideUp 0.8s forwards;
  z-index: 2;
}

.classleft,
.classright {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
}

.classleft {
  background: #fff;
  text-align: center;
  flex-direction: column;
}

.classright {
  background-color: #4d84e2;
  transform: translateX(100%);
  animation: slideInRight 1s ease-out forwards;
  transition: transform 0.5s ease-out;
}

.classright .image {
  max-width: 100%;
  height: auto;
  border-radius: 10px;
  opacity: 0;
  animation: fadeIn 1s ease-out 0.5s forwards;
  transform: scale(1.05);
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
  opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.title {
  font-size: 3rem;
  color: #444;
  margin-bottom: 2rem;
  animation: fadeIn 0.5s ease-out;
}

.input-field {
  display: flex;
  align-items: center;
  background-color: #f0f0f0;
  margin: 1rem 0;
  padding: 1rem;
  border-radius: 25px;
  transition: all 0.3s ease-in-out;
  width: 500px;
  max-width: none;
}

.input-field:hover {
  background-color: #e4e4e4;
  transform: scale(1.02);
}

.input-field i {
  margin-right: 10px;
  color: #acacac;
}

.input-field input {
  border: none;
  background: none;
  outline: none;
  flex: 1;
  font-size: 0.9rem;
  color: #333;
  font-weight: 400;
}

.input-fields {
  display: flex;
  align-items: center;
  background-color: #f0f0f0;
  margin: 1rem 0;
  padding: 1rem;
  border-radius: 25px;
  transition: all 0.3s ease-in-out;
  width: 100%;
  max-width: none;
}

.input-fields:hover {
  background-color: #e4e4e4;
  transform: scale(1.02);
}

.input-fields i {
  margin-right: 10px;
  color: #acacac;
}

.input-fields input {
  border: none;
  background: none;
  outline: none;
  flex: 1;
  font-size: 0.9rem;
  color: #333;
  font-weight: 400;
}

.btn {
  width: 100%;
  background-color: #5995fd;
  border: none;
  outline: none;
  padding: 12px;
  border-radius: 25px;
  color: #fff;
  font-weight: 500;
  cursor: pointer;
  transition: 0.3s ease-in-out;
  max-width: 300px;
}

.btn:hover {
  background-color: #4d84e2;
  transform: scale(1.05);
}

.forgot-password,
.back-to-login {
  text-align: right;
  margin: 1rem 0;
}

.forgot-password a,
.back-to-login a {
  color: #4d84e2;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  transition: color 0.3s ease-in-out;
}

.forgot-password a:hover,
.back-to-login a:hover {
  color: #5995fd;
  text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .classright {
    transform: translateX(100%);
    animation: slideInRight 0.8s ease-out forwards;
  }
}

@media (max-width: 768px) {
  .signin-signup {
    flex-direction: column;
    max-width: 90%;
    height: auto;
  }

  .classleft {
    width: 100%;
    padding: 1rem;
  }

  .classright {
    width: 100%;
    padding: 1rem;
    transform: translateX(100%);
    animation: slideInRight 0.8s ease-out forwards;
    transition: transform 0.5s ease-out;
  }

  .classright .image {
    max-width: 80%;
  }

  .title {
    font-size: 2rem;
  }

  .input-field,
  .input-fields {
    width: 100%;
    max-width: 100%;
  }

  .btn {
    width: 100%;
    max-width: 100%;
  }
}

@media (max-width: 480px) {
  .signin-signup {
    width: 95%;
  }

  .classleft,
  .classright {
    padding: 1rem;
  }

  .title {
    font-size: 1.5rem;
  }

  .input-field,
  .input-fields {
    padding: 8px;
  }

  .btn {
    padding: 10px;
  }

  .forgot-password a,
  .back-to-login a {
    font-size: 0.8rem;
  }
}

.alert {
  padding: 15px;
  margin: 10px 0;
  border-radius: 5px;
}
.alert-success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

/******************ERROR MESSAGE****************************/
.error-message {
  color: red;
  font-size: 16px;
  background-color: #f8d7da;
  padding: 10px;
  border: 1px solid #f5c6cb;
  border-radius: 5px;
  margin: 10px 0;
  text-align: center;
}

</style>
<body>
<div class="container">
    <div class="signin-signup">
        <div class="classleft">
            <button class="back-btn" onclick="history.back()">
                <i class="bx bx-arrow-back"></i>
            </button>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php elseif (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($showForm && empty($success)): ?>
                <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST" class="forgot-password-form">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <h2 class="title">Reset Password</h2>
                    <div class="input-fields">
                        <i class="bx bxs-lock-alt"></i>
                        <input type="password" name="password" placeholder="Enter new password" required />
                    </div>
                    <div class="input-fields">
                        <i class="bx bxs-lock-alt"></i>
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
<script>
    //
document.getElementById('change-password-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const current_password = document.getElementById('current_password').value;
    const new_password = document.getElementById('new_password').value;
    const messageDiv = document.getElementById('message');

    try {
        const response = await fetch('change_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, current_password, new_password })
        });

        const data = await response.json();
        
        messageDiv.innerHTML = `<div class="alert alert-${data.success ? 'success' : 'danger'}">${data.message}</div>`;
        
        if (data.success) {
            document.getElementById('change-password-form').reset();
            setTimeout(() => window.location.href = 'dashboard.php', 2000); // Redirect after 2 seconds
        }
    } catch (error) {
        messageDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    }
});
</script>
</body>
</html>

