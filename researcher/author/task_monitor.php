<?php  
session_start();
include('../../include/db_connect.php');

$hasAcceptedOrPublished = hasAcceptedOrPublishedPapers($conn);
$result = getCompletedEditorTasks($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Task Monitor</title>
  <style>
    body { font-family: Arial; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
    th { background: linear-gradient(to right, #4e54c8, #8f94fb); color: white; }
    .overdue { background-color: #ffdddd; color: red; }
    .completed { background-color: #d4edda; color: green; }
    .pending { background-color: #fff3cd; }
    .processed { background-color: #cce5ff; color: blue; }
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

    .btn { padding: 5px 10px; border: none; background-color: #007bff; color: white; cursor: pointer; border-radius: 4px; }
  </style>
  <meta http-equiv="refresh" content="30">
</head>
<body>
<a href="chief-dashboard.php" class="btn-back">← Back to Dashboard</a>
<h2>📋 Assigned Task Monitor - Chief Editor</h2>

<?php if (!$hasAcceptedOrPublished): ?>
<table>
  <tr>
    <th>Editor</th>
    <th>Paper</th>
    <th>Task</th>
    <th>Status</th>
    <th>Deadline</th>
    <th>Response Date</th>
    <th>Action</th>
  </tr>

<?php
while ($row = $result->fetch_assoc()):
    $today = date('Y-m-d');
    $deadline = $row['deadline'];
    $status = $row['status'];
    $task_result = $row['result'];
    $task_type = (int)$row['task_type'];
    $paper_id = $row['paper_id'];
    $row_class = ($status == 'Completed') ? 'completed' : (($status == 'Pending' && $deadline < $today) ? 'overdue' : 'pending');

    // Check if next task exists
    $nextTaskType = $task_type + 1;
    $nextTaskExistsQuery = "
        SELECT 1 FROM editor_tasks 
        WHERE paper_id = $paper_id AND task_type = $nextTaskType LIMIT 1
    ";
    $nextTaskResult = $conn->query($nextTaskExistsQuery);
    $nextTaskAssigned = ($nextTaskResult && $nextTaskResult->num_rows > 0);
?>
  <tr class="<?= $row_class ?>">
    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
    <td><?= htmlspecialchars($row['paper_title']) ?></td>
    <td><?= htmlspecialchars($task_type) ?></td>
    <td><?= htmlspecialchars($status) ?></td>
    <td><?= htmlspecialchars($deadline) ?></td>
    <td><?= $row['response_date'] ?? 'N/A' ?></td>
    <td>
      <?php if ($task_result == 'Processed for Next Level' && !$nextTaskAssigned): ?>
        <span style="color: blue;">📈 Processed for Next Level</span>
        <a href="task.php?task_id=<?= $row['id'] ?>" class="btn">Go to Task</a>
      <?php elseif ($task_result == 'Not Processed'): ?>
        <span style="color: blue;">Not Processed</span>
        <a href="task.php?task_id=<?= $row['id'] ?>" class="btn">Reassign</a>

        <?php if ($status == 'Completed'): ?>
          <form method="post" action="reject_task.php">
            <input type="hidden" name="paper_id" value="<?= $paper_id ?>">
            <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
            <textarea name="comment" rows="2" cols="30" placeholder="Enter rejection comment" required></textarea><br>
            <button type="submit" class="btn">Reject Task</button>
          </form>
        <?php endif; ?>

      <?php elseif ($task_result == 'Revision Request'): ?>
        <span style="color: orange;">🔄 Revision Requested</span>
      <?php elseif ($status != 'Completed' && $deadline < $today): ?>
        <?php if ($row['reminder_sent']): ?>
          <span style="color: green;">✅ Reminder Sent</span>
        <?php else: ?>
          <form method="post" action="send_reminder.php">
            <input type="hidden" name="editor_id" value="<?= $row['editor_id'] ?>">
            <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
            <button type="submit" class="btn">⚠️ Send Reminder</button>
          </form>
        <?php endif; ?>
      <?php else: ?>
        -
      <?php endif; ?>

      <?php if (!empty($row['feedback'])): ?>
        <p><strong>Feedback:</strong> <?= htmlspecialchars($row['feedback']) ?></p>
      <?php endif; ?>
    </td>
  </tr>
<?php endwhile; ?>
</table>
<?php else: ?>
  <p><strong>Note:</strong> The task table is hidden because there are papers with status "Accepted (Final Decision)" or "Published".</p>
<?php endif; ?>

</body>
</html>
