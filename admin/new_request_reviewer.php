<?php
include("../include/db_connect.php");

// Fetch journal requests
$result = fetchNewReviewerRequests($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewer Requests</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f4f7f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header {
           background: #002147;
            color: white;
            padding: 25px 30px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            font-weight: 600;
        }
        .table thead {
            background-color: #f0f0f0;
        }
        .btn-success, .btn-danger {
            width: 80px;
        }
        .card {
            background-color: white;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-radius: 12px;
            padding: 20px;
        }
        .back-btn {
            margin: 20px 30px 0;
        }
    </style>
</head>
<body>


<!-- Header Section -->
<div class="header">
    <h2>📝 Reviewer Journal Requests</h2>
</div>
<!-- Back Button -->
<div class="back-btn">
    <a href="admin_dashboard.php" class="btn btn-outline-secondary">⬅ Back to Dashboard</a>
</div>

<!-- Main Content -->
<div class="container">
    <div class="card">
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead>
                        <tr>
                            <th>Reviewer</th>
                            <th>Journal</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['journal_name']) ?></td>
                                <td>
                                    <?php
                                        $badgeClass = $row['status'] === 'accepted' ? 'bg-success' :
                                                     ($row['status'] === 'rejected' ? 'bg-danger' : 'bg-warning text-dark');
                                        echo "<span class='badge $badgeClass'>" . htmlspecialchars($row['status']) . "</span>";
                                    ?>
                                </td>
                                <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <?php if ($row['status'] !== 'accepted'): ?>
                                        <a href="handle_request.php?action=accept&id=<?= $row['id']; ?>" class="btn btn-success btn-sm">Accept</a>
                                    <?php endif; ?>
                                    <?php if ($row['status'] !== 'rejected'): ?>
                                        <a href="handle_request.php?action=reject&id=<?= $row['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No reviewer journal requests found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
