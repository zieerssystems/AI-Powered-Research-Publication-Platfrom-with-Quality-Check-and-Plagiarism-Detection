<?php
session_start();
$paper_id = $_POST['paper_id'];
$amount = $_POST['amount'];

// Simulate payment success (replace with real API)
if (!isset($_SESSION['paid_papers'])) $_SESSION['paid_papers'] = [];
$_SESSION['paid_papers'][] = $paper_id;

header("Location: paper_details.php?paper_id=$paper_id");
exit();
