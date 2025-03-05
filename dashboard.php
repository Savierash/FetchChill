<?php
session_start();  

include 'pet_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $ownername = $_POST['ownerName'] ?? null;
    $petname = $_POST['petName'] ?? null;
    $breed = $_POST['breed'] ?? null;
    $weight = $_POST['weight'] ?? null;
    $age = $_POST['age'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $visitdate = $_POST['checkupDate'] ?? null;
    $time = $_POST['time'] ?? null;
    $diagnosis = $_POST['diagnosis'] ?? null;
    $treatment = $_POST['treatment'] ?? null;

    
    if (!$ownername || !$petname || !$breed || !$weight || !$age || !$gender || !$visitdate || !$time || !$diagnosis || !$treatment) {
        echo "<div style='background-color: red; color: white; padding: 10px; text-align: center;'>
                Error: Missing required fields!
              </div>";
        exit();
    }

    $sql = "INSERT INTO petrecords (ownername, petname, breed, weight, age, gender, visitdate, time, diagnosis, treatment) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiiissss", $ownername, $petname, $breed, $weight, $age, $gender, $visitdate, $time, $diagnosis, $treatment);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Record added successfully!";
        header("Location: dashboard.php");  
        exit();
    } else {
        echo "<div style='background-color: red; color: white; padding: 10px; text-align: center;'>
                Error adding record!
              </div>";
    }
    $stmt->close();
}


if (isset($_SESSION['success_message'])) {
    echo "<div id='successMessage' style='background-color: #4CAF50; color: white; padding: 10px; text-align: center;'>
            {$_SESSION['success_message']}
          </div>";
    unset($_SESSION['success_message']);  
}

$sql = "SELECT * FROM petrecords";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    
    $records = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $records = [];
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch & Chill</title>
    
    <!------------------ Google Fonts ---------------------->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">

    <!-------------------------------- Icons ------------------------>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<!---------------------------- Sidebar ------------------------->
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

    <!-------------------------- Main Content ------------------------------>
    <div class="content">
        <div class="notification-bell" onclick="toggleDropdown()">
            <i class='bx bxs-bell'></i>
            <span class="badge" id="notifCount">3</span> 
        </div>

    <!-------------------------- Notification Dropdown--------------------->
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

        <!------------------------ Dashboard -------------------------->
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


    <!----------------------------Appointments------------------------->
        <div id="appointments" class="appointment-container" style="display:none;">
    <!---------------------Search Bar------------------------------------>
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

<!------------------------------Medical Records------------------------------------->
        <div id="medicalRecords" class="medical-container" style="display:none;">

        <!--------------------Search Bar--------------------->
        <div class="medical-search">
            <div class="search-container">
                <i class='bx bx-search'></i>
                <input type="text" id="search-bar" placeholder="Search your appointment..." onkeyup="searchAppointments()">
            </div>
        </div>
            <h1>Pet Records</h1>

    

<!------------------------------------Trigger Button----------------------------->
<button class="add-record" onclick="openPopup()">Add Medical Record</button>


<div id="popupForm" class="popup-container" style="display: none;">
    <div class="popup-content">
        <button class="close-btn" onclick="closePopup()">&times;</button>
        <h2>Add Client Record</h2>
        <form action="dashboard.php" class="medic-form" id="medicalForm" method="POST">
            <label for="ownerName">Owner Name:</label>
            <input type="text" id="ownerName" name="ownerName" required>

            <label for="petName">Pet Name:</label>
            <input type="text" id="petName" name="petName" required>

            <label for="breed">Breed:</label>
            <input type="text" id="breed" name="breed" required>

            <label for="weight">Weight (kg):</label>
            <input type="number" id="weight" name="weight" required>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <label for="checkupDate">Date of Check-Up:</label>
            <input type="date" id="checkupDate" name="checkupDate" required>

            <label for="time">Time:</label>
            <input type="time" id="time" name="time" required>

            <label for="diagnosis">Diagnosis:</label>
            <input type="text" id="diagnosis" name="diagnosis" required>

            <label for="treatment">Treatment:</label>
            <input type="text" id="treatment" name="treatment" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</div>

<!---------------------------- Table Display Records ---------------------------->
<div class="med-form">
    <table border="1">
        <thead>
            <tr>
                <th>Owner Name</th>
                <th>Pet Name</th>
                <th>Breed</th>
                <th>Weight (kg)</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Date of Check-Up</th>
                <th>Time</th>
                <th>Diagnosis</th>
                <th>Treatment</th>
            </tr>
        </thead>
        <tbody id="tableBody">
        <?php
           
            if (!empty($records)) {
                foreach ($records as $record) {
                    echo "<tr>";
                    echo "<td>{$record['ownername']}</td>";
                    echo "<td>{$record['petname']}</td>";
                    echo "<td>{$record['breed']}</td>";
                    echo "<td>{$record['weight']}</td>";
                    echo "<td>{$record['age']}</td>";
                    echo "<td>{$record['gender']}</td>";
                    echo "<td>{$record['visitdate']}</td>";
                    echo "<td>{$record['time']}</td>";
                    echo "<td>{$record['diagnosis']}</td>";
                    echo "<td>{$record['treatment']}</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10' style='text-align: center;'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>


<!------------------------User Management----------------------------->
<div id="userManagement" class="management-container" style="display:none;">
    
          
</div>


<script src="dashboard.js"></script>
</body>
</html>
