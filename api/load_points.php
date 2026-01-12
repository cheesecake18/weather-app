<?php
header('Content-Type: application/json');
include '../config.php';

$sql = "SELECT points, streak FROM points WHERE id=1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['points' => $row['points'], 'streak' => $row['streak']]);
} else {
    echo json_encode(['points' => 0, 'streak' => 1]);
}

$conn->close();
?>