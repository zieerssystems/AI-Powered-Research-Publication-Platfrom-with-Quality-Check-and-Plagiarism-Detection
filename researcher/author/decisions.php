<?php  
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$editor_id = $_SESSION['editor_id'];

// Count only for task_type = 3 and current editor
$processed_count = $conn->query("
    SELECT COUNT(*) AS count 
    FROM editor_tasks 
    WHERE result = 'Processed for Next Level' 
    AND task_type = 3 
    AND editor_id = $editor_id
")->fetch_assoc()['count'];

$not_processed_count = $conn->query("
    SELECT COUNT(*) AS count 
    FROM editor_tasks 
    WHERE result = 'Not Processed' 
    AND task_type = 3 
    AND editor_id = $editor_id
")->fetch_assoc()['count'];

// Fetch all papers with completed assignment, and optional result from editor_tasks
$query = "
    SELECT 
        p.id AS paper_id,
        p.title,
        j.id AS journal_id,
        CONCAT(au.first_name, ' ', COALESCE(au.middle_name, ''), ' ', au.last_name) AS author_name,
        GROUP_CONCAT(CONCAT('Reviewer ', ru.first_name, ' ', ru.last_name, ': ', f.feedback) SEPARATOR '\n\n') AS all_feedbacks,
        (
            SELECT result 
            FROM editor_tasks et 
            WHERE et.paper_id = p.id 
              AND et.task_type = 3 
              AND et.editor_id = $editor_id 
              AND et.status = 'Completed' 
            ORDER BY et.id DESC 
            LIMIT 1
        ) AS editor_result
    FROM paper_assignments pa
    INNER JOIN papers p ON pa.paper_id = p.id
    INNER JOIN journals j ON p.journal_id = j.id
    INNER JOIN feedback f ON f.paper_id = p.id
    INNER JOIN reviewers rev ON f.reviewer_id = rev.id
    INNER JOIN users ru ON rev.user_id = ru.id    -- reviewer user info
    INNER JOIN author a ON f.author_id = a.id
    INNER JOIN users au ON a.user_id = au.id      -- author user info
    WHERE pa.status = 'Completed'
    GROUP BY p.id
";


$papers = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editor Review Decisions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>
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
<body>
    <a href="editor_dashboard.php" class="btn-back">← Back to Editor Dashboard</a>
<div class="container mt-4">
    <h2>🧾 Editor Review Decisions</h2>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card p-3 bg-success text-white">
                <h5>✅ Processed for Next Level</h5>
                <p><strong><?php echo $processed_count; ?></strong> Papers</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 bg-danger text-white">
                <h5>❌ Not Processed</h5>
                <p><strong><?php echo $not_processed_count; ?></strong> Papers</p>
            </div>
        </div>
    </div>

    <h3>📑 Pending Editorial Actions</h3>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Feedback</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $papers->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['paper_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                    <td><pre><?php echo htmlspecialchars($row['all_feedbacks']); ?></pre></td>
                    <td>
                        <?php if ($row['editor_result']) { ?>
                            <span class="badge bg-<?php echo $row['editor_result'] == 'Processed for Next Level' ? 'success' : 'danger'; ?>">
                                <?php echo $row['editor_result']; ?>
                            </span>
                        <?php } else { ?>
                            <form action="update_editor_task.php" method="POST" class="d-inline">
                                <input type="hidden" name="paper_id" value="<?php echo $row['paper_id']; ?>">
                                <input type="hidden" name="result" value="Processed for Next Level">
                                <button type="submit" class="btn btn-sm btn-success">✔ Processed</button>
                            </form>

                            <form action="update_editor_task.php" method="POST" class="d-inline">
                                <input type="hidden" name="paper_id" value="<?php echo $row['paper_id']; ?>">
                                <input type="hidden" name="result" value="Not Processed">
                                <button type="submit" class="btn btn-sm btn-danger">❌ Not Processed</button>
                            </form>
                            <a href="reviewers.php?paper_id=<?php echo $row['paper_id']; ?>&journal_id=<?php echo $row['journal_id']; ?>" class="btn btn-sm btn-info">🔁 Reassign</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
