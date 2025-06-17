<?php  
include(__DIR__ . "/../../include/db_connect.php");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'user_id'          => $_POST['user_id'],
        'title'            => trim($_POST['title']),
        'telephone'        => $_POST['telephone'] ?? null,
        'degree'           => $_POST['degree'] ?? null,
        'address'          => $_POST['address'] ?? null,
        'gender'           => trim($_POST['gender']),
        'researcher_type'  => trim($_POST['researcher_type']),
        'position'         => $_POST['position'] ?? null,
        'institution'      => $_POST['institution'] ?? null,
        'department'       => $_POST['department'] ?? null,
        'street_address'   => $_POST['street_address'] ?? null,
        'city'             => $_POST['city'] ?? null,
        'state'            => $_POST['state'] ?? null,
        'zip_code'         => $_POST['zip_code'] ?? null,
        'country'          => trim($_POST['country'])
    ];

    if (!$data['user_id'] || !$data['title']) {
        die("Error: All required fields must be filled.");
    }

    $journal_ids = isset($_POST['journal_ids']) ? $_POST['journal_ids'] : [];
    if (empty($journal_ids) && isset($_POST['journal_id'])) {
        $journal_ids = [intval($_POST['journal_id'])];
    }

    // Check if user already exists
    $checkStmt = $conn->prepare("SELECT id FROM author WHERE user_id = ?");
    $checkStmt->bind_param("s", $data['user_id']);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        echo "<script>alert('This user ID is already registered.'); window.history.back();</script>";
        exit();
    }
    $checkStmt->close();

    // Insert author and assign journals
    $author_id = insert_process_author($conn, $data);
    if ($author_id) {
        if (!empty($journal_ids)) {
            assignToAuthor($conn, $author_id, $journal_ids);
        }

        $journal_id_for_redirect = $journal_ids[0] ?? 0;
        echo "<script>
            alert('Registration successful!');
            window.location.href = 'submit-article.php?journal_id={$journal_id_for_redirect}';
        </script>";
        exit();
    } else {
        echo "<script>alert('Error occurred during registration. Please try again.'); window.history.back();</script>";
        exit();
    }
}
