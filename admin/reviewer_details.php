<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}

include("process_reviewer_details.php");
$total_reviewers = $approved_reviewers + $pending_reviewers;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviewer Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <style>
    body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(to right, #f3f4f6, #e0f7fa);
}

.container {
    max-width: 96%;
    margin: 30px auto;
    background: #fff;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #263238;
    font-weight: 600;
}

.stats-cards {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 16px;
    margin-bottom: 20px;
}

.card {
    width: 160px;
    padding: 14px 10px;
    background: linear-gradient(145deg, #e1f5fe, #ffffff);
    color: #212121;
    text-align: center;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.25s ease-in-out;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
}

.card h3 {
    font-size: 16px;
    margin-bottom: 6px;
    color: #0277bd;
}

.card p {
    font-size: 22px;
    font-weight: bold;
}

.filter-btns {
    text-align: center;
    margin-bottom: 20px;
}

.filter-btns button {
    padding: 8px 18px;
    margin: 0 6px;
    border: none;
    border-radius: 6px;
    background: #0277bd;
    color: white;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.filter-btns button:hover {
    background-color: #01579b;
}

.reviewer-table-container {
    overflow-x: auto;
    margin-top: 10px;
}

.reviewer-table {
    width: 100%;
    border-collapse: collapse;
}

.reviewer-table th,
.reviewer-table td {
    padding: 10px;
    border: 1px solid #d7dee3;
    text-align: center;
    font-size: 13px;
}

.reviewer-table th {
    background: linear-gradient(to right, #81d4fa, #e1f5fe);
    color: #263238;
    font-weight: 600;
}

.btn {
    padding: 5px 10px;
    font-size: 12px;
    border-radius: 6px;
    text-decoration: none;
    color: white;
    margin: 2px;
    display: inline-block;
}

.approve-btn { background-color: #43a047; }
.reject-btn { background-color: #e53935; }
.delete-btn { background-color: #6d6d6d; }
.email-btn { background-color: #0288d1; }
.verify-btn { background-color: #fbc02d; color: #212121; }

.status-approved { color: #388e3c; font-weight: bold; }
.status-rejected { color: #d32f2f; font-weight: bold; }
.status-sent, .status-signed, .status-pending, .status-unknown {
    display: block;
    margin: 5px 0;
    color: #424242;
    font-weight: 500;
}

.back-btn {
    display: inline-block;
    margin-top: 25px;
    background: #0288d1;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    transition: background 0.2s ease;
}

.back-btn:hover {
    background-color: #0277bd;
}

@media screen and (max-width: 768px) {
    .stats-cards {
        flex-direction: column;
        align-items: center;
    }

    .reviewer-table th, .reviewer-table td {
        font-size: 11px;
        padding: 6px;
    }

    .card {
        width: 90%;
    }
}
</style>
</head>
<body>
<div class="container">
    <h2>Reviewer Details</h2>

    <!-- Statistics -->
    <div class="stats-cards">
        <div class="card" onclick="filterTable('all')">
            <h3>Total Reviewers</h3><p><?= $total_reviewers; ?></p>
        </div>
        <div class="card" onclick="filterTable('approved')">
            <h3>Approved Reviewers</h3><p><?= $approved_reviewers; ?></p>
        </div>
        <div class="card" onclick="filterTable('pending')">
            <h3>Pending Applications</h3><p><?= $pending_reviewers; ?></p>
        </div>
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <a href="admin_dashboard.php" class="btn" style="background: #336b87; padding: 10px 20px; border-radius: 8px; color: white;">⬅ Back to Dashboard</a>
    </div>

    <!-- Filter Buttons -->
    <div class="filter-btns">
        <button onclick="filterTable('all')">Show All</button>
    </div>

    <!-- Table -->
    <div class="reviewer-table-container">
        <table class="reviewer-table">
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Phone</th>
                    <th>Reviewer_Type</th><th>Experience</th><th>CV</th><th>Contract Status</th><th>Registration</th><th>Actions</th>
                </tr>
            </thead>
            <tbody id="reviewerTableBody">
            <?php while ($reviewer = $reviewers->fetch_assoc()): ?>
                <tr data-status="<?= htmlspecialchars($reviewer['registration_status']); ?>">
                    <td><?= htmlspecialchars($reviewer['id']); ?></td>
                    <td><?= htmlspecialchars($reviewer['first_name'] . " " . $reviewer['middle_name'] . " " . $reviewer['last_name']); ?></td>
                    <td><?= htmlspecialchars($reviewer['email']); ?></td>
                    <td><?= htmlspecialchars($reviewer['telephone']); ?></td>
                    <td><?= htmlspecialchars($reviewer['reviewer_type']); ?></td>
                    <td><?= htmlspecialchars($reviewer['experience']); ?></td>
                    <td>
                            <?php if (!empty($reviewer['cv_path'])): ?>
                                <a href="/my_publication_site/<?= htmlspecialchars($reviewer['cv_path']); ?>" target="_blank" class="btn email-btn">View</a>
                            <?php else: ?>
                                <span style="color: gray;">No CV</span>
                            <?php endif; ?>
                            </td>
                    <td>
                        <?php if ($reviewer['contract_status'] == 'not_sent'): ?>
                            <a href="send_email.php?id=<?= $reviewer['id']; ?>" class="btn email-btn">Send</a>
                        <?php elseif ($reviewer['contract_status'] == 'sent'): ?>
                            <span class="status-sent">📩 Sented</span>
                        <?php elseif ($reviewer['contract_status'] == 'pending_verification'): ?>
                            <span class="status-pending">⏳ Pending Verification</span>
                        <?php elseif ($reviewer['contract_status'] == 'signed'): ?>
                            <span class="status-signed">✅ Signed</span>
                        <?php else: ?>
                            ❓
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($reviewer['registration_status'] == 'approved'): ?>
                            <span class="status-approved">✅ Approved</span>
                        <?php elseif ($reviewer['registration_status'] == 'rejected'): ?>
                            <span class="status-rejected">❌ Rejected</span>
                        <?php else: ?>
                            <span class="status-pending">🕓 Pending</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($reviewer['registration_status'] == 'pending' && $reviewer['contract_status'] == 'not_sent'): ?>
                            <a href="reject_reviewer_editor.php?id=<?= $reviewer['id']; ?>" class="btn reject-btn" onclick="return confirm('Are you sure you want to reject and delete this reviewer?');">Reject</a>
 <?php endif; ?>
                       </td>
                </>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filterTable(status) {
    const rows = document.querySelectorAll('#reviewerTableBody tr');
    rows.forEach(row => {
        const currentStatus = row.getAttribute('data-status');
        row.style.display = (status === 'all' || currentStatus === status) ? '' : 'none';
    });
}
</script>
</body>
</html>
