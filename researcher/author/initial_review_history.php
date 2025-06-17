<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

$editor_id = $_SESSION['editor_id'];
$task_type = 1;
$status_filter = isset($_GET['filter']) ? $_GET['filter'] : 'Accepted';

// Fetch accepted or rejected based on filter
$result = getEditorReviewHistory($conn, $editor_id, $task_type, $status_filter);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            padding: 30px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .highlight {
            border: 2px solid red !important;
            background-color: #ffe6e6 !important;
        }
        .btn-group-filter a {
            margin-right: 10px;
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
  <a href="editor_dashboard.php" class="btn-back">← Back to Editor Dashboard</a>
<div class="container">
    <h2 class="mb-4 text-center">Review History (Accepted / Rejected)</h2>

    <!-- Filter and Navigation -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div class="btn-group-filter">
            <a href="?filter=Accepted" class="btn btn-outline-success <?= $status_filter == 'Accepted' ? 'active' : '' ?>">Accepted</a>
            <a href="?filter=Rejected" class="btn btn-outline-danger <?= $status_filter == 'Rejected' ? 'active' : '' ?>">Rejected</a>
        </div>
    </div>

    <?php if ($result->num_rows > 0): ?>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()):
            // Highlight the row if the result is empty and status is accepted
            $highlight = (empty($row['result']) && $row['status'] == 'Accepted') ? 'highlight' : '';
        ?>
        <div class="col-md-6 mb-4">
            <div class="card p-3 <?= $highlight ?>">
                <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                <p class="mb-1"><strong>Journal:</strong> <?= htmlspecialchars($row['journal_name']) ?></p>
                <p class="mb-2"><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
                <?php if (strtolower($row['status']) == 'accepted'): ?>
                    <a href="initial_paper_detail.php?paper_id=<?= $row['paper_id'] ?>" class="btn btn-sm btn-info">View Details</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-info text-center">No previously reviewed tasks.</div>
    <?php endif; ?>
</div>
</body>
</html>

<?php $conn->close(); ?>