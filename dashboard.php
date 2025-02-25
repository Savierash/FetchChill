<!--<?php
//session_start();
//if (!isset($_SESSION['username'])) {
    //header("Location: index.php");
    //exit();
//}
?>-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch & Chill</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">

    <!-- Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<!-- Sidebar -->
<div class="container">
    <div class="sidebar">
        <div class="logo">
            <img src="img/logo.jpg.jpg" alt="Logo">
        </div>
        <div class="logo-name">Fetch & Chill</div>
        <ul class="menu">
            <li onclick="changeContent('dashboard')">Dashboard</li>
            <li onclick="changeContent('appointments')">Appointments</li>
            <li onclick="changeContent('medicalRecords')">Medical Records</li>
            <li onclick="changeContent('userManagement')">User Management</li>
            <hr />
            <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="notification-bell" onclick="toggleDropdown()">
            <i class='bx bxs-bell'></i>
            <span class="badge" id="notifCount">3</span> 
        </div>

        <!-- Notification Dropdown -->
        <div id="notifDropdown" class="notification-dropdown">
            <div class="notification-item">🔔 New message received</div>
            <div class="notification-item">📌 Medical Request</div>
            <div class="notification-item">✅ Client request approved</div>
        </div>

        <!-- Dashboard -->
        <div id="dashboard" class="content-section">
            <h1>Dashboard</h1>
                <div class="box-container">
                    <div class="content-box">
                        <i class='bx bx-cog'></i>
                    <div>
                    <p>Services</p>
                    <h2 id="services-count">0</h2>
                </div>
            </div>
        <div class="content-box">
            <i class='bx bxs-check-circle'></i>
                <div>
                    <p>Confirmed Requests</p>
                    <h2 id="confirmed-count">0</h2>
                </div>
            </div>
            <div class="content-box">
                <i class='bx bx-time-five'></i>
                <div>
                    <p>Pending Requests</p>
                    <h2 id="pending-count">0</h2>
                </div>
            </div>
            <div class="content-box">
                <i class='bx bxs-x-circle'></i>
                <div>
                    <p>Cancelled Requests</p>
                    <h2 id="cancelled-count">0</h2>
                </div>
            </div>
        </div>
    </div>


        <!-- Appointments -->
        <div id="appointments" class="appointment-container" style="display:none;">
            <h1>Appointments</h1>
            <table>
                <thead>
                    <tr>
                        <th>APPOINTMENT DATE</th>
                        <th>CUSTOMERS</th>
                        <th>SERVICE</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2025-10-03 10:00 AM</td>
                        <td>Juan Dela Cruz</td>
                        <td>Grooming</td>
                        <td class="status">Pending</td>
                        <td class="buttons">
                            <button class="confirm" onclick="updateStatus(this, 'Confirmed')">Confirm</button>
                            <button class="pending" onclick="updateStatus(this, 'Pending')">Pending</button>
                            <button class="cancel" onclick="updateStatus(this, 'Cancelled')">Cancel</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Medical Records -->
        <div id="medicalRecords" class="medical-container" style="display:none;">
            <h1>Medical Records</h1>
            <table class="table-med">
            <thead>
              <tr>
                <th class="thead">Owner Name</th>
                <th class="thead">Pet Name</th>
                <th class="thead">Breed</th>
                <th class="thead">Weight (kg)</th>
                <th class="thead">Age</th>
                <th class="thead">Gender</th>
                <th class="thead">Date of Check-up</th>
                <th class="thead">Diagnosis</th>
                <th class="thead">Treatment</th>
              </tr>
            </thead>

                <tbody id="recordsTable">
                    <!-- Records will be inserted here dynamically -->
                </tbody>
            </table>

            <h2>Add Medical Record</h2>
            <form class="med-form" id="medicalForm">
                <input class="med-input" type="text" id="owner" placeholder="Owner Name" required>
                <input class="med-input" type="text" id="pet" placeholder="Pet Name" required>
                <input class="med-input" type="text" id="breed" placeholder="Breed" required>
                <input class="med-input" type="number" id="weight" placeholder="Weight (kg)" required>
                <input class="med-input" type="number" id="age" placeholder="Age" required>
                <input class="med-input" type="text" id="gender" placeholder="Gender" required>
                <input class="med-input" type="date" id="date" required>
                <input class="med-input" type="text" id="diagnosis" placeholder="Diagnosis" required>
                <input class="med-input" type="text" id="treatment" placeholder="Treatment" required>
                <button class="btn-medical" type="submit">Add Record</button>
            </form>
        </div>

        <!-- User Management -->
        <div id="userManagement" class="management-container" style="display:none;">
          <h1>User and Management</h1>
            <div class="user-list" id="userList">
        <!-- User list will appear here -->
        </div>

        <!-- Add User Form -->
        <div class="add-user">
          <input type="text" id="newUserName" placeholder="Enter new user name" />
            <button id="addUserBtn">Add User</button>
        </div>
      </div>

<script src="dashboard.js"></script>
</body>
</html>
