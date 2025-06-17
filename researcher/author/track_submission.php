<?php
session_start();
if (!isset($_SESSION['author_id'])) {
    header("Location: author_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");


$author_id = $_SESSION['author_id'];
list($papers, $paper_ids) = fetchAuthorPapersWithStatus($conn, $author_id);
$editor_tasks = fetchEditorTasks($conn, $paper_ids);

// Task labels
$task_labels = [
    1 => 'Initial Review',
    2 => 'Plagiarism Check',
    3 => 'Assign Reviewer',
    4 => 'Format Check'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Track Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        .timeline-container {
            width: 80%;
            margin: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .timeline-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #007bff;
            text-align: left;
            position: relative;
        }
        .timeline-box::before {
            content: '📝';
            position: absolute;
            left: -25px;
            top: 15px;
            font-size: 20px;
        }
        .timeline-title {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .timeline-details {
            font-size: 14px;
            color: #555;
        }
        .status-timeline {
            margin-top: 10px;
            font-size: 14px;
            background: #e9f5ff;
            padding: 8px;
            border-radius: 5px;
            border-left: 3px solid #007bff;
        }
        .editor-task-status {
            margin-top: 10px;
            background: #f0f8ff;
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #28a745;
        }
        .editor-task-status p {
            margin: 3px 0;
            font-size: 14px;
            color: #333;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-back {
        display: inline-block;
        margin: 20px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #4b6cb7, #182848);
        color: white;
        border: none;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: linear-gradient(135deg, #182848, #4b6cb7);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }
    </style>
</head>
<body>
<a href="author_dashboard.php" class="btn-back">← Back to Author Dashboard</a>
<h2>📊 Track Submission</h2>

<div class="timeline-container">
    <?php foreach ($papers as $row) { ?>
        <div class="timeline-box">
            <div class="timeline-title"><?= htmlspecialchars($row['paper_title']) ?></div>
            <div class="timeline-details">📅 Submitted: <?= $row['submission_date'] ?></div>
            <div class="timeline-details">📖 Journal: <?= htmlspecialchars($row['journal_name']) ?></div>
            <div class="status-timeline">
                🔄 Status Timeline: <?= $row['status_timeline'] ? $row['status_timeline'] : 'Pending' ?>
            </div>
            <div class="timeline-details"><strong>📌 Current Status: </strong> <?= htmlspecialchars($row['current_status']) ?></div>

            <?php if (!empty($editor_tasks[$row['paper_id']])) { ?>
                <div class="editor-task-status">
                    <?php foreach ($task_labels as $type => $label) { ?>
                        <p>🔹 <?= $label ?>: <?= $editor_tasks[$row['paper_id']][$type] ?? 'Not Started' ?></p>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
</body>
</html>
