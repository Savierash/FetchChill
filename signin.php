<?php
require_once 'db_connection.php'; 
session_start();

function showError($message) {
    return "<p class='error-message'>$message</p>";
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $admin['password'])) {
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = $admin['role']; // Use the role from DB, not hardcoded 'admin'
            header("Location: homepage.php");
            exit();
        } else {
            $error_message = showError("Incorrect password.");
        }
    } else {
        $error_message = showError("No such admin account.");
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Fetch & Chill</title>
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
            overflow: hidden;
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
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .title {
            font-size: 3rem;
            color: #444;
            margin-bottom: iž2rem;
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

        .forgot-password {
            text-align: right;
            margin: 1rem 0;
        }

        .forgot-password a {
            color: #4d84e2;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease-in-out;
        }

        .forgot-password a:hover {
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

            .classleft,
            .classright {
                width: 100%;
                padding: 1rem;
            }

            .classright {
                transform: translateX(100%);
                animation: slideInRight 0.8s ease-out forwards;
            }

            .classright .image {
                max-width: 80%;
            }

            .title {
                font-size: 2rem;
            }

            .input-field {
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

            .input-field {
                padding: 8px;
            }

            .btn {
                padding: 10px;
            }

            .forgot-password a {
                font-size: 0.8rem;
            }
        }

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
                        <input type="text" name="username" placeholder="Username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required />
                    </div>
                    <div class="input-field">
                        <i class='bx bxs-lock-alt'></i>
                        <input type="password" name="password" placeholder="Password" required />
                    </div>

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

    <!-- Remove script tag if not needed -->
    <!-- <script src="script.js"></script> -->
</body>
</html>