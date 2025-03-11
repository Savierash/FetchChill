<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="home.css" />
    <title>Fetch & Chill</title>
  </head>   
  <body>

    <!------------NAVBAR-------------->
    <nav class="navbar">
        <a href="#"><img src="img/logo.jpg.jpg" alt="Logo" class="logo"></a>
            <ul class="nav-links">
                <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
            </ul>
    </nav>


    <!-----------HOME SECTION------------->
    <section class="home" id="home">
        <div class="home-container">
            <div class="home-content">
                <h1>Hi, <span class="username"> <?php echo $_SESSION['username']; ?>!</h1></span>
                <h1>Welcome to Fetch&Chill</h1>
                <p>Where every fetch ends with cuddle</p>
                <!------------Go to Dashboard Button-------------->
                <a href="dashboard.php" class="btn_dashboard">Go to Dashboard</a>
            </div>
            <div class="home-image">
                <img src="img/pic4.jpg" alt="Happy Dog & Cat">
            </div>
        </div>
    </section>


    




<script src="home.js"></script>
  </body>
</html>