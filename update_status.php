<?php
header('Content-Type: application/json'); // Ensure the response is JSON

// Kunin ang input data
$input = json_decode(file_get_contents('php://input'), true);
$appointmentId = $input['id'] ?? null;
$newStatus = $input['status'] ?? null;

if (!$appointmentId || !$newStatus) {
    http_response_code(400); // Bad request
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// I-connect sa MySQL database
$mysqli = new mysqli('localhost', 'root', '', 'fetch_chill_db');

// I-check ang connection
if ($mysqli->connect_error) {
    http_response_code(500); // Internal server error
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Gumawa ng prepared statement para i-update ang status
$stmt = $mysqli->prepare("UPDATE appointments SET status = ? WHERE id = ?");
if (!$stmt) {
    http_response_code(500); // Internal server error
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
    exit;
}

// I-bind ang mga parameter
$stmt->bind_param('si', $newStatus, $appointmentId);

// I-execute ang query
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
} else {
    http_response_code(500); // Internal server error
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}

// Isara ang statement at connection
$stmt->close();
$mysqli->close();
?>