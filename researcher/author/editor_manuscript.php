<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}
$editor_id = $_SESSION['editor_id'];

$selected_journal = $_GET['journal_name'] ?? '';
$selected_status = $_GET['status'] ?? '';
$selected_reviewer = $_GET['reviewer_assigned'] ?? '';
$paper_id = $_GET['paper_id'] ?? '';

$journals_result = getDistinctJournals($conn);
$manuscripts = getManuscripts_1($conn, $editor_id, $paper_id, $selected_journal, $selected_status, $selected_reviewer);
$pending_count = getPendingCount($conn, $editor_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manuscripts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f9;
        }
        .content {
            padding: 20px;
        }
        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }
        .file-link {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .badge-notify {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border-radius: 50%;
            font-size: 14px;
        }
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
    <a href="editor_dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
    <div class="container mt-4">
        <h2>📄 Manuscripts Overview 
            <?php if ($pending_count > 0): ?>
                <span class="badge-notify"><?php echo $pending_count; ?> New</span>
            <?php endif; ?>
        </h2>
        <form method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label for="journal_name" class="form-label">Filter by Journal</label>
                <select name="journal_name" id="journal_name" class="form-control">
                    <option value="">All Journals</option>
                    <?php while ($row = $journals_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['journal_name']; ?>" 
                            <?php echo ($selected_journal == $row['journal_name']) ? 'selected' : ''; ?>>
                            <?php echo $row['journal_name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="reviewer_assigned" class="form-label">Reviewer Assigned</label>
                <select name="reviewer_assigned" id="reviewer_assigned" class="form-control">
                    <option value="">All</option>
                    <option value="Assigned" <?php echo ($selected_reviewer == "Assigned") ? 'selected' : ''; ?>>Assigned</option>
                    <option value="Not Assigned" <?php echo ($selected_reviewer == "Not Assigned") ? 'selected' : ''; ?>>Not Assigned</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">All Statuses</option>
                    <?php
                    $statuses = ['Pending', 'Under Review', 'Rejected (Pre-Review)', 'Rejected (Post-Review)', 
                                 'Revision Requested', 'Revised Submitted', 'Reinstated for Review', 
                                 'Accepted with Revisions', 'Accepted (Final Decision)'];
                    foreach ($statuses as $status) {
                        echo '<option value="' . $status . '" ' . ($selected_status == $status ? 'selected' : '') . '>' . $status . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">🔍 Apply Filters</button>
            </div>
        </form>
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Journal Name</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Abstract</th>
                        <th>Submission Date</th>
                        <th>Status</th>
                        <th>Reviewer Assigned</th>
                        <th>Manuscript</th>
                        <th>Supplementary Files</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $manuscripts->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['journal_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($row['abstract'], 0, 100)) . "..."; ?></td>
                            <td><?php echo $row['submission_date']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo ($row['status'] == 'Under Review') ? 'warning' : (($row['status'] == 'Accepted (Final Decision)') ? 'success' : 'danger'); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td><?php echo $row['reviewer_name'] ? "✅ Assigned to: " . $row['reviewer_name']: "❌ Not Assigned"; ?></td>
                            <td>
                                <?php if (!empty($row['file_path'])): ?>
                                    <a class="file-link" href="../../uploads/<?php echo basename($row['file_path']); ?>" target="_blank">View</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row['supplementary_files_path'])): ?>
                                    <a class="file-link" href="../../uploads/<?php echo basename($row['supplementary_files_path']); ?>" target="_blank">View</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>