<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Journal - Zieers</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background: #e7f1fb;
            display: flex;
        }

        .sidebar {
            width: 220px;
            background: #002147;
            color: #fff;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
        }

        .sidebar a i {
            margin-right: 12px;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .main-content {
            margin-left: 220px;
            padding: 30px;
            flex: 1;
        }

        .header {
             background: #002147;
            color: #fff;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            border-radius: 8px;
        }

        .container {
            background: #fff;
            padding: 30px;
            margin-top: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 26px;
        }

        .info {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        .btn {
            background-color: #0056b3;
            color: #fff;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #003d82;
        }

        .back-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            margin-bottom: 20px;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background-color: #5a6268;
        }

        .success-message {
            display: none;
            text-align: center;
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
    <script>
        const subjects = {
            "Computer Science": ["AI", "Cybersecurity", "Data Science"],
            "Engineering": ["Civil", "Mechanical", "Electrical"],
            "Medicine": ["Cardiology", "Neurology", "Pediatrics"],
            "Social Sciences": ["Psychology", "Sociology", "Economics"],
            "Chemistry": ["Bioengineering", "Catalysis", "Chemical Health and Safety"]
        };

        function updateSecondarySubjects() {
            let primary = document.getElementById("primary_subject").value;
            let secondarySelect = document.getElementById("secondary_subject");
            secondarySelect.innerHTML = "<option value=''>Select Secondary Subject</option>";

            if (subjects[primary]) {
                subjects[primary].forEach(sub => {
                    let option = document.createElement("option");
                    option.value = sub;
                    option.textContent = sub;
                    secondarySelect.appendChild(option);
                });
            }
        }

        $(document).ready(function () {
            $("#journalForm").on("submit", function (event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "process_journal.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.trim() === "success") {
                            $(".success-message").text("Journal created successfully! Redirecting...")
                                .css("color", "green").fadeIn();
                            setTimeout(function () {
                                window.location.href = "view-journal.php";
                            }, 2000);
                        } else {
                            $(".success-message").text("Error: " + response)
                                .css("color", "red").fadeIn();
                        }
                    },
                    error: function () {
                        $(".success-message").text("Error processing request.")
                            .css("color", "red").fadeIn();
                    }
                });
            });
        });
    </script>
</head>
<body>
<div class="sidebar">
        <h2>Admin</h2>
        <a href="journal-creation.php"><i class="fas fa-book"></i> <span>Create Journal</span></a>
        <a href="view-journal.php"><i class="fas fa-book"></i> View Journal</a>
        <a href="review_papers.php"><i class="fas fa-file-alt"></i> <span>Review Papers</span></a>
        <a href="reviewer_details.php"><i class="fas fa-user-check"></i> <span>Reviewer Details</span></a>
        <a href="editor_details.php"><i class="fas fa-user-edit"></i> <span>Editor Details</span></a>
        <a href="reviewer_contracts.php"><i class="fas fa-file-contract"></i> <span>Reviewer Contracts</span></a>
        <a href="editor_contracts.php"><i class="fas fa-file-signature"></i> <span>Editor Contracts</span></a>
        <a href="new_request_reviewer.php"><i class="fas fa-user-plus"></i> <span>Reviewer Requests</span></a>
        <a href="assign_editor.php"><i class="fas fa-tasks"></i> <span>Assign Editor</span></a>
    </div>

    <div class="main-content">
        <div class="header">
            <div>Journal Creation</div>

            <a href="admin_logout.php" style="color: white; text-decoration: none;">Logout</a>
        </div>

        <div class="container">
            <button onclick="window.location.href='admin_dashboard.php'" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </button>

            <h2>Create a Journal</h2>
            <p class="info">Please fill out all required information accurately before creating a journal.</p>

            <form id="journalForm" enctype="multipart/form-data">
                <label>Journal Name:</label>
                <input type="text" name="journal_name" required>

                <label>Journal Abbreviation:</label>
                <input type="text" name="journal_abbreviation" required>

                <label>Editorial Team (Optional):</label>
<select name="editorial_team_id" id="editorial_team_id">
    <option value="">-- Select Editorial Team (Optional) --</option>
