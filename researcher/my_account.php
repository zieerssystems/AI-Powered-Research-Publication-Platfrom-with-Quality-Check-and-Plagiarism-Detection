<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

// Ensure the editor is logged in
if (!isset($_SESSION['editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$editor_id = $_SESSION['editor_id'];

// Fetch editor details using the function from db_connect.php
$editor = getEditorDetails($conn, $editor_id);

// Fetch previous payment request, if any
// $payment_request = getPreviousPaymentRequest($conn, $editor_id);

// Handle NULL values to avoid warnings
function safe_html($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function toggleEditForm() {
            document.getElementById("profile").style.display = "none";
            document.getElementById("editForm").style.display = "block";
        }

        function cancelEdit() {
            document.getElementById("profile").style.display = "block";
            document.getElementById("editForm").style.display = "none";
        }

        function toggleInstitutionFields() {
            let editorType = document.getElementById("editor_type").value;
            let institutionDetails = document.getElementById("institutionDetails");
            let requiredFields = ["institution", "department", "position", "street_address", "city", "state", "zip_code", "country"];

            if (editorType === "Affiliated") {
                institutionDetails.style.display = "block";
                requiredFields.forEach(id => document.getElementById(id).setAttribute("required", "required"));
            } else {
                institutionDetails.style.display = "none";
                requiredFields.forEach(id => document.getElementById(id).removeAttribute("required"));
            }
        }
    </script>
</head>
<body>
<div class="container mt-4">
    <h2>👤 My Account</h2>
    <button class="btn btn-primary" onclick="toggleEditForm()">✏ Edit Profile</button>
    <button class="btn btn-secondary" onclick="window.history.back()">🔙 Back</button>
     <a href="../../profile.php">👤 change role</a>

    <div id="profile">
        <p><strong>Name:</strong> <?php echo safe_html($editor['first_name'] . ' ' . $editor['middle_name'] . ' ' . $editor['last_name']); ?></p>
        <p><strong>Email:</strong> <?php echo safe_html($editor['email']); ?></p>
        <p><strong>Telephone:</strong> <?php echo safe_html($editor['telephone']); ?></p>
        <p><strong>Degree:</strong> <?php echo safe_html($editor['degree']); ?></p>
        <p><strong>Gender:</strong> <?php echo safe_html($editor['gender']); ?></p>
        <p><strong>Address:</strong> <?php echo safe_html($editor['address']); ?></p>
        <p><strong>Editor Type:</strong> <?php echo safe_html($editor['editor_type']); ?></p>
        <p><strong>Payment Status:</strong>
            <?php
                echo (isset($editor['editor_payment_type']) && strtolower($editor['editor_payment_type']) === 'paid')
                    ? 'Paid'
                    : 'Unpaid';
            ?>
        </p>
        <p><strong>Last Login:</strong> <?php echo safe_html($editor['last_login']); ?></p>

        <!-- Show Institution Details only if editor is affiliated -->
        <?php if ($editor['editor_type'] === 'Affiliated'): ?>
            <p><strong>Institution:</strong> <?php echo safe_html($editor['institution']); ?></p>
            <p><strong>Department:</strong> <?php echo safe_html($editor['department']); ?></p>
            <p><strong>Position:</strong> <?php echo safe_html($editor['position']); ?></p>
            <p><strong>Street Address:</strong> <?php echo safe_html($editor['street_address']); ?></p>
            <p><strong>City:</strong> <?php echo safe_html($editor['city']); ?></p>
            <p><strong>State:</strong> <?php echo safe_html($editor['state']); ?></p>
            <p><strong>Zip Code:</strong> <?php echo safe_html($editor['zip_code']); ?></p>
            <p><strong>Country:</strong> <?php echo safe_html($editor['country']); ?></p>
        <?php endif; ?>
    </div>

    <!-- Edit Form -->
    <form id="editForm" method="POST" action="update_editor.php" style="display: none;">
        <input type="hidden" name="editor_id" value="<?php echo $editor_id; ?>">

        <label>First Name:</label>
        <input type="text" name="first_name" class="form-control" value="<?php echo safe_html($editor['first_name']); ?>" required>

        <label>Last Name:</label>
        <input type="text" name="last_name" class="form-control" value="<?php echo safe_html($editor['last_name']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" class="form-control" value="<?php echo safe_html($editor['email']); ?>" required>

        <label>Telephone:</label>
        <input type="text" name="telephone" class="form-control" value="<?php echo safe_html($editor['telephone']); ?>" required>

        <label>Address:</label>
        <input type="text" name="address" class="form-control" value="<?php echo safe_html($editor['address']); ?>">

        <label>Editor Type:</label>
        <select name="editor_type" id="editor_type" class="form-control" onchange="toggleInstitutionFields()">
            <option value="Individual" <?php echo ($editor['editor_type'] == 'Individual') ? 'selected' : ''; ?>>Individual</option>
            <option value="Affiliated" <?php echo ($editor['editor_type'] == 'Affiliated') ? 'selected' : ''; ?>>Affiliated</option>
        </select>

        <!-- Institution Details (Hidden when "Individual" is selected) -->
        <div id="institutionDetails" style="display: <?php echo ($editor['editor_type'] == 'Affiliated') ? 'block' : 'none'; ?>;">
            <label>Institution:</label>
            <input type="text" name="institution" id="institution" class="form-control" value="<?php echo safe_html($editor['institution']); ?>">

            <label>Department:</label>
            <input type="text" name="department" id="department" class="form-control" value="<?php echo safe_html($editor['department']); ?>">

            <label>Position:</label>
            <input type="text" name="position" id="position" class="form-control" value="<?php echo safe_html($editor['position']); ?>">

            <label>Street Address:</label>
            <input type="text" name="street_address" id="street_address" class="form-control" value="<?php echo safe_html($editor['street_address']); ?>">

            <label>City:</label>
            <input type="text" name="city" id="city" class="form-control" value="<?php echo safe_html($editor['city']); ?>">

            <label>State:</label>
            <input type="text" name="state" id="state" class="form-control" value="<?php echo safe_html($editor['state']); ?>">

            <label>Zip Code:</label>
            <input type="text" name="zip_code" id="zip_code" class="form-control" value="<?php echo safe_html($editor['zip_code']); ?>">

            <label>Country:</label>
            <input type="text" name="country" id="country" class="form-control" value="<?php echo safe_html($editor['country']); ?>">
        </div>
        <button type="submit" class="btn btn-success mt-3">✅ Update</button>
        <button type="button" class="btn btn-danger mt-3" onclick="cancelEdit()">❌ Cancel</button>
    </form>
</div>
</body>
</html>
