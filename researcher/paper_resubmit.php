<?php
include(__DIR__ . "/../../include/db_connect.php");
require __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$author_id = $_SESSION['author_id'] ?? null;
$errorMsg = '';

if (!isset($_GET['paper_id'])) {
    header("Location: author_dashboard.php");
    exit;
}

$paper_id = $_GET['paper_id'];

// Fetch paper info
$paper = getPaperById($conn, $paper_id, $author_id);

if (!$paper) {
    header("Location: author_dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $abstract = $_POST['abstract'];
    $keywords = $_POST['keywords'];

    $upload_dir = __DIR__ . "/../../uploads/";
    $file_path = "";
    $supplementary_files_path = "";
    $file_hash = "";

    if (isset($_FILES['file_path']) && $_FILES['file_path']['error'] == 0) {
        $tmp_path = $_FILES['file_path']['tmp_name'];
        $file_hash = hash_file('sha256', $tmp_path);

        if (checkDuplicateFileHash($conn, $file_hash, $paper_id)) {
            $errorMsg = "❗ A similar file has already been uploaded. Please modify your document.";
        } else {
            $file_name = basename($_FILES['file_path']['name']);
            $file_path = $upload_dir . uniqid() . '-' . $file_name;
            move_uploaded_file($tmp_path, $file_path);
        }
    }

    if (empty($errorMsg) && isset($_FILES['supplementary_files_path']) && $_FILES['supplementary_files_path']['error'] == 0) {
        $supp_name = basename($_FILES['supplementary_files_path']['name']);
        $supplementary_files_path = $upload_dir . uniqid() . '-' . $supp_name;
        move_uploaded_file($_FILES['supplementary_files_path']['tmp_name'], $supplementary_files_path);
    }

    if (empty($errorMsg)) {
        update_Paper($conn, $title, $abstract, $keywords, $file_path, $supplementary_files_path, $file_hash, $paper_id, $author_id);
        updatePaperAssignmentsStatus($conn, $paper_id);
        updateEditorTasks($conn, $paper_id);

        // Send confirmation email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@gmail.com';
            $mail->Password = 'your_password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your_email@example.com', 'Your Publication Platform');
            $mail->addAddress($paper['email'], 'Author');
            $mail->isHTML(true);
            $mail->Subject = 'Paper Status Updated: Revised Submitted';
            $mail->Body = '
                <h2>Your Paper has been successfully reuploaded.</h2>
                <p>The status of your paper titled <strong>' . htmlspecialchars($paper['title']) . '</strong> has been updated to "Revised Submitted".</p>
                <p>Thank you for your submission!</p>
            ';
            $mail->send();
        } catch (Exception $e) {
            // Silent fail
        }

        header("Location: revision_management.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reupload Paper</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #e0f7fa, #ffffff);
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }

        .container {
            max-width: 850px;
            margin: 40px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            margin-bottom: 30px;
            color: #0077b6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .btn-black {
            background-color: #000;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        .btn-black:hover {
            background-color: #333;
        }

        .error-box {
            background: #ffe5e5;
            color: #b00020;
            border-left: 5px solid #b00020;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .back-link {
            display: inline-block;
            margin: 20px;
            color: #0077b6;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <a class="back-link" href="revision_management.php">⬅ Back</a>
    <div class="container">
        <h1>🔄 Reupload Paper</h1>

        <?php if (!empty($errorMsg)): ?>
            <div class="error-box"><?php echo $errorMsg; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Paper Title</label>
                <input type="text" name="title" id="title" required value="<?php echo htmlspecialchars($paper['title']); ?>">
            </div>

            <div class="form-group">
                <label for="abstract">Abstract</label>
                <textarea name="abstract" id="abstract" rows="5"><?php echo htmlspecialchars($paper['abstract']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="keywords">Keywords</label>
                <textarea name="keywords" id="keywords" rows="3"><?php echo htmlspecialchars($paper['keywords']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="file_path">Upload New Paper (PDF)</label>
                <input type="file" name="file_path" id="file_path" required>
            </div>

            <div class="form-group">
                <label for="supplementary_files_path">Supplementary Files (optional)</label>
                <input type="file" name="supplementary_files_path" id="supplementary_files_path">
            </div>

            <button type="submit" class="btn-black">Submit Revised Paper</button>
        </form>
    </div>
</body>
</html>