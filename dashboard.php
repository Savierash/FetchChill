<?php
session_start();
include 'pet_connection.php'; 

//for appointment part
require 'Appointment.php'; 
$appointment = new Appointment();
$appointments = $appointment->GetAllAppointments();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ownername = htmlspecialchars($_POST['ownerName'] ?? '');
    $petname = htmlspecialchars($_POST['petName'] ?? '');
    $petType = htmlspecialchars($_POST['petType'] ?? '');
    $breed = htmlspecialchars($_POST['breed'] ?? '');
    $weight = is_numeric($_POST['weight']) ? intval($_POST['weight']) : null;
    $age = is_numeric($_POST['age']) ? intval($_POST['age']) : null;
    $gender = htmlspecialchars($_POST['gender'] ?? '');
    $visitdate = htmlspecialchars($_POST['checkupDate'] ?? '');
    $time = htmlspecialchars($_POST['time'] ?? '');
    $vaccine = htmlspecialchars($_POST['vaccine'] ?? 'N/A');
    $veterinarian = htmlspecialchars($_POST['veterinarian'] ?? 'N/A');
    $diagnosis = htmlspecialchars($_POST['diagnosis'] ?? '');
    $treatment = htmlspecialchars($_POST['treatment'] ?? '');

    if (empty($ownername) || empty($petname) || empty($petType) || empty($breed) || empty($weight) || empty($age) || empty($gender) || empty($visitdate) || empty($time) || empty($diagnosis) || empty($treatment)) {
        $_SESSION['error_message'] = "Error: Missing required fields!";
        header("Location: dashboard.php");
        exit();
    }

    $sql = "INSERT INTO petrecords (ownername, petname, petType, breed, weight, age, gender, visitdate, time, vaccine, veterinarian, diagnosis, treatment) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssssiisssssss", $ownername, $petname, $petType, $breed, $weight, $age, $gender, $visitdate, $time, $vaccine, $veterinarian, $diagnosis, $treatment);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Record added successfully!";
        } else {
            $_SESSION['error_message'] = "Error adding record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Error preparing SQL statement: " . $conn->error;
    }

    header("Location: dashboard.php");
    exit();
}

// Handle search
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

if (!empty($search)) {
    $sql = "SELECT * FROM petrecords WHERE ownername LIKE ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $searchTerm = "%$search%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $records = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $records = [];
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Error preparing SQL statement: " . $conn->error;
        $records = [];
    }
} else {
    // Fetch all records
    $sql = "SELECT * FROM petrecords";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $records = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $records = [];
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Error preparing SQL statement: " . $conn->error;
        $records = [];
    }
}

// Fetch admin 
$stmt = $conn->prepare("SELECT * FROM admin");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Error preparing SQL statement: " . $conn->error;
    $users = [];
}

