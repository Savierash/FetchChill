<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'pet_connection.php';

// Validate database connection
if (!isset($conn) || !$conn) {
    die("Database connection failed: " . (isset($conn) ? $conn->connect_error : "No connection object"));
}

// Check if petrecords table exists
$table_check = $conn->query("SHOW TABLES LIKE 'petrecords'");
if ($table_check->num_rows == 0) {
    die("Error: 'petrecords' table does not exist in the database");
}

require_once 'Appointment.php';

try {
    $appointment = new Appointment();
    $appointments = $appointment->GetAllAppointments();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ownername = htmlspecialchars($_POST['ownerName'] ?? '');
        $petname = htmlspecialchars($_POST['petName'] ?? '');
        $petType = htmlspecialchars($_POST['petType'] ?? '');
        $breed = htmlspecialchars($_POST['breed'] ?? '');
        $weight = intval($_POST['weight'] ?? 0);
        $age = intval($_POST['age'] ?? 0);
        $gender = htmlspecialchars($_POST['gender'] ?? '');
        $visitdate = htmlspecialchars($_POST['checkupDate'] ?? '');
        $time = htmlspecialchars($_POST['time'] ?? '');
        $vaccine = htmlspecialchars($_POST['vaccine'] ?? '');
        $veterinarian = htmlspecialchars($_POST['veterinarian'] ?? '');
        $diagnosis = htmlspecialchars($_POST['diagnosis'] ?? '');
        $treatment = htmlspecialchars($_POST['treatment'] ?? '');

        if (empty($ownername) || empty($petname) || empty($breed) || empty($weight) || empty($age) || empty($gender) || empty($visitdate) || empty($time) || empty($diagnosis) || empty($treatment)) {
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
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
    header("Location: dashboard.php");
    exit();
}

// Display messages directly instead of using $messageHtml
if (isset($_SESSION['success_message'])) {
    echo "<div class='message success'>{$_SESSION['success_message']}</div>";
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo "<div class='message error'>{$_SESSION['error_message']}</div>";
    unset($_SESSION['error_message']);
}

try {
    // Fetch all records
    $records = [];
    $stmt = $conn->prepare("SELECT * FROM petrecords");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $records = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    // Fetch recent appointments
    $recentAppointments = [];
    $stmt = $conn->prepare("SELECT * FROM appointments ORDER BY appointment_date DESC LIMIT 5");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $recentAppointments = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    // Fetch recent records
    $recentRecords = [];
    $stmt = $conn->prepare("SELECT * FROM petrecords ORDER BY visitdate DESC LIMIT 5");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $recentRecords = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    // Search functionality
    $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
    $searchResults = [];
    if ($search) {
        $sql = "SELECT * FROM petrecords WHERE ownername LIKE ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $searchTerm = "%$search%";
            $stmt->bind_param("s", $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            $searchResults = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
    } else {
        $searchResults = $records; // Use all records if no search term
    }

    // User management data
    $users = [];
    $stmt = $conn->prepare("SELECT * FROM admin");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    // Appointment counts by status
    $counts = ['confirmed' => 0, 'cancelled' => 0, 'pending' => 0];
    foreach (['Confirmed', 'Cancelled', 'Pending'] as $status) {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM appointments WHERE status = ?");
        if ($stmt) {
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $counts[strtolower($status)] = $result['count'];
            $stmt->close();
        }
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
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

<style>
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap");
@import url('https://fonts.googleapis.com/css2?family=Mochiy+Pop+One&family=Roboto+Flex:opsz,wght@8..144,100..1000&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&family=Mochiy+Pop+One&family=Roboto+Flex:opsz,wght@8..144,100..1000&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: #E7F1FF;
}

#dashboard {
    display: block;
}

#appointments,
#medicalRecords,
#userManagement {
    display: none;
}

/* DASHBOARD CODE */
.container {
    display: flex;
    height: 100vh;
    flex-direction: column;
}

.sidebar {
    width: 250px;
    background: #7096D1;
    color: black;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    transition: 0.3s;
}

.logo {
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
}

.logo-name {
    margin-top: 10px;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    font-family: "Lexend Deca";
    color: white;
}

.menu {
    list-style: none;
    width: 100%;
    margin-top: 20px;
}

.menu li {
    width: 100%;
    padding: 15px;
    text-align: center;
    cursor: pointer;
    transition: 0.3s;
    border-radius: 20px;
    color: white;
    font-weight: 500;
}

.menu li:hover {
    background: #D9D9D9;
    color: #333;
}

.menu li.active {
    background: #FFFFFF;
    color: #7096D1;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.logout-btn {
    background-color: #FFFFFF;
    color: black;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.3s;
    font-weight: bold;
    margin-top: 100px;
}

.logout-btn:hover {
    background-color: #E4813A;
    transform: scale(1.05);
}

.logout-btn:active {
    background-color: #D07F2A;
    transform: scale(1);
}

/* INSIDE DASHBOARD */
.content {
    flex: 1;
    background: #ffffff;
    margin-left: 250px;
    transition: margin-left 0.3s;
}

/* Notification bell */
.notification-bell {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 30px;
    cursor: pointer;
}

.notification-bell i {
    color: #333;
}

.notification-bell .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: red;
    color: white;
    font-size: 12px;
    border-radius: 50%;
    padding: 3px 7px;
}

.notification-bell:hover {
    color: #000000;
    transform: scale(1.05);
}

.notification-bell:active {
    color: #000000;
}

/* Notification Dropdown */
.notification-dropdown {
    width: 250px;
    background: white;
    border: 1px solid #ccc;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    border-radius: 5px;
    overflow: hidden;
    display: none;
    position: absolute;
    right: 10px;
    top: 40px;
}

.notification-header {
    font-weight: bold;
    padding: 10px;
    text-align: center;
}

.notification-filters {
    display: flex;
    justify-content: space-around;
    padding: 5px;
}

.notification-filters button {
    border: none;
    background: none;
    cursor: pointer;
    font-weight: bold;
    padding: 5px 10px;
    transition: background 0.3s;
}

.notification-filters button:hover {
    background: #f0f0f0;
    border-radius: 3px;
}

.notification-filters .active {
    background: #ddd;
    border-radius: 3px;
}

.notification-list {
    max-height: 300px;
    overflow-y: auto;
}

.notification-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}

