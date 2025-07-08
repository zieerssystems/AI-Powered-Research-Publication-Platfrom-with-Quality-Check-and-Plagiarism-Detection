<?php
session_start();

// Include necessary files
include(__DIR__ . '/../include/db_connect.php'); // Include the function file

// Fetch published papers using the function
$result = getPublishedPapers($conn);
$published_count = $result->num_rows;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Published Papers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2 class="mb-4">✅ Published Papers</h2>

    <?php if (isset($_SESSION['publish_success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['publish_success'] ?></div>
        <?php unset($_SESSION['publish_success']); ?>
    <?php endif; ?>

    <p><strong>Total Published:</strong> <?= $published_count ?></p>
    <div class="text-center mb-3">
        <a href="admin_dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
    </div>

    <?php if ($published_count > 0): ?>
        <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>DOI</th>
                <th>Journal</th>
                <th>Author</th>
                <th>Reviewer</th>
                <th>Editor</th>
                <th>Volume</th>
                <th>Issue</th>
                <th>Completed Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['doi']) ?></td>
                    <td><?= htmlspecialchars($row['journal_name']) ?></td>
                    <td><?= htmlspecialchars($row['author_first_name'] . ' ' . $row['author_last_name']) ?></td>
                    <td><?= htmlspecialchars($row['reviewer_first_name'] . ' ' . $row['reviewer_last_name']) ?></td>
                    <td><?= htmlspecialchars($row['editor_first_name'] . ' ' . $row['editor_last_name']) ?></td>
                    <td><?= htmlspecialchars($row['volume']) ?></td>
                    <td><?= htmlspecialchars($row['issue']) ?></td>
                    <td><?= date('d M Y', strtotime($row['completed_date'])) ?></td> <!-- ✅ Display completed_date -->
                </tr>
            <?php endwhile; ?>
        </tbody>
        </table>
    <?php else: ?>
        <p>No papers published yet.</p>
    <?php endif; ?>
</body>
</html>