// Display success and error messages
if (isset($_SESSION['success_message'])) {
    echo "<div id='successMessage' style='background-color: #4CAF50; color: white; padding: 10px; text-align: center;'>
            {$_SESSION['success_message']}
          </div>";
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo "<div id='errorMessage' style='background-color: red; color: white; padding: 10px; text-align: center;'>
            {$_SESSION['error_message']}
          </div>";
    unset($_SESSION['error_message']);
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch & Chill</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<!------------------ Sidebar ---------------------->
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

    <!------------------ Content ---------------------->
    <div class="content">
        <div class="notification-bell" onclick="toggleDropdown()">
            <i class='bx bxs-bell'></i>
            <span class="badge" id="notifCount">3</span> 
        </div>

        <!------------------ Notification Dropdown ---------------------->
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

        <!------------------ Dashboard Sections ---------------------->
        <div id="dashboard" class="content-section" style="display:block;">
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

        <!------------------ Appointments ---------------------->
<div id="appointments" class="appointment-container" style="display:none;">
    <!------------------ Search ---------------------->
    <div class="appointment-search">
        <div class="search-container">
            <i class='bx bx-search'></i>
            <input type="text" id="search-appointment" placeholder="Search your appointment..." onkeyup="searchAppointments()">
        </div>
    </div>
    <h1>Appointments</h1>
    <div class="appointment-filter">
        <button onclick="filterAppointments('all')">All Appointments</button>
        <button onclick="filterAppointments('confirmed')">Confirmed</button>
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
    <?php if (!empty($appointments)): ?>
        <?php foreach ($appointments as $appointment): ?>
            <tr data-id="<?php echo $appointment['id']; ?>" data-status="<?php echo htmlspecialchars($appointment['status'] ?? 'pending'); ?>">
                <td><?php echo isset($appointment['appointment_date']) ? htmlspecialchars($appointment['appointment_date']) : 'N/A'; ?></td>
                <td><?php echo isset($appointment['user_id']) ? htmlspecialchars($appointment['user_id']) : 'N/A'; ?></td>
                <td><?php echo isset($appointment['service_type']) ? htmlspecialchars($appointment['service_type']) : 'N/A'; ?></td>
                <td class="status"><?php echo isset($appointment['status']) ? htmlspecialchars($appointment['status']) : 'Pending'; ?></td>
                <td class="buttons">
                    <button class="confirm" onclick="updateStatus(<?php echo $appointment['id']; ?>, 'Confirmed')">Confirm</button>
                    <button class="cancel" onclick="updateStatus(<?php echo $appointment['id']; ?>, 'Cancelled')">Cancel</button>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" style="text-align: center;">No appointments found</td>
        </tr>
    <?php endif; ?>
</tbody>
</table>
</div>

        <!------------------ Medical Records ---------------------->
        <div id="medicalRecords" class="medical-container" style="display:none;">
             <!------------------ Search ---------------------->
             <div class="medical-search">
                <div class="search-container">
                    <i class='bx bx-search'></i>
                    <input type="text" id="search-medical" placeholder="Search your medical records..." onkeyup="searchMedicals()">
                </div>
            </div>
            <h1>Client Records</h1>
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

                        <label for="petType">Pet Type:</label>
                        <select id="petType" name="petType" required onchange="updateBreeds()">
                            <option value="">Select a pet type</option>
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                        </select>

                        <label for="breed">Breed:</label>
                        <select id="breed" name="breed" required>
                            <option value="">Select a breed</option>
                        </select>

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

                        <label for="vaccine">Vaccine:</label>
                        <input type="text" id="vaccine" name="vaccine" required>

                        <label for="veterinary">Veterinary:</label>
                        <input type="text" id="veterinarian" name="veterinarian" required>

                        <label for="diagnosis">Diagnosis:</label>
                        <input type="text" id="diagnosis" name="diagnosis" required>

                        <label for="treatment">Treatment:</label>
                        <input type="text" id="treatment" name="treatment" required>

                        <button type="submit">Submit</button>
                    </form>
                </div>
            </div>

            <table class="med-form">
        <thead>
            <tr>
                <th class="med-heading">Owner Name</th>
                <th class="med-heading">Action</th>
            </tr>
        </thead>
        <tbody id="medical-list">
            <?php if (!empty($records)): ?>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['ownername'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="view_record.php?ownername=<?php echo isset($record['ownername']) ? urlencode($record['ownername']) : ''; ?>">
                                <button class="view-button">View</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>



        <!------------------ User Management ---------------------->
        <div id="userManagement" class="management-container" style="display:none;">
    <h1>User Management</h1>
    <div class="management-search">
        <div class="search-container">
            <i class='bx bx-search'></i>
            <input type="text" id="search-management" placeholder="Search your user..." onkeyup="searchUsers()">
        </div>
    </div>

    
    <table>
        <thead>
            <tr>
                <th class="tablehead">Name</th>
                <th class="tablehead">Email</th>
                <th class="tablehead">Role</th>
                <th class="tablehead">Remove Account</th>
            </tr>
        </thead>
        <tbody id="user-list">
            <?php foreach ($users as $user): ?>
                <tr>
                    <td class="tabledata"><?php echo htmlspecialchars($user['username']); ?></td>
                    <td class="tabledata"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="tabledata"><?php echo htmlspecialchars($user['role']); ?></td>
                    <td class="tabledata">
                        <button class="delete-button" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<script src="dashboard.js"></script>
</body>
</html>