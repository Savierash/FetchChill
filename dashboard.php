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
    <link rel="stylesheet" href="dashboard.css" />
    <title>Fetch & Chill</title>
  </head>   
  <body>

  <div class="container">
    <div class="sidebar">
        <div class="logo">
            <img src="img/logo.jpg.jpg" alt="Logo">
        </div>
        <div class="logo-name">Fetch & Chill</div>
        <ul class="menu">
            <li onclick="changeContent('dashboard')">Dashboard</li>
            <li onclick="changeContent('userProfile')">User Profile</li>
            <li onclick="changeContent('medicalRecords')">Medical Records</li>
            <li onclick="changeContent('feedback')">Review & Feedback</li><hr/>
            <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
        </ul>
    </div>
    
    <!-- Content Area -->
    <div class="content">
        <div id="dashboard" class="content-section">
          <h1>Dashboard</h1>
            <div class="box-container">
              <div class="content-box">Services</div>
              <div class="content-box">Confirmed Request</div>
              <div class="content-box">Pending Request</div>
              <div class="content-box">Cancelled Request</div>
            </div>
        </div>

        <div id="userProfile" class="content-section" style="display:none;">
            <h1>User Profile</h1>
            <!-- Add user profile content here -->
        </div>
        <div id="medicalRecords" class="content-section" style="display:none;">
            <h1>Medical Records</h1>
            <!-- Add medical records content here -->
        </div>
        <div id="feedback" class="content-section" style="display:none;">
            <h1>Review & Feedback</h1>
            <!-- Add feedback content here -->
        </div>
    </div>
</div>



    <script src="dashboard.js"></script>
  </body>
</html>