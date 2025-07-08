<?php 
session_start();
include(__DIR__ . "/../include/db_connect.php");
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['paper_id'])) {
    $paper_id = intval($_POST['paper_id']);

    $result = getPaperStatus($conn, $paper_id);

    if ($result && $row = $result->fetch_assoc()) {
        if ($row['status'] === 'Accepted (Final Decision)') {
            $doi = '10.1234/zieers.' . uniqid();

            if (updatePaperDOI($conn, $paper_id, $doi)) {
                echo "<script>alert('DOI generated successfully.'); window.location.href='review_paper.php';</script>";
            } else {
                echo "<script>alert('Failed to update DOI.'); window.location.href='review_paper.php';</script>";
            }
        } else {
            echo "<script>alert('DOI can only be generated for Accepted (Final Decision) papers.'); window.location.href='review_paper.php';</script>";
        }
    } else {
        echo "<script>alert('Paper not found.'); window.location.href='review_paper.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href='review_paper.php';</script>";
}
?>
