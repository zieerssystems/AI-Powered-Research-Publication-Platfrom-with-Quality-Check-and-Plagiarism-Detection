<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

// Check if editor is logged in
if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$journal_id = isset($_GET['journal_id']) ? intval($_GET['journal_id']) : 0;
$paper_id = isset($_GET['paper_id']) ? intval($_GET['paper_id']) : 0;
$exclude_reviewer = isset($_GET['exclude_reviewer']) ? intval($_GET['exclude_reviewer']) : 0;

// Get filter options from request
$journal_filter = isset($_GET['journal_filter']) ? trim($_GET['journal_filter']) : '';
$assignment_filter = isset($_GET['assignment_filter']) ? trim($_GET['assignment_filter']) : '';

// Fetch available journal names for the dropdown using the function from db_connect.php
$journals_result = fetchDistinctJournalNames($conn);

// Fetch reviewers based on filters using the function from db_connect.php
$reviewers = fetchReviewers($conn, $journal_filter, $assignment_filter, $journal_id, $paper_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .scroll-box {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .table th, .table td {
            white-space: nowrap;
            vertical-align: middle;
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
        <h2>📑 Reviewers List</h2>

        <form method="GET" class="mb-3">
            <div class="row">
                <!-- Journal Filter -->
                <div class="col-md-4">
                    <label for="journal_filter" class="form-label">Filter by Journal</label>
                    <select name="journal_filter" id="journal_filter" class="form-select">
                        <option value="">All Journals</option>
                        <?php while ($row = $journals_result->fetch_assoc()) { ?>
                            <option value="<?php echo htmlspecialchars($row['journal_name']); ?>"
                                <?php echo ($journal_filter == $row['journal_name']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['journal_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Assignment Filter -->
                <div class="col-md-4">
                    <label for="assignment_filter" class="form-label">Filter by Assignment</label>
                    <select name="assignment_filter" id="assignment_filter" class="form-select">
                        <option value="">All Reviewers</option>
                        <option value="unassigned" <?php echo ($assignment_filter == 'unassigned') ? 'selected' : ''; ?>>Unassigned Reviewers</option>
                        <option value="assigned" <?php echo ($assignment_filter == 'assigned') ? 'selected' : ''; ?>>Assigned Reviewers</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </div>
        </form>

        <div class="scroll-box">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Telephone</th>
                        <th>Degree</th>
                        <th>Institution</th>
                        <th>Department</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Country</th>
                        <th>Experience</th>
                        <th>Review Frequency</th>
                        <th>Journals</th>
                        <th>Assigned Papers (Status)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($reviewers->num_rows > 0) { ?>
                        <?php while ($row = $reviewers->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['user_last_name'] . ', ' . $row['user_first_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['telephone']); ?></td>
                                <td><?php echo htmlspecialchars($row['degree']); ?></td>
                                <td><?php echo htmlspecialchars($row['institution']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td><?php echo htmlspecialchars($row['city']); ?></td>
                                <td><?php echo htmlspecialchars($row['state']); ?></td>
                                <td><?php echo htmlspecialchars($row['country']); ?></td>
                                <td><?php echo htmlspecialchars($row['experience']); ?></td>
                                <td><?php echo htmlspecialchars($row['review_frequency']); ?></td>
                                <td><?php echo htmlspecialchars($row['journals'] ?: 'None'); ?></td>
                                <td>
                                    <?php echo $row['assigned_papers'] ? $row['assigned_papers'] : '❌ No Papers Assigned'; ?>
                                </td>
                                <td>
                                    <a href="assign_reviewer.php?reviewer_id=<?php echo $row['id']; ?>&journal_id=<?php echo $journal_id; ?>&paper_id=<?php echo $paper_id; ?>" class="btn btn-primary">Assign Paper</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr><td colspan="15" class="text-center">No reviewers found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
