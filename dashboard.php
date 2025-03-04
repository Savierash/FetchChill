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
        <div class="notification-header">
            <span>Notifications</span>
        </div>
        <hr>
        <div class="notification-filters">
            <button id="unreadBtn" class="filter-btn active">Unread</button>
            <button id="allBtn" class="filter-btn">All</button>
        </div>
        <hr>
        <div class="notification-list">
            <div class="notification-item unread" onclick="markAsRead(this)">🔔 New message received</div>
            <div class="notification-item unread" onclick="markAsRead(this)">📌 Medical Request</div>
            <div class="notification-item read" onclick="markAsRead(this)">✅ Client request approved</div>
        </div>
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
    <!-- Search Bar -->
        <div class="appointment-search">
            <div class="search-container">
                <i class='bx bx-search'></i>
                <input type="text" id="search-bar" placeholder="Search your appointment..." onkeyup="searchAppointments()">
            </div>
        </div>


        <h1>Appointments</h1>

        <div class="appointment-filter">
        <button onclick="filterAppointments('all')">All Appointments</button>
        <button onclick="filterAppointments('confirmed')">Confirmed</button>
        <button onclick="filterAppointments('pending')">Pending</button>
        <button onclick="filterAppointments('cancelled')">Cancelled</button>

    </div>
        <table>
            <thead>
                <tr>
                    <th class="tableh">APPOINTMENT DATE</th>
                    <th class="tableh">CUSTOMERS</th>
                    <th class="tableh">SERVICE</th>
                    <th class="tableh">STATUS</th>
                    <th class="tableh">ACTION</th>
                </tr>
            </thead>
            <tbody id="appointment-list">
                <tr data-status="pending">
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
            <!-- another row for many appoinment -->
         </tbody>
        </table>
    </div>

        <!-- Medical Records -->
        <div id="medicalRecords" class="medical-container" style="display:none;">

        <!-- Search Bar -->
        <div class="medical-search">
            <div class="search-container">
                <i class='bx bx-search'></i>
                <input type="text" id="search-bar" placeholder="Search your appointment..." onkeyup="searchAppointments()">
            </div>
        </div>
            <h1>Pet Records</h1>
            <button id="popupButton" class="add-record">Add Medical Record</button>
            
            

            <form class="med-form" id="medicalForm">
                <table border="1">
                    <thead>
                <tr>
                    <th>Owner Name</th>
                    <th>Pet Name</th>
                    <th>Weight (kg)</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Date of Check-Up</th>
                    <th>Time</th>
                    <th>Diagnosis</th>
                    <th>Treatment</th>
                </tr>
            </thead>
             <tbody>
            <!-- Data rows can go here -->
            <tr>
                <td>John Doe</td>
                <td>Buddy</td>
                <td>5.2</td>
                <td>3</td>
                <td>Male</td>
                <td>2025-03-04</td>
                <td>10:00 AM</td>
                <td>Fever</td>
                <td>Medication</td>
            </tr>
        </tbody>
    </table>


<script src="dashboard.js"></script>
</body>
</html>
