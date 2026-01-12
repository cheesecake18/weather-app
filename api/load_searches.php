<?php
header('Content-Type: application/json');
require_once '../src/classes/Searches.php';

$searches = new Searches();
$data = $searches->getRecentSearches();
echo json_encode($data);
?>