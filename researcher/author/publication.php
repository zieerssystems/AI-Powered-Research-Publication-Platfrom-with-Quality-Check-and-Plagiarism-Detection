<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

$editor_id = $_SESSION['editor_id'] ?? null;
if (!$editor_id) {
    echo "Editor not logged in.";
    exit;
}

// Get team_id from editorial_team_member using the function from db_connect.php
$team_id = getTeamId($conn, $editor_id);

if (!$team_id) {
    echo "No team found for this editor.";
    exit;
}

// Get team_name from editorial_teams using the function from db_connect.php
$team_name = getTeamName($conn, $team_id);

// Fetch published papers by this team using the function from db_connect.php
$paperResult = fetchPublishedPapersByTeam($conn, $team_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Publications</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; border: 1px solid #ccc; }
        th { background-color: #f4f4f4; }
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
  <a href="editor_dashboard.php" class="btn-back">← Back to editor Dashboard</a>
<h2>📚 Published Papers - Team: <?= htmlspecialchars($team_name) ?></h2>

<?php if ($paperResult->num_rows > 0): ?>
<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Journal</th>
            <th>Volume</th>
            <th>Issue</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $paperResult->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['journal_name']) ?></td>
            <td><?= htmlspecialchars($row['volume']) ?></td>
            <td><?= htmlspecialchars($row['issue']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
    <p>No published papers found for your team.</p>
<?php endif; ?>

</body>
</html>
