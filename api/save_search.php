<?php
header('Content-Type: application/json');
require_once '../src/classes/Searches.php';

$data = json_decode(file_get_contents('php://input'), true);
$searches = new Searches();
$result = $searches->saveSearch($data['city']);
echo json_encode($result);
?>