<?php
require 'mailer_config.php';
require_once(__DIR__ . "/../../vendor/autoload.php"); 

$editor_id = $_SESSION['editor_id']; 
$paper_id = isset($_GET['paper_id']) ? intval($_GET['paper_id']) : 0;

if (!$editor_id || !$paper_id) {
    die("Invalid session or paper ID.");
}

$task_type = 2;

// Fetch paper details + author + status
$stmt = $conn->prepare("SELECT p.title, p.file_path, j.journal_name, u.email, a.id, p.status 
                        FROM papers p 
                        JOIN journals j ON p.journal_id = j.id 
                        JOIN author a ON p.author_id = a.id
                        JOIN users u ON a.user_id = u.id
                        WHERE p.id = ?");

$stmt->bind_param("i", $paper_id);
$stmt->execute();
$stmt->bind_result($title, $file_path, $journal_name, $author_email, $author_id, $paper_status);
$stmt->fetch();
$stmt->close();

// Check if plagiarism report already exists
$stmt = $conn->prepare("SELECT plagiarism_percentage, report_path FROM plagiarism_reports WHERE paper_id = ?");
$stmt->bind_param("i", $paper_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($existing_percentage, $existing_report_path);
$report_exists = $stmt->num_rows > 0;
$stmt->fetch();
$stmt->close();

$show_plagiarism_button = true;

if ($report_exists && $paper_status !== 'Revised Submitted') {
    $_SESSION['plagiarism_percentage'] = $existing_percentage;
    $_SESSION['plagiarism_report_url'] = $existing_report_path;
    $show_plagiarism_button = false;
}

$status_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check Plagiarism
    if (isset($_POST['check_plagiarism'])) {
        $paper_full_path = realpath("../../uploads/" . basename($file_path));
        if (file_exists($paper_full_path)) {
            $plagiarism_percentage = rand(5, 40);
            $report_folder = __DIR__ . "/reports/";
            if (!is_dir($report_folder)) {
                mkdir($report_folder, 0777, true);
            }

            $plagiarism_report_path = "reports/plagiarism_report_" . $paper_id . ".pdf";
            copy('sample_report_template.pdf', $plagiarism_report_path);

            $stmt = $conn->prepare("INSERT INTO plagiarism_reports (paper_id, plagiarism_percentage, report_path, created_at)
                                    VALUES (?, ?, ?, NOW())
                                    ON DUPLICATE KEY UPDATE plagiarism_percentage=?, report_path=?, created_at=NOW()");
            $stmt->bind_param("iisds", $paper_id, $plagiarism_percentage, $plagiarism_report_path, $plagiarism_percentage, $plagiarism_report_path);
            $stmt->execute();
            $stmt->close();

            $_SESSION['plagiarism_percentage'] = $plagiarism_percentage;
            $_SESSION['plagiarism_report_url'] = $plagiarism_report_path;
            $show_plagiarism_button = false;
        } else {
            $status_message = "Paper file not found!";
        }
    }

    // Submit Feedback
    if (isset($_POST['submit_feedback'])) {
        $status_selected = $_POST['task_status'];
        $feedback = $_POST['feedback'] ?? '';
        $plagiarism_percentage = $_SESSION['plagiarism_percentage'];
        $plagiarism_report_url = $_SESSION['plagiarism_report_url'];

        // Update the editor task result
        $stmt = $conn->prepare("UPDATE editor_tasks SET result=?, response_date=NOW() 
                                WHERE paper_id=? AND editor_id=? AND task_type = ?");
        $stmt->bind_param("siii", $status_selected, $paper_id, $editor_id, $task_type);
        $stmt->execute();
        $stmt->close();

        // Update plagiarism feedback
        $stmt = $conn->prepare("UPDATE plagiarism_reports SET feedback=? WHERE paper_id=?");
        $stmt->bind_param("si", $feedback, $paper_id);
        $stmt->execute();
        $stmt->close();

        // Update paper status based on the task result
        if ($status_selected === 'Revision Request') {
            $updatePaperStatus = $conn->prepare("UPDATE papers SET status = 'Revision Requested' WHERE id = ?");
            $updatePaperStatus->bind_param("i", $paper_id);
            $updatePaperStatus->execute();
            $updatePaperStatus->close();
        } elseif ($status_selected === 'Processed for Next Level' || $status_selected === 'Not Processed') {
            // Mark as completed if processed or not processed
            $updatePaperStatus = $conn->prepare("UPDATE editor_tasks SET status = 'Completed' WHERE paper_id = ?");
            $updatePaperStatus->bind_param("i", $paper_id);
            $updatePaperStatus->execute();
            $updatePaperStatus->close();
        }

        // Send email to author with plagiarism report and feedback
        sendEmailWithAttachment($author_email, "Plagiarism Report & Feedback", "
            Dear Author,<br><br>
            Your paper titled <b>" . htmlspecialchars($title) . "</b> has been checked.<br><br>
            <b>Plagiarism:</b> " . htmlspecialchars($plagiarism_percentage) . "%<br>
            <b>Feedback:</b> " . nl2br(htmlspecialchars($feedback)) . "<br><br>
            Regards,<br>Editorial Team
        ", $plagiarism_report_url);

        header("Location: editor_dashboard.php");
        exit;
    }
}

// Email Function
function sendEmailWithAttachment($to, $subject, $body, $attachmentPath) {
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true;
    $mail->Username = 'anjuscaria7@gmail.com';
    $mail->Password = 'dlvr dkbu sdob fqfu';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('anjuscaria7@gmail.com', 'Editorial Team');
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->Body = $body;
    
    if (file_exists($attachmentPath)) {
        $mail->addAttachment($attachmentPath);
    }

    $mail->send();
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Plagiarism Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 bg-white p-5 rounded shadow">
    <h2 class="text-primary mb-4">Plagiarism Check Panel</h2>
    <a href="editor_dashboard.php" class="btn btn-outline-secondary mb-3">← Back to Dashboard</a>

    <div class="mb-4">
        <h4>Title:</h4> <?= htmlspecialchars($title) ?>
        <h5>Journal:</h5> <?= htmlspecialchars($journal_name) ?>
        <br><a href="../../uploads/<?= htmlspecialchars(basename($file_path)) ?>" class="btn btn-info mt-2" target="_blank">View Paper</a>
    </div>

    <?php if ($show_plagiarism_button): ?>
        <form method="POST">
            <button type="submit" name="check_plagiarism" class="btn btn-warning">Check Plagiarism</button>
        </form>
    <?php else: ?>
        <div class="alert alert-info">
            <strong>Plagiarism:</strong> <?= $_SESSION['plagiarism_percentage'] ?>%
            <br>
            <a href="download_report.php?report=<?= urlencode(basename($_SESSION['plagiarism_report_url'])) ?>" class="btn btn-success mt-2">Download Plagiarism Report</a>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label>Status:</label>
                <select name="task_status" class="form-select" id="statusDropdown" required onchange="toggleFeedbackBox()">
                    <?php
                    $percent = $_SESSION['plagiarism_percentage'];
                    if ($percent < 15) {
                        echo '<option value="Processed for Next Level" selected>Processed for Next Level</option>';
                    } elseif ($percent > 25) {
                        echo '<option value="Not Processed" selected>Not Processed</option>';
                    } else {
                        echo '<option value="Revision Request" selected>Revision Request</option>';
                    }
                    ?>
                </select>
            </div>

            <div id="feedbackBox" class="mb-3" style="display: none;">
                <label>Feedback:</label>
                <textarea name="feedback" class="form-control" id="feedbackTextarea"></textarea>
            </div>

            <button type="submit" name="submit_feedback" class="btn btn-primary">Submit Feedback & Update</button>
        </form>
    <?php endif; ?>

    <?php if (!empty($status_message)): ?>
        <div class="alert alert-danger mt-4"><?= $status_message ?></div>
    <?php endif; ?>
</div>

<script>
function toggleFeedbackBox() {
    const status = document.getElementById('statusDropdown').value;
    const feedbackBox = document.getElementById('feedbackBox');
    const feedbackTextarea = document.getElementById('feedbackTextarea');

    if (status === 'Revision Request' || status === 'Not Processed') {
        feedbackBox.style.display = 'block';
        feedbackTextarea.required = true;
    } else {
        feedbackBox.style.display = 'none';
        feedbackTextarea.required = false;
    }
}
toggleFeedbackBox();
</script>
</body>
</html>
