<?php
include 'config.php'; // Ensure config.php contains a working DB connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['username'], $_POST['email'], $_POST['password'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];

        // Check if the username already exists
        $checkQuery = "SELECT * FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($conn, $checkQuery)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                echo json_encode(["status" => "error", "message" => "Username already taken"]);
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert the user data into the database
                $insertQuery = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                if ($insertStmt = mysqli_prepare($conn, $insertQuery)) {
                    mysqli_stmt_bind_param($insertStmt, "sss", $username, $email, $hashedPassword);
                    if (mysqli_stmt_execute($insertStmt)) {
                        echo json_encode(["status" => "success", "message" => "Signup successful"]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Error: Unable to register"]);
                    }
                    mysqli_stmt_close($insertStmt);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error: Failed to prepare insert query"]);
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: Failed to prepare select query"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Form not submitted properly"]);
    }
}

mysqli_close($conn); // Always close the connection when done
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
          <form action="signin.php" method="POST" id="loginForm" class="sign-in-form">
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
               <i class="bx bxl-twitter"></i>
              </a>
              <a href="#" class="social-icon">
                <i class="bx bxl-google"></i>
              </a>
            </div>
          </form>
      

          <!--signup-->
          <form action="signup.php" method="POST" id="registerForm" class="sign-up-form">
            <h2 class="title">Sign up</h2>
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
                <i class="bx bxl-twitter"></i>
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
            <button class="btn transparent" id="sign-up-btn">
              Sign up
            </button>
          </div>
          <img src="img/undraw_cat_lqdj.svg" class="image" alt="" />
        </div>
        <div class="panel right-panel">
          <div class="content">
            <h3>Hi Friend ?</h3>
            <p>
                Your pup deserves the best care! Book an appointment today for a healthy, happy tail-wagging companion.
            </p>
            <button class="btn transparent" id="sign-in-btn">
              Sign in
            </button>
          </div>
          <img src="img/undraw_dog_jfxm.svg" class="image" alt="" />
        </div>
      </div>
    </div>

    <script src="script.js"></script>
  </body>
</html>

