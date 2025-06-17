<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

$user_id = $_POST['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];
    $cv_path = '';

    // Validate title
    $title = trim($_POST['title']);
    if (stripos($title, 'dr') === false) {
        $errors[] = "Title must include 'Dr'.";
    }

    // Validate paper name
    if (empty($_POST['paper_name'])) {
        $errors[] = "At least one research paper name is required.";
    }

    // CV Upload validation
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['cv']['tmp_name'];
        $file_name = basename($_FILES['cv']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if ($file_ext !== 'pdf') {
            $errors[] = "Only PDF files are allowed.";
        }
        if ($_FILES['cv']['size'] > 2 * 1024 * 1024) {
            $errors[] = "File size exceeds 2MB.";
        }

        if (empty($errors)) {
            $upload_dir = __DIR__ . "/../../uploads/editors_cv/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $unique_name = uniqid("cv_") . "." . $file_ext;
            $destination = $upload_dir . $unique_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                $cv_path = "uploads/editors_cv/" . $unique_name;
            } else {
                $errors[] = "Error uploading CV file.";
            }
        }
    }

    // If errors, redirect back
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header("Location: editor_reg.php");
        exit();
    }

    // Prepare form data
    $editor_data = [
        'title' => $_POST['title'],
        'paper_name' => $_POST['paper_name'],
        'co_author' => $_POST['co_author'] ?? '',
        'user_id' => $_POST['user_id'],
        'telephone' => $_POST['telephone'],
        'degree' => $_POST['degree'],
        'address' => $_POST['address'],
        'gender' => $_POST['gender'],
        'editor_type' => $_POST['editor_type'] ?? '',
        'editor_board' => $_POST['editor_board'],
        'editor_experience' => $_POST['editor_experience'],
        'editor_payment_type' => $_POST['editor_payment_type'],
        'position' => $_POST['position'] ?? '',
        'institution' => $_POST['institution'] ?? '',
        'department' => $_POST['department'] ?? '',
        'street_address'=>$_POST['street_address'] ?? '',
        'city'=>$_POST['city'] ?? '',
        'state'=>$_POST['state'] ?? '',
        'zip_code'=>$_POST['zip_code'] ?? '',
        'country'=>$_POST['country'] ?? '',
        'editor_account_holder' => ($_POST['editor_payment_type'] == 'paid') ? $_POST['editor_account_holder'] : '',
        'editor_bank_name' => ($_POST['editor_payment_type'] == 'paid') ? $_POST['editor_bank_name'] : '',
        'editor_account_number' => ($_POST['editor_payment_type'] == 'paid') ? $_POST['editor_account_number'] : '',
        'editor_ifsc' => ($_POST['editor_payment_type'] == 'paid') ? $_POST['editor_ifsc'] : '',
        'editor_branch_name' => ($_POST['editor_payment_type'] == 'paid') ? $_POST['editor_branch_name'] : '',
        'editor_bank_country' => ($_POST['editor_payment_type'] == 'paid') ? $_POST['editor_bank_country'] : '',
        'cv_path' => $cv_path
    ];

    $insert_result = insertEditor($editor_data);

    if ($insert_result) {
        header("Location: editor_login.php?success=true");
        exit();
    } else {
        $_SESSION['errors'] = ["Something went wrong. Try again."];
        $_SESSION['old_input'] = $_POST;
        header("Location: editor_reg.php");
        exit();
    }
}
?>

