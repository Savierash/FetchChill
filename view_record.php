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

// Handle update if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $ownername = $_POST['ownername'] ?? '';
    $petname = $_POST['petname'] ?? '';
    $pet_type = $_POST['pet_type'] ?? '';
    $breed = $_POST['breed'] ?? '';
    $weight = $_POST['weight'] ?? 0.0;
    $gender = $_POST['gender'] ?? '';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $treatment = $_POST['treatment'] ?? '';
    $vaccine = $_POST['vaccine'] ?? '';
    $veterinarian = $_POST['veterinarian'] ?? '';
    $visitdate = $_POST['visitdate'] ?? '';
    $checkup_time = $_POST['checkup_time'] ?? '';

    $update_sql = "UPDATE petrecords SET 
        ownername = ?, petname = ?, pet_type = ?, breed = ?, weight = ?, 
        gender = ?, diagnosis = ?, treatment = ?, vaccine = ?, 
        veterinarian = ?, visitdate = ?, checkup_time = ? 
        WHERE ownername = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    if ($update_stmt === false) {
        die("Error: Failed to prepare update statement - " . $conn->error);
    }

    $update_stmt->bind_param("ssssdssssssss", 
        $ownername, $petname, $pet_type, $breed, $weight, 
        $gender, $diagnosis, $treatment, $vaccine, 
        $veterinarian, $visitdate, $checkup_time, $ownername
    );

    if ($update_stmt->execute()) {
        echo "<script>alert('Record updated successfully'); window.location.href = '?ownername=" . urlencode($ownername) . "';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating record: " . $update_stmt->error . "');</script>";
    }
    $update_stmt->close();
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<style>
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
}

.close-button:hover {
    color: black;
}

.button-container {
    margin-top: 20px;
    text-align: center;
}

.edit-button, .update-button {
    padding: 10px 20px;
    margin: 0 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.edit-button {
    background-color: #4CAF50;
    color: white;
}

.update-button {
    background-color: #2196F3;
    color: white;
}

.edit-button:hover, .update-button:hover {
    opacity: 0.9;
}

.edit-input {
    width: 100%;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
</style>

<body>
<div class="modal-container">
    <span class="close-button" onclick="window.history.back();">×</span>

    <form method="POST" id="updateForm">
        <input type="hidden" name="update" value="1">
        <div class="section-header">Medical Information</div>
        <div class="info"><span class="label">Owner Name:</span> <span class="value" data-field="ownername"><?php echo htmlspecialchars($record['ownername'] ?? ''); ?></span></div>
        <div class="info"><span class="label">Pet Name:</span> <span class="value" data-field="petname"><?php echo htmlspecialchars($record['petname'] ?? ''); ?></span></div>
        <div class="info"><span class="label">Pet Type:</span> <span class="value" data-field="pet_type"><?php echo htmlspecialchars($record['pet_type'] ?? ''); ?></span></div>
        <div class="info"><span class="label">Breed:</span> <span class="value" data-field="breed"><?php echo htmlspecialchars($record['breed'] ?? ''); ?></span></div>
        <div class="info"><span class="label">Weight:</span> <span class="value" data-field="weight"><?php echo htmlspecialchars($record['weight'] ?? '0'); ?> kg</span></div>
        <div class="info"><span class="label">Gender:</span> <span class="value" data-field="gender"><?php echo htmlspecialchars($record['gender'] ?? ''); ?></span></div>

        <div class="section-header">Health Concerns</div>
        <div class="info"><span class="label">Diagnosis:</span> <span class="value" data-field="diagnosis"><?php echo htmlspecialchars($record['diagnosis'] ?? ''); ?></span></div>
        <div class="info"><span class="label">Treatment:</span> <span class="value" data-field="treatment"><?php echo htmlspecialchars($record['treatment'] ?? ''); ?></span></div>

        <div class="section-header">Vaccination and Veterinary</div>
        <div class="info"><span class="label">Vaccine:</span> <span class="value" data-field="vaccine"><?php echo htmlspecialchars($record['vaccine'] ?? ''); ?></span></div>
        <div class="info"><span class="label">Veterinarian:</span> <span class="value" data-field="veterinarian"><?php echo htmlspecialchars($record['veterinarian'] ?? ''); ?></span></div>

        <div class="section-header">Date & Time</div>
        <div class="info"><span class="label">Date:</span> <span class="value" data-field="visitdate"><?php echo htmlspecialchars($record['visitdate'] ?? ''); ?></span></div>
        <div class="info"><span class="label">Time:</span> <span class="value" data-field="checkup_time"><?php echo htmlspecialchars($record['checkup_time'] ?? ''); ?></span></div>

        <div class="button-container">
            <button type="button" class="edit-button" onclick="enableEdit()">Edit</button>
            <button type="submit" class="update-button">Update</button>
        </div>
    </form>
</div>

<script>
function enableEdit() {
    const values = document.querySelectorAll('.value');
    values.forEach(value => {
        const currentText = value.textContent.replace(' kg', ''); // Remove 'kg' for weight
        const input = document.createElement('input');
        input.type = 'text';
        input.name = value.getAttribute('data-field');
        input.value = currentText;
        input.className = 'edit-input';
        value.innerHTML = '';
        value.appendChild(input);
    });
    document.querySelector('.edit-button').disabled = true;
}
</script>
</body>
</html>