<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "alert('Unauthorized access!'); window.location.href='admin_login.php';";
    exit();
}
include("process_editor_details.php"); // Make sure this file sets $editors, $approved_editors, $rejected_editors, $pending_editors, $total_editors
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Editor Details</title>
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

.editor-table-container {
    overflow-x: auto;
    margin-top: 10px;
}

.editor-table {
    width: 100%;
    border-collapse: collapse;
}

.editor-table th,
.editor-table td {
    padding: 10px;
    border: 1px solid #d7dee3;
    text-align: center;
    font-size: 13px;
}

.editor-table th {
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

    .editor-table th, .editor-table td {
        font-size: 11px;
        padding: 6px;
    }

    .card {
        width: 90%;
    }
}
</style>
<script>
        function filterTable(status) {
            const rows = document.querySelectorAll(".editor-table tbody tr");
            rows.forEach(row => {
                const regStatus = row.getAttribute("data-reg-status");
                row.style.display = (status === 'all' || regStatus === status) ? "" : "none";
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Editor Details</h2>

        <div class="stats-cards">
            <div class="card" onclick="filterTable('all')">
                <h3>Total Editors</h3>
                <p><?= $total_editors; ?></p>
            </div>
            <div class="card" onclick="filterTable('approved')">
                <h3>Approved</h3>
                <p><?= $approved_editors; ?></p>
            </div>
            <div class="card" onclick="filterTable('pending')">
                <h3>Pending</h3>
                <p><?= $pending_editors; ?></p>
            </div>
        </div>
        <div style="text-align: center; margin-bottom: 20px;">
        <a href="admin_dashboard.php" class="btn" style="background: #336b87; padding: 10px 20px; border-radius: 8px; color: white;">⬅ Back to Dashboard</a>
    </div>
         <div class="filter-btns">
            <button onclick="filterTable('all')">Show All</button>
        </div>

        <div class="editor-table-container">
            <table class="editor-table">
                <thead>
                <tr>
  <th>ID</th><th>Name</th><th>Email</th><th>Phone</th>
  <th>Degree</th><th>Gender</th><th>Type</th><th>Experience</th><th>CV</th>
  <th>Contract</th><th>Status</th><th>Actions</th>
    </tr>
        </thead>
                <tbody>
                    <?php foreach ($editors as $editor): ?>
                    <tr data-reg-status="<?= $editor['registration_status']; ?>">
                        <td><?= htmlspecialchars($editor['editor_id']); ?></td>
                       <td><?= htmlspecialchars(trim(($editor['first_name'] ?? '') . ' ' . ($editor['middle_name'] ?? '') . ' ' . ($editor['last_name'] ?? ''))); ?></td>
<td><?= htmlspecialchars($editor['email'] ?? ''); ?></td>

                        <td><?= htmlspecialchars($editor['telephone']); ?></td>
                        <td><?= htmlspecialchars($editor['degree']); ?></td>
                        <td><?= htmlspecialchars($editor['gender']); ?></td>
                        <td><?= htmlspecialchars($editor['editor_type']); ?></td>
                        <td><?= htmlspecialchars($editor['editor_experience']); ?></td>
                        <td>
                            <?php if (!empty($editor['cv_path'])): ?>
                                <a href="/my_publication_site/<?= htmlspecialchars($editor['cv_path']); ?>" target="_blank" class="btn email-btn">View</a>
<?php else: ?>
                                <span style="color: gray;">No CV</span>
                            <?php endif; ?>
                            </td>
                        <td>
                        <?php if ($editor['contract_status'] == 'not_sent'): ?>
                                <a href="send_email.php?editor_id=<?= $editor['editor_id']; ?>" class="btn email-btn">Send</a>
                            <?php elseif ($editor['contract_status'] == 'sent'): ?>
                                <span class="status-sent">📩 Sented</span>
                            <?php elseif ($editor['contract_status'] == 'pending_verification'): ?>
                                <span class="status-pending">⏳ Pending Verification</span><br>
                            <?php elseif ($editor['contract_status'] == 'signed'): ?>
                                <span class="status-signed">✅ Signed</span>
                            <?php else: ?>
                                <span class="status-unknown">❓ Unknown</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($editor['registration_status'] == 'rejected'): ?>
                                <span class="status-rejected">❌ Rejected</span>
                            <?php elseif ($editor['registration_status'] == 'approved'): ?>
                                <span class="status-approved">✅ Approved</span>
                            <?php else: ?>
                                <span class="status-pending">🕓 Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($editor['registration_status'] == 'pending'): ?>
                                <a href="reject_reviewer_editor.php?editor_id=<?= $editor['editor_id']; ?>" class="btn reject-btn" onclick="return confirm('Are you sure you want to reject and delete this editor?');">Reject</a>
                            <?php endif; ?>
                            </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
   </div>
</body>
</html>
