<?php
session_start();
if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");
$editor_id = $_SESSION['editor_id'];

// Fetch revised submitted papers using the function from db_connect.php
$result = fetchRevisedSubmittedPapers($conn, $editor_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Revised Submitted Papers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .paper-card {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .paper-card h5 {
            font-weight: 600;
        }
        .view-button, .plagiarism-button {
            margin-top: 10px;
        }
        body {
            background-color: #f1f3f5;
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
<div class="container mt-5">
    <h2 class="mb-4 text-primary">📄 Revised Submitted Papers</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="paper-card">
                <h5><?= htmlspecialchars($row['title']) ?></h5>
                <p><strong>Author:</strong> <?= htmlspecialchars($row['author_name']) ?></p>

                <!-- View Manuscript Button -->
                <a href="editor_manuscript.php?paper_id=<?= $row['paper_id'] ?>" class="btn btn-outline-primary btn-sm view-button">👁 View Manuscript</a>

                <!-- Plagiarism Check Button -->
                <a href="handle_plagiarism_update.php?paper_id=<?= $row['paper_id'] ?>" class="btn btn-outline-warning btn-sm plagiarism-button">🔍 Plagiarism Check</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">No revised submitted papers available at the moment.</div>
    <?php endif; ?>
</div>
</body>
</html>