.notification-item:last-child {
    border-bottom: none;
}

.unread {
    font-weight: bold;
}

.read {
    color: gray;
    font-weight: normal;
}

/* Dashboard Content */
.content-section h1 {
    margin-bottom: 20px;
    padding: 20px;
    font-size: 28px;
}

.box-container {
    display: flex;
    gap: 30px;
    justify-content: center;
    margin: auto;
    flex-wrap: wrap;
    padding: 20px;
}

@media (max-width: 768px) {
    .box-container {
        gap: 20px;
    }
}

@media (max-width: 480px) {
    .box-container {
        gap: 10px;
        flex-direction: column;
        align-items: center;
    }
}

.content-box {
    background: white;
    width: 300px;
    height: 150px;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    gap: 20px;
    font-size: 14px;
    font-weight: bold;
    border: solid 1px #000000;
    transition: all 0.3s ease-in-out;
}

.content-box:hover {
    background: #f0f0f0;
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
}

.content-box i {
    font-size: 50px;
    color: #7096D1;
}

.content-box p {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.content-box h2 {
    margin: 5px 0 0;
    font-size: 28px;
    color: #000;
}

.grid-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    padding: 20px;
    width: 90%;
    margin: 0 auto;
}

.parent-container {
    background-color: #f9f9f9;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    padding: 20px;
    box-sizing: border-box;
}

.list-container {
    width: 100%;
    height: 400px;
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 15px;
    overflow-y: auto;
}

.recent-items {
    list-style: none;
    padding: 0;
    margin: 0;
}

.recent-items li {
    padding: 10px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
}

.recent-items li:last-child {
    border-bottom: none;
}

.recent-items .date {
    flex: 1;
    font-weight: bold;
    color: #333;
}

.recent-items .customer,
.recent-items .owner {
    flex: 1;
    color: #666;
}

