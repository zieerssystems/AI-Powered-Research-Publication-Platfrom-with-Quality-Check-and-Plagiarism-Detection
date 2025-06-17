<?php
session_start();
if (!isset($_SESSION['author_id'])) {
    header("Location: author_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");

$author_id = $_SESSION['author_id']; // Assuming you store author_id in the session

// Fetch papers for the given author with feedback, editorial team info, and journal details
$result = fetchAuthorPapersWithFeedback($conn, $author_id);
$papers = $result['papers'];
$has_comments = $result['has_comments'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .content {
            width: 90%;
            max-width: 900px;
            padding: 30px;
            background: #ffffff;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h2 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        .paper-card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }
        .paper-card:hover {
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
        }
        .paper-card h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .paper-card p {
            font-size: 16px;
            color: #444;
            margin: 5px 0;
        }
        .status {
            font-weight: bold;
            font-size: 16px;
            color: #007bff;
        }
        .no-comments {
            background-color: #f0ad4e;
            color: white;
            padding: 12px;
            text-align: center;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 10px;
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

<div class="content">
    <h2>📑 Author Dashboard - Accepted Papers</h2>
    <a class="btn-back" href="author_dashboard.php">⬅ Back to Dashboard</a>

    <?php if (!$has_comments): ?>
        <div class="no-comments">
            No feedback available yet.
        </div>
    <?php endif; ?>

    <?php foreach ($papers as $paper): ?>
        <?php if (trim($paper['feedback']) !== ''): ?>
            <div class="paper-card">
                <h3><?= htmlspecialchars($paper['title']) ?></h3>
                <p><strong>Journal:</strong> <?= htmlspecialchars($paper['journal_name']) ?></p>
                <p class="status">Status: <?= htmlspecialchars($paper['status']) ?></p>
                <p><strong>Editorial Team:</strong> <?= htmlspecialchars($paper['editorial_team_name']) ?></p>

                <div class="comment-section">
                    <p><strong>Feedback:</strong></p>
                    <p><?= nl2br(htmlspecialchars($paper['feedback'])) ?></p>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

</body>
</html>
