<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$paper_id = intval($_GET['id']);

$paper = fetchPaperDetails1($conn, $paper_id);
if (!$paper) {
    die("Manuscript not found.");
}

$editor_tasks = fetchEditorTasksForPaper($conn, $paper_id);
$reviews = fetchReviewAssignments($conn, $paper_id);

// Labels (can also be moved to separate file if reused a lot)
$status_labels = [
    'Uploaded' => 'Manuscript Uploaded',
    'Pending' => 'Pending Review',
    'Under Review' => 'under going Review',
    'In Review' => 'Review in Progress',
    'Completed' => 'Review Completed',
    'Accepted' => 'Accepted for Publication',
    'Rejected' => 'Rejected by Editor',
    'Revision Requested' => 'Revision Required'
];

$task_names = [
    1 => 'Initial Review',
    2 => 'Plagiarism Check',
    3 => 'Assign Reviewer',
    4 => 'Format Check'
];

$task_colors = [
    1 => 'bg-primary',
    2 => 'bg-secondary',
    3 => 'bg-warning',
    4 => 'bg-success'
];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Track Manuscript</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .timeline {
            border-left: 4px solid #0d6efd;
            margin-top: 20px;
        }
        .timeline-step {
            position: relative;
            margin-left: 20px;
            margin-bottom: 25px;
        }
        .timeline-step::before {
            content: '';
            position: absolute;
            left: -28px;
            top: 5px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: #0d6efd;
        }
        .status-badge {
            font-size: 0.875rem;
            padding: 5px 12px;
            border-radius: 20px;
            color: white;
            margin-bottom: 8px;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <a href="chief-dashboard.php" class="btn btn-outline-secondary mb-3">← Back to Dashboard</a>

    <div class="card shadow p-4">
        <h3 class="mb-3">📄 Tracking Manuscript</h3>
        <p><strong>Title:</strong> <?= htmlspecialchars($paper['title']) ?></p>
        <p><strong>Author:</strong> <?= htmlspecialchars($paper['author_name']) ?></p>
        <p><strong>Submitted on:</strong> <?= $paper['submission_date'] ?></p>
        <p><strong>Last Updated:</strong> <?= $paper['updated_at'] ?></p>
        <p><strong>Status:</strong> 
            <span class="badge bg-info"><?= $status_labels[$paper['status']] ?? htmlspecialchars($paper['status']) ?></span>
        </p>

        <hr>
        <h5 class="mt-4">📌 Timeline</h5>
        <div class="timeline">
            <!-- Uploaded -->
            <div class="timeline-step">
                <span class="status-badge bg-primary"><?= $status_labels['Uploaded'] ?></span>
                <br><small>Manuscript Uploaded on <?= $paper['submission_date'] ?></small>
            </div>

            <!-- Editor Tasks -->
            <?php foreach ($editor_tasks as $task): ?>
                <?php if ($task['task_type'] == 4): ?>
                    <!-- Inject reviewer assignment block before format check -->
                    <?php foreach ($reviews as $review): ?>
                        <div class="timeline-step">
                            <span class="status-badge bg-warning"><?= $status_labels['Under Review'] ?></span>
                            <br><strong>Assigned to:</strong> <?= htmlspecialchars($review['reviewer_name']) ?>
                            <br><strong>On:</strong> <?= $review['assigned_date'] ?>
                            <br><strong>Status:</strong> <?= $status_labels[$review['review_status']] ?? 'Pending' ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="timeline-step">
                    <span class="status-badge <?= $task_colors[$task['task_type']] ?? 'bg-dark' ?>">
                        <?= $task_names[$task['task_type']] ?? 'Task' ?>
                    </span>
                    <br>Status: <strong><?= htmlspecialchars($task['status']) ?></strong>
                    <?php if ($task['result']): ?>
                        <br>Result: <strong><?= htmlspecialchars($task['result']) ?></strong>
                    <?php endif; ?>
                    <?php if ($task['response_date']): ?>
                        <br>Response Date: <?= $task['response_date'] ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <!-- Final Decision -->
            <?php if (in_array($paper['status'], ['Accepted', 'Rejected', 'Revision Requested'])): ?>
                <div class="timeline-step">
                    <span class="status-badge bg-success">
                        <?= $status_labels[$paper['status']] ?? htmlspecialchars($paper['status']) ?>
                    </span>
                    <br>Final Decision Recorded on <?= $paper['updated_at'] ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
