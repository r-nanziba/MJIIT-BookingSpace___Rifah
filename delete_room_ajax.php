<?php
// Include database configuration file
include 'config.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if room_id is set
if (!isset($_POST['room_id'])) {
    echo json_encode(['success' => false, 'message' => 'Room ID not provided']);
    exit;
}

$room_id = $_POST['room_id'];

// Prepare and execute the delete query
$stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = ?");
$stmt->bind_param("i", $room_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();