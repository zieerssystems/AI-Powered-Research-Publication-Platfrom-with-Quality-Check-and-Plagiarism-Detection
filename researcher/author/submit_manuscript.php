<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (!isset($_SESSION['author_id'])) {
    header("Location: author_dash_login.php");
    exit();
}

$author_id = $_SESSION['author_id'];

// Fetch all papers by this author using the function from db_connect.php
$papers = fetchPapersByAuthorId($conn, $author_id);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manuscript Details - Submit Manuscript</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Header */
        header {
            background-color: #0056b3;
            color: white;
            padding: 15px 0;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 10px 0 0;
            text-align: center;
        }

        nav ul li {
            display: inline;
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        /* Main Container */
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Manuscript Details */
        .manuscript-details {
            background: #ffffff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 5px solid #0056b3;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .manuscript-details h3 {
            color: #0056b3;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }

        p, ul {
            line-height: 1.6;
        }

        ul {
            padding-left: 20px;
        }

        ul li {
            margin: 8px 0;
        }

        a {
            color: #0056b3;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Buttons */
        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s ease-in-out;
            font-weight: bold;
        }

        .button:hover {
            background-color: #004494;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 15px;
            background-color: #333;
            color: white;
            margin-top: 30px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px;
            }

            nav ul li {
                display: block;
                margin: 10px 0;
            }
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

<div class="container">
    <h2>Your Manuscripts</h2>
    <a class="btn-back" href="author_dashboard.php">⬅ Back to Author Dashboard</a>
    <?php if (count($papers) > 0): ?>
        <?php foreach ($papers as $paper): ?>
            <div class="manuscript-details">
                <h3>Manuscript Details</h3>
                <p><strong>Title:</strong> <?php echo htmlspecialchars($paper['title']); ?></p>
                <p><strong>Abstract:</strong> <?php echo nl2br(htmlspecialchars($paper['abstract'])); ?></p>
                <p><strong>Keywords:</strong> <?php echo htmlspecialchars($paper['keywords']); ?></p>
                <p><strong>Submission Date:</strong> <?php echo date('F j, Y', strtotime($paper['submission_date'])); ?></p>
                <p><strong>DOI:</strong> <?php echo htmlspecialchars($paper['doi']); ?></p>

                <h3>Files</h3>
                <ul>
                    <div class="col-md-6"><strong>Cover Letter:</strong>
                        <?= $paper['cover_letter_path'] ? "<a class='file-link' href='/my_publication_site/uploads/" . basename($paper['cover_letter_path']) . "' target='_blank'>View</a>" : "N/A" ?>
                    </div>
                    <div class="col-md-6"><strong>Copyright Agreement:</strong>
                        <?= $paper['copyright_agreement_path'] ? "<a class='file-link' href='/my_publication_site/uploads/" . basename($paper['copyright_agreement_path']) . "' target='_blank'>View</a>" : "N/A" ?>
                    </div>
                    <div class="col-md-6"><strong>Manuscript:</strong>
                        <?= $paper['file_path'] ? "<a class='file-link' href='/my_publication_site/uploads/" . basename($paper['file_path']) . "' target='_blank'>View</a>" : "N/A" ?>
                    </div>
                    <div class="col-md-6 mt-2"><strong>Supplementary Files:</strong>
                        <?= $paper['supplementary_files_path'] ? "<a class='file-link' href='/my_publication_site/uploads/" . basename($paper['supplementary_files_path']) . "' target='_blank'>View</a>" : "N/A" ?>
                    </div>
                </ul>

                <h3>Status</h3>
                <p><strong>Current Status:</strong> <?php echo htmlspecialchars($paper['status']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No manuscripts found.</p>
    <?php endif; ?>
</div>
</body>
</html>
