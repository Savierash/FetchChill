<?php
session_start();
require_once 'pet_connection.php';
require_once 'Appointment.php';

try {
    // Initialize Appointment class and fetch appointments
    $appointment = new Appointment();
    $appointments = $appointment->GetAllAppointments();

    // Handle POST request for medical records
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $recordId = filter_input(INPUT_POST, 'recordId', FILTER_VALIDATE_INT) ?? null;
        $ownername = filter_input(INPUT_POST, 'ownerName', FILTER_SANITIZE_SPECIAL_CHARS);
        $petname = filter_input(INPUT_POST, 'petName', FILTER_SANITIZE_SPECIAL_CHARS);
        $petType = filter_input(INPUT_POST, 'petType', FILTER_SANITIZE_SPECIAL_CHARS);
        $breed = filter_input(INPUT_POST, 'breed', FILTER_SANITIZE_SPECIAL_CHARS);
        $weight = filter_input(INPUT_POST, 'weight', FILTER_VALIDATE_FLOAT);
        $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
        $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_SPECIAL_CHARS);
        $visitdate = filter_input(INPUT_POST, 'checkupDate', FILTER_SANITIZE_SPECIAL_CHARS);
        $time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_SPECIAL_CHARS);
        $vaccine = filter_input(INPUT_POST, 'vaccine', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'N/A';
        $veterinarian = filter_input(INPUT_POST, 'veterinarian', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'N/A';
        $diagnosis = filter_input(INPUT_POST, 'diagnosis', FILTER_SANITIZE_SPECIAL_CHARS);
        $treatment = filter_input(INPUT_POST, 'treatment', FILTER_SANITIZE_SPECIAL_CHARS);

        // Validate required fields
        $requiredFields = [$ownername, $petname, $petType, $breed, $weight, $age, $gender, $visitdate, $time, $diagnosis, $treatment];
        if (in_array(null, $requiredFields, true) || in_array('', $requiredFields, true)) {
            throw new Exception("Missing required fields!");
        }

        $sql = $recordId ?
            "UPDATE petrecords SET ownername=?, petname=?, petType=?, breed=?, weight=?, age=?, gender=?, visitdate=?, time=?, vaccine=?, veterinarian=?, diagnosis=?, treatment=? WHERE id=?" :
            "INSERT INTO petrecords (ownername, petname, petType, breed, weight, age, gender, visitdate, time, vaccine, veterinarian, diagnosis, treatment) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $params = $recordId ?
            [$ownername, $petname, $petType, $breed, $weight, $age, $gender, $visitdate, $time, $vaccine, $veterinarian, $diagnosis, $treatment, $recordId] :
            [$ownername, $petname, $petType, $breed, $weight, $age, $gender, $visitdate, $time, $vaccine, $veterinarian, $diagnosis, $treatment];
        
        $types = $recordId ? "ssssdissssssi" : "ssssdissssss";
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = $recordId ? "Record updated successfully!" : "Record added successfully!";
        } else {
            throw new Exception("Execution failed: " . $stmt->error);
        }
        $stmt->close();
        header("Location: dashboard.php");
        exit();
    }

    // Fetch recent appointments (last 5)
    $stmt = $conn->prepare("SELECT * FROM appointments ORDER BY appointment_date DESC LIMIT 5");
    $stmt->execute();
    $recentAppointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch recent medical records (last 5)
    $stmt = $conn->prepare("SELECT * FROM petrecords ORDER BY visitdate DESC LIMIT 5");
    $stmt->execute();
    $recentRecords = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Handle search for medical records
    $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
    $records = [];
    $sql = $search ?
        "SELECT * FROM petrecords WHERE ownername LIKE ?" :
        "SELECT * FROM petrecords";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    if ($search) {
        $searchTerm = "%$search%";
        $stmt->bind_param("s", $searchTerm);
    }
    
    $stmt->execute();
    $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch admin data
    $users = [];
    $stmt = $conn->prepare("SELECT * FROM admin");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Appointment counts
    $counts = [
        'confirmed' => $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'Confirmed'")->fetch_assoc()['count'],
        'cancelled' => $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'Cancelled'")->fetch_assoc()['count'],
        'pending' => $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'Pending'")->fetch_assoc()['count']
    ];

} catch (Exception $e) {
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
}

