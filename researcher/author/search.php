<?php
include(__DIR__ . "/../../include/db_connect.php");
header('Content-Type: application/json');

// Decode POST data
$data = json_decode(file_get_contents("php://input"), true);

// Retrieve and sanitize input
$type = $data['type'] ?? '';
$input = $data['input'] ?? '';
$minAcceptance = isset($data['acceptance']) ? (float)$data['acceptance'] : 1;
$minCiteScore = isset($data['citescore']) ? (float)$data['citescore'] : 1;
$minImpact = isset($data['impact_factor']) ? (float)$data['impact_factor'] : 1;

// Fetch journals based on criteria using the function from db_connect.php
$journals = fetchJournals($conn, $type, $input, $minAcceptance, $minCiteScore, $minImpact);

echo json_encode($journals);
?>
