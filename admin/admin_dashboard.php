<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1){
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}
include("process_admin_dashboard.php");

$show_notification = ($pending_reviews > 0 && !isset($_SESSION['seen_review_notification']));
$show_pending_notification = ($pending_reviews > 0 && !isset($_SESSION['seen_pending']));
$show_accepted_notification = ($accepted_papers > 0 && !isset($_SESSION['seen_accepted']));
$show_rejected_notification = ($rejected_papers > 0 && !isset($_SESSION['seen_rejected']));


// $editor_contract_query = "SELECT COUNT(*) FROM editors WHERE contract_status IN ('pending_verification', 'reupload')";
// $editor_contract_result = $conn->query($editor_contract_query);
// $editor_contract_count = $editor_contract_result->fetch_row()[0];

// // Check contract status for reviewers (only interested in pending or reupload status)
// $reviewer_contract_query = "SELECT COUNT(*) FROM reviewers WHERE contract_status IN ('pending_verification', 'reupload')";
// $reviewer_contract_result = $conn->query($reviewer_contract_query);
// $reviewer_contract_count = $reviewer_contract_result->fetch_row()[0];
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
            display: flex;
        }

        .sidebar {
            width: 240px;
            background: #002147;
            color: white;
            position: fixed;
            height: 100vh;
            padding-top: 30px;
            transition: 0.3s;
            z-index: 999;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .sidebar a i { margin-right: 10px; }

        .main-content {
            margin-left: 240px;
            padding: 20px;
            flex-grow: 1;
            width: calc(100% - 240px);
        }

        .header {
            background: #002147;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header a {
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s;
            position: relative;
        }

        .card:hover { transform: translateY(-5px); }

        .card h3 { font-size: 18px; color: #333; }
        .card p { font-size: 24px; font-weight: bold; margin-top: 10px; color: #007bff; }

        a.card-link { text-decoration: none; }

        /* Hamburger & Responsive */
        .hamburger {
            display: none;
            font-size: 24px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                width: 240px;
                z-index: 1000;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .hamburger {
                display: block;
            }
        }

        .notification {
            position: absolute;
            top: 10px;
            right: 15px;
            background: red;
            color: white;
            padding: 3px 7px;
            font-size: 12px;
            border-radius: 50%;
            font-weight: bold;
        }
        .badge {
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 3px 6px;
        font-size: 14px;
        position: absolute;
        top: 5px;
        right: 5px;
    }
    .sidebar a {
        position: relative;
    }
    </style>
</head>
<body>
<script>
    // Only refresh once using sessionStorage
    window.addEventListener('load', function () {
        if (!sessionStorage.getItem('refreshed')) {
            sessionStorage.setItem('refreshed', 'true');
            location.reload();
        }
    });
</script>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Admin</h2>
        <a href="journal-creation.php"><i class="fas fa-book"></i> <span>Create Journal</span></a>
        <!-- <a href="journal-approval.php"><i class="fas fa-check-circle"></i> <span>Review Journals</span></a> -->
        <a href="review_paper.php?filter=all"><i class="fas fa-file-alt"></i> <span>Review Papers</span></a>
        <a href="reviewer_details.php"><i class="fas fa-user-check"></i> <span>Reviewer Details</span></a>
        <a href="editor_details.php"><i class="fas fa-user-edit"></i> <span>Editor Details</span></a><!-- Reviewer Contracts -->
    <a href="reviewer_contracts.php">
        <i class="fas fa-file-contract"></i> <span>Reviewer Contracts</span>
        <?php if ($reviewer_contract_count > 0) { ?>
            <span class="badge">!</span> <!-- Red badge for pending/reupload contracts -->
        <?php } ?>
    </a>

    <!-- Editor Contracts -->
    <a href="editor_contracts.php">
        <i class="fas fa-file-signature"></i> <span>Editor Contracts</span>
        <?php if ($editor_contract_count > 0) { ?>
            <span class="badge">!</span> <!-- Red badge for pending/reupload contracts -->
        <?php } ?>
    </a>
        <a href="new_request_reviewer.php"><i class="fas fa-user-plus"></i> <span>Reviewer Requests</span></a>
        <a href="assign_editor.php"><i class="fas fa-tasks"></i> <span>Assign Editor</span></a>
        <a href="admin_editorial_teams.php"><i class="fas fa-tasks"></i> <span> Editorial Team</span></a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <span class="hamburger" onclick="toggleSidebar()">&#9776;</span>
            <h2>Admin Dashboard</h2>
            <a href="admin_logout.php" onclick="sessionStorage.removeItem('refreshed')">Logout</a>
        </div>

        <div class="dashboard">
            <a href="review_paper.php?filter=Pending" class="card-link">
                <div class="card">
                    <h3>Papers for Review</h3>
                    <p><?php echo $pending_reviews; ?></p>
                    <?php if ($show_pending_notification): ?>
                        <span class="notification">!</span>
                    <?php endif; ?>
                </div>
            </a>

            <a href="review_paper.php?filter=Accepted (Final Decision)" class="card-link">
                <div class="card">
                    <h3>Accepted Papers</h3>
                    <p><?php echo $accepted_papers; ?></p>
                    <?php if ($show_accepted_notification): ?>
                        <span class="notification" style="background: green;">✔</span>
                    <?php endif; ?>
                </div>
            </a>

            <a href="review_paper.php?filter=Rejected (Post-Review)" class="card-link">
                <div class="card">
                    <h3>Rejected Papers</h3>
                    <p><?php echo $rejected_papers; ?></p>
                    <?php if ($show_rejected_notification): ?>
                        <span class="notification" style="background: darkred;">×</span>
                    <?php endif; ?>
                </div>
            </a>

            <a href="published_papers.php?" class="card-link">
                <div class="card">
                    <h3>published Papers</h3>
                    <p><?php echo $published_count; ?></p>
                </div>
            </a>

            <?php if ($pending_reviewer_applications > 0): ?>
                <a href="reviewer_details.php" class="card-link">
                    <div class="card">
                        <h3>Pending Applications</h3>
                        <p><?php echo $pending_reviewer_applications; ?></p>
                        <span class="notification">!</span>
                    </div>
                </a>
            <?php endif; ?>

            <?php if ($pending_editor_applications > 0): ?>
                <a href="editor_details.php" class="card-link">
                    <div class="card">
                        <h3>Editor Applications</h3>
                        <p><?php echo $pending_editor_applications; ?></p>
                        <span class="notification">!</span>
                    </div>
                </a>
            <?php endif; ?>

            <a href="view-journal.php" class="card-link">
                <div class="card">
                    <h3>Total Journals</h3>
                    <p><?php echo $total_journals; ?></p>
                </div>
            </a>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
    </script>
</body>
</html>
