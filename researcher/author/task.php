<?php
session_start();
if (!isset($_SESSION['chief_editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");


$loggedInEditorId = $_SESSION['chief_editor_id'] ?? null;
if (!$loggedInEditorId) die("Unauthorized access");

// Optional alert message
if (isset($_SESSION['popup_message'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            alert('" . addslashes($_SESSION['popup_message']) . "');
        });
    </script>";
    unset($_SESSION['popup_message']);
}

// Get data using functions
$teamId = getTeamIdForChiefEditor($conn, $loggedInEditorId);
if (!$teamId) die("Team not found for the logged-in chief editor.");

$editorResult = getTeamEditors($conn, $teamId, $loggedInEditorId);
$paperResult = getPapersByChiefEditor($conn, $loggedInEditorId);
$assignedTasks = getAllAssignedTasks($conn);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Task Assignment Panel</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f4f4f4; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: left; }
        th { background: #2c3e50; color: #fff; }
        .assign-form { display: flex; gap: 10px; flex-wrap: wrap; }
        .assigned-info { font-size: 0.9em; margin-top: 5px; color: #444; }
        .btn-back {
            display: inline-block; margin: 20px 0;
            background: linear-gradient(135deg, #4b6cb7, #182848);
            color: white; padding: 10px 20px; border-radius: 25px;
            text-decoration: none; font-weight: bold;
        }
        .disabled-form { opacity: 0.5; pointer-events: none; }
        .disabled-row { background-color: #ffe6e6; }
    </style>
</head>
<body>
<a href="chief-dashboard.php" class="btn-back">← Back to Dashboard</a>
<h2>📋 Assign Tasks to Editors (Team Members Only)</h2>
<!-- Add History Button on Top -->
<button onclick="window.location.href='history.php'">History</button>

<table>
    <tr>
        <th>Paper Title</th>
        <th>Author</th>
        <th>Assign Task</th>
        <th>Current Assignments</th>
    </tr>
    <?php while ($paper = mysqli_fetch_assoc($paperResult)) { ?>
<tr>
    <td>
        <a href="manuscripts.php?paper_id=<?= $paper['paper_id'] ?>">
            <?= htmlspecialchars($paper['paper_title']) ?>
        </a><br>
        <small>Status: <?= htmlspecialchars($paper['status']) ?></small>
    </td>
    <td><?= htmlspecialchars($paper['author_first'] . ' ' . $paper['author_last']) ?></td>
    <td>
        <!-- Only display task assignment form if the paper is not rejected or accepted -->
        <?php if (!in_array($paper['status'], ['Rejected (Pre-Review)', 'Rejected (Post-Review)', 'Accepted (Final Decision)'])): ?>
            <form class="assign-form" action="assign_task_submit.php" method="POST">
                <input type="hidden" name="paper_id" value="<?= $paper['paper_id'] ?>">

                <select name="task_type" required>
                    <option value="">-- Select Task --</option>
                    <option value="1">🔍 Task 1 - Initial Review</option>
                   <?php if (hasPreviousTaskBeenProcessed($conn, $paper['paper_id'], 2)): ?>
                        <option value="2">🔬 Task 2 - Plagiarism Check</option>
                    <?php endif; ?>
                    <?php if (hasPreviousTaskBeenProcessed($conn, $paper['paper_id'], 3)): ?>

                        <option value="3">📝 Task 3 - Assign Reviewer</option>
                    <?php endif; ?>
                    <?php if (hasPreviousTaskBeenProcessed($conn, $paper['paper_id'], 4)): ?>

                        <option value="4">🛠️ Task 4 - Format Check</option>
                    <?php endif; ?>
                </select>

                <select name="editor_id" required>
                    <option value="">-- Select Editor --</option>
                    <?php 
                    mysqli_data_seek($editorResult, 0);
                    while ($editor = mysqli_fetch_assoc($editorResult)) { ?>
                        <option value="<?= $editor['editor_id'] ?>">
                         <?= htmlspecialchars($editor['editor_name']) ?>
                         </option>

                    <?php } ?>
                </select>
                <input type="date" name="deadline" required>
                <button type="submit">Assign</button>
            </form>
        <?php else: ?>
            <span>Paper status is final. No task assignment possible.</span>
        <?php endif; ?>
    </td>
    <td>
        <?php
        if (isset($assignedTasks[$paper['paper_id']])) {
            foreach ($assignedTasks[$paper['paper_id']] as $task) {
                echo "<div class='assigned-info'>
                        🧑 <a href='editors.php?id={$task['editor_id']}' target='_blank'>" . 
                        htmlspecialchars($task['first_name'] . ' ' . $task['last_name']) . "</a> <br>
                        🔢 Task Type: {$task['task_type']} <br>
                        📅 Deadline: {$task['deadline']}
                        <form method='POST' action='delete_task.php' onsubmit='return confirm(\"Are you sure?\");' style='margin-top:5px;'> 
                            <input type='hidden' name='paper_id' value='{$paper['paper_id']}'>
                            <input type='hidden' name='task_type' value='{$task['task_type']}'>
                            <input type='hidden' name='editor_id' value='{$task['editor_id']}'>
                            <button type='submit' style='color:red; background:none; border:none; cursor:pointer;'>❌ Delete</button>
                        </form>
                      </div><hr>";
            }
        } else {
            echo "❌ Not Assigned Yet";
        }
        ?>
    </td>
</tr>
<?php } ?>
</table>

</body>
</html>
