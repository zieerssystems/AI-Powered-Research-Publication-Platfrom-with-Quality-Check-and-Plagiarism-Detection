<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$editor_id = $_SESSION['editor_id'];

// Soft delete logic
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_message'])) {
    $message_id = $_POST['message_id'];
    $delete = "UPDATE editor_reviewer_messages SET deleted_at = NOW() WHERE id = ? AND recipient_id = ? AND recipient_role = 'editor'";
    $stmt = $conn->prepare($delete);
    $stmt->bind_param("ii", $message_id, $editor_id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch messages for logged-in editor
function fetchEditorMessages($editor_id) {
    global $conn;
    $data = [];

    $query = "SELECT 
    erm.id, 
    erm.sender_id, 
    erm.recipient_id, 
    erm.subject, 
    erm.message, 
    erm.created_at,
    u.first_name AS sender_name
FROM editor_reviewer_messages erm
JOIN editors e ON erm.sender_id = e.editor_id
JOIN users u ON e.user_id = u.id
WHERE erm.recipient_id = ? 
  AND erm.recipient_role = 'editor' 
  AND erm.deleted_at IS NULL
ORDER BY erm.created_at DESC
";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}

$messages = fetchEditorMessages($editor_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Reviewer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f9;
        }
        .container {
            max-width: 900px;
            background: white;
            padding: 20px;
            margin-top: 50px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .message-section {
            margin-bottom: 30px;
        }
        .section-header {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #007bff;
        }
        .message-box {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }
        .message-box h5 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .message-box p {
            font-size: 1rem;
            margin: 5px 0;
        }
        .message-box .message-date {
            font-size: 0.9rem;
            color: #777;
        }
        .section-container {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        .message-box-wrapper {
            flex: 1;
            min-width: 300px;
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
<div class="container">
    <h2 class="mb-4">Messages to You</h2>
    <?php if (count($messages) > 0): ?>
        <?php foreach ($messages as $message): ?>
            <div class="message-box">
                <h5>Subject: <?= htmlspecialchars($message['subject']) ?></h5>
                <p><strong>From:</strong> <?= htmlspecialchars($message['sender_name']) ?> (Chief Editor)</p>
                <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($message['message'])) ?></p>
                <p class="message-date"><strong>Sent:</strong> <?= date("F j, Y, g:i a", strtotime($message['created_at'])) ?></p>

                <form method="post" style="display: inline;">
                    <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                    <button type="submit" name="delete_message" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
                <button class="btn btn-sm btn-outline-primary" onclick="reply('<?= htmlspecialchars($message['subject']) ?>')">Reply</button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No messages found.</p>
    <?php endif; ?>
</div>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send_to_chief'])) {
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Find the team_id for this editor
    $teamQuery = "SELECT team_id FROM editorial_team_members WHERE editor_id = ?";
    $stmt = $conn->prepare($teamQuery);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $stmt->bind_result($team_id);
    $stmt->fetch();
    $stmt->close();

    // Find the Chief Editor in this team
    $chiefQuery = "SELECT editor_id FROM editorial_team_members WHERE team_id = ? AND role = 'Chief Editor' LIMIT 1";
    $stmt = $conn->prepare($chiefQuery);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $stmt->bind_result($chief_editor_id);
    $stmt->fetch();
    $stmt->close();

    if ($chief_editor_id) {
        // Insert into unified messages table
        $insert = "INSERT INTO messages (sender_id, sender_role, recipient_id, subject, message)
                   VALUES (?, 'editor', ?, ?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("iiss", $editor_id, $chief_editor_id, $subject, $message);
        $stmt->execute();
        $stmt->close();
        echo "<div class='alert alert-success'>Message sent to Chief Editor!</div>";
    } else {
        echo "<div class='alert alert-danger'>Chief Editor not found in your team.</div>";
    }
}

?>

<!-- Send Message to Chief Editor -->
<div class="container mt-4">
    <div class="section-header">📩 Send Message to Your Chief Editor</div>
    <form method="POST">
        <div class="mb-3">
            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
            <input type="text" name="subject" id="subject" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
            <textarea name="message" id="message" rows="5" class="form-control" required></textarea>
        </div>
        <button type="submit" name="send_to_chief" class="btn btn-primary">Send Message</button>
    </form>
</div>

<script>
    function reply(subject) {
        document.getElementById('subject').value = "RE: " + subject;
        document.getElementById('message').focus();
        window.scrollTo({
            top: document.getElementById('subject').offsetTop - 100,
            behavior: 'smooth'
        });
    }
</script>
</body>
</html>