// Display messages
$messageHtml = '';
if (isset($_SESSION['success_message'])) {
    $messageHtml .= "<div class='message success'>{$_SESSION['success_message']}</div>";
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $messageHtml .= "<div class='message error'>{$_SESSION['error_message']}</div>";
    unset($_SESSION['error_message']);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch & Chill - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .message { padding: 10px; margin: 10px; text-align: center; }
        .message.success { background-color: #4CAF50; color: white; }
        .message.error { background-color: #ff0000; color: white; }
        .content-section { padding: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .bulk-actions { margin: 10px 0; }
    </style>
</head>
<body>
    <?php echo $messageHtml; ?>
    
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <img src="img/logo.jpg" alt="Logo">
            </div>
            <div class="logo-name">Fetch & Chill</div>
            <ul class="menu">
                <li onclick="changeContent('dashboard')">Dashboard</li>
                <li onclick="changeContent('appointments')">Appointments</li>
                <li onclick="changeContent('medicalRecords')">Medical Records</li>
                <li onclick="changeContent('userManagement')">User Management</li>
                <hr>
                <li><button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button></li>
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
                <div class="notification-header">Notifications</div>
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

            <!-- Dashboard Section -->
            <div id="dashboard" class="content-section" style="display:block;">
                <h1>Dashboard</h1>
                <div class="box-container">
                    <div class="content-box">
                        <i class='bx bx-cog'></i>
                        <div>
                            <p>Services</p>
                            <h2 id="services-count"><?php echo count($appointments); ?></h2>
                        </div>
                    </div>
                    <div class="content-box">
                        <i class='bx bxs-check-circle'></i>
                        <div>
                            <p>Confirmed Requests</p>
                            <h2 id="confirmed-count"><?php echo $counts['confirmed']; ?></h2>
                        </div>
                    </div>
                    <div class="content-box">
                        <i class='bx bx-time-five'></i>
                        <div>
                            <p>Pending Requests</p>
                            <h2 id="pending-count"><?php echo $counts['pending']; ?></h2>
                        </div>
                    </div>
                    <div class="content-box">
                        <i class='bx bxs-x-circle'></i>
                        <div>
                            <p>Cancelled Requests</p>
                            <h2 id="cancelled-count"><?php echo $counts['cancelled']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="grid-container">
                    <div class="parent-container recent-list">
                        <h2>Recent Appointments</h2>
                        <div class="list-container">
                            <?php if (empty($recentAppointments)): ?>
                                <p>No recent appointments found.</p>
                            <?php else: ?>
                                <ul class="recent-items">
                                    <?php foreach ($recentAppointments as $appt): ?>
                                        <li>
                                            <span class="date"><?php echo htmlspecialchars($appt['appointment_date'] ?? 'N/A'); ?></span>
                                            <span class="customer"><?php echo htmlspecialchars($appt['user_id'] ?? 'N/A'); ?></span>
                                            <span class="service"><?php echo htmlspecialchars($appt['service_type'] ?? 'N/A'); ?></span>
                                            <span class="status <?php echo strtolower($appt['status'] ?? 'pending'); ?>">
                                                <?php echo htmlspecialchars($appt['status'] ?? 'Pending'); ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="parent-container recent-list">
                        <h2>Recent Medical Records</h2>
                        <div class="list-container">
                            <?php if (empty($recentRecords)): ?>
                                <p>No recent medical records found.</p>
                            <?php else: ?>
                                <ul class="recent-items">
                                    <?php foreach ($recentRecords as $record): ?>
                                        <li>
                                            <span class="date"><?php echo htmlspecialchars($record['visitdate'] ?? 'N/A'); ?></span>
                                            <span class="owner"><?php echo htmlspecialchars($record['ownername'] ?? 'N/A'); ?></span>
                                            <span class="pet"><?php echo htmlspecialchars($record['petname'] ?? 'N/A'); ?></span>
                                            <span class="diagnosis"><?php echo htmlspecialchars($record['diagnosis'] ?? 'N/A'); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointments Section -->
            <div id="appointments" class="content-section" style="display:none;">
                <div class="appointment-search">
                    <div class="search-container">
                        <i class='bx bx-search'></i>
                        <input type="text" id="search-appointment" placeholder="Search appointments..." onkeyup="searchAppointments()">
                    </div>
                </div>
                <h1>Appointments</h1>
                <div class="appointment-filter">
                    <button onclick="filterAppointments('all')">All Appointments</button>
                    <button onclick="filterAppointments('confirmed')">Confirmed</button>
                    <button onclick="filterAppointments('cancelled')">Cancelled</button>
                </div>
                <div class="bulk-actions">
                    <button id="confirm-all">Confirm All</button>
                    <button id="cancel-all">Cancel All</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"> APPOINTMENT DATE</th>
                            <th>CUSTOMERS</th>
                            <th>SERVICE</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                <tbody id="appointment-list">
                    <?php foreach ($appointments as $appt): ?>
                        <tr data-id="<?php echo $appt['id']; ?>" data-status="<?php echo htmlspecialchars($appt['status'] ?? 'pending'); ?>">
                            <td>
                                <input type="checkbox" name="select_appointment" value="<?php echo $appt['id']; ?>">
                                <?php echo htmlspecialchars($appt['appointment_date'] ?? 'N/A'); ?>
                            </td>
                            <td><?php echo htmlspecialchars($appt['user_id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($appt['service_type'] ?? 'N/A'); ?></td>
                            <td class="status"><?php echo htmlspecialchars($appt['status'] ?? 'Pending'); ?></td>
                            <td class="buttons">
                            <button class="confirm" onclick="updateStatus(<?php echo $appt['id']; ?>, 'Confirmed')">Confirm</button>
                            <button class="cancel" onclick="updateStatus(<?php echo $appt['id']; ?>, 'Cancelled')">Cancel</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
            </div>

            <!-- Medical Records Section -->
            <div id="medicalRecords" class="content-section" style="display:none;">
                <div class="medical-search">
                    <div class="search-container">
                        <i class='bx bx-search'></i>
                        <input type="text" id="search-medical" placeholder="Search records..." onkeyup="searchMedicals()">
                    </div>
                </div>
                <h1>Client Records</h1>
                <button class="add-record" onclick="openPopup('add')">Add Medical Record</button>
                
                <!-- Popup Form -->
                <div id="popupForm" class="popup-container" style="display:none;">
                    <div class="popup-content">
                        <button class="close-btn" onclick="closePopup()">×</button>
                        <h2 id="popupTitle">Add Client Record</h2>
                        <form action="dashboard.php" method="POST" id="medicalForm" class="medic-form">
                            <div class="form-group">
                                <label for="ownerName">Owner Name:</label>
                                <input type="text" id="ownerName" name="ownerName" required>
                            </div>
                            <div class="form-group">
                                <label for="petName">Pet Name:</label>
                                <input type="text" id="petName" name="petName" required>
                            </div>
                            <div class="form-group">
                                <label for="petType">Pet Type:</label>
                                <select id="petType" name="petType" required onchange="updateBreeds()">
                                    <option value="">Select a pet type</option>
                                    <option value="Dog">Dog</option>
                                    <option value="Cat">Cat</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="breed">Breed:</label>
                                <select id="breed" name="breed" required></select>
                            </div>
                            <div class="form-group">
                                <label for="weight">Weight (kg):</label>
                                <input type="number" id="weight" name="weight" required min="0" step="0.1">
                            </div>
                            <div class="form-group">
                                <label for="age">Age:</label>
                                <input type="number" id="age" name="age" required min="0">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select id="gender" name="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="checkupDate">Date of Check-Up:</label>
                                <input type="date" id="checkupDate" name="checkupDate" required>
                            </div>
                            <div class="form-group">
                                <label for="time">Time:</label>
                                <input type="time" id="time" name="time" required>
                            </div>
                            <div class="form-group">
                                <label for="vaccine">Vaccine:</label>
                                <input type="text" id="vaccine" name="vaccine">
                            </div>
                            <div class="form-group">
                                <label for="veterinarian">Veterinarian:</label>
                                <input type="text" id="veterinarian" name="veterinarian">
                            </div>
                            <div class="form-group">
                                <label for="diagnosis">Diagnosis:</label>
                                <input type="text" id="diagnosis" name="diagnosis" required>
                            </div>
                            <div class="form-group">
                                <label for="treatment">Treatment:</label>
                                <input type="text" id="treatment" name="treatment" required>
                            </div>
                            <button type="submit">Submit</button>
                        </form>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Owner Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="medical-list">
                        <?php foreach ($records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['ownername'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="view_record.php?ownername=<?php echo urlencode($record['ownername'] ?? ''); ?>">
                                        <button class="view-button">View</button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- User Management Section -->
            <div id="userManagement" class="content-section" style="display:none;">
                <div class="management-search">
                    <div class="search-container">
                        <i class='bx bx-search'></i>
                        <input type="text" id="search-management" placeholder="Search users..." onkeyup="searchUsers()">
                    </div>
                </div>
                <h1>User Management</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="user-list">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td>
                                    <button class="delete-button" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>