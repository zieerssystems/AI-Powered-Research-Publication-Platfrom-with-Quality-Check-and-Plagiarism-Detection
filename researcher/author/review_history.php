<?php
session_start();
if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");

$reviewer_id = $_SESSION['reviewer_id'];

// Fetch review history using the function from db_connect.php
$history = ReviewHistory($conn, $reviewer_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Review History</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 900px;
        }
        .card {
            margin-bottom: 15px;
            border-left: 5px solid #007bff;
        }
        .feedback {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        .status-pending {
            background-color: #ffc107;
            color: black;
        }
        .status-completed {
            background-color: #28a745;
            color: white;
        }
        .status-rejected {
            background-color: #dc3545;
            color: white;
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
<a href="reviewer_dashboard.php" class="btn-back">← Back to Reviewer Dashboard</a>
<div class="container mt-4">
    <h2 class="mb-4">📜 Review History</h2>

    <?php while ($row = $history->fetch_assoc()): ?>
        <div class="card p-3">
            <h5 class="mb-1"><?= htmlspecialchars($row['title']) ?></h5>
            <p><strong>Journal:</strong> <?= htmlspecialchars($row['journal_name'] ?? 'N/A') ?></p>

            <p><strong>Review Status:</strong>
                <span class="status-badge
                    <?= $row['review_status'] == 'Completed' ? 'status-completed' :
                        ($row['review_status'] == 'Pending' ? 'status-pending' : 'status-rejected') ?>">
                    <?= htmlspecialchars($row['review_status']) ?>
                </span>
            </p>
            <?php if ($row['paper_status'] === 'Revision Requested'): ?>
                <p><strong>Paper Status:</strong>
                    <span class="status-badge status-pending"><?= htmlspecialchars($row['paper_status']) ?></span>
                </p>
            <?php endif; ?>

            <p><strong>Assigned Date:</strong> <?= $row['assigned_date'] ?? 'N/A' ?></p>
            <p><strong>Completed Date:</strong> <?= $row['completed_date'] ?? 'N/A' ?></p>

            <?php if ($row['feedback']): ?>
                <div class="feedback">
                    <p><strong>Feedback Given:</strong></p>
                    <p><?= nl2br(htmlspecialchars($row['feedback'])) ?></p>
                    <p><strong>Reviewed on:</strong> <?= $row['review_date'] ?? 'N/A' ?></p>
                </div>
            <?php else: ?>
                <p><em>No feedback submitted yet.</em></p>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
  </div>

</body>
</html>
