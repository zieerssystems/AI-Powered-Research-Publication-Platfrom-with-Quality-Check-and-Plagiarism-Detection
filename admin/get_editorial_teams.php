<?php
include(__DIR__ . "/../include/db_connect.php");
$teams = getAllEditorialTeams($conn);
echo json_encode($teams);
?>
