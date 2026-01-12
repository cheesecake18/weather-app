<?php
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$entry = $conn->real_escape_string($data['entry']);

$sql = "INSERT INTO journals (entry) VALUES ('$entry')";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>