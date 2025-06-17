<?php
session_start();
if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");

$reviewer_id = $_SESSION['reviewer_id'];

// Fetch manuscripts assigned to the reviewer that are 'In Review' and have paper status 'Under Review'
$result = getAssignedManuscriptsForReviewer($conn, $reviewer_id);

if (!$result) {
    die("Query execution failed: " . $stmt->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paper_id = $_POST['paper_id'];
    $status = $_POST['status'];

    $success = updateReviewerPaperStatus($conn, $paper_id, $reviewer_id, $status);

    if ($success) {
        header("Location: submit_review.php?paper_id=$paper_id&status=$status");
        exit();
    } else {
        die("Failed to update paper status.");
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manuscripts for Review</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>.btn-back {
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
    }</style>
<body>

<div class="container mt-4">
    <a href="reviewer_dashboard.php" class="btn-back mb-3">⬅ Back to Dashboard</a>
    <h2>Manuscripts Assigned to You</h2>

    <?php if ($result->num_rows === 0): ?>
        <p class="alert alert-info">No manuscripts found for review.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Journal</th>
                    <th>Cover Letter</th>
                    <th>Supplementary Files</th>
                    <th>Manuscript</th>
                    <th>Keywords</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['journal_name']) ?></td>
                        <td>
                            <?php if (!empty($row['file_path'])): ?>
                                <a class="file-link" href="../../uploads/<?php echo basename($row['file_path']); ?>" target="_blank" class="btn btn-primary btn-sm">📖 Read</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['supplementary_files_path'])): ?>
                                <a class="file-link" href="../../uploads/<?php echo basename($row['supplementary_files_path']); ?>" target="_blank" class="btn btn-info btn-sm">📂 View</a>

                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['cover_letter_path'])): ?>
                                <a class="file-link" href="../../uploads/<?php echo basename($row['cover_letter_path']); ?>" target="_blank" class="btn btn-info btn-sm">📄 View</a>

                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($row['keywords']) ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="paper_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="status" value="Accepted" class="btn btn-success btn-sm">✔ Accept</button>
                                <button type="submit" name="status" value="Rejected" class="btn btn-danger btn-sm">✖ Reject</button>
                                <button type="submit" name="status" value="Revision Requested" class="btn btn-warning btn-sm">🔄 Request Revision</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
