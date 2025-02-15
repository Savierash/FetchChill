<?php
include 'config.php'; // Ensure config.php contains a working DB connection
session_start();

// If the user is already logged in, redirect them to home
if (isset($_SESSION['username'])) {
    header("Location: home.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['username'], $_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Using prepared statements to prevent SQL injection
    $query = "SELECT * FROM users WHERE username = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        // Check if the user exists and verify the password
        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                // Correct password, create session
                $_SESSION['username'] = $user['username'];
                
                // Return JSON response with success message
                echo json_encode(["status" => "success", "message" => "Login successful"]);
                exit();
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "User not found"]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: Could not prepare statement"]);
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
            
            <?php
              if (isset($error)) {
                  echo "<p style='color: red;'>$error</p>"; // Display error message
              }
            ?>

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
