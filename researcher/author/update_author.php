<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");
include(__DIR__ . "/../../include/author_profile_functions.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $author_id = $_POST["author_id"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $telephone = $_POST["telephone"];
    $address = $_POST["address"];
    $researcher_type = $_POST["researcher_type"];

    // Default to null for institutional details
    $institution = $department = $position = $street_address = $city = $state = $zip_code = $country = null;

    if ($researcher_type === "Affiliated") {
        $institution = $_POST["institution"] ?? null;
        $department = $_POST["department"] ?? null;
        $position = $_POST["position"] ?? null;
        $street_address = $_POST["street_address"] ?? null;
        $city = $_POST["city"] ?? null;
        $state = $_POST["state"] ?? null;
        $zip_code = $_POST["zip_code"] ?? null;
        $country = $_POST["country"] ?? null;
    }

    // Prepare data array
    $authorData = [
        'author_id' => $author_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'telephone' => $telephone,
        'address' => $address,
        'researcher_type' => $researcher_type,
        'institution' => $institution,
        'department' => $department,
        'position' => $position,
        'street_address' => $street_address,
        'city' => $city,
        'state' => $state,
        'zip_code' => $zip_code,
        'country' => $country,
    ];

    // Execute update using function
    if (updateAuthorProfile($conn, $authorData)) {
        $_SESSION["success_message"] = "Author profile updated successfully!";
    } else {
        $_SESSION["error_message"] = "Failed to update author profile. Please try again.";
    }

    $conn->close();
    header("Location: update_author_profile.php");
    exit();
}
?>
