<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['chief_editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$editor_id = $_SESSION['chief_editor_id'];

$team_query = $conn->prepare("SELECT team_id FROM editorial_team_members WHERE editor_id = ?");
$team_query->bind_param("i", $editor_id);
$team_query->execute();
$team_result = $team_query->get_result();
$team = $team_result->fetch_assoc();

if (!$team) {
    die("Team information not found.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_msg_id'])) {
    $msg_id = intval($_POST['delete_msg_id']);

    $delete_stmt = $conn->prepare("UPDATE messages SET deleted_at = NOW() WHERE id = ? AND recipient_id = ?");
    $delete_stmt->bind_param("ii", $msg_id, $editor_id);

    if ($delete_stmt->execute()) {
        echo "<script>alert('Message deleted successfully.'); window.location.href = window.location.href;</script>";
        exit();
    } else {
        echo "<script>alert('Failed to delete message.');</script>";
    }
}


$team_id = $team['team_id'];
// Fetch team editors (excluding chief editor)
$query = $conn->prepare("
    SELECT e.editor_id, CONCAT_WS(' ', u.first_name, u.middle_name, u.last_name) AS full_name
    FROM editors e
    INNER JOIN users u ON e.user_id = u.id
    INNER JOIN editorial_team_members etm ON e.editor_id = etm.editor_id
    WHERE etm.team_id = ? AND e.editor_id != ?
");

$query->bind_param("ii", $team_id, $editor_id);

$query->execute();
$result = $query->get_result();

$team_editors = [];
while ($row = $result->fetch_assoc()) {
    $team_editors[] = $row;
}
// Fetch journals associated with the chief editor's team
$journal_query = $conn->prepare("
    SELECT id, journal_name 
    FROM journals 
    WHERE editorial_team_id = ?
");
$journal_query->bind_param("i", $team_id);
$journal_query->execute();
$journal_result = $journal_query->get_result();

$journals = [];
$journal_ids = [];
while ($row = $journal_result->fetch_assoc()) {
    $journals[$row['id']] = $row['journal_name'];
    $journal_ids[] = $row['id'];
}

// Fetch reviewers associated with these journals
$reviewers = [];
if (!empty($journal_ids)) {
    $placeholders = implode(',', array_fill(0, count($journal_ids), '?'));
    $types = str_repeat('i', count($journal_ids));
    $reviewer_query = $conn->prepare("
    SELECT r.id, u.first_name, u.last_name, rj.journal_id
    FROM reviewers r
    INNER JOIN users u ON r.user_id = u.id
    INNER JOIN reviewer_journals rj ON r.id = rj.reviewer_id
    WHERE rj.journal_id IN ($placeholders) AND r.registration_status = 'approved'
");


    $reviewer_query->bind_param($types, ...$journal_ids);
    $reviewer_query->execute();
    $reviewer_result = $reviewer_query->get_result();

    while ($row = $reviewer_result->fetch_assoc()) {
        $reviewers[] = [
            'reviewer_id' => $row['id'],
            'full_name' => $row['first_name'] . ' ' . $row['last_name'],
            'journal_name' => $journals[$row['journal_id']] ?? ''
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Team Editor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 40px;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
            font-weight: 600;
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
<a href="chief-dashboard.php" class="btn-back">← Back to Chief Dashboard</a>
<div class="container">
<?php
// Fetch messages for the logged-in chief editor
$msg_query = $conn->prepare("
    SELECT m.*, 
        CASE 
  WHEN m.sender_role = 'reviewer' THEN (
    SELECT CONCAT(u.first_name, ' ', u.last_name)
    FROM reviewers r
    INNER JOIN users u ON r.user_id = u.id
    WHERE r.id = m.sender_id
  )
  WHEN m.sender_role = 'editor' THEN (
    SELECT CONCAT(u.first_name, ' ', u.last_name)
    FROM editors e
    INNER JOIN users u ON e.user_id = u.id
    WHERE e.editor_id = m.sender_id
  )
  ELSE 'Unknown'
END AS sender_name
    FROM messages m
    WHERE m.recipient_id = ? AND m.deleted_at IS NULL
    ORDER BY m.created_at DESC
");

$msg_query->bind_param("i", $editor_id);
$msg_query->execute();
$msg_result = $msg_query->get_result();
?>
<hr>
<h4 class="mt-4">Inbox</h4>
<?php if ($msg_result->num_rows > 0): ?>
    <div class="list-group">
        <?php while ($msg = $msg_result->fetch_assoc()): ?>
            <div class="list-group-item mb-3">
                <h5 class="mb-1"><?= htmlspecialchars($msg['subject']) ?></h5>
                <p class="mb-1"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                <small>
                    From: <?= htmlspecialchars(ucfirst($msg['sender_role'])) ?> - 
                    <?= htmlspecialchars($msg['sender_name']) ?> 
                    <?= $msg['created_at'] ?>
                </small>
                <br>
                <button 
                    class="btn btn-sm btn-outline-primary mt-2 reply-btn"
                    data-recipient-id="<?= $msg['sender_id'] ?>"
                    data-recipient-role="<?= $msg['sender_role'] ?>"
                    data-subject="Re: <?= htmlspecialchars($msg['subject']) ?>"
                >Reply</button>
            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this message?');" style="display:inline;">
    <input type="hidden" name="delete_msg_id" value="<?= $msg['id'] ?>">
    <button type="submit" class="btn btn-sm btn-outline-danger mt-2 ms-2">Delete</button>
</form>
</div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p>No messages yet.</p>
<?php endif; ?>

    <h2>Contact Team Editor And Reviewer</h2>
    <form action="process_contact_team.php" method="POST">
    <!-- Select Recipients -->
    <div class="mb-3">
        <label for="recipient_ids" class="form-label">Select Recipients</label>
        <select class="form-select" id="recipient_ids" name="recipient_ids[]" multiple required>
            <optgroup label="Editors">
                <?php foreach ($team_editors as $editor): ?>
                    <option value="editor_<?= htmlspecialchars($editor['editor_id']) ?>">
                        <?= htmlspecialchars($editor['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </optgroup>
            <optgroup label="Reviewers">
                <?php foreach ($reviewers as $reviewer): ?>
                    <option value="reviewer_<?= htmlspecialchars($reviewer['reviewer_id']) ?>">
                        <?= htmlspecialchars($reviewer['full_name']) ?> (<?= htmlspecialchars($reviewer['journal_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </optgroup>
        </select>
        <small class="form-text text-muted">Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.</small>
    </div>

    <!-- Subject -->
    <div class="mb-3">
        <label for="subject" class="form-label">Subject</label>
        <input type="text" class="form-control" id="subject" name="subject" required>
    </div>

    <!-- Message -->
    <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
    </div>

    <!-- Submit Buttons -->
    <button type="submit" class="btn btn-primary">Send Message</button>
    <button type="reset" class="btn btn-secondary">Reset</button>
</form>
</div>

<script>
document.querySelectorAll('.reply-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const recipientId = this.getAttribute('data-recipient-id');
        const recipientRole = this.getAttribute('data-recipient-role');  // ✅ this was missing!
        const subject = this.getAttribute('data-subject');

        // Set recipient in the multiselect
        const select = document.getElementById('recipient_ids');
        for (let i = 0; i < select.options.length; i++) {
            const option = select.options[i];
            option.selected = false; // Clear previous selection first
            if (option.value === `${recipientRole}_${recipientId}`) {
                option.selected = true;
            }
        }

        // Set subject field
        document.getElementById('subject').value = subject;

        // Scroll to form and focus
        window.scrollTo({ top: 0, behavior: 'smooth' });
        document.getElementById('message').focus();
    });
});

</script>
</body>
</html>
