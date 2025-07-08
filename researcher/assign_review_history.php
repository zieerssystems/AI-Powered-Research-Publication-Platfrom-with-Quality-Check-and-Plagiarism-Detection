<?php
include(__DIR__ . "/../../include/db_connect.php");

$editor_id = $_SESSION['editor_id'];
$task_type = 3;

$status_filter = isset($_GET['filter']) ? $_GET['filter'] : 'Accepted';

// Fetch review history using the function from db_connect.php
$result = fetchReviewHistory($conn, $editor_id, $task_type, $status_filter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f2f2f2, #e6f2ff);
            font-family: 'Segoe UI', sans-serif;
        }
        .review-card {
            border-radius: 15px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .review-card:hover {
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .highlight {
            border: 2px solid red !important;
            background-color: #ffe6e6 !important;
        }
        .filter-btn {
            margin-left: 10px;
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
    <a href="editor_dashboard.php" class="btn-back">← Dashboard</a>
    <div class="container my-5">
        <h2 class="mb-4 text-primary">📘 Review History (Accepted / Rejected)</h2>

        <!-- Filter Buttons -->
        <div class="mb-4">
            <a href="?filter=Accepted" class="btn btn-outline-success filter-btn <?= $status_filter == 'Accepted' ? 'active' : '' ?>">✅ Accepted</a>
            <a href="?filter=Rejected" class="btn btn-outline-danger filter-btn <?= $status_filter == 'Rejected' ? 'active' : '' ?>">❌ Rejected</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php while ($row = $result->fetch_assoc()):
                    $highlight = ($row['status'] == 'Accepted' && empty($row['result'])) ? 'highlight' : '';
                ?>
                    <div class="col">
                        <div class="review-card <?= $highlight ?>">
                            <h5 class="text-dark"><?= htmlspecialchars($row['title']) ?></h5>
                            <p><strong>Journal:</strong> <?= htmlspecialchars($row['journal_name']) ?></p>
                            <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
                            <p><strong>Deadline:</strong> <?= htmlspecialchars($row['deadline']) ?></p>
                            <?php if (strtolower($row['status']) == 'accepted'): ?>
                                <a href="reviewers.php?paper_id=<?= $row['paper_id'] ?>&journal_id=<?= $row['journal_id'] ?>" class="btn btn-sm btn-info">View Details</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mt-4">No previously reviewed tasks.</div>
        <?php endif; ?>
    </div>
</body>
</html>
