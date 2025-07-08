<?php
// Connect to DB
include(__DIR__ . "/../../include/db_connect.php");

// Assume editor ID is retrieved from session (dynamic ID)
$editor_id = $_SESSION['editor_id']; // Ensure editor_id is in the session
$task_type = 1; // Initial Review task type

// Fetch papers assigned to this editor for Initial Review
$result = getEditorTasks($conn, $editor_id, $task_type);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initial Review Tasks</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            font-size: 26px;
            color: #333;
            margin-bottom: 20px;
        }
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }
        .btn {
            padding: 12px 20px;
            font-size: 16px;
            color: #fff;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
            flex: 1 1 auto;
            text-align: center;
        }
        .btn-primary { background-color: #007bff; }
        .btn-secondary { background-color: #6c757d; }
        .btn-success { background-color: #28a745; }
        .btn-danger { background-color: #dc3545; }
        .btn:hover { opacity: 0.85; }

        .task-container {
            background-color: #f8f9fa;
            border: 1px solid #e1e1e1;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .task-container h4 {
            font-size: 18px;
            margin: 0 0 10px;
            color: #333;
        }
        .task-container p {
            font-size: 16px;
            color: #555;
            margin: 0 0 10px;
        }

        @media (max-width: 500px) {
            .btn-group {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Format Review Tasks</h2>

    <!-- Back Button and View History -->
    <div class="btn-group">
        <a href="editor_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        <a href="initial_review_history.php" class="btn btn-secondary">View Previously Reviewed Papers</a>
    </div>

    <?php
    // Check if there are any results to display
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
            // Check if the paper is accepted or rejected
            // Only show if status is NOT accepted or rejected
            if (strtolower($row['status']) != 'accepted' && strtolower($row['status']) != 'rejected'):
                echo '<div style="border:1px solid #ccc; padding: 15px; margin:10px 0;">';
                echo '<h4>Paper Title: ' . htmlspecialchars($row['title']) . '</h4>';
                echo '<p>Journal: ' . htmlspecialchars($row['journal_name']) . '</p>';
    ?>
                <!-- Accept/Reject forms -->
                <form method="POST" action="update_status.php" style="display:inline-block; margin-right:10px;">
                    <input type="hidden" name="paper_id" value="<?php echo $row['paper_id']; ?>">
                    <input type="hidden" name="editor_id" value="<?php echo $editor_id; ?>">
                    <input type="hidden" name="task_type" value="<?php echo $task_type; ?>">
                    <button type="submit" name="decision" value="accept" class="btn btn-success">Accept</button>
                </form>

                <form method="POST" action="update_status.php" style="display:inline-block;">
                    <input type="hidden" name="paper_id" value="<?php echo $row['paper_id']; ?>">
                    <input type="hidden" name="editor_id" value="<?php echo $editor_id; ?>">
                    <input type="hidden" name="task_type" value="<?php echo $task_type; ?>">
                    <button type="submit" name="decision" value="reject" class="btn btn-danger">Reject</button>
                </form>
    </div>
    <?php
            endif;
        endwhile;
    else:
        echo '<p>No tasks available for review.</p>';
    endif;

    $conn->close();
    ?>
</div>
</body>
</html>
