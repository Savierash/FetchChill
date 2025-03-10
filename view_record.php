<?php
session_start();
include 'pet_connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if 'ownername' is provided in the URL
if (!isset($_GET['ownername'])) {
    die("Error: No ownername provided.");
}

// Get and sanitize the ownername from the URL
$ownername = $_GET['ownername'];  
$ownername = htmlspecialchars($ownername);  

// Check if the column 'ownername' exists in the table petrecords
$sql_check_column = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'petrecords' AND COLUMN_NAME = 'ownername'";
$result_check = $conn->query($sql_check_column);

if ($result_check->num_rows == 0) {
    die("Error: 'ownername' column does not exist in the petrecords table. Please check the column name.");
}

// If the column exists, proceed with the query
$sql = "SELECT * FROM petrecords WHERE ownername = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error: Failed to prepare SQL statement.");
}

// Bind the ownername parameter and execute the query
$stmt->bind_param("s", $ownername);
$stmt->execute();

// Get the result and fetch the record
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Error: Record not found.");
}

// Process the record if found (You can use $record here)

// Close the statement and connection
$stmt->close();
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
    <link rel="stylesheet" href="view_record.css">

    <!-------------------------------- Icons ------------------------>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<div class="modal-container">
        <span class="close-button" onclick="window.history.back();">&times;</span>

        <div class="section-header">Owner Name: <?php echo htmlspecialchars($record['ownername']); ?></div>
        <div class="info"><span class="label">Pet Name:</span> <span class="value"><?php echo htmlspecialchars($record['petname']); ?></span></div>
        <div class="info"><span class="label">Pet Type:</span> <span class="value">Dog</span></div>
        <div class="info"><span class="label">Breed:</span> <span class="value"><?php echo htmlspecialchars($record['breed']); ?></span></div>
        <div class="info"><span class="label">Weight:</span> <span class="value"><?php echo htmlspecialchars($record['weight']); ?> kg</span></div>
        <div class="info"><span class="label">Gender:</span> <span class="value"><?php echo htmlspecialchars($record['gender']); ?></span></div>

        <div class="section-header">Health Concerns</div>
        <div class="info"><span class="label">Diagnosis:</span> <span class="value"><?php echo htmlspecialchars($record['diagnosis']); ?></span></div>
        <div class="info"><span class="label">Treatment:</span> <span class="value"><?php echo htmlspecialchars($record['treatment']); ?></span></div>

        <div class="section-header">Vaccination and Veterinary</div>
        <div class="info"><span class="label">Vaccine:</span> <span class="value">Rabies</span></div>
        <div class="info"><span class="label">Veterinarian:</span> <span class="value">John Smith</span></div>

        <div class="section-header">Date & Time</div>
        <div class="info"><span class="label">Date:</span> <span class="value"><?php echo htmlspecialchars($record['visitdate']); ?></span></div>
        <div class="info"><span class="label">Time:</span> <span class="value"><?php echo htmlspecialchars($record['time']); ?></span></div>
    </div>
</body>
</html>
