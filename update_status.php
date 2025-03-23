<?php
require_once 'pet_connection.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $ids = $data['ids'] ?? [];
    $status = $data['status'] ?? '';

    if (empty($ids) || !in_array($status, ['Confirmed', 'Cancelled'])) {
        throw new Exception('Invalid input');
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "UPDATE appointments SET status = ? WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $types = 's' . str_repeat('i', count($ids)); // 's' for status, 'i' for each ID
    $stmt->bind_param($types, $status, ...$ids);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Execution failed: " . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>