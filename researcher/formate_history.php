<?php 
include(__DIR__ . "/../../include/db_connect.php");

$editor_id = $_SESSION['editor_id'];
$task_type = 4;

$status_filter = isset($_GET['filter']) ? $_GET['filter'] : 'Accepted';

// Fetch tasks using the function from db_connect.php
$result = getEditorTasks_1($conn, $editor_id, $task_type, $status_filter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review History</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 30px;
        }
        .filter-buttons {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-info {
            background-color: #17a2b8;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .paper-card {
            padding: 20px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }
        .paper-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .paper-card h4 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        .paper-card p {
            font-size: 16px;
            color: #555;
        }
        .paper-card .status {
            font-weight: bold;
        }
        .highlight {
            border: 2px solid red;
            background-color: #ffe6e6;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Review History (Accepted / Rejected)</h2>

    <!-- Navigation and Filter Buttons -->
    <div class="filter-buttons">
        <a href="editor_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        <a href="?filter=Accepted" class="btn btn-success">Accepted</a>
        <a href="?filter=Rejected" class="btn btn-danger">Rejected</a>
    </div>

    <?php
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
            // Highlight accepted papers without a result
            $highlightClass = ($row['status'] == 'Accepted' && empty($row['result'])) ? 'highlight' : '';
    ?>
        <div class="paper-card <?= $highlightClass ?>">
            <h4><?= htmlspecialchars($row['title']) ?></h4>
            <p>Journal: <?= htmlspecialchars($row['journal_name']) ?></p>
            <p>Status: <span class="status"><?= htmlspecialchars($row['status']) ?></span></p>
            
            <!-- Only show "View Details" for accepted papers -->
            <?php if (strtolower($row['status']) == 'accepted'): ?>
                <a href="formate.php?paper_id=<?= $row['paper_id'] ?>" class="btn btn-info">View Details</a>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
    <?php else: ?>
        <p>No previously reviewed tasks.</p>
    <?php endif; ?>

</div>

</body>
</html>