.recent-items .service,
.recent-items .pet {
    flex: 1;
    color: #666;
}

.recent-items .status,
.recent-items .diagnosis {
    flex: 1;
    text-align: right;
}

.recent-items .status.confirmed {
    color: #28a745;
    font-weight: bold;
}

.recent-items .status.pending {
    color: #ff9800;
    font-weight: bold;
}

.recent-items .status.cancelled {
    color: #dc3545;
    font-weight: bold;
}

.recent-list p {
    text-align: center;
    color: #666;
    padding: 20px;
    margin: 0;
}

.parent-container:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.list-container:hover {
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

/* RESPONSIVE STYLES DASHBOARD */
@media screen and (max-width: 768px) {
    .container {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }

    .content {
        margin-left: 0;
        padding: 10px;
    }

    .grid-container {
        grid-template-columns: 1fr;
    }
}

@media screen and (max-width: 480px) {
    .logo {
        width: 80px;
        height: 80px;
    }

    .logo-name {
        font-size: 14px;
    }

    .menu li {
        font-size: 12px;
        padding: 10px;
    }

    .logout-btn {
        font-size: 0.9rem;
        padding: 8px 15px;
        margin-left: 100px;
        margin-top: 30px;
    }
}

/* APPOINTMENT */
.appointment-container {
    width: 80%;
    margin: auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    margin-top: 60px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    display: none;
}

.content h1 {
    margin-bottom: 20px;
    font-family: "Lexend Deca";
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th,
td {
    border: 1px solid #6b6a6a;
    padding: 10px;
    text-align: left;
}

th {
    background-color: #7096D1;
    color: white;
}

.status {
    font-weight: bold;
}

.buttons button {
    margin-right: 5px;
    padding: 5px 10px;
    border: none;
    cursor: pointer;
}

.confirm {
    background-color: green;
    color: white;
}

.pending {
    background-color: orange;
    color: white;
}

.cancel {
    background-color: red;
    color: white;
}

/* confirm all and cancel all */
#confirm-all,
#cancel-all {
    padding: 10px 20px;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin: 5px;
    float: right;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

#confirm-all {
    background-color: #28a745;
    color: white;
}

#confirm-all:hover {
    background-color: #218838;
    transform: scale(1.05);
}

#cancel-all {
    background-color: #dc3545;
    color: white;
}

#cancel-all:hover {
    background-color: #c82333;
    transform: scale(1.05);
}

#confirm-all:active,
#cancel-all:active {
    transform: scale(0.95);
}

#confirm-all:disabled,
#cancel-all:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
    opacity: 0.7;
}

/* Appointment title */
.appointment-filter {
    background-color: #ffffff;
    padding: 15px;
}

.appointment-filter button {
    color: #534E46;
    border: none;
    padding: 0 20px;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    font-weight: bold;
    background: none;
}

.appointment-filter button:hover {
    color: #1B3579;
    background-color: lightblue;
    border: none;
    padding: 0 20px;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    text-decoration: underline;
    text-decoration-color: #1B3579;
    text-decoration-thickness: 1px;
}

/* Search Bar for Appointment */
.appointment-search {
    width: 100%;
    max-width: 500px;
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.search-container {
    position: relative;
    width: 100%;
    max-width: 500px;
    display: flex;
    align-items: center;
}

#search-appointment {
    width: 100%;
    padding: 15px 30px;
    font-size: 16px;
    border: 1px solid #5c5959;
    border-radius: 15px;
    outline: none;
    padding-left: 35px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

#search-appointment:hover {
    border-color: #0077ff;
    box-shadow: 0 0 8px rgba(0, 119, 255, 0.3);
}

#search-appointment:focus {
    border-color: #000000;
    box-shadow: 0 0 12px rgba(0, 119, 255, 0.5);
}

#search-appointment::placeholder {
    color: #999;
    font-weight: bold;
}

.search-container i {
    position: absolute;
    left: 10px;
    font-size: 30px;
    color: #0077ff;
    transition: color 0.3s ease;
}

.search-container:hover i {
    color: #005bb5;
}

/* MEDICAL RECORDS */
.medical-container {
    width: 80%;
    margin: auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    margin-top: 60px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    display: none;
}

