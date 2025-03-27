<?php
require_once 'pet_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            http_response_code(200);
        } else {
            http_response_code(500);
        }
        $stmt->close();
    } else {
        http_response_code(400);
    }
}
$conn->close();
?>