<?php
include(__DIR__ . "/../../include/db_connect.php");

$paper_id = $_GET['paper_id'] ?? 0;
$paper = getPaperDetails_4($conn, $paper_id);

if (!$paper) {
    die("Paper not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Format Check for: <?= htmlspecialchars($paper['title']) ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        a {
            display: block;
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
            font-weight: bold;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }
        input[type="file"], textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
            height: 100px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 24px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        button:focus {
            outline: none;
        }
        .feedback-container {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Format Check for: <?= htmlspecialchars($paper['title']) ?></h2>
    <a href="editor_manuscript.php?paper_id=<?= $paper_id ?>" target="_blank">View Manuscript</a>

    <form action="process_formate.php" method="POST" enctype="multipart/form-data" onsubmit="return validateFeedback();">
        <input type="hidden" name="paper_id" value="<?= $paper_id ?>">
        <input type="hidden" name="task_type" value="4">

        <div class="feedback-container">
            <label for="formatted_file">Upload Formatted Paper (optional)</label>
            <input type="file" name="formatted_file" accept=".pdf,.doc,.docx">
        </div>

        <div class="feedback-container">
            <label for="feedback">Feedback (optional):</label>
            <textarea name="feedback" rows="5" cols="50" placeholder="Provide feedback (if any)..."></textarea>
        </div>

        <div class="button-container" style="text-align: center;">
            <button type="submit" name="result" value="Processed for Next Level">Processed for Next Level</button>
            <button type="submit" name="result" value="Not Processed">Not Processed</button>
            <button type="submit" name="result" value="Revision Request">Request Revision</button>
        </div>
    </form>
</div>

<script>
    function validateFeedback() {
        var feedback = document.querySelector('[name="feedback"]').value;
        var result = document.querySelector('button[type="submit"]:focus').value;

        if ((result === "Not Processed" || result === "Revision Request") && feedback.trim() === "") {
            alert("Please provide feedback before proceeding with Not Processed or Revision Request.");
            return false; // Prevent form submission
        }
        return true; // Allow form submission if feedback is provided
    }
</script>

</body>
</html>