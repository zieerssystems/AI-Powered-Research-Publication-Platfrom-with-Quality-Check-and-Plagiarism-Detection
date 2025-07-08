<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

$reviewer_id = $_SESSION['reviewer_id'];
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_message'])) {
    $message_id = $_POST['message_id'];
    $delete = "UPDATE editor_reviewer_messages SET deleted_at = NOW() WHERE id = ? AND recipient_id = ? AND recipient_role = 'reviewer'";
    $stmt = $conn->prepare($delete);
    $stmt->bind_param("ii", $message_id, $reviewer_id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// Fetch papers and editors
$papers = fetchPapersWithAuthorsAndEditors();

// Extract unique editors
$chiefEditors = [];
foreach ($papers as $row) {
    $chiefEditors[$row['chief_editor_id']] = $row['chief_first_name'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Editor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f9;
        }
        .container {
            max-width: 700px;
            background: white;
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
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
    <a href="reviewer_dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
<!-- Messages Sent To Reviewer -->
<div class="container mt-5 p-4 bg-white shadow rounded" style="max-width: 720px;">
    <h3 class="mb-3">📨 Messages From Chief Editor</h3>
    <?php
    $inboxStmt = $conn->prepare("SELECT * FROM editor_reviewer_messages 
                                 WHERE recipient_role = 'reviewer' AND recipient_id = ? 
                                 ORDER BY created_at DESC");
    $inboxStmt->bind_param("i", $reviewer_id);
    $inboxStmt->execute();
    $inboxResult = $inboxStmt->get_result();

    if ($inboxResult->num_rows > 0):
        while ($msg = $inboxResult->fetch_assoc()):
    ?>
        <div class="border rounded p-3 mb-4">
            <h5 class="text-primary"><?= htmlspecialchars($msg['subject']) ?></h5>
            <small class="text-muted">Received on <?= date('d M Y, h:i A', strtotime($msg['created_at'])) ?></small>
            <p class="mt-2"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>

            <!-- Reply Button -->
            <form action="reply_editor_message.php" method="post" class="mt-2">
                <input type="hidden" name="reply_to" value="<?= $msg['id'] ?>">
                <input type="hidden" name="subject" value="Re: <?= htmlspecialchars($msg['subject']) ?>">
                <button type="submit" class="btn btn-outline-primary btn-sm">Reply</button>
            </form>
            <!-- Delete Button -->
            <form method="post" style="display: inline;">
            <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                    <button type="submit" name="delete_message" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>

        </div>
    <?php endwhile; else: ?>
        <p class="text-muted">No messages received from editors yet.</p>
    <?php endif; ?>
</div>

<!-- Contact Editor Section -->
<div class="container mt-5 p-4 bg-white shadow rounded" style="max-width: 720px;">
    <h2 class="mb-3">📩 Contact Chief Editor</h2>
    <p class="text-muted mb-4">Use this form to send a message to the chief editor regarding a specific paper or general queries.</p>

    <form action="process_contact_editor.php" method="post">
    <!-- Select Paper (Optional) -->
    <div class="mb-3">
        <label for="paper_id" class="form-label">Select Paper (only if message is about a paper)</label>
        <select class="form-select" id="paper_id" name="paper_id">
            <option value="">-- No Paper Selected --</option>
            <?php foreach ($papers as $row): ?>
                <option value="<?= htmlspecialchars($row['paper_id']) ?>"
                    data-author-name="<?= htmlspecialchars($row['author_first_name']) ?>"
                    data-journal-name="<?= htmlspecialchars($row['journal_name']) ?>">
                    <?= htmlspecialchars($row['paper_title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Author Name (Auto-filled, hidden initially) -->
    <div class="mb-3 d-none" id="author_group">
        <label for="author_name" class="form-label">Corresponding Author</label>
        <input type="text" class="form-control" id="author_name" name="author_name" readonly>
    </div>

    <!-- Journal Name (Auto-filled, hidden initially) -->
    <div class="mb-3 d-none" id="journal_group">
        <label for="journal_name" class="form-label">Journal</label>
        <input type="text" class="form-control" id="journal_name" name="journal_name" readonly>
    </div>

    <!-- Chief Editor -->
    <div class="mb-3">
        <label for="editor_id" class="form-label">Select Chief Editor</label>
        <select class="form-select" id="editor_id" name="editor_id" required>
            <option value="">-- Select Chief Editor --</option>
            <?php foreach ($chiefEditors as $editor_id => $editor_name): ?>
                <option value="<?= htmlspecialchars($editor_id) ?>">
                    <?= htmlspecialchars($editor_name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Subject -->
    <div class="mb-3">
        <label for="subject" class="form-label">Subject</label>
        <input type="text" class="form-control" id="subject" name="subject" required>
    </div>

    <!-- Message -->
    <div class="mb-4">
        <label for="general_message" class="form-label">Message</label>
        <textarea class="form-control" id="general_message" name="general_message" rows="5" required></textarea>
    </div>

    <!-- Buttons -->
    <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">Send Message</button>
    </div>
</form>
</div>

<!-- Auto-fill Script -->
<script>
    $('#paper_id').on('change', function () {
        const selected = $(this).find('option:selected');
        const author = selected.data('author-name') || '';
        const journal = selected.data('journal-name') || '';

        if (author || journal) {
            $('#author_name').val(author);
            $('#journal_name').val(journal);
            $('#author_group, #journal_group').removeClass('d-none');
        } else {
            $('#author_name').val('');
            $('#journal_name').val('');
            $('#author_group, #journal_group').addClass('d-none');
        }
    });
</script>

</body>
</html>
