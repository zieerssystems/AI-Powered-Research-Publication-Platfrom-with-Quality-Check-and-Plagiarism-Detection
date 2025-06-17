<?php
// Connect to DB
include(__DIR__ . "/../../include/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get values from the form submission
    $paper_id = $_POST['paper_id'];
    $editor_id = $_POST['editor_id'];
    $task_type = $_POST['task_type'];
    $decision = $_POST['decision'];

    if ($decision == 'accept') {
        // Update status to Accepted using function
        if (acceptEditorTask($conn, $paper_id, $editor_id, $task_type)) {
            // Redirect to the editor dashboard to avoid resubmitting
            header("Location: formate.php?paper_id=" . $paper_id);
            exit();
        } else {
            // Handle error (optional: display error message)
            echo "Error updating task to Accepted.";
        }
    } elseif ($decision == 'reject') {
        // Update status to Rejected using function
        if (rejectEditorTask($conn, $paper_id, $editor_id, $task_type)) {
            // Redirect back to the page without showing paper details
            header("Location: formate_accp_rej.php");
            exit();
        } else {
            // Handle error (optional: display error message)
            echo "Error updating task to Rejected.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paper Decision</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
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
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .btn {
            padding: 12px 30px;
            font-size: 16px;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            width: 48%;
            cursor: pointer;
        }
        .btn-accept {
            background-color: #28a745;
        }
        .btn-reject {
            background-color: #dc3545;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #e1e1e1;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .info-box p {
            margin: 0;
            font-size: 16px;
            color: #555;
        }
        .info-box strong {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Decision on Paper</h2>

    <div class="info-box">
        <p><strong>Paper ID:</strong> <?= htmlspecialchars($paper_id) ?></p>
        <p><strong>Task Type:</strong> <?= htmlspecialchars($task_type) ?></p>
    </div>

    <!-- Decision buttons -->
    <div class="btn-group">
        <form action="" method="POST">
            <input type="hidden" name="paper_id" value="<?= htmlspecialchars($paper_id) ?>">
            <input type="hidden" name="editor_id" value="<?= htmlspecialchars($editor_id) ?>">
            <input type="hidden" name="task_type" value="<?= htmlspecialchars($task_type) ?>">

            <button type="submit" name="decision" value="accept" class="btn btn-accept">Accept</button>
            <button type="submit" name="decision" value="reject" class="btn btn-reject">Reject</button>
        </form>
    </div>

</div>

</body>
</html>