<?php
session_start();
$_SESSION['seen_review_notification'] = true;

if (isset($_GET['filter'])) {
    $_SESSION['seen_' . $_GET['filter']] = true;
}
include(__DIR__ . "/../include/db_connect.php");

// Check if editor is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}
if (isset($_GET['filter'])) {
    $_SESSION['filter_condition'] = "";

    if ($_GET['filter'] === 'Pending') {
        $_SESSION['filter_condition'] = "WHERE status = 'Pending'";
    } elseif ($_GET['filter'] === 'Accepted (Final Decision)') {
        $_SESSION['filter_condition'] = "WHERE status = 'Accepted (Final Decision)'";
    } elseif ($_GET['filter'] === 'Rejected (Post-Review)') {
        $_SESSION['filter_condition'] = "WHERE status = 'Rejected (Post-Review)'";
    }
    header("Location: review_paper.php");
    exit();
}
$filter_condition = $_SESSION['filter_condition'] ?? "";

// Fetch papers using the function from db_connect.php
$result = getPapersForReview($conn, $filter_condition);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Review Papers</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(to right, #f9fbfd, #eef1f5);
      color: #333;
      padding-top: 30px;
    }

    h2 {
      font-weight: 600;
      color: #003366;
    }

    .card {
      border-radius: 20px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
    }

    .badge-status {
      padding: 6px 14px;
      font-size: 0.85rem;
      border-radius: 20px;
      text-transform: capitalize;
    }

    .file-link {
      color: #0056b3;
      font-weight: 500;
      text-decoration: none;
    }

    .file-link:hover {
      text-decoration: underline;
    }

    .btn-custom {
      border-radius: 30px;
      padding: 6px 18px;
    }

    .alert-warning {
      font-size: 0.9rem;
    }

    label.form-label {
      font-weight: 500;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

<div class="header text-center">
    <h2>📚 Research Papers for Review</h2>
</div>
<div class="text-center mb-3">
    <a href="admin_dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
</div>
<div class="text-center mb-3">
    <a href="review_paper.php?filter=all" class="btn btn-outline-secondary">Show All</a>
</div>

<div class="container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card p-4">
                <h4><?= htmlspecialchars($row['title']) ?></h4>
                <p><strong>Abstract:</strong> <?= nl2br(htmlspecialchars($row['abstract'])) ?></p>
                <p><strong>Keywords:</strong> <?= htmlspecialchars($row['keywords']) ?></p>
                <p><strong>Submission Date:</strong> <?= date('d M Y', strtotime($row['submission_date'])) ?></p>
                <p><strong>Status:</strong> 
                    <?php
                        $badgeClass = $row['status'] === 'accepted' ? 'bg-success' :
                                     ($row['status'] === 'rejected' ? 'bg-danger' :
                                     ($row['status'] === 'in review' ? 'bg-warning text-dark' : 'bg-secondary'));
                        echo "<span class='badge $badgeClass badge-status'>" . htmlspecialchars($row['status']) . "</span>";
                    ?>
                </p>
                <div class="row mt-3">
                    <div class="col-md-6"><strong>Cover Letter:</strong> 
                        <?= $row['cover_letter_path'] ? "<a class='file-link' href='/my_publication_site/uploads/" . basename($row['cover_letter_path']) . "' target='_blank'>view</a>" : "N/A" ?>
                    </div>
                    <div class="col-md-6"><strong>Copyright Agreement:</strong> 
                        <?= $row['copyright_agreement_path'] ? "<a class='file-link' href='/my_publication_site/uploads/" . basename($row['copyright_agreement_path']) . "' target='_blank'>View</a>" : "N/A" ?>
                    </div>
                    <div class="col-md-6"><strong>Manuscript:</strong> 
                        <?= $row['file_path'] ? "<a class='file-link' href='/my_publication_site/uploads/" . basename($row['file_path']) . "' target='_blank'>View</a>" : "N/A" ?>
                    </div>
                    <div class="col-md-6 mt-2"><strong>Supplementary Files:</strong> 
                        <?= $row['supplementary_files_path'] ? "<a class='file-link' href='/my_publication_site/uploads/" . basename($row['supplementary_files_path']) . "' target='_blank'>View</a>" : "N/A" ?>
                    </div>
                <hr>
                <!-- Check if payment status is for open access journals -->
                <?php if ($row['payment_status'] === 'Not Paid'): ?>
                    <div class="alert alert-warning mt-3">
                        <strong>Payment Status:</strong> Not Paid. <button class="reminder-btn">Send Reminder</button>
                    </div>
                <?php endif; ?>
                <?php if ($row['status'] === 'Accepted (Final Decision)' && $row['payment_status'] !== 'Not Paid'): ?>
                 
    <?php if (empty($row['doi'])): ?>
        <form method="POST" action="generate_doi.php">
            <input type="hidden" name="paper_id" value="<?= $row['id'] ?>">
            <button type="submit" class="btn btn-sm btn-success mt-2">Generate DOI</button>
        </form>
    <?php else: ?>
        <form method="POST" action="update_doi.php" class="row g-2 mt-2 align-items-end">
            <input type="hidden" name="paper_id" value="<?= $row['id'] ?>">
            <div class="col-md-4">
                <label class="form-label">DOI</label>
                <input type="text" name="doi" class="form-control" value="<?= htmlspecialchars($row['doi']) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Volume</label>
                <input type="text" name="volume" class="form-control" value="<?= htmlspecialchars($row['volume'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Issue</label>
                <input type="text" name="issue" class="form-control" value="<?= htmlspecialchars($row['issue'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Year</label>
                <input type="number" name="year" class="form-control" 
                    value="<?= htmlspecialchars($row['year'] ?? date('Y')) ?>" min="2000" max="<?= date('Y') ?>">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-warning btn-sm w-100">Update</button>
            </div>
        </form>
    <?php endif; ?>
<?php endif; ?>
<?php if (
    $row['status'] === 'Accepted (Final Decision)' &&
    !empty($row['doi']) && !empty($row['volume']) && !empty($row['issue'])
): ?>
    <button class="btn btn-success btn-sm mt-2 px-3 py-1 rounded-pill shadow-sm publish-btn"
            data-paper-id="<?= $row['id'] ?>">
        📢 <span style="margin-left: 4px;">Publish</span>
    </button>
<?php endif; ?>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('.publish-btn').click(function () {
        const paperId = $(this).data('paper-id');
        if (confirm('Are you sure you want to publish this paper?')) {
            $.ajax({
                url: 'publish_paper.php',
                type: 'POST',
                data: { paper_id: paperId },
                success: function (response) {
                    alert('Paper published successfully!');
                    window.location.href = 'published_papers.php';

                },
                error: function () {
                    alert('Failed to publish the paper. Please try again.');
                }
            });
        }
    });
});
</script>
                <p><strong>Editor Comment:</strong> <?= nl2br(htmlspecialchars($row['feedback'] ?? 'No comments yet')) ?></p>
                <p><strong>Final Approval:</strong> <?= htmlspecialchars($row['status'] ?? 'Pending') ?></p>
                <p><strong>Updated At:</strong> <?= htmlspecialchars($row['updated_at']) ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">No papers found for review.</div>
    <?php endif; ?>
</div>
</body>
</html>
