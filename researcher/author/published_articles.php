<?php
session_start();
if (!isset($_SESSION['author_id'])) {
    header("Location: author_dash_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");

// Fetch papers with DOI and "Published" status using the function from db_connect.php
$result = fetchPublishedPapersWithDOI($conn, $_SESSION['author_id']);

// Check if any papers were found
if ($result->num_rows > 0) {
    $papers = [];
    while ($paper = $result->fetch_assoc()) {
        $papers[$paper['paper_id']][] = $paper;
    }
} else {
    $no_paper_message = "No papers with DOI and 'Published' found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paper Details</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e0eafc, #cfdef3);
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .card h3 {
            margin: 0;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group p {
            margin: 5px 0;
        }
        .no-paper-message {
            text-align: center;
            font-size: 1.5em;
            color: #ff4d4d;
            padding: 20px;
            border-radius: 10px;
            background-color: #ffebeb;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin: 30px auto;
            max-width: 600px;
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
    <a class="btn-back" href="author_dashboard.php">⬅ Back to Author Dashboard</a>

    <?php if (isset($no_paper_message)): ?>
        <div class="no-paper-message">
            <?php echo $no_paper_message; ?>
        </div>
    <?php else: ?>
        <h1>📄 Paper Details</h1>

        <?php foreach ($papers as $paper_id => $paper_group): ?>
            <div class="card">
                <?php
                // Get the first paper in the group
                $paper = $paper_group[0];
                ?>
                <h3>Paper Title: <?php echo htmlspecialchars($paper['title']); ?></h3>

                <div class="form-group">
                    <label>Journal:</label>
                    <p><?php echo htmlspecialchars($paper['journal_name']); ?></p>
                </div>

                <div class="form-group">
                    <label>DOI:</label>
                    <p><?php echo htmlspecialchars($paper['doi']); ?></p>
                </div>

                <div class="form-group">
                    <label>Final Decision:</label>
                    <p><?php echo htmlspecialchars($paper['status']); ?></p>
                </div>

                <div class="form-group">
                    <label>Co-authors:</label>
                    <?php
                    // Display all co-authors for the paper
                    foreach ($paper_group as $co_author) {
                        echo "<p><strong>Name:</strong> " . htmlspecialchars($co_author['co_author_name'] ?? '') . "<br>";
                        echo "<strong>Email:</strong> " . htmlspecialchars($co_author['co_author_email'] ?? '') . "<br>";
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
