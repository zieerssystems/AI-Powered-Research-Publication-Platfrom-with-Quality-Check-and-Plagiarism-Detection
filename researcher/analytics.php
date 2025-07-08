<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

// Check if editor is logged in
if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

// Fetch statistics
$total_manuscripts = fetchTotalManuscripts($conn);
$pending_reviews = fetchPendingReviews($conn);
$accepted_papers = fetchAcceptedPapers($conn);
$rejected_papers = fetchRejectedPapers($conn);

$total_reviewers = fetchTotalReviewers($conn);
$active_reviewers = fetchActiveReviewers($conn);

$completed_reviews = fetchCompletedReviews($conn);
$top_authors = fetchTopAuthors($conn);

$journal_data = fetchJournalData($conn);

$journals = [];
$accepted_counts = [];
$rejected_counts = [];
while ($row = $journal_data->fetch_assoc()) {
    $journals[] = $row['journal_name'];
    $accepted_counts[] = $row['accepted'];
    $rejected_counts[] = $row['rejected'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f9;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #2c3e50;
            color: white;
            position: fixed;
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
            background: #1abc9c;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center">Editor Panel</h4>
    <a href="editor_dashboard.php">📊 Dashboard</a>
    <a href="publication.php">📢 Publication</a>
    <a href="analytics.php">📈 Reports & Analytics</a>
    <a href="my_account.php">👤 My Account</a>
    <a href="logout.php" class="text-danger">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2>📈 Reports & Analytics</h2>
    <p>Key performance insights for manuscripts, reviewers, and journals.</p>

    <!-- Dashboard Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card p-3">
                <h5>📄 Total Manuscripts</h5>
                <p><strong><?php echo $total_manuscripts; ?></strong> Submissions</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h5>🔍 Pending Reviews</h5>
                <p><strong><?php echo $pending_reviews; ?></strong> Manuscripts</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h5>✅ Accepted</h5>
                <p><strong><?php echo $accepted_papers; ?></strong> Papers</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h5>❌ Rejected</h5>
                <p><strong><?php echo $rejected_papers; ?></strong> Papers</p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <h3 class="mt-4">📊 Journal-wise Acceptance & Rejection</h3>
    <canvas id="journalChart"></canvas>

    <h3 class="mt-4">🏆 Top 5 Authors</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Author Name</th>
                <th>Total Papers</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $top_authors->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                    <td><?php echo $row['total_papers']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
    const ctx = document.getElementById('journalChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($journals); ?>,
            datasets: [
                {
                    label: 'Accepted Papers',
                    data: <?php echo json_encode($accepted_counts); ?>,
                    backgroundColor: 'green'
                },
                {
                    label: 'Rejected Papers',
                    data: <?php echo json_encode($rejected_counts); ?>,
                    backgroundColor: 'red'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

</body>
</html>
