<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

// Check if editor is logged in
$loggedInEditorId = $_SESSION['chief_editor_id'] ?? null;
if (!$loggedInEditorId) {
    die("Unauthorized access");
}

// Fetch papers assigned to the logged-in editor
$paperResult = getPapersByEditorId($conn, $loggedInEditorId);

// Fetch papers into array
$papers = [];
$paperIds = []; // To ensure unique paper IDs

while ($paper = $paperResult->fetch_assoc()) {
    // Only add the paper if it's not already in the list
    if (!in_array($paper['paper_id'], $paperIds)) {
        $papers[] = $paper;
        $paperIds[] = $paper['paper_id'];
    }
}

// Fetch co-authors for each paper
foreach ($papers as &$paper) {
    $paper['co_authors'] = getCoAuthorsByPaperId($conn, $paper['paper_id']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Paper History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .back-button {
            background: #3498db;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        h2, h3 {
            color: #333;
        }
        p {
            margin: 5px 0;
        }
        .co-authors {
            margin-left: 20px;
        }
        .completed-date {
            color: #777;
        }
    </style>
</head>
<body>

<a href="task.php" class="back-button">← Back</a>

<div class="box">
    <h2>Paper History</h2>

    <?php if (!empty($papers)): ?>
        <?php foreach ($papers as $paper): ?>
            <div class="paper-box">
                <p><strong>Title:</strong> <?= htmlspecialchars($paper['paper_title']) ?></p>
                <p><strong>Journal:</strong> <?= htmlspecialchars($paper['journal_name']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($paper['status']) ?></p>
                <p class="completed-date"><strong>Completed Date:</strong> <?= htmlspecialchars($paper['completed_date'] ?? '') ?></p>
                <p><strong>Author:</strong> <?= htmlspecialchars($paper['author_first'] . ' ' . $paper['author_last']) ?></p>

                <h3>Co-authors:</h3>
                <ul class="co-authors">
                    <?php if (!empty($paper['co_authors'])): ?>
                        <?php foreach ($paper['co_authors'] as $coAuthor): ?>
                            <li><?= htmlspecialchars($coAuthor) ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No co-authors found.</li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No papers found or access not permitted.</p>
    <?php endif; ?>
</div>

</body>
</html>
