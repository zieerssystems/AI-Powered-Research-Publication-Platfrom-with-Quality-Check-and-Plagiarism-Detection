<?php
session_start();
include(__DIR__ . "/../include/db_connect.php");

// Check if editor is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}

$result = getPendingPapersWithAuthorAndJournal($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Editor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7f9;
            margin: 0;
        }
        .header {
            background: #002147;
            color: white;
            padding: 20px 30px;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 28px;
        }
        .content {
            padding: 0 20px 20px;
        }
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .modal-content {
            border-radius: 10px;
        }
        .btn-primary {
            background: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background: #0b5ed7;
        }
    </style>
</head>
<body>
  <a href="admin_dashboard.php" class="btn btn-outline-secondary mt-3">⬅ Back to Dashboard</a>

<!-- Modern Header -->
<div class="header">
    <h2>📑 Assign Editor to Submitted Papers</h2>
</div>

<div class="container content">
    <table class="table table-bordered shadow-sm">
        <thead class="table-light">
            <tr>
                <th>Paper Title</th>
                <th>Author</th>
                <th>Journal Name</th>
                <th>Current Editor</th>
                <th>Assign Editor</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['journal_name']); ?></td>
                    <td>
                        <?php
                        if ($row['editor_id']) {
                            $editor_query = "SELECT first_name FROM editors WHERE editor_id = " . $row['editor_id'];
                            $editor_result = $conn->query($editor_query);
                            $editor = $editor_result->fetch_assoc();
                            echo htmlspecialchars($editor['first_name']);
                        } else {
                            echo "No editor assigned";
                        }
                        ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignEditorModal<?php echo $row['paper_id']; ?>">Assign Editor</button>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="assignEditorModal<?php echo $row['paper_id']; ?>" tabindex="-1" aria-labelledby="assignEditorModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="assignEditorModalLabel">Assign Editor to: <?php echo htmlspecialchars($row['title']); ?></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="assign_editor_process.php" method="POST">
                                    <input type="hidden" name="paper_id" value="<?php echo $row['paper_id']; ?>">
                                    <div class="mb-3">
                                        <label for="editor_id" class="form-label">Select Editor</label>
                                        <select name="editor_id" id="editor_id" class="form-select" required>
                                            <?php
                                           $editor_query = "SELECT e.editor_id, u.first_name 
                 FROM editors e
                 JOIN users u ON e.user_id = u.id";

                                            $editor_result = $conn->query($editor_query);
                                            while ($editor = $editor_result->fetch_assoc()) {
                                                echo "<option value='" . $editor['editor_id'] . "'>" . htmlspecialchars($editor['first_name']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Assign Editor</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </tbody>
    </table>
  </div>

</body>
</html>
