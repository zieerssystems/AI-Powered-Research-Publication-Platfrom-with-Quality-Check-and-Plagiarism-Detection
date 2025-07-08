<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reviewer_id = $_POST["reviewer_id"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $telephone = $_POST["telephone"];
    $personal_address = $_POST["address"];
    $reviewer_type = $_POST["reviewer_type"] ?? '';

    $institution = $department = $position = $street_address = $city = $state = $zip_code = $country = NULL;

    if ($reviewer_type === "Affiliated") {
        $institution = $_POST["institution"] ?? NULL;
        $department = $_POST["department"] ?? NULL;
        $position = $_POST["position"] ?? NULL;
        $street_address = $_POST["street_address"] ?? NULL;
        $city = $_POST["city"] ?? NULL;
        $state = $_POST["state"] ?? NULL;
        $zip_code = $_POST["zip_code"] ?? NULL;
        $country = $_POST["country"] ?? NULL;
    }

    $user_id = getUserIdByReviewerId($conn, $reviewer_id);

    if ($user_id) {
        updateUserDetails($conn, $user_id, $first_name, $last_name, $email);
        updateReviewerDetails(
            $conn, $reviewer_id, $telephone, $personal_address, $reviewer_type,
            $institution, $department, $position, $street_address,
            $city, $state, $zip_code, $country
        );
        $_SESSION["success_message"] = "Reviewer profile updated successfully!";
    } else {
        $_SESSION["error_message"] = "Reviewer not found!";
    }

    $conn->close();
    header("Location: update_profile.php");
    exit();
}
?>
