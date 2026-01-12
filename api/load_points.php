<?php
header('Content-Type: application/json');
require_once '../src/classes/Points.php';

$points = new Points();
$data = $points->getPointsAndStreak();
echo json_encode($data);
?>