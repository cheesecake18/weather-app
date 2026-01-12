<?php
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$points = intval($data['points']);
$streak = intval($data['streak']);

$sql = "INSERT INTO points (id, points, streak) VALUES (1, $points, $streak) ON DUPLICATE KEY UPDATE points=$points, streak=$streak";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>