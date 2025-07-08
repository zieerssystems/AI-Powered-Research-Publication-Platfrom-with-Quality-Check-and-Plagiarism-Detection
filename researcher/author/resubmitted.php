<?php
session_start();
if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");

$reviewer_id = $_SESSION['reviewer_id'];

// Fetch resubmitted papers using the function from db_connect.php
$result = fetchResubmittedPapers($conn, $reviewer_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Resubmitted Papers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .container {
            max-width: 900px;
        }
        .card {
            margin-bottom: 20px;
            border-left: 5px solid #6c757d;
        }
        .action-buttons button {
            margin-right: 10px;
        }
        .file-link {
            text-decoration: none;
            color: #0d6efd;
        }
        .file-link:hover {
            text-decoration: underline;
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
<a href="reviewer_dashboard.php" class="btn-back">⬅ Back to Reviewer Dashboard</a>
<div class="container mt-4">
    <h2 class="mb-4">📄 Resubmitted Papers for Review</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card p-3">
                <h5 class="mb-1">Title: <?= htmlspecialchars($row['title']) ?></h5>
                <p><strong>Journal:</strong> <?= htmlspecialchars($row['journal_name']) ?></p>
                <p><strong>Submitted On:</strong> <?= $row['submission_date'] ?></p>
                <p><strong>Assigned Date:</strong> <?= $row['assigned_date'] ?></p>
                <p><strong>Download Files:</strong></p>
                <ul>
                    <?php if (!empty($row['file_path'])): ?>
                        <li>
                            <a class="file-link" href="../../uploads/<?= htmlspecialchars(basename($row['file_path'])) ?>" target="_blank">📄 Manuscript</a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($row['supplementary_files_path'])): ?>
                        <li>
                            <a class="file-link" href="../../uploads/<?= htmlspecialchars(basename($row['supplementary_files_path'])) ?>" target="_blank">📎 Supplementary Files</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <form action="submit_review.php" method="POST">
                    <input type="hidden" name="paper_id" value="<?= $row['paper_id'] ?>">
                    <input type="hidden" name="title" value="<?= htmlspecialchars($row['title']) ?>">
                    <input type="hidden" name="journal_name" value="<?= htmlspecialchars($row['journal_name']) ?>">

                    <div class="mb-2">
                        <label for="feedback_<?= $row['paper_id'] ?>">Comments:</label>
                        <textarea class="form-control" name="feedback" id="feedback_<?= $row['paper_id'] ?>" rows="3" required></textarea>
                    </div>

                    <div class="action-buttons">
                        <button type="submit" name="action" value="Accepted" class="btn btn-success">✅ Accept</button>
                        <button type="submit" name="action" value="Rejected" class="btn btn-danger">❌ Reject</button>
                        <button type="submit" name="action" value="Revision Requested" class="btn btn-warning">🔁 Request Revision</button>
                    </div>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No resubmitted papers available at the moment.</p>
    <?php endif; ?>
</div>
</body>
</html>
