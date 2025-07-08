<?php
include(__DIR__ . "/../include/db_connect.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $journal_name = $_POST['journal_name'];
    $journal_abbreviation = $_POST['journal_abbreviation'];
    $editorial_team_id = !empty($_POST['editorial_team_id']) ? $_POST['editorial_team_id'] : null;
    $primary_subject = $_POST['subject'];
    $secondary_subject = $_POST['secondary_subject'];
    $description = $_POST['description'];
    $publisher = $_POST['publisher'];
    $issn = !empty($_POST['issn']) ? $_POST['issn'] : null;
    $country = $_POST['country'];
    $publication_frequency = $_POST['publication_frequency'];
    $indexing_info = !empty($_POST['indexing_info']) ? $_POST['indexing_info'] : null;
    $scope = $_POST['scope'];
    $review_process = $_POST['review_process'];
    $impact_factor = $_POST['impact_factor'] !== '' ? floatval($_POST['impact_factor']) : null;
    $citescore = $_POST['citescore'] !== '' ? floatval($_POST['citescore']) : null;
    $acceptance_rate = $_POST['acceptance_rate'] !== '' ? floatval($_POST['acceptance_rate']) : null;
    $access_type = $_POST['access_type'];
    $submission_status = $_POST['submission_status'];
    $author_payment_required = $_POST['author_payment_required'];
    $reader_payment_required = $_POST['reader_payment_required'];
    $author_apc_amount = $_POST['author_apc_amount'] !== '' ? floatval($_POST['author_apc_amount']) : 0.0;
    $reader_fee_amount = $_POST['reader_fee_amount'] !== '' ? floatval($_POST['reader_fee_amount']) : 0.0;
    $payment_currency = $_POST['payment_currency'];
    $payment_notes = $_POST['payment_notes'];
    $payment_link = !empty($_POST['payment_link']) ? $_POST['payment_link'] : "https://payment.example.com/pay?id=" . uniqid();
    $author_guidelines = $_POST['author_guidelines_text'] ?? null;
    $keywords = mysqli_real_escape_string($conn, $_POST['keywords']);


    // Handle image upload
    $journal_image = null;
    if (!empty($_FILES["journal_image"]["name"])) {
        $target_dir = "uploads/";
        $unique_name = uniqid() . "_" . basename($_FILES["journal_image"]["name"]);
        $journal_image = $target_dir . $unique_name;

        if (!move_uploaded_file($_FILES["journal_image"]["tmp_name"], $journal_image)) {
            echo "Image upload failed!";
            exit();
        }
    }

    // Check for duplicates using the new function
    $check_result = checkForDuplicates($conn, $journal_name, $journal_abbreviation, $issn);

    if ($check_result->num_rows > 0) {
        echo "Error: Journal name, abbreviation, or ISSN already exists!";
        exit();
    }

    // Insert journal into database using the new function
    if (InsertJournal($conn, $journal_name, $journal_abbreviation, $editorial_team_id, $primary_subject, $secondary_subject, $description, $publisher, $issn, $country, $publication_frequency, $indexing_info, $scope, $review_process, $impact_factor, $citescore, $acceptance_rate, $access_type, $submission_status, $author_guidelines, $journal_image, $author_payment_required, $reader_payment_required, $author_apc_amount, $reader_fee_amount, $payment_currency, $payment_link, $payment_notes, $keywords)) {
        echo "success";
    } else {
        echo "Error: Journal insertion failed.";
    }

    $conn->close();
}

?>
