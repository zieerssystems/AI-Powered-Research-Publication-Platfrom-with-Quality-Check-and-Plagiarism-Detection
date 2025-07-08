<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . "/../../include/db_connect.php");

// Check if editor is logged in
// Ensure that only Chief Editors can access the dashboard
if (!isset($_SESSION['chief_editor_id'])) {
    header("Location: editor_login.php");
    exit();
}
$editor_id = $_SESSION['chief_editor_id'];


$editor = getEditorDetails_1($conn, $editor_id);

$total_manuscripts = getTotalManuscripts_1($conn, $editor_id);
$pending_reviews = getPendingReviews_1($conn, $editor_id);
$decisions_made = getDecisionsMade_1($conn, $editor_id);
$new_manuscripts_count = getNewManuscriptsCount_1($conn, $editor_id);
$manuscripts = getManuscripts_2($conn, $editor_id);
$co_authors_list = getCoAuthors_1($conn);
$show_decision_badge = hasDecisionBadge_1($conn);
$new_editors_count = getNewEditorsCount_1($conn, $editor_id);
markEditorsAsOld_1($conn, $editor_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: #002147;
            color: white;
            position: fixed;
            overflow-y: auto;
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #3949ab;
        }

        .content {
            margin-left: 250px;
            height: 100vh;
            overflow-y: auto;
            padding: 20px;
            background-color: #f4f7f9;
        }

        .card {
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .badge {
            font-size: 0.9rem;
        }
        .task-notifications {
  margin-top: 30px;
  background-color: #ffecec;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0 0 5px red;
}

.task-alert {
  background: #fff;
  padding: 10px;
  margin-bottom: 10px;
  border-left: 5px solid red;
  border-radius: 5px;
}

.view-task-btn {
  padding: 8px 16px;
  background-color: #002147;
  color: white;
  text-decoration: none;
  border-radius: 4px;
  display: inline-block;
}

.view-task-btn:hover {
  background-color: #0056b3;
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center">Editor Panel</h4>
     <a href="../../publish.php">🏡 Home</a>
    <a href="chief-dashboard.php">📊 Dashboard</a>
    <a href="manuscripts.php">
        📄 Manuscripts 
        <?php if ($new_manuscripts_count > 0 && empty($_SESSION['manuscripts_visited'])): ?>
            <span class="badge bg-danger"><?php echo $new_manuscripts_count; ?></span>
        <?php endif; ?>
    </a>

    <a href="editors.php">
    👨‍🏫 Editors
    <?php if ($new_editors_count > 0): ?>
        <span class="badge bg-danger"><?php echo $new_editors_count; ?></span>
    <?php endif; ?>
    </a>
    <a href="change_login_details_chief.php">✍ Change login details</a>
    <a href="chief_decision.php">
    ✅ Decisions 
    <?php if ($show_decision_badge): ?>
        <span class="badge bg-danger">!</span>
    <?php endif; ?>
    </a>

    <!-- <a href="review_feedback.php">📜 Review Feedback</a> -->
    <a href="task.php">📋 Task</a>
    <!-- <a href="publication.php">📢 Publication</a> -->
    <a href="chief_analytics.php">📈 Reports & Analytics</a>
    <a href="contact_team_editors.php">📧 Contact Editor/Reviewer</a>
    <a href="chief_account.php">👤 My Account</a>
    <a href="logout.php" class="text-danger">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
   <h2>Welcome <?php echo htmlspecialchars($editor['first_name'] . ' ' . $editor['last_name']); ?></h2>
    <p>Last Login: <?php echo $editor['last_login']; ?></p>

    <!-- Dashboard Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="card p-3">
                <h5>📄 Total Manuscripts</h5>
                <p><strong><?php echo $total_manuscripts; ?></strong> Submissions</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">
                <h5>🔍 Under Reviews</h5>
                <p><strong><?php echo $pending_reviews; ?></strong> Manuscripts</p>
                <a href="manuscripts.php" class="btn btn-sm btn-primary">📜 Manage Reviews</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">
                <h5>✅ Decisions Made</h5>
                <p><strong><?php echo $decisions_made; ?></strong> Accepted / Rejected</p>
            </div>
        </div>
    </div>

    <!-- Manuscripts Table -->
    <h3 class="mt-4">Manuscripts Overview</h3>
    <table class="table table-bordered mt-2">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Submitted By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $manuscripts->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo ($row['status'] == 'Under Review') ? 'warning' : (($row['status'] == 'Accepted') ? 'success' : 'danger'); ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td>
                        <?php 
    $primary_author = $row['primary_author'] ?? ''; 
    echo htmlspecialchars(trim($primary_author)); 
?>

                        <br>
                        <small><strong>Co-Authors:</strong> 
                            <?php echo isset($co_authors_list[$row['id']]) ? implode(', ', $co_authors_list[$row['id']]) : 'None'; ?>
                        </small>
                    </td>
                    <td>
                          <a href="track_manuscript.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">📌 Track</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php

$alertResult = getTaskAlerts_1($conn);
$alertsToShow = [];

if (!isset($_SESSION['shown_alerts']) || !is_array($_SESSION['shown_alerts'])) {
    $_SESSION['shown_alerts'] = [];
}

if ($alertResult && $alertResult->num_rows > 0) {
    while ($alert = $alertResult->fetch_assoc()) {
        if (!in_array($alert['id'], $_SESSION['shown_alerts'])) {
            $alertsToShow[] = $alert;
            $_SESSION['shown_alerts'][] = $alert['id'];
        }
    }
}

?>

<?php if (!empty($alertsToShow)): ?>
  <div class="task-notifications" id="alertContainer">
    <h3 style="color: red;">🔔 Important Task Alerts</h3>
    <?php foreach ($alertsToShow as $alert): ?>
      <div class="task-alert" data-task-id="<?= $alert['id'] ?>">
        <p><strong>Task:</strong> <?= $alert['task_type'] ?> (<?= $alert['paper_title'] ?>)</p>
        <p><strong>Editor:</strong> <?= $alert['first_name'] . ' ' . $alert['last_name'] ?></p>
        <p><strong>Status:</strong>
          <?php if ($alert['status'] == 'Accepted' && $alert['deadline'] < date('Y-m-d')): ?>
            <span style="color: orange;">⚠️ Accepted (Overdue)</span>
          <?php elseif ($alert['status'] == 'Rejected'): ?>
            <span style="color: red;">❌ Rejected</span>
          <?php elseif ($alert['status'] == 'Pending' && $alert['deadline'] < date('Y-m-d')): ?>
            <span style="color: red;">⏳ Pending (Overdue)</span>
          <?php elseif ($alert['status'] == 'Completed'): ?>
            <span style="color: green;">✅ Completed</span>
          <?php endif; ?>
        </p>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Always show this button -->
<div style="margin-top: 20px;">
  <a href="task_monitor.php" class="view-task-btn">🔍 View in Task Monitor</a>
</div>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const alerts = document.querySelectorAll(".task-alert");
    let allSeen = true;

    alerts.forEach(alert => {
      const taskId = alert.getAttribute("data-task-id");
      if (localStorage.getItem("alert_seen_" + taskId)) {
        alert.remove(); // Remove seen alert
      } else {
        localStorage.setItem("alert_seen_" + taskId, "1");
        allSeen = false; // At least one alert is new
      }
    });

    // If all alerts were already seen, remove the entire alert container
    if (allSeen && document.getElementById("alertContainer")) {
      document.getElementById("alertContainer").remove();
    }
  });
</script>
</body>
</html>
