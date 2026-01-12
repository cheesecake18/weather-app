<?php
header('Content-Type: application/json');
include '../config.php';

$sql = "SELECT entry, date FROM journals ORDER BY date DESC";
$result = $conn->query($sql);

$journals = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $journals[] = $row;
    }
}

echo json_encode($journals);
$conn->close();
?>