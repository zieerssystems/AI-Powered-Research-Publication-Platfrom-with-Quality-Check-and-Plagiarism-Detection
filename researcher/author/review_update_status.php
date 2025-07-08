<?php
include 'db_connect.php';

// Fetch review history using the function from db_connect.php
$result = fetchHistory($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Review History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h2 class="mb-4">Review History</h2>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Paper Title</th>
            <th>Journal Name</th>
            <th>Status</th>
            <th>Assign Reviewer</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['journal_name']) ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <?php if ($row['status'] === 'Accepted') { ?>
                    <form method="GET" action="assign_reviewer.php">
                        <input type="hidden" name="paper_id" value="<?= $row['paper_id'] ?>">
                        <input type="hidden" name="journal_name" value="<?= $row['journal_name'] ?>">
                        <button class="btn btn-primary btn-sm">Assign Now</button>
                    </form>
                <?php } else { echo "—"; } ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>
