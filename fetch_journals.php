<?php
session_start();
// Database connection
include("include/db_connect.php");

// Get filters from AJAX request
$search = isset($_POST['search']) ? "%" . $_POST['search'] . "%" : "%%";
$primary_subject = $_POST['primary_subject'] ?? "";
$secondary_subject = $_POST['secondary_subject'] ?? "";
$open_access = $_POST['open_access'] ?? "";
$subscription = $_POST['subscription'] ?? "";
$submissions = $_POST['submissions'] ?? "";
$closed_submissions = $_POST['closed_submissions'] ?? "";

// Start building SQL query and parameters
$query = "SELECT id, journal_name, primary_subject, secondary_subject, access_type, submission_status FROM journals WHERE journal_name LIKE ?";
$params = ["s", &$search];

// Filter: Primary Subject
if (!empty($primary_subject) && $primary_subject !== "Select primary subject") {
    $query .= " AND primary_subject = ?";
    $params[0] .= "s";
    $params[] = &$primary_subject;
}

// Filter: Secondary Subject
if (!empty($secondary_subject) && $secondary_subject !== "Select secondary subject") {
    $query .= " AND secondary_subject = ?";
    $params[0] .= "s";
    $params[] = &$secondary_subject;
}

// Filter: Access Type (Open Access / Subscription)
if (!empty($open_access) || !empty($subscription)) {
    $query .= " AND (";
    $conditions = [];

    if (!empty($open_access)) {
        $conditions[] = "access_type = ?";
        $params[0] .= "s";
        $params[] = &$open_access;
    }

    if (!empty($subscription)) {
        $conditions[] = "access_type = ?";
        $params[0] .= "s";
        $params[] = &$subscription;
    }

    $query .= implode(" OR ", $conditions) . ")";
}

// Filter: Submission Status
// Filter: Submission Status
$submissionStatuses = [];
if (!empty($submissions)) {
    $submissionStatuses[] = $submissions;
}
if (!empty($closed_submissions)) {
    $submissionStatuses[] = $closed_submissions;
}

if (!empty($submissionStatuses)) {
    $placeholders = implode(',', array_fill(0, count($submissionStatuses), '?'));
    $query .= " AND submission_status IN ($placeholders)";
    $params[0] .= str_repeat('s', count($submissionStatuses));
    foreach ($submissionStatuses as &$status) {
        $params[] = &$status;
    }
}

// Prepare and execute statement
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param(...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate HTML output
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<li class='journal-item'>
                <a href='journal_detail.php?journal_id=" . urlencode($row['id']) . "'>{$row['journal_name']}</a><br>
                <span>• {$row['access_type']} | Submission Status: {$row['submission_status']}</span>
            </li>";
        }
    } else {
        echo "<li>No journals found.</li>";
    }

    $stmt->close();
} else {
    echo "<li>Error in query preparation.</li>";
}

$conn->close();
?>
