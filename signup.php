<?php
session_start();
include 'config.php'; 

// If the user is already logged in, redirect to homepage
if (isset($_SESSION['username'])) {
    header("Location: homepage.php");
    exit();
}

// Function to show errors
function showError($message) {
    return "<p class='error-message'>$message</p>";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    $name = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 🔎 Check if username already exists
    $checkUsername = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $checkUsername->bind_param("s", $name);
    $checkUsername->execute();
    $checkUsername->store_result();

    if ($checkUsername->num_rows > 0) {
        $error_message = showError("Username already exists! Please choose another one.");
    } else {
        // 🔎 Check if email already exists
        $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            $error_message = showError("Email already registered! Please use another one.");
        } else {
            // 📝 Insert new user into the database if no duplicates
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashedPassword);

            if ($stmt->execute()) {
                $_SESSION['signup_success'] = "Registration successful! Please log in.";
                header("Location: signin.php");
                exit();
            } else {
                $error_message = showError("Error: Could not register. Please try again.");
            }
            $stmt->close();
        }
        $checkEmail->close();
    }
    $checkUsername->close();
}

mysqli_close($conn); 
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
          
         <!--signin-->
         <div class="signin-signup">
          <form action="signin.php" method="POST" id="login-form" class="sign-in-form">
            <h2 class="title">Sign in</h2>
            <div class="input-field">
                <i class='bx bxs-user'></i>
              <input type="text" name="username" placeholder="Username" required/>
            </div>
            <div class="input-field">
              <i class='bx bxs-lock-alt'></i>
              <input type="password" name="password" placeholder="Password" required/>
            </div>
            <input type="submit" value="Login" class="btn solid" />
            <p class="social-text">Or Sign in with social platforms</p>
            <div class="social-media">
              <a href="#" class="social-icon">
                <i class="bx bxl-facebook"></i>
              </a>
              <a href="#" class="social-icon">
                <i class="bx bxl-google"></i>
              </a>
            </div>
          </form>
      

          <!--signup-->
          <form action="signup.php" method="POST" id="register-form" class="sign-up-form">
            <h2 class="title">Sign up</h2>

            <?php if (isset($error_message)) echo $error_message; ?>
            
            <div class="input-field">
              <i class="bx bxs-user"></i>
              <input type="text" name="username" placeholder="Username" required/>
            </div>
            <div class="input-field">
              <i class="bx bx-mail-send"></i>
              <input type="email" name="email" placeholder="Email" required/>
            </div>
            <div class="input-field">
              <i class="bx bxs-lock-alt"></i>
              <input type="password" name="password" placeholder="Password" required/>
            </div>
            <input type="submit" class="btn" value="Sign up" />
            <p class="social-text">Or Sign up with social platforms</p>
            <div class="social-media">
              <a href="#" class="social-icon">
                <i class="bx bxl-facebook"></i>
              </a>
              <a href="#" class="social-icon">
               <i class="bx bxl-google"></i>    
              </a>
            </div>
          </form>


        </div>
      </div>

      <div class="panels-container">
        <div class="panel left-panel">
          <div class="content">
            <h3>New here ?</h3>
            <p>
                A happy pet starts with the best care! Schedule your dog's appointment today for tail wags and good health.
            </p>
            <button class="btn transparent" id="sign-up-btn"> Sign up </button>
          </div>
          <img src="img/undraw_cat_lqdj.svg" class="image" alt="" />
        </div>
        <div class="panel right-panel">
          <div class="content">
            <h3>Hi Friend ?</h3>
            <p>
                Your pup deserves the best care! Book an appointment today for a healthy, happy tail-wagging companion.
            </p>
            <button class="btn transparent" id="sign-in-btn">Sign in</button>
          </div>
          <img src="img/undraw_dog_jfxm.svg" class="image" alt="" />
        </div>
      </div>
    </div>

    <script src="script.js"></script>
  </body>
</html>

