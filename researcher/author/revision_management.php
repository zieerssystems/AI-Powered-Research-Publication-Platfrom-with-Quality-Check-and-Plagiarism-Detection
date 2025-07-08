<?php
include(__DIR__ . "/../../include/db_connect.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$author_id = $_SESSION['author_id'];

// Fetch reviewer feedback-based revisions using the function from db_connect.php
$result1 = fetchReviewerFeedbackRevisions($conn, $author_id);

// Fetch editor task-based revision requests using the function from db_connect.php
$result2 = fetchEditorTaskRevisions($conn, $author_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Revisions</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #f2f2f2, #e6f0ff);
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #003366;
            margin-bottom: 30px;
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

        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 20px;
            margin: 20px auto;
            max-width: 700px;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin-top: 0;
            color: #003366;
        }

        .feedback {
            background: #f9f9f9;
            padding: 12px;
            border-left: 4px solid #003366;
            margin-top: 10px;
            white-space: pre-wrap;
        }

        .reupload-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 16px;
            background-color: #0073e6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .reupload-btn:hover {
            background-color: #005bb5;
        }

        @media screen and (max-width: 768px) {
            .card {
                margin: 10px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<a class="btn-back" href="author_dashboard.php">⬅ Back to Author Dashboard</a>
<h1>🔄 Manage Revisions</h1>

<?php while($row = $result1->fetch_assoc()): ?>
    <div class="card">
        <h3><?php echo htmlspecialchars($row['title']); ?> <small>(<?php echo htmlspecialchars($row['journal_name']); ?>)</small></h3>
        <p><strong>Review Date:</strong> <?php echo htmlspecialchars($row['review_date']); ?></p>
        <div class="feedback">
            <strong>Reviewer Feedback:</strong><br>
            <?php echo nl2br(htmlspecialchars($row['feedback'])); ?>
        </div>
        <a class="reupload-btn" href="paper_resubmit.php?journal_id=<?php echo $row['journal_id']; ?>&paper_id=<?php echo $row['paper_id']; ?>&reupload=1">Reupload Paper</a>
    </div>
<?php endwhile; ?>

<?php while($row = $result2->fetch_assoc()): ?>
    <div class="card">
        <h3><?php echo htmlspecialchars($row['title']); ?> <small>(<?php echo htmlspecialchars($row['journal_name']); ?>)</small></h3>
        <p><strong>Requested by Editor:</strong> <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></p>
        <p><strong>Task Type:</strong>
            <?php
                if ($row['task_type'] == 2) {
                    echo 'Plagiarism Check';
                } elseif ($row['task_type'] == 4) {
                    echo 'Format Check';
                } else {
                    echo 'Other';
                }
            ?>
        </p>
        <div class="feedback">
            <strong>Editorial Feedback:</strong><br>
            <?php
                $editorialFeedback = fetchFeedbackByTaskType($conn, $row['task_type'], $row['paper_id']);
                echo nl2br(htmlspecialchars($editorialFeedback));
            ?>
        </div>
        <a class="reupload-btn" href="paper_resubmit.php?journal_id=<?php echo $row['journal_id']; ?>&paper_id=<?php echo $row['paper_id']; ?>&reupload=1">Reupload Paper</a>
    </div>
<?php endwhile; ?>

</body>
</html>
