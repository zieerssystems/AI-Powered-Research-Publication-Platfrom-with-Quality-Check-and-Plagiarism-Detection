<?php
session_start();
include("../include/db_connect.php");

$editor_id = null;
$contract_status = '';
$old_contract_file = '';
$message = '';

// Step 1: Ensure editor_id is provided
if (!isset($_GET['editor_id']) || empty($_GET['editor_id'])) {
    $message = "<div class='error-box'>Error: Editor ID is missing.</div>";
} else {
    $editor_id = intval($_GET['editor_id']);

    // Step 2: Fetch contract details only if editor_id is valid
    $query = $conn->prepare("SELECT contract_status, contract_file FROM editors WHERE editor_id = ?");
    $query->bind_param("i", $editor_id);
    $query->execute();
    $result = $query->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $contract_status = $row['contract_status'];
        $old_contract_file = $row['contract_file'];

        if ($contract_status !== 'sent' && $contract_status !== 'reupload') {
            $message = "<div class='error-box'>Reupload not allowed unless requested by admin.</div>";
        }
    } else {
        $message = "<div class='error-box'>Editor not found.</div>";
    }
}

// Step 3: Handle file upload only if editor_id is valid and form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $editor_id && isset($_FILES['signed_contract'])) {
    $upload_dir = "../admin/contracts/signed/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_tmp = $_FILES['signed_contract']['tmp_name'];
    $file_ext = strtolower(pathinfo($_FILES['signed_contract']['name'], PATHINFO_EXTENSION));

    if ($file_ext !== 'pdf') {
        $message = "<div class='error-box'>Invalid file format! Please upload a PDF document.</div>";
    } else {
        $new_file_name = "signed_contract_editor_{$editor_id}_" . time() . ".pdf";
        $target_file = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            if (!empty($old_contract_file) && file_exists($upload_dir . $old_contract_file)) {
                unlink($upload_dir . $old_contract_file);
            }

            $current_date = date("Y-m-d");
            $update_query = $conn->prepare("UPDATE editors SET contract_status = 'pending_verification', contract_file = ?, upload_date = ? WHERE editor_id = ?");
            $update_query->bind_param("ssi", $new_file_name, $current_date, $editor_id);
            $update_query->execute();

            $message = "<div class='success-box'>Upload successful! Contract saved.</div>
            <script>
                setTimeout(function() {
                    window.location.href = 'upload_editor_contract.php?editor_id={$editor_id}';
                }, 2000);
            </script>";
        } else {
            $message = "<div class='error-box'>Error uploading contract.</div>";
        }
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
<h2>Reupload Editor Contract</h2>
<form action="upload_editor_contract.php?editor_id=<?= $editor_id; ?>" method="post" enctype="multipart/form-data">
    <label>Upload Signed Contract (PDF Only):</label>
    <input type="file" name="signed_contract" accept=".pdf" required>
    <button type="submit" <?= ($contract_status !== 'reupload' && $contract_status !== 'sent') ? 'disabled style="background: #ccc; cursor: not-allowed;"' : '' ?>>Upload</button>
</form>
<?php if (!empty($message)) echo $message; ?>
<a class="back-link" href="../index.php">⬅ Back to Home</a>
</div>
</body>
</html>
