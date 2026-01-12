<?php
header('Content-Type: application/json');
require_once '../src/classes/Journal.php';

$journal = new Journal();
$journals = $journal->getAllJournals();
echo json_encode($journals);
?>