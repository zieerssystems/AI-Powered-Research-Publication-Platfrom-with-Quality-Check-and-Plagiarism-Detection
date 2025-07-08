<?php
session_start();
if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");

$reviewer_id = $_SESSION['reviewer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paper_id = intval($_POST['paper_id']);
    $action = $_POST['action'];

    if ($action === 'accept') {
        acceptReviewTask($conn, $paper_id, $reviewer_id);
    } elseif ($action === 'reject') {
        rejectReviewTask($conn, $paper_id, $reviewer_id);
    }
    header("Location: reviewer_dashboard.php");
    exit();
}

// Fetch data
$manuscripts = getAssignedManuscripts($conn, $reviewer_id);
$co_authors_list = getCoAuthors($conn, $reviewer_id);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pending Reviews</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function showAbstract(abstract) {
            document.getElementById("abstractModalBody").innerText = abstract;
            new bootstrap.Modal(document.getElementById("abstractModal")).show();
        }
    </script>
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

<div class="container mt-4">
    <a href="reviewer_dashboard.php" class="btn-back mb-3">⬅ Back to  Reviewer Dashboard</a>
    <h2>Pending Reviews</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Manuscript Title</th>
                <th>Submission Date</th>
                <th>Review Deadline</th>
                <th>Review Status</th>
                <th>Primary Author</th>
                <th>Co-Authors</th>
                <th>Journal</th>
                <th>Abstract</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $manuscripts->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['submission_date']) ?></td>
                    <td><?= htmlspecialchars($row['review_deadline'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['review_status']) ?></td>
                    <td><?= htmlspecialchars(trim($row['primary_author'])) ?></td>
                    <td><?= isset($co_authors_list[$row['id']]) ? htmlspecialchars(implode(", ", $co_authors_list[$row['id']])) : "N/A" ?></td>
                    <td><?= htmlspecialchars($row['journal_name']) ?></td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="showAbstract(`<?= htmlspecialchars($row['abstract']) ?>`)">📜 View Abstract</button>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="paper_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">✔ Accept</button>
                        </form>
                        <form method="POST" style="display:inline;" onsubmit="return confirmReject();">
    <input type="hidden" name="paper_id" value="<?= $row['id'] ?>">
    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">✖ Reject</button>
</form>

                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Abstract Modal -->
<div class="modal fade" id="abstractModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manuscript Abstract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="abstractModalBody"></div>
        </div>
    </div>
</div>
<script>
    function confirmReject() {
        return confirm("Are you not taking this task?");
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
