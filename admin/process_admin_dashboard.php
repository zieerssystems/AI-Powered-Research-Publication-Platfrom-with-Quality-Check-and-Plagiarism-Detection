<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}

include(__DIR__ . "/../include/db_connect.php");

// Fetch total number of journals
$approved_reviewers = getReviewerCount($conn, 'approved');
$rejected_reviewers = getReviewerCount($conn, 'rejected');
// $pending_reviewers = getReviewerCount($conn, 'pending');
// $pending_editors = getEditorCount($conn, 'pending');
$total_journals = getTotalJournals($conn, );
$pending_reviewer_applications = getPendingReviewerApplications($conn);
$pending_editor_applications = getPendingEditorApplications($conn);
$pending_reviews = getPendingPaperCount($conn);
$accepted_papers = getAcceptedPaperCount($conn);
$rejected_papers = getRejectedPaperCount($conn);
$published_count=getPublishedPaperCount($conn);
$editor_contract_count = getEditorContractCount($conn);
$reviewer_contract_count = getReviewerContractCount($conn);

// Fetch all reviewers
$reviewers = getAllReviewersWithJournals($conn);

?>
