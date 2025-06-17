<?php 
session_start();
include(__DIR__ . "/../../include/db_connect.php");


// Ensure the editor is logged in
if (!isset($_SESSION['chief_editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

// Pre-fill editor form
if (isset($_GET['editor_id'])) {
    $editor_id = $_GET['editor_id'];
    $editor = getEditorDetails3($conn, $editor_id);

    if (!$editor) {
        echo "Editor not found.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editor_id = $_POST['editor_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $address = $_POST['address'];
    $editor_type = $_POST['editor_type'];
    $institution = $_POST['institution'] ?? '';
    $department = $_POST['department'] ?? '';
    $position = $_POST['position'] ?? '';
    $street_address = $_POST['street_address'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $zip_code = $_POST['zip_code'] ?? '';
    $country = $_POST['country'] ?? '';

    $user_id = getEditorUserId3($conn, $editor_id);
    if (!$user_id) {
        echo "Editor not found.";
        exit();
    }

    updateUserDetails3($conn, $user_id, $first_name, $last_name, $email);
    updateEditorDetails3(
        $conn, $editor_id, $telephone, $address, $editor_type,
        $institution, $department, $position,
        $street_address, $city, $state, $zip_code, $country
    );

    echo "<script>
        alert('Profile updated successfully.');
        window.location.href = 'chief_account.php';
    </script>";
    exit();
}
?>
