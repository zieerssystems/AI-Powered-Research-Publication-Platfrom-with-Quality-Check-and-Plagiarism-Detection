<?php
session_start();
include("../include/db_connect.php");

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $message = "<div class='error-box'>Error: Reviewer ID is missing.</div>";
} else {
    $reviewer_id = intval($_GET['id']);
    $row = getReviewerContractDetails($conn, $reviewer_id); // 🔁 use function

    if ($row) {
        $contract_status = $row['contract_status'];
        $old_contract_file = $row['contract_file'];

        if ($contract_status !== 'sent' && $contract_status !== 'reupload') {
            $message = "<div class='error-box'>Reupload not allowed unless requested by admin.</div>";
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['signed_contract'])) {
            $upload_dir = "../admin/contracts/signed/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_tmp = $_FILES['signed_contract']['tmp_name'];
            $file_ext = strtolower(pathinfo($_FILES['signed_contract']['name'], PATHINFO_EXTENSION));

            if ($file_ext !== 'pdf') {
                $message = "<div class='error-box'>Invalid file format! Please upload a PDF document.</div>";
            } else {
                $new_file_name = "signed_contract_{$reviewer_id}_" . time() . ".pdf";
                $target_file = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    if (!empty($old_contract_file) && file_exists($upload_dir . $old_contract_file)) {
                        unlink($upload_dir . $old_contract_file);
                    }

                    $current_date = date("Y-m-d");

                    if (updateContractDetails($conn, $reviewer_id, $new_file_name, $current_date)) {
                        $message = "<div class='success-box'>Upload successful! Contract saved.</div>
                        <script>
                            setTimeout(function() {
                                window.location.href = 'upload_reviewer_contract.php?id={$reviewer_id}';
                            }, 2000);
                        </script>";
                    } else {
                        $message = "<div class='error-box'>Database update failed!</div>";
                    }
                } else {
                    $message = "<div class='error-box'>Error uploading contract.</div>";
                }
            }
        }
    } else {
        $message = "<div class='error-box'>Reviewer not found.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reupload Contract - Zieers</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f5f7fa;
        }

        .header {
            background: #003366;
            color: white;
            padding: 15px 30px;
            font-size: 24px;
            font-weight: bold;
        }

        .container {
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
            color: #003366;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input[type="file"] {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 100%;
        }

        button {
            background: #003366;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #005299;
        }

        .success-box {
            background: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .error-box {
            background: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #003366;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="header">Zieers</div>

<div class="container">
    <h2>Reupload Reviewer Contract</h2>
    <form action="upload_reviewer_contract.php?id=<?= $reviewer_id; ?>" method="post" enctype="multipart/form-data">
        <label>Upload Signed Contract (PDF Only):</label>
        <input type="file" name="signed_contract" accept=".pdf" required>
        <button type="submit" <?= ($contract_status !== 'reupload' && $contract_status !== 'sent') ? 'disabled style="background: #ccc; cursor: not-allowed;"' : '' ?>>Upload</button>
    </form>
    <?php if (!empty($message)) echo $message; ?>
    <a class="back-link" href="../index.php">⬅ Back to Home</a>
</div>

</body>
</html>
