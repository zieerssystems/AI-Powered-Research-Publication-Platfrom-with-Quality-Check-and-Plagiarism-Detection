<?php
include(__DIR__ . "/../../include/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle CV upload
    $cvPath = '';
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
        $cvDir = __DIR__ . "/../../uploads/reviewer_cvs/";
        if (!file_exists($cvDir)) {
            mkdir($cvDir, 0777, true);
        }

        $fileTmp = $_FILES['cv_file']['tmp_name'];
        $fileName = uniqid('cv_') . '_' . basename($_FILES['cv_file']['name']);
        $filePath = $cvDir . $fileName;

        if (move_uploaded_file($fileTmp, $filePath)) {
            $cvPath = "uploads/reviewer_cvs/" . $fileName;
        } else {
            die("CV upload failed. TMP: $fileTmp, DEST: $filePath");
        }
    }

    // Collect form data
    $data = [
        'user_id' => trim($_POST['user_id'] ?? ''),
        'title' => trim($_POST['title'] ?? ''),
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
        'account_holder_name' => trim($_POST['account_holder'] ?? ''),
        'bank_name' => trim($_POST['bank_name'] ?? ''),
        'account_number' => trim($_POST['account_number'] ?? ''),
        'ifsc_code' => trim($_POST['ifsc'] ?? ''),
        'branch_name' => trim($_POST['branch_name'] ?? ''),
        'bank_country' => trim($_POST['bank_country'] ?? ''),
        'cv_path' => $cvPath
    ];

    // Insert reviewer data using the function from db_connect.php
    $reviewer_id = insertReviewer($conn, $data);

    // Assign journals using the function from db_connect.php
    if (!empty($_POST['journals'])) {
        assignJournalsToReviewer($conn, $reviewer_id, $_POST['journals']);
    }

    $_SESSION['success_message'] = "Registration successful!";
    header("Location: reviewer_login.php");
    exit();
}
?>
