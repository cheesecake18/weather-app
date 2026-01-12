<?php
header('Content-Type: application/json');
include '../config.php';

$sql = "SELECT city FROM searches ORDER BY date DESC LIMIT 5";
$result = $conn->query($sql);

$searches = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $searches[] = $row['city'];
    }
}

echo json_encode($searches);
$conn->close();
?>