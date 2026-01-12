<?php
header('Content-Type: application/json');
require_once '../src/classes/Points.php';

$data = json_decode(file_get_contents('php://input'), true);
$points = new Points();
$result = $points->savePointsAndStreak($data['points'], $data['streak']);
echo json_encode($result);
?>