.table-med {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.view-button {
    background-color: #288de0;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    text-decoration: none;
    display: inline-block;
    margin-right: 5px;
}

.view-button:hover {
    background-color: #1a73c8;
    transform: scale(1.05);
}

.view-button:active {
    background-color: #155a9c;
    transform: scale(0.95);
}

/* Medical search bar */
.medical-search {
    width: 100%;
    max-width: 500px;
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

#search-medical {
    width: 100%;
    padding: 15px 30px;
    font-size: 16px;
    border: 1px solid #5c5959;
    border-radius: 15px;
    outline: none;
    padding-left: 35px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

#search-medical:hover {
    border-color: #0077ff;
    box-shadow: 0 0 8px rgba(0, 119, 255, 0.3);
}

#search-medical:focus {
    border-color: #000000;
    box-shadow: 0 0 12px rgba(0, 119, 255, 0.5);
}

#search-medical::placeholder {
    color: #999;
    font-weight: bold;
}

.add-record {
    display: inline-block;
    padding: 12px 20px;
    background: linear-gradient(135deg, #7096D1, #0056b3);
    color: white;
    text-decoration: none;
    border-radius: 30px;
    margin-bottom: 25px;
    float: right;
    font-size: 16px;
    border: none;
    font-weight: bold;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease-in-out;
}

.add-record:hover {
    background: linear-gradient(135deg, #0056b3, #7096D1);
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    cursor: pointer;
}

.update-button {
    background-color: #4CAF50; 
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-left: 5px;
}

.update-button:hover {
    background-color: #45a049;
}

/* Popup Container for add record */
.popup-container {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    overflow-y: auto;
}

.popup-content {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 90%;
    max-width: 300px;
    margin: 20px auto;
    max-height: 80vh;
    overflow-y: auto;
}

.close-btn {
    float: right;
    cursor: pointer;
    color: #ffffff;
    font-size: 24px;
    font-weight: bold;
    background-color: #ff4757;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.close-btn:hover {
    background-color: #ff6b81;
    transform: scale(1.1);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

.close-btn:active {
    transform: scale(0.9);
}

@media (min-width: 600px) {
    .popup-content {
        width: 70%;
        max-width: 400px;
    }
}

@media (min-width: 900px) {
    .popup-content {
        width: 50%;
        max-width: 500px;
    }
}

@media (min-width: 1200px) {
    .popup-content {
        width: 40%;
        max-width: 600px;
    }
}

/* Form Layout */
.medic-form label {
    display: block;
    margin-top: 15px;
    font-size: 14px;
    color: #333;
    font-weight: 500;
}

.medic-form input,
.medic-form select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    color: #555;
    background-color: #f9f9f9;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.medic-form input:focus,
.medic-form select:focus {
    border-color: #0056b3;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 86, 179, 0.3);
}

.medic-form select {
    appearance: none;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23333"><path d="M7 10l5 5 5-5z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 12px;
}

.medic-form button {
    margin-top: 20px;
    padding: 10px 20px;
    background: #7096D1;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.medic-form button:hover {
    background: #0056b3;
}

/* User Management Container */
.management-container {
    width: 80%;
    margin: auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    margin-top: 60px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    display: none;
}

.delete-button {
    background-color: #ff4d4d;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
}

.delete-button:hover {
    background-color: #cc0000;
}

/* User Search Bar */
.management-search {
    width: 100%;
    max-width: 500px;
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

#search-management {
    width: 100%;
    padding: 15px 30px;
    font-size: 16px;
    border: 1px solid #5c5959;
    border-radius: 15px;
    outline: none;
    padding-left: 35px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

#search-management:hover {
    border-color: #0077ff;
    box-shadow: 0 0 8px rgba(0, 119, 255, 0.3);
}

#search-management:focus {
    border-color: #000000;
    box-shadow: 0 0 12px rgba(0, 119, 255, 0.5);
}

#search-management::placeholder {
    color: #999;
    font-weight: bold;
}

</style>
<body>
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
            <!-- Notification Bell and Dropdown (unchanged) -->
            <div class="notification-bell" onclick="toggleDropdown()">
                <i class='bx bxs-bell'></i>
                <span class="badge" id="notifCount">3</span>
            </div>
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
                        <?php foreach ($searchResults as $record): ?>
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

    <script>
        // JavaScript remains largely unchanged, with updated medical form handling
        function changeContent(sectionId) {
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => section.style.display = 'none');
            document.getElementById(sectionId).style.display = 'block';
            
            const sectionMap = {
                'dashboard': 'Dashboard',
                'appointments': 'Appointments',
                'medicalRecords': 'Medical Records',
                'userManagement': 'User Management'
            };
            document.querySelectorAll('.menu li').forEach(li => {
                li.classList.remove('active');
                if (li.textContent.trim() === sectionMap[sectionId]) {
                    li.classList.add('active');
                }
            });
        }

        function toggleDropdown() {
            const dropdown = document.getElementById("notifDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        function handleNotificationFilter() {
            const unreadBtn = document.getElementById('unreadBtn');
            const allBtn = document.getElementById('allBtn');
            unreadBtn.addEventListener('click', () => {
                unreadBtn.classList.add('active');
                allBtn.classList.remove('active');
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.style.display = item.classList.contains('unread') ? 'block' : 'none';
                });
            });
            allBtn.addEventListener('click', () => {
                allBtn.classList.add('active');
                unreadBtn.classList.remove('active');
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.style.display = 'block';
                });
            });
        }

        function markAsRead(element) {
            element.classList.remove('unread');
            element.classList.add('read');
            updateNotificationCount();
        }

        function updateNotificationCount() {
            const unreadCount = document.querySelectorAll('.notification-item.unread').length;
            const badge = document.getElementById('notifCount');
            badge.textContent = unreadCount;
            badge.style.display = unreadCount > 0 ? 'inline' : 'none';
        }

        async function updateStatus(appointmentIds, newStatus) {
            if (!Array.isArray(appointmentIds)) appointmentIds = [appointmentIds];
            if (appointmentIds.length === 0) {
                alert('No appointments selected.');
                return;
            }
            if (!confirm(`Are you sure you want to mark ${appointmentIds.length} appointment(s) as ${newStatus}?`)) return;
            try {
                const response = await fetch('update_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids: appointmentIds, status: newStatus })
                });
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();
                if (data.success) {
                    appointmentIds.forEach(id => {
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            const statusCell = row.querySelector('.status');
                            statusCell.textContent = newStatus;
                            row.setAttribute('data-status', newStatus.toLowerCase());
                            row.style.backgroundColor = newStatus === 'Confirmed' ? '#e6ffe6' : 
                                                    newStatus === 'Cancelled' ? '#ffe6e6' : '';
                            row.querySelectorAll('.buttons button').forEach(btn => btn.disabled = true);
                        }
                    });
                    alert(`Updated ${appointmentIds.length} appointment(s) to ${newStatus}.`);
                }
            } catch (error) {
                console.error('Error updating status:', error);
                alert('An error occurred while updating the status.');
            }
        }

        function handleBulkActions() {
            const selectAll = document.getElementById('select-all');
            const confirmAll = document.getElementById('confirm-all');
            const cancelAll = document.getElementById('cancel-all');
            selectAll.addEventListener('change', () => {
                document.querySelectorAll('input[name="select_appointment"]').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
            confirmAll.addEventListener('click', () => {
                const checkedBoxes = document.querySelectorAll('input[name="select_appointment"]:checked');
                const ids = Array.from(checkedBoxes).map(cb => cb.value);
                if (ids.length > 0) updateStatus(ids, 'Confirmed');
                else alert('Please select at least one appointment to confirm.');
            });
            cancelAll.addEventListener('click', () => {
                const checkedBoxes = document.querySelectorAll('input[name="select_appointment"]:checked');
                const ids = Array.from(checkedBoxes).map(cb => cb.value);
                if (ids.length > 0) updateStatus(ids, 'Cancelled');
                else alert('Please select at least one appointment to cancel.');
            });
        }

        function filterAppointments(filter) {
            const rows = document.querySelectorAll('#appointment-list tr');
            rows.forEach(row => {
                const status = row.getAttribute('data-status').toLowerCase();
                row.style.display = (filter === 'all' || status === filter) ? '' : 'none';
            });
        }

        function searchAppointments() {
            const searchTerm = document.getElementById('search-appointment').value.toLowerCase();
            const rows = document.querySelectorAll('#appointment-list tr');
            rows.forEach(row => {
                const customer = row.cells[1].textContent.toLowerCase();
                row.style.display = customer.includes(searchTerm) ? '' : 'none';
            });
        }

        function updateBreeds() {
            const petType = document.getElementById("petType").value;
            const breedSelect = document.getElementById("breed");
            breedSelect.innerHTML = '<option value="">Select a breed</option>';
            const breeds = {
                Dog: ["Labrador Retriever", "German Shepherd", "Golden Retriever", "Bulldog", "Beagle", 
                      "Poodle", "Rottweiler", "Shih Tzu", "Siberian Husky", "Chihuahua", "Pug", 
                      "Doberman", "Dalmatian", "Border Collie", "Corgi"],
                Cat: ["Persian", "Siamese", "Maine Coon", "Ragdoll", "Bengal", "Sphynx", 
                      "Scottish Fold", "British Shorthair", "Abyssinian", "Russian Blue", 
                      "Siberian", "Norwegian Forest Cat"]
            };
            (breeds[petType] || []).forEach(breed => {
                const option = document.createElement("option");
                option.value = breed;
                option.textContent = breed;
                breedSelect.appendChild(option);
            });
        }

        function openPopup() {
            const popup = document.getElementById('popupForm');
            popup.style.display = 'flex';
            setTimeout(() => popup.classList.add('show'), 10);
        }

        function closePopup() {
            const popup = document.getElementById('popupForm');
            popup.classList.remove('show');
            setTimeout(() => popup.style.display = 'none', 300);
        }

        function handleMedicalForm() {
            document.getElementById('medicalForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                try {
                    const formData = new FormData(e.target);
                    const response = await fetch('dashboard.php', {
                        method: 'POST',
                        body: formData
                    });
                    if (!response.ok) throw new Error('Form submission failed');
                    closePopup();
                    const medicalResponse = await fetch('dashboard.php');
                    const html = await medicalResponse.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    document.getElementById('medical-list').innerHTML = 
                        doc.getElementById('medical-list').innerHTML;
                } catch (error) {
                    console.error('Error submitting form:', error);
                    alert('Error saving medical record');
                }
            });
        }

        function searchMedicals() {
            const searchTerm = document.getElementById('search-medical').value.toLowerCase();
            const rows = document.querySelectorAll('#medical-list tr');
            rows.forEach(row => {
                const ownerName = row.cells[0].textContent.toLowerCase();
                row.style.display = ownerName.includes(searchTerm) ? '' : 'none';
            });
        }

        function searchUsers() {
            const searchTerm = document.getElementById('search-management').value.toLowerCase();
            const rows = document.querySelectorAll('#user-list tr');
            rows.forEach(row => {
                const text = Array.from(row.cells).slice(0, 3).map(cell => cell.textContent.toLowerCase()).join(' ');
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }

        async function deleteUser(userId) {
            if (!confirm('Are you sure you want to delete this user?')) return;
            try {
                const response = await fetch('delete_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${userId}`
                });
                if (!response.ok) throw new Error('Delete failed');
                location.reload();
            } catch (error) {
                console.error('Error deleting user:', error);
                alert('Error deleting user');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.menu li').forEach(li => {
                if (li.textContent.trim() === 'Dashboard') {
                    li.classList.add('active');
                }
            });
            document.addEventListener('click', (e) => {
                const dropdown = document.getElementById('notifDropdown');
                const bell = document.querySelector('.notification-bell');
                if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
            handleNotificationFilter();
            updateNotificationCount();
            handleMedicalForm();
            handleBulkActions();
            setTimeout(() => {
                document.querySelectorAll('.message').forEach(msg => msg.style.display = 'none');
            }, 3000);
        });
    </script>
</body>
</html>