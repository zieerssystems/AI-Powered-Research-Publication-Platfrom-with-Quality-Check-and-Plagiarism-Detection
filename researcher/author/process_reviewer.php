<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];
    $cv_path = '';

    // Validate title (optional check like editor)
    $title = trim($_POST['title']);
    if (stripos($title, 'dr') === false) {
        $errors[] = "Title must include 'Dr'.";
    }

    // Validate CV
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['cv_file']['tmp_name'];
        $file_name = basename($_FILES['cv_file']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if ($file_ext !== 'pdf') {
            $errors[] = "Only PDF files are allowed.";
        }

        if ($_FILES['cv_file']['size'] > 2 * 1024 * 1024) {
            $errors[] = "File size exceeds 2MB.";
        }

        if (empty($errors)) {
            $upload_dir = __DIR__ . "/../../uploads/reviewer_cvs/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $unique_name = uniqid("cv_") . "." . $file_ext;
            $destination = $upload_dir . $unique_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                $cv_path = "uploads/reviewer_cvs/" . $unique_name;
            } else {
                $errors[] = "Error uploading CV file.";
            }
        }
    }

    // If validation failed
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header("Location: reviewer_reg.php");
        exit();
    }

    // Prepare clean data for insertion
    $reviewer_data = [
        'user_id' => trim($_POST['user_id'] ?? ''),
        'title' => $title,
        'telephone' => trim($_POST['telephone'] ?? ''),
        'degree' => trim($_POST['degree'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'gender' => trim($_POST['gender'] ?? ''),
        'reviewer_type' => trim($_POST['researcher_type'] ?? ''),
        'position' => trim($_POST['position'] ?? ''),
        'institution' => trim($_POST['institution'] ?? ''),
        'department' => trim($_POST['department'] ?? ''),
        'street_address' => trim($_POST['street_address'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'state' => trim($_POST['state'] ?? ''),
        'zip_code' => trim($_POST['zip_code'] ?? ''),
        'country' => trim($_POST['country'] ?? ''),
        'experience' => trim($_POST['experience'] ?? '0'),
        'review_frequency' => trim($_POST['review_frequency'] ?? '0'),
        'payment_type' => trim($_POST['payment_type'] ?? ''),
        'account_holder_name' => ($_POST['payment_type'] == 'paid') ? trim($_POST['account_holder_name'] ?? '') : '',
        'bank_name' => ($_POST['payment_type'] == 'paid') ? trim($_POST['bank_name'] ?? '') : '',
        'account_number' => ($_POST['payment_type'] == 'paid') ? trim($_POST['account_number'] ?? '') : '',
        'ifsc_code' => ($_POST['payment_type'] == 'paid') ? trim($_POST['ifsc_code'] ?? '') : '',
        'branch_name' => ($_POST['payment_type'] == 'paid') ? trim($_POST['branch_name'] ?? '') : '',
        'bank_country' => ($_POST['payment_type'] == 'paid') ? trim($_POST['bank_country'] ?? '') : '',
        'cv_path' => $cv_path
    ];

    // Insert reviewer
    $reviewer_id = insertReviewer($conn, $reviewer_data);
    if (!$reviewer_id) {
    die("Failed to insert reviewer.");
}


    // Assign journals if selected
    if (!empty($_POST['journals'])) {
        assignJournalsToReviewer($conn, $reviewer_id, $_POST['journals']);
    }

    $_SESSION['success_message'] = "Reviewer registered successfully!";
    header("Location: reviewer_login.php");
    exit();
}
?>
