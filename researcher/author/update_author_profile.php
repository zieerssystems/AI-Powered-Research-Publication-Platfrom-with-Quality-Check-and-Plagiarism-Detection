<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");


if (!isset($_SESSION['author_id'])) {
    header("Location: author_dash_login.php");
    exit();
}

$author_id = $_SESSION['author_id'];
$author = getAuthorDetails_profile($conn, $author_id);

// Escape helper
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
    </script>
</head>
<style>
    .btn-back {
        display: inline-block;
        margin: 20px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #4b6cb7, #182848);
        color: white;
        border: none;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: linear-gradient(135deg, #182848, #4b6cb7);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }
</style>
<body>
<a href="author_dashboard.php" class="btn-back">← Back to Author Dashboard</a>
<div class="container mt-4">
    <h2>My Account</h2>
    <button class="btn btn-primary" onclick="toggleEditForm()">Edit Profile</button>
    <button class="btn btn-secondary" onclick="window.history.back()">Back</button>
   <a href="../../profile.php">👤 change role</a>


    <!-- Profile Details -->
    <div id="profile">
        <p><strong>Name:</strong> <?php echo safe_html($author['first_name'] . ' ' . $author['last_name']); ?></p>
        <p><strong>Email:</strong> <?php echo safe_html($author['email']); ?></p>
        <p><strong>Telephone:</strong> <?php echo safe_html($author['telephone']); ?></p>
        <p><strong>Address:</strong> <?php echo safe_html($author['address']); ?></p>
        <p><strong>Researcher Type:</strong> <?php echo safe_html($author['researcher_type']); ?></p>
    </div>

    <!-- Edit Form -->
    <form id="editForm" method="POST" action="update_author.php" style="display: none;">
        <input type="hidden" name="author_id" value="<?php echo $author_id; ?>">
    
        <label>First Name:</label>
        <input type="text" name="first_name" class="form-control" value="<?php echo safe_html($author['first_name']); ?>" required>

        <label>Last Name:</label>
        <input type="text" name="last_name" class="form-control" value="<?php echo safe_html($author['last_name']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" class="form-control" value="<?php echo safe_html($author['email']); ?>" required>

        <label>Telephone:</label>
        <input type="text" name="telephone" class="form-control" value="<?php echo safe_html($author['telephone']); ?>" required>

        <label>Address:</label>
        <input type="text" name="address" class="form-control" value="<?php echo safe_html($author['address']); ?>">

         <label>Researcher Type:</label>
        <input type="text" name="researcher_type" class="form-control" value="<?php echo safe_html($author['researcher_type']); ?>">

        <button type="submit" class="btn btn-success mt-3">Update Profile</button>
        <button type="button" class="btn btn-danger mt-3" onclick="cancelEdit()">Cancel</button>
    </form>
</div>

</body>
</html>
