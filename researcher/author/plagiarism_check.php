<?php
session_start();
if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");


$editor_id = $_SESSION['editor_id'];
$task_type = 2;

$result = getPlagiarismTasks($conn, $editor_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Plagiarism Check Tasks</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f7f9fc;
            color: #333;
        }
        h2 {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 20px;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-3px);
        }
        .btn {
            padding: 10px 20px;
            border: none;
            margin: 5px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-primary {
            background: #4e54c8;
            color: white;
        }
        .btn-accept {
            background: #28a745;
            color: white;
        }
        .btn-reject {
            background: #dc3545;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .back-button {
            margin: 10px 0;
        }
    </style>
</head>
<body>

<h2>📄 Plagiarism Check Tasks</h2>

<div class="back-button">
    <a href="editor_dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
</div>

<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()):
        if (strtolower($row['status']) !== 'accepted' && strtolower($row['status']) !== 'rejected'): ?>
        
        <div class="card">
            <h3><?= htmlspecialchars($row['title']) ?></h3>
            <p><strong>Journal:</strong> <?= htmlspecialchars($row['journal_name']) ?></p>
            <div>
                <form method="POST" action="update_plg_status.php" style="display:inline;">
                    <input type="hidden" name="paper_id" value="<?= $row['paper_id']; ?>">
                    <input type="hidden" name="editor_id" value="<?= $editor_id; ?>">
                    <input type="hidden" name="task_type" value="<?= $task_type; ?>">
                    <button type="submit" name="decision" value="accept" class="btn btn-accept">✅ Accept</button>
                </form>

                <form method="POST" action="update_plg_status.php" style="display:inline;">
                    <input type="hidden" name="paper_id" value="<?= $row['paper_id']; ?>">
                    <input type="hidden" name="editor_id" value="<?= $editor_id; ?>">
                    <input type="hidden" name="task_type" value="<?= $task_type; ?>">
                    <button type="submit" name="decision" value="reject" class="btn btn-reject">❌ Reject</button>
                </form>
            </div>
        </div>

    <?php endif; endwhile; ?>
<?php else: ?>
    <p>No tasks available for plagiarism check.</p>
<?php endif; ?>

<?php $conn->close(); ?>

</body>
</html>
