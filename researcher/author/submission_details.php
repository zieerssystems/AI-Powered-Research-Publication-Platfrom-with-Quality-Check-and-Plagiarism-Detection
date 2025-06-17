<?php
session_start();
if (!isset($_SESSION['author_id'])) {
    header("Location: author_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");

$author_id = $_SESSION['author_id'];

// Fetch paper details along with review process and editorial team info
$result = fetchPaperDetailsWithReviewInfo($conn, $author_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Submission Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background:rgb(26, 35, 115);
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
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
<a href="author_dashboard.php" class="btn-back">← Back to Author Dashboard</a>
    <h2>📑 Submission Details</h2>

    <table>
        <tr>
            <th>Paper Title</th>
            <th>Journal</th>
            <th>Editor</th>
            <th>Status</th>
            <th>Reviewers</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) {
            // Fetch editorial team name based on editorial_team_id
            $editorial_team_name = fetchEditorialTeamName($conn, $row['editorial_team_id']);

            // Only show reviewers if review process is "Open Review"
            $reviewers_display = ($row['review_process'] === 'Open Review') ? htmlspecialchars($row['reviewers']) : 'Not Assigned';
        ?>
            <tr>
                <td><?= htmlspecialchars($row['paper_title']) ?></td>
                <td><?= htmlspecialchars($row['journal_name']) ?></td>
                <td><?= htmlspecialchars($editorial_team_name) ?></td>
                <td><strong><?= htmlspecialchars($row['paper_status']) ?></strong></td>
                <td><?= $reviewers_display ?></td>
            </tr>
        <?php } ?>
    </table>

</body>
</html>
