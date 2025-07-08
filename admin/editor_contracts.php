<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    echo "<script>alert('Unauthorized access!'); window.location.href='admin-login.php';</script>";
    exit();
}
include("../include/db_connect.php");
$result = getPendingEditorContracts($conn);

?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editor Contracts</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #f0f4ff, #e6f7ff);
            margin: 0;
            padding: 20px;
        }

        .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.header h2 {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    margin: 0 auto;
}


        .btn-back {
            padding: 10px 15px;
            background: #336b87;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }

        th {
            background: linear-gradient(to right, #3f87a6, #ebf8e1);
            color: white;
        }

        tr:hover {
            background-color: #f1f9ff;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-view { background-color: #28a745; }
        .btn-verify { background-color: #007bff; }
        .btn-verify:hover { background-color: #0056b3; }
        .btn-reupload { background-color: #ff9800; }
        .btn-reupload:hover { background-color: #e08800; }

        .close-btn {
            background: red;
            color: white;
            padding: 6px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }

        iframe {
            width: 100%;
            height: 500px;
            border: none;
            display: none;
            margin-top: 20px;
        }

        /* Modal Styling */
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
            margin: 10% auto;
            padding: 25px;
            width: 40%;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }

        .close {
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: #888;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            resize: none;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .btn-send {
            background: #007bff;
            margin-top: 15px;
        }
    </style>
    <script>
        function viewContract(contractFile) {
            const viewer = document.getElementById("contract-viewer");
            const closeButton = document.getElementById("close-viewer");

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

        function verifyContract(editorId) {
    const xhrCheck = new XMLHttpRequest();
    xhrCheck.open("POST", "check_editor_team_assignment.php", true);
    xhrCheck.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhrCheck.onreadystatechange = function () {
        if (xhrCheck.readyState === 4 && xhrCheck.status === 200) {
            if (xhrCheck.responseText.trim() === 'assigned') {
                if (confirm("Are you sure you want to verify this contract?")) {
                    const xhrVerify = new XMLHttpRequest();
                    xhrVerify.open("POST", "verify_editor_contract.php", true);
                    xhrVerify.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhrVerify.onreadystatechange = function () {
                        if (xhrVerify.readyState === 4 && xhrVerify.status === 200) {
                            alert(xhrVerify.responseText);
                            sendVerificationEmail(editorId);
                            location.reload();
                        }
                    };
                    xhrVerify.send("editor_id=" + editorId);
                }
            } else {
                alert("Cannot verify. This editor is not assigned to any editorial team.");
            }
        }
    };
    xhrCheck.send("editor_id=" + editorId);
}

function sendVerificationEmail(editorId) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "send_verification_email.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert("Verification email sent successfully!");
        }
    };
    xhr.send("editor_id=" + editorId);
}

        function requestReupload(userId, role, contractFile) {
            let modalHTML = `
                <div id="reuploadModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal()">&times;</span>
                        <h3>Request Contract Reupload</h3>
                        <p><strong>Attached Contract:</strong> ${contractFile ? contractFile : 'No file found'}</p>
                        <label for="issue_message">Reason for Reupload:</label>
                        <textarea id="issue_message" rows="4" placeholder="Enter the issue details..." required></textarea>
                        <button class="btn btn-send" onclick="sendReuploadRequest(${userId}, '${role}', '${contractFile}')">Send Request</button>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            document.getElementById("reuploadModal").style.display = "block";
        }

        function closeModal() {
            let modal = document.getElementById("reuploadModal");
            if (modal) modal.remove();
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
<div class="header">
    <h2>Editor Contracts Pending Verification</h2>
    <a href="admin_dashboard.php" class="btn-back">← Back to Dashboard</a>
</div>

<br>
<table>
    <tr>
        <th>Editor Name</th>
        <th>Personal Address</th>
        <th>Uploaded Date</th>
        <th>Contract</th>
        <th>Contract Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()):
        $editor_name = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
        $address = htmlspecialchars($row['address']);
        $upload_date = date("d M Y", strtotime($row['upload_date']));
        $contract_file = $row['contract_file'];
        $status = htmlspecialchars($row['contract_status']);
    ?>
    <tr>
        <td><?= $editor_name; ?></td>
        <td><?= $address; ?></td>
        <td><?= $upload_date; ?></td>
        <td>
            <?php if (!empty($contract_file) && file_exists("../admin/contracts/signed/" . $contract_file)): ?>
                <button class="btn btn-view" onclick="viewContract('<?= $contract_file; ?>')">View</button>
            <?php else: ?>
                <span style="color:red;">No Contract Uploaded</span>
            <?php endif; ?>
        </td>
        <td><?= $status; ?></td>
        <td>
        <button class="btn btn-verify" onclick="verifyContract(<?= $row['editor_id']; ?>)">Verify</button>
            <?php if ($status !== 'reupload'): ?>
                <button class="btn btn-reupload" onclick="requestReupload(<?= $row['editor_id']; ?>, 'editor', '<?= $contract_file; ?>')">Reupload</button>
            <?php endif; ?>
       <!-- Assign Team Button -->
    <a href="admin_editorial_teams.php" class="btn btn-assign" style="background-color: #6f42c1; margin-top: 5px; display: inline-block; margin-left: 5px;">Assign Team</a>
</td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- PDF Viewer -->
<iframe id="contract-viewer"></iframe>
<button id="close-viewer" class="close-btn" onclick="closeContractViewer()" style="display: none;">Close</button>

</body>
</html>
