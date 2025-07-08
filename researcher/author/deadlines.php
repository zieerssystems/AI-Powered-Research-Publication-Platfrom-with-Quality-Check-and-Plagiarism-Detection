<?php
session_start();
if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");

$reviewer_id = $_SESSION['reviewer_id'];
$today = date("Y-m-d");
$two_days_before = date("Y-m-d", strtotime("-2 days"));
$one_day_before = date("Y-m-d", strtotime("-1 days"));

// Fetch only INCOMPLETE papers with deadlines
$sql = "SELECT p.id, p.title, pa.deadline, pa.status
        FROM paper_assignments pa
        JOIN papers p ON pa.paper_id = p.id
        WHERE pa.reviewer_id = ? AND pa.status != 'Completed'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reviewer_id);
$stmt->execute();
$deadlines = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Deadlines & Reminders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f9; }
        .content { padding: 20px; }
        .card { box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); border-radius: 8px; padding: 15px; margin-bottom: 10px; }
        .green { background: #d4edda; } /* More than 3 days left */
        .yellow { background: #fff3cd; } /* 2 days left */
        .dark-yellow { background: #ffecb5; } /* 1 day left */
        .red { background: #f8d7da; } /* Overdue */
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
    <a href="reviewer_dashboard.php" class="btn-back">← Back to Reviewer Dashboard</a>
<div class="content">
    <h2>📅 Deadlines & Reminders</h2>

    <?php if ($deadlines->num_rows === 0): ?>
        <p class="alert alert-info"> No pending deadlines!</p>
    <?php else: ?>
        <?php while ($row = $deadlines->fetch_assoc()): 
            $deadline = $row['deadline'];
            $paper_id = $row['id'];
            $title = htmlspecialchars($row['title']);

            // Determine notification color
            if ($deadline > date("Y-m-d", strtotime("+3 days"))) {
                $color = "green";
            } elseif ($deadline == $two_days_before) {
                $color = "yellow";
            } elseif ($deadline == $one_day_before) {
                $color = "dark-yellow";
            } elseif ($deadline < $today) {
                $color = "red";
            } else {
                $color = "green";
            }
        ?>
            <div class="card <?= $color ?>">
                <h5>📑 <?= $title ?></h5>
                <p><strong>Deadline:</strong> <?= $deadline ?></p>
                <a href="view_manuscripts.php?paper_id=<?= $paper_id ?>" class="btn btn-primary btn-sm">✔ Verify Paper</a>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
</body>
</html>
