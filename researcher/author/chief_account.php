<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");


if (!isset($_SESSION['chief_editor_id'])) {
    header("Location: editor_login.php");
    exit();
}

$editor_id = $_SESSION['chief_editor_id'];

if (isset($_SESSION["success_message"])) {
    echo "<div class='alert alert-success'>{$_SESSION["success_message"]}</div>";
    unset($_SESSION["success_message"]);
}

// ✅ Use the function to fetch editor details
$editor = getEditorDetails($conn, $editor_id);

// ✅ Handle NULL values safely
function safe_html($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
    </div>

    <form id="editForm" method="POST" action="update_chief_editor.php" style="display: none;">
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

    <!-- Institution Details -->
    <div id="institutionDetails" style="display: <?php echo ($editor['editor_type'] == 'Affiliated') ? 'block' : 'none'; ?>;">
        <label>Institution:</label>
        <input type="text" name="institution" class="form-control" value="<?php echo safe_html($editor['institution']); ?>">

        <label>Department:</label>
        <input type="text" name="department" class="form-control" value="<?php echo safe_html($editor['department']); ?>">

        <label>Position:</label>
        <input type="text" name="position" class="form-control" value="<?php echo safe_html($editor['position']); ?>">

        <label>Street Address:</label>
        <input type="text" name="street_address" class="form-control" value="<?php echo safe_html($editor['street_address']); ?>">

        <label>City:</label>
        <input type="text" name="city" class="form-control" value="<?php echo safe_html($editor['city']); ?>">

        <label>State:</label>
        <input type="text" name="state" class="form-control" value="<?php echo safe_html($editor['state']); ?>">

        <label>Zip Code:</label>
        <input type="text" name="zip_code" class="form-control" value="<?php echo safe_html($editor['zip_code']); ?>">

        <label>Country:</label>
        <input type="text" name="country" class="form-control" value="<?php echo safe_html($editor['country']); ?>">
    </div>
     <button type="submit" class="btn btn-success mt-3">✅ Update</button>
    <button type="button" class="btn btn-danger mt-3" onclick="cancelEdit()">❌ Cancel</button>
</form>

<script> 
function toggleInstitutionFields() {
    var editorType = document.getElementById('editor_type').value;
    var institutionDetails = document.getElementById('institutionDetails');
    institutionDetails.style.display = editorType === 'Affiliated' ? 'block' : 'none';
}
function cancelEdit() {
    document.getElementById("editForm").style.display = "none";
}
</script>

</div>
</body>
</html>
