<?php
  
  session_start();

  $error = [
    'signin' => $_SESSION['signin_error'] ?? '',
    'signup' => $_SESSION['signup_error'] ?? ''
];
$active_form = $_SESSION['active_form'] ?? 'signin';

session_unset();

function showError($error) {
  return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActive($formName, $active_form) {
  return $formName === $active_form ? 'active' : '';
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
          
         <!--signin-->
         <div class="signin-signup">

          <!--warning to user-->
          <!--?= isActiveForm('signin', $active_form) ? -->

          <form action="signin.php" method="POST" id="loginForm" class="sign-in-form">
            <h2 class="title">Sign in</h2>

            <!--error message-->
            <?= showError($error['signin']) ?>

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
          <!--warning to user-->
          <!--?= isActiveForm('signup', $active_form) ?-->
          <form action="signup.php" method="POST" id="register-form" class="sign-up-form">
            <h2 class="title">Sign up</h2>

            <!--error message-->
            <?= showError($error['signup']) ?>
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
            <button type="submit" class="btn" name="signup">Sign up</button>
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
