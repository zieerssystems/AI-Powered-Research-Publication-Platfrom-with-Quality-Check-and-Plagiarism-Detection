<?php
include(__DIR__ . "/../../include/db_connect.php");


$data = fetchPrimarySubjectsAndJournals();

if (!$data) {
    echo json_encode(["error" => "No subjects found or database query failed"]);
} else {
    echo json_encode($data);
}
?>
