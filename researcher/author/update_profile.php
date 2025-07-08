<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

// Ensure the reviewer is logged in
if (!isset($_SESSION['reviewer_id'])) {
    header("Location: reviewer_login.php");
    exit();
}

$reviewer_id = $_SESSION['reviewer_id'];

// 🔹 Call function to get reviewer details
$reviewer = getReviewer($conn, $reviewer_id);
// Fetch subjects for journal request
$subjects = getPrimarySubjects($conn);

// Handle NULL values safely
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

        function fetchJournals(subject) {
            if (subject === "") {
                document.getElementById("journal_id").innerHTML = "<option value=''>Select Journal</option>";
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "fetch_journals_dash.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("journal_id").innerHTML = xhr.responseText;
                }
            };

            xhr.send("primary_subject=" + encodeURIComponent(subject));
        }
    </script>
</head>
<body>

<div class="container mt-4">
    <h2>My Account</h2>
    <button class="btn btn-primary" onclick="toggleEditForm()">Edit Profile</button>
    <button class="btn btn-secondary" onclick="window.history.back()">Back</button>
     <a href="../../profile.php">👤 change role</a>

    <!-- Profile Details -->
    <div id="profile">
        <p><strong>Name:</strong> <?php echo safe_html($reviewer['first_name'] . ' ' . $reviewer['last_name']); ?></p>
        <p><strong>Email:</strong> <?php echo safe_html($reviewer['email']); ?></p>
        <p><strong>Telephone:</strong> <?php echo safe_html($reviewer['telephone']); ?></p>
        <p><strong>Address:</strong> <?php echo safe_html($reviewer['address']); ?></p>
        <!-- <p><strong>Reviewer Type:</strong> <?php echo safe_html($reviewer['reviewer_type']); ?></p> -->
    </div>

    <!-- Edit Form -->
    <form id="editForm" method="POST" action="update_reviewer.php" style="display: none;">
        <input type="hidden" name="reviewer_id" value="<?php echo $reviewer_id; ?>">
        
        <label>First Name:</label>
        <input type="text" name="first_name" class="form-control" value="<?php echo safe_html($reviewer['first_name']); ?>" required>

        <label>Last Name:</label>
        <input type="text" name="last_name" class="form-control" value="<?php echo safe_html($reviewer['last_name']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" class="form-control" value="<?php echo safe_html($reviewer['email']); ?>" required>

        <label>Telephone:</label>
        <input type="text" name="telephone" class="form-control" value="<?php echo safe_html($reviewer['telephone']); ?>" required>

        <label>Address:</label>
        <input type="text" name="address" class="form-control" value="<?php echo safe_html($reviewer['address']); ?>">

        <button type="submit" class="btn btn-success mt-3">Update Profile</button>
        <button type="button" class="btn btn-danger mt-3" onclick="cancelEdit()">Cancel</button>
    </form>

    <!-- Journal Request Section -->
    <h2 class="mt-5">Request Journal Access</h2>
    <form method="POST" action="request_journal_access.php">
        <select name="primary_subject" class="form-control" required onchange="fetchJournals(this.value)">
            <option value="">Select Primary Subject</option>
           <?php foreach ($subjects as $row): ?>
    <option value="<?php echo safe_html($row['primary_subject']); ?>">
        <?php echo safe_html($row['primary_subject']); ?>
    </option>
<?php endforeach; ?>

        </select>

        <select name="journal_id" id="journal_id" class="form-control mt-3" required>
            <option value="">Select Journal</option>
        </select>

        <button type="submit" name="request_journal" class="btn btn-success mt-3">Request Access</button>
    </form>
</div>
<?php if (isset($_SESSION["success_message"])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION["success_message"]; unset($_SESSION["success_message"]); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION["error_message"])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION["error_message"]; unset($_SESSION["error_message"]); ?>
    </div>
<?php endif; ?>

</body>
</html>