</select>
<script>
$(document).ready(function () {
    // Load editorial teams into dropdown
    $.ajax({
        url: "get_editorial_teams.php",
        type: "GET",
        success: function(response) {
            let teams = JSON.parse(response);
            let dropdown = $('#editorial_team_id');
            teams.forEach(team => {
                dropdown.append(`<option value="${team.team_id}">${team.team_name}</option>`);
            });
        }
    });
});
</script>


                <label>Primary Subject:</label>
                <select name="subject" id="primary_subject" onchange="updateSecondarySubjects()" required>
                    <option value="">Select Subject</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Engineering">Engineering</option>
                    <option value="Medicine">Medicine</option>
                    <option value="Social Sciences">Social Sciences</option>
                    <option value="Chemistry">Chemistry</option>
                </select>

                <label>Secondary Subject:</label>
                <select name="secondary_subject" id="secondary_subject" required>
                    <option value="">Select Secondary Subject</option>
                </select>

                
                <label>Description:</label>
                <textarea name="description" rows="3" required></textarea>

                <label>Publisher:</label>
                <input type="text" name="publisher" required>

                <label for="issn">ISSN (optional)</label>
<input type="text" id="issn" name="issn" placeholder="Leave empty if applying later">


                <label>Country of Publication:</label>
                <input type="text" name="country" required>

                <label>Publication Frequency:</label>
                <input type="text" name="publication_frequency" required>

                <label>Indexing Information (Optional):</label>
<input type="text" name="indexing_info" placeholder="Enter indexing info if available">


                <label>Scope of the Journal:</label>
                <textarea name="scope" rows="3" required></textarea>

                <label>Author Guidelines:</label>
                <textarea name="author_guidelines_text" rows="4"></textarea>

                <label>Review Process Type:</label>
                <select name="review_process" required>
                <option value="Single blind">Single-blind</option>
                    <option value="Open Peer Review">Open Peer Review</option>
                </select>

                <label>Impact Factor (Optional):</label>
                <input type="text" name="impact_factor">

                <label>CiteScore (Optional):</label>
                <input type="text" name="citescore">

                <label>Acceptance Rate (Optional):</label>
                <input type="text" name="acceptance_rate">

                <label>Access Type:</label>
                <select name="access_type" required>
                    <option value="Open Access">Open Access</option>
                    <option value="Subscription Based">Subscription Based</option>
                </select>
                <label>Keywords (comma-separated):</label>
<input type="text" name="keywords" placeholder="e.g. AI, Cybersecurity, Data Mining">

<!-- Add this inside the <form id="journalForm"> just before the submit button -->
<label>Author Payment Required:</label>
<select name="author_payment_required" required>
    <option value="0">No</option>
    <option value="1">Yes</option>
</select>

<label>Reader Payment Required:</label>
<select name="reader_payment_required" required>
    <option value="0">No</option>
    <option value="1">Yes</option>
</select>

<label>Author APC Amount (if applicable):
  <input type="number" name="author_apc_amount" step="0.01" placeholder="0.00">
</label>

<label>Reader Fee Amount (if applicable):
<input type="number" name="reader_fee_amount" step="0.01" placeholder="0.00">
</label>

<label>Payment Currency:</label>
<input type="text" name="payment_currency" placeholder="e.g. USD, INR">

<label>Payment Link:</label>
<input type="url" name="payment_link" placeholder="https://your-payment-link.com">

<label>Payment Notes:</label>
<textarea name="payment_notes" rows="3" placeholder="Add any additional payment instructions here..."></textarea>

                <label>Submission Status:</label>
                <select name="submission_status" required>
                    <option value="Accepting Submissions">Accepting Submissions</option>
                    <option value="Closed for Submissions">Closed for Submissions</option>
                </select>

                <label>Journal Image (Optional):</label>
                <input type="file" name="journal_image" accept="image/*">

                <button type="submit" class="btn">Create Journal</button>
            </form>
            <p class="success-message"></p>
        </div>
    </div>
    <script>
    $(document).ready(function () {
        function togglePaymentFields() {
            const authorPayment = $("select[name='author_payment_required']").val();
            const readerPayment = $("select[name='reader_payment_required']").val();

            if (authorPayment === "1") {
                $("input[name='author_apc_amount']").closest("label").show();
            } else {
                $("input[name='author_apc_amount']").closest("label").hide();
            }

            if (readerPayment === "1") {
                $("input[name='reader_fee_amount']").closest("label").show();
            } else {
                $("input[name='reader_fee_amount']").closest("label").hide();
            }
        }

        // Initial state
        togglePaymentFields();

        // On change
        $("select[name='author_payment_required'], select[name='reader_payment_required']").on("change", togglePaymentFields);
    });
</script>

</body>
</html>
