<?php
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$city = $conn->real_escape_string($data['city']);

// Remove duplicates, keep latest
$conn->query("DELETE FROM searches WHERE city='$city'");
$sql = "INSERT INTO searches (city) VALUES ('$city')";

if ($conn->query($sql) === TRUE) {
    // Keep only last 5
    $conn->query("DELETE FROM searches WHERE id NOT IN (SELECT id FROM (SELECT id FROM searches ORDER BY date DESC LIMIT 5) tmp)");
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>