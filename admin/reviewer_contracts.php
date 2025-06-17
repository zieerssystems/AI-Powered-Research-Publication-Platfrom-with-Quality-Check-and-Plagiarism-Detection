<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}

include("../include/db_connect.php");
 // Include the new queries file

$result = getReviewers();  // Get the result set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reviewer Contracts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        /* Your existing CSS styles here (unchanged) */
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #f2f4f7, #dfe9f3);
            margin: 0;
            padding: 30px;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        th {
            background: linear-gradient(to right, #3f87a6, #ebf8e1);
            color: #ffffff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 8px 14px;
            border: none;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
            font-size: 14px;
        }
        .btn-view { background-color: #28a745; }
        .btn-verify { background-color: #007bff; }
        .btn-verify:hover { background-color: #0056b3; }
        .btn-reupload { background-color: #ff9800; }
        .btn-reupload:hover { background-color: #e68900; }
        .close-btn {
            background: #dc3545;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        iframe {
            width: 100%;
            height: 600px;
            border: none;
            display: none;
            margin-top: 30px;
        }
        .dashboard-btn {
            display: inline-flex;
            align-items: center;
            background: #3f87a6;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            font-weight: 500;
            transition: background 0.3s ease;
        }
        .dashboard-btn i {
            margin-right: 8px;
        }
        .dashboard-btn:hover {
            background: #336b87;
        }
        .modal {
            display: block;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            width: 40%;
            border-radius: 10px;
            text-align: center;
        }
        .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }
    </style>

<script>
function viewContract(contractFile) {
    var viewer = document.getElementById("contract-viewer");
    var closeButton = document.getElementById("close-viewer");

    if (contractFile) {
        viewer.src = "../admin/contracts/signed/" + contractFile;
        viewer.style.display = "block";
        closeButton.style.display = "block";
    } else {
        alert("No contract file available.");
    }
}

function closeContractViewer() {
    document.getElementById("contract-viewer").style.display = "none";
    document.getElementById("close-viewer").style.display = "none";
}

function verifyContract(reviewerId) {
    if (confirm("Are you sure you want to verify this contract?")) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "verify_contract.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText);
                location.reload();
            }
        };
        xhr.send("reviewer_id=" + reviewerId);
    }
}

function requestReupload(userId, role, contractFile) {
    let modalHTML = `
    <div id="reuploadModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">×</span>
            <h3>Request Contract Reupload</h3>
            <p><strong>Attached Contract:</strong> ${contractFile ? contractFile : 'No file found'}</p>
            <label for="issue_message">Reason for Reupload:</label>
            <textarea id="issue_message" rows="4" placeholder="Enter the issue details..." required></textarea>
            <button class="btn btn-send" onclick="sendReuploadRequest(${userId}, '${role}', '${contractFile}')">
                Send Request
            </button>
        </div>
    </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    document.getElementById("reuploadModal").style.display = "block";
}

function closeModal() {
    let modal = document.getElementById("reuploadModal");
    if (modal) {
        modal.remove();
    }
}

function sendReuploadRequest(userId, role, contractFile) {
    let issueMessage = document.getElementById("issue_message").value;
    if (!issueMessage.trim()) {
        alert("Please enter a reason for the reupload request.");
        return;
    }

    let formData = new FormData();
    formData.append("user_id", userId);
    formData.append("role", role);
    formData.append("issue_message", issueMessage);
    formData.append("contract_file", contractFile);

    fetch("process_reupload_request.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        alert(result);
        closeModal();
        location.reload();
    })
    .catch(error => console.error("Error:", error));
}
</script>

</head>
<body>

<a href="admin_dashboard.php" class="dashboard-btn">
    <i class="fas fa-arrow-left"></i> Back to Dashboard
</a>

<h2>Reviewer Contracts Pending Verification</h2>
<table>
    <tr>
        <th>Reviewer Name</th>
        <th>Personal Address</th>
        <th>Uploaded Date</th>
        <th>Contract</th>
        <th>Contract Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): 
        $reviewer_name = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
        $address = htmlspecialchars($row['address']);
        $upload_date = date("d M Y", strtotime($row['upload_date']));
        $contract_file = $row['contract_file'];
        $status = htmlspecialchars($row['contract_status']);
        $email = htmlspecialchars($row['email']);
    ?>
    <tr>
        <td><?= $reviewer_name; ?></td>
        <td><?= $address; ?></td>
        <td><?= $upload_date; ?></td>
        <td>
            <?php if (!empty($contract_file) && file_exists("../admin/contracts/signed/" . $contract_file)): ?>
                <button class="btn btn-view" onclick="viewContract('<?= $contract_file; ?>')">View Contract</button>
            <?php else: ?>
                <span style="color:red;">No Contract Uploaded</span>
            <?php endif; ?>
        </td>
        <td><?= $status; ?></td>
        <td>
            <button class="btn btn-verify" onclick="verifyContract(<?= $row['id']; ?>)">Verified</button>
            <?php if ($status !== 'reupload'): ?>
                <button class="btn btn-reupload" 
                        onclick="requestReupload(<?= $row['id']; ?>, 'reviewer', '<?= $contract_file; ?>')">
                    Request Reupload
                </button>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- PDF Viewer -->
<iframe id="contract-viewer"></iframe>
<button id="close-viewer" class="close-btn" onclick="closeContractViewer()" style="display: none;">Close</button>

</body>
</html>
