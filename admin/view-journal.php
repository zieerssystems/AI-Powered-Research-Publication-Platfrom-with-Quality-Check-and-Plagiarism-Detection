<?php
include(__DIR__ . "/../include/db_connect.php");   

// Check if the user is an admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='../admin_login.php';</script>";
    exit();
}

// Fetch journals using the function from db_connect.php
$result = process_view_journal($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Journals - Admin Panel</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e3f2fd, #bbdefb); 
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
        }
        .container {
            max-width: 1200px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
            margin: 0;
        }
        .action-btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 15px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: 0.3s ease-in-out;
        }
        .back-btn {
            background:rgb(101, 38, 194);
        }
        .back-btn:hover {
            background:rgb(82, 70, 212);
        }
        .add-btn {
            background: #28a745;
        }
        .add-btn:hover {
            background: #218838;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            background: white;
            margin-top: 20px;
        }
        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: linear-gradient(to bottom, #2c3e50, #3498db);
            color: white;
        }
        td {
            background: #f9f9f9;
        }
        td img {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            object-fit: cover;
            display: block;
            margin: auto;
        }
        .edit-btn, .delete-btn {
            padding: 5px 5px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            color: white;
            transition: 0.3s;
            margin: 5px;
            display: inline-block;
        }
        .edit-btn {
            background: #ffc107;
        }
        .edit-btn:hover {
            background: #e0a800;
        }
        .delete-btn {
            background: #dc3545;
        }
        .delete-btn:hover {
            background: #c82333;
        }
        .table-container {
    max-height: 400px; /* Adjust height as needed */
    overflow-y: auto;
    margin-top: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
}

    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <a href="admin_dashboard.php" class="action-btn back-btn">⬅ Back to Dashboard</a>
        <h2>Journals List</h2>
        <a href="journal-creation.php" class="action-btn add-btn">➕ Add New Journal</a>
    </div>
    <div class="table-container">
    <table>
        <tr>
            <th>ID</th>
            <th>Journal Name</th>
            <th>Primary Subject</th>
            <th>Secondary Subject</th>
            <th>Publisher</th>
            <th>ISSN</th>
            <th>Access Type</th>
            <th>Submission Status</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr id="journal-<?= $row['id'] ?>">
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['journal_name']) ?></td>
                <td><?= htmlspecialchars($row['primary_subject']) ?></td>
                <td><?= htmlspecialchars($row['secondary_subject']) ?></td>
                <td><?= htmlspecialchars($row['publisher']) ?></td>
                <td><?= htmlspecialchars($row['issn']?? '') ?></td>
                <td><?= htmlspecialchars($row['access_type']) ?></td>
                <td><?= htmlspecialchars($row['submission_status']) ?></td>
                <td>
                    <?php 
                        if (!empty($row['journal_image'])) {
                            $imagePath = htmlspecialchars($row['journal_image']);
                            echo "<img src='$imagePath' alt='Journal Image'>";
                        } else {
                            echo '<span>No Image Available</span>';
                        }
                    ?>
                </td>
                <td>
                    <a href="edit-journal.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                    <a href="#" class="delete-btn" onclick="deleteJournal(event, <?= $row['id'] ?>)">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    </div>
</div>

<script>
function deleteJournal(event, journalId) {
    event.preventDefault(); 
    if (confirm("Are you sure you want to delete this journal?")) {
        fetch(`delete-journal.php?id=${journalId}`, { method: "GET" })
        .then(response => response.text())
        .then(data => {
            alert(data.trim()); 
            document.getElementById(`journal-${journalId}`).remove(); 
        })
        .catch(error => alert("Error deleting journal"));
    }
}
</script>

</body>
</html>

<?php $conn->close(); ?>
