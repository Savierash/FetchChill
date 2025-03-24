<?php
session_start();
include 'pet_connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check database connection
if (!$conn || $conn->connect_error) {
    die("Error: Database connection failed - " . $conn->connect_error);
}

// Check if ownername is provided
if (!isset($_GET['ownername']) || empty(trim($_GET['ownername']))) {
    die("Error: No owner name provided. Please select a valid record.");
}

$ownername = trim($_GET['ownername']);

// Optional: Validate column existence (remove if schema is stable)
$sql_check_column = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'petrecords' AND COLUMN_NAME = 'ownername'";
$result_check = $conn->query($sql_check_column);

if ($result_check->num_rows == 0) {
    die("Error: 'ownername' column does not exist in the petrecords table. Please check your database schema.");
}

// Fetch record
$sql = "SELECT * FROM petrecords WHERE ownername = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error: Failed to prepare SQL statement - " . $conn->error);
}

$stmt->bind_param("s", $ownername);
$stmt->execute();

$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Error: No medical record found for owner '$ownername'.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch & Chill - View Medical Record</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<style>
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap");
@import url('https://fonts.googleapis.com/css2?family=Mochiy+Pop+One&family=Roboto+Flex:opsz,wght@8..144,100..1000&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&family=Mochiy+Pop+One&family=Roboto+Flex:opsz,wght@8..144,100..1000&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap');


body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
}

.modal-container {
    background: white;
    padding: 20px;
    width: 90%; 
    max-width: 600px;
    height: 90vh; 
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    border-radius: 20px; 
    position: fixed; 
    top: 50%; 
    left: 50%; 
    transform: translate(-50%, -50%);
    overflow: auto;
}

.section-header {
    background: #cce0f5;
    padding: 10px;
    font-weight: bold;
    border-radius: 5px;
}

.info {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #ddd;
}

.info:last-child {
    border-bottom: none;
}

.label {
    font-weight: bold;
    color: #333;
}

.value {
    color: #555;
}

.close-button {
    position: absolute; 
    top: 15px;
    right: 30px; 
    font-size: 40px;
    color: gray;
    cursor: pointer;
    background: none; 
    border: none; 
    padding: 0; 
}

.close-button:hover {
    color: black;
}

</style>

<body>
    <div class="modal-container">
        <span class="close-button" onclick="window.history.back();">×</span>

        <div class="section-header">Medical Information</div>
        <div class="info"><span class="label">Owner Name:</span> <span class="value"><?php echo htmlspecialchars($record['ownername']); ?></span></div>
        <div class="info"><span class="label">Pet Name:</span> <span class="value"><?php echo htmlspecialchars($record['petname']); ?></span></div>
        <div class="info"><span class="label">Pet Type:</span> <span class="value"><?php echo htmlspecialchars($record['petType']); ?></span></div>
        <div class="info"><span class="label">Breed:</span> <span class="value"><?php echo htmlspecialchars($record['breed']); ?></span></div>
        <div class="info"><span class="label">Weight:</span> <span class="value"><?php echo htmlspecialchars($record['weight']); ?> kg</span></div>
        <div class="info"><span class="label">Gender:</span> <span class="value"><?php echo htmlspecialchars($record['gender']); ?></span></div>

        <div class="section-header">Health Concerns</div>
        <div class="info"><span class="label">Diagnosis:</span> <span class="value"><?php echo htmlspecialchars($record['diagnosis']); ?></span></div>
        <div class="info"><span class="label">Treatment:</span> <span class="value"><?php echo htmlspecialchars($record['treatment']); ?></span></div>

        <div class="section-header">Vaccination and Veterinary</div>
        <div class="info"><span class="label">Vaccine:</span> <span class="value"><?php echo htmlspecialchars($record['vaccine']); ?></span></div>
        <div class="info"><span class="label">Veterinarian:</span> <span class="value"><?php echo htmlspecialchars($record['veterinarian']); ?></span></div>

        <div class="section-header">Date & Time</div>
        <div class="info"><span class="label">Date:</span> <span class="value"><?php echo htmlspecialchars($record['visitdate']); ?></span></div>
        <div class="info"><span class="label">Time:</span> <span class="value"><?php echo htmlspecialchars($record['time']); ?></span></div>
    </div>
</body>
</html>