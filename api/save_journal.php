<?php
header('Content-Type: application/json');
require_once '../src/classes/Journal.php';

$data = json_decode(file_get_contents('php://input'), true);
$journal = new Journal();
$result = $journal->saveJournal($data['entry']);
echo json_encode($result);
?>