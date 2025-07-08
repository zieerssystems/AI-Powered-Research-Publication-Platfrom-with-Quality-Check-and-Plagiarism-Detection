
<?php
session_start(); // Start session to access stored data
include(__DIR__ . "/../../include/db_connect.php");
// Check if required session variables are set
if (!isset($_SESSION['first_name'], $_SESSION['last_name'], $_SESSION['email'])) {
    die("Error: Required user information is missing.");
}
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
// Retrieve data from session
$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'] ?? ''; // Middle name is optional
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
$journal_id = $_SESSION['journal_id'] ?? 0;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $primary_subject = $_POST['primary_subject'];
    $selected_journals = isset($_POST['journals']) ? $_POST['journals'] : [];

    if (!empty($selected_journals)) {
        echo "<h4>Selected Journals:</h4>";
        foreach ($selected_journals as $journal_id) {
            echo "Journal ID: " . htmlspecialchars($journal_id) . "<br>";
        }
    } else {
        echo "<p>No journals selected.</p>";
    }
}
$user_id = $_SESSION['user_id'] ?? 0;
$common_data = $user_id ? getUserCommonDetails(user_id: $user_id) : [];
$bankDetails = getReviewerBankDetails($user_id);

if (!$bankDetails || empty($bankDetails['account_holder_name'])) {
    $editorBank = getEditorBankDetails($user_id);
    if ($editorBank) {
        $bankDetails = [
            'payment_type' => 'paid',
            'account_holder_name' => $editorBank['editor_account_holder'],
            'bank_name' => $editorBank['editor_bank_name'],
            'account_number' => $editorBank['editor_account_number'],
            'ifsc_code' => $editorBank['editor_ifsc'],
            'branch_name' => $editorBank['editor_branch_name'],
            'bank_country' => $editorBank['editor_bank_country']
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewer Registration</title>
    <!-- jQuery (Ensure it's included before Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS (Load after jQuery) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9; /* Light Gray Background */
            margin: 0;
            padding: 0;
        }

       header {
  background: #002147;
  color: white;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Keep logo on left */
.logo {
  font-size: 28px;
  font-weight: bold;
  color: #ECF0F1;
}

/* Container for the links on right */
.nav-links {
  display: flex;
  align-items: center;
  gap: 20px; /* space between links */
}

/* Style links */
.nav-links a {
  color: #ECF0F1;
  text-decoration: none;
  font-size: 16px;
  cursor: pointer;
}
    .dropdown {
    position: relative;
}
.dropdown-menu {
    display: none;
    position: absolute;
    background-color: #002147;
    list-style: none;
    padding: 0;
    margin: 0;
    z-index: 100;
    border-radius: 5px;
}
.dropdown-menu li a {
    display: block;
    padding: 10px 20px;
    color: white;
    text-decoration: none;
}
.dropdown:hover .dropdown-menu {
    display: block;
}
.dropdown-menu li a:hover {
     background:rgb(2, 51, 107);
}
.container {
    width: 90%;
    max-width: 700px;
    margin: 40px auto;
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
}

h2, h3 {
    margin-top: 25px;
    font-size: 20px;
    color: #002147;
}

label {
    display: block;
    margin: 12px 0 6px;
    font-weight: 500;
}

/* Ensures all input fields, select, and textareas look uniform */
.container input[type="text"],
.container input[type="email"],
.container input[type="password"],
.container input[type="number"],
.container input[type="textarea"],
.container select,
.container textarea {
    width: 100%;
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
    margin-bottom: 12px;
    box-sizing: border-box;
    background-color: #fff;
}

/* Fix for textarea element */
.container textarea {
    resize: vertical;
    min-height: 100px;
}

/* Fix for tooltip close button */
.tooltip button {
    background-color: transparent;
    color: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 5px 8px;
    margin-top: 5px;
    cursor: pointer;
}


.container input[type="radio"] {
    margin-right: 6px;
}

.password-container {
    position: relative;
}

.eye-icon {
    position: absolute;
    top: 38px;
    right: 12px;
    cursor: pointer;
}

.btn {
    width: 100%;
    padding: 14px;
    border-radius: 6px;
    background: #002147;
    color: white;
    border: none;
    font-size: 16px;
    margin-top: 20px;
    cursor: pointer;
}

#tooltip {
    display: none;
    position: absolute;
    background: #333;
    color: #fff;
    padding: 10px;
    border-radius: 6px;
    font-size: 13px;
    margin-top: 5px;
    width: 250px;
}

#institution-info {
    margin-top: 15px;
}

 footer {
            background: #002147;
            color: white;
            text-align: center;
            padding: 20px 10px;
        }

        footer p {
            cursor: pointer;
        }

        footer p:hover {
            text-decoration: underline;
        } .site-footer {
  background-color: #002147;
  color: white;
  padding: 40px 10%;
  font-family: 'Poppins', sans-serif;
}

.footer-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 30px;
}

.footer-column {
  flex: 1;
  min-width: 250px;
}

.footer-column h3,
.footer-column h4 {
  margin-bottom: 15px;
  color: #ffffff;
}

.footer-column p,
.footer-column a,
.footer-column li {
  font-size: 14px;
  color: #ccc;
  line-height: 1.6;
  text-decoration: none;
}

.footer-column a:hover {
  color: #ffffff;
  text-decoration: underline;
}

.footer-column ul {
  list-style: none;
  padding-left: 0;
}

.footer-bottom {
  text-align: center;
  margin-top: 40px;
  border-top: 1px solid #444;
  padding-top: 20px;
  font-size: 13px;
  color: #aaa;
}
.social-link {
  display: flex;
  align-items: center;
  color: #ccc;
  text-decoration: none;
  margin-top: 10px;
}

.social-link:hover {
  color: white;
  text-decoration: underline;
}

.social-icon {
  width: 20px;
  height: 20px;
  margin-right: 8px;
}
    </style>
    <script>
        function showTooltip() {
            document.getElementById("tooltip").style.display = "block";
        }
        function hideTooltip() {
            document.getElementById("tooltip").style.display = "none";
        }
        function toggleInstitutionFields(show) {
    const instSection = document.getElementById("institution-info");
    instSection.style.display = show ? "block" : "none";

    // Disable or enable all fields inside institution-info
    const inputs = instSection.querySelectorAll("input, select, textarea");
    inputs.forEach(input => {
        input.disabled = !show;
    });
}

        $(document).ready(function() {
    $(".select2").select2(); // Ensure elements have class "select2"
});
 </script>
</head>
<body>
<header>
     <div class="logo">
  <a href="index.php">
    <img src="../../images/logo.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>
  <nav class="nav-links">
    <a href="../../publish.php">Home</a>
    <a href="help.php">Help</a>
    <div class="dropdown">
    <a href="#">For Users ▼</a>
    <ul class="dropdown-menu">
        <li><a href="../../for_author.php">For Author</a></li>
        <li><a href="../../for_reviewer.php">For Reviewer</a></li>
        <li><a href="../../for_editor.php">For Editor</a></li>
    </ul>
</div>
</nav>
</header>
<div style="margin: 20px 0 0 20px;">
    <button onclick="history.back()" style="
        background-color: #f1f1f1;
        border: 1px solid #ccc;
        padding: 8px 16px;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
    ">
        ← Back
    </button>
</div>
    <div class="container">
        <h2>Reviewer Registration</h2>
        <form action="process_reviewer.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="role" value="<?php echo htmlspecialchars($_GET['role'] ?? ''); ?>">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
  
        <h2>Personal Details</h2>
        <label for="title">Title*</label>
        <input type="text" id="title" name="title" required
               onfocus="showTooltip()" onblur="hideTooltip()">

        <div id="tooltip" class="tooltip">
            Enter your personal title (e.g., PhD, MD, Prof, Dr.)
            <button type="button" onclick="hideTooltip()">Close</button>
        </div>
            <label for="telephone">Telephone (include country code)*</label>
<input type="text" id="telephone" name="telephone"  required
       value="<?php echo htmlspecialchars($common_data['telephone'] ?? '', ENT_QUOTES); ?>">

<label for="degree">Degree (PhD, MD, etc.)*</label>
<input type="text" name="degree" required
       value="<?php echo htmlspecialchars($common_data['degree'] ?? '', ENT_QUOTES); ?>">

<label for="address">personal_address*</label>
<textarea name="address" required><?php echo htmlspecialchars($common_data['address'] ?? '', ENT_QUOTES); ?></textarea>
<label for="gender">Gender*</label>
<select name="gender" id="gender">
  <option value="">Select Gender</option>
  <option value="Male" <?= (isset($common_data['gender']) && $common_data['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
  <option value="Female" <?= (isset($common_data['gender']) && $common_data['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
  <option value="Other" <?= (isset($common_data['gender']) && $common_data['gender'] === 'Other') ? 'selected' : '' ?>>Other</option>
</select>
<h2>Researcher Type</h2>
<label>
    <input type="radio" name="researcher_type" value="individual" onclick="toggleInstitutionFields(false)" checked>
    Individual Researcher
</label>
<label>
    <input type="radio" name="researcher_type" value="affiliated" onclick="toggleInstitutionFields(true)">
    Affiliated with Institution
</label>

<div id="institution-info" style="<?= ($details['reviewer_type'] ?? '') === 'affiliated' ? '' : 'display:none;' ?>">
 <h2>Institution Details</h2>
<label for="institution">position*</label>
<input type="text" name="position" id="institution_position"
       value="<?php echo htmlspecialchars($common_data['position'] ?? '', ENT_QUOTES); ?>">
<label for="institution">Institution*</label>
<input type="text" name="institution" id="institution_name"
       value="<?php echo htmlspecialchars($common_data['institution'] ?? '', ENT_QUOTES); ?>">
<label for="institution">Department*</label>
<input type="text" name="department" id="institution_department"
       value="<?php echo htmlspecialchars($common_data['department'] ?? '', ENT_QUOTES); ?>">
       
<label for="street_address">Street Address*</label>
<input type="text" name="street_address" id="street_address"
       value="<?php echo htmlspecialchars($common_data['street_address'] ?? '', ENT_QUOTES); ?>">

<label for="city">City*</label>
<input type="text" name="city" id="city"
       value="<?php echo htmlspecialchars($common_data['city'] ?? '', ENT_QUOTES); ?>">

<label for="state">State*</label>
<input type="text" name="state" id="state"
       value="<?php echo htmlspecialchars($common_data['state'] ?? '', ENT_QUOTES); ?>">

<label for="zip_code">Zip Code*</label>
<input type="text" name="zip_code" id="zip_code"
       value="<?php echo htmlspecialchars($common_data['zip_code'] ?? '', ENT_QUOTES); ?>">
<select id="countrySelect" class="input-box" name="country">
    <option value="">Select Your Country</option>
    <option value="AF" <?= (isset($details['country']) && $details['country'] === 'AF') ? 'selected' : '' ?>>Afghanistan</option>
    <option value="AL" <?= (isset($details['country']) && $details['country'] === 'AL') ? 'selected' : '' ?>>Albania</option>
    <option value="DZ" <?= (isset($details['country']) && $details['country'] === 'DZ') ? 'selected' : '' ?>>Algeria</option>
    <option value="AD" <?= (isset($details['country']) && $details['country'] === 'AD') ? 'selected' : '' ?>>Andorra</option>
    <option value="AO" <?= (isset($details['country']) && $details['country'] === 'AO') ? 'selected' : '' ?>>Angola</option>
    <option value="AR" <?= (isset($details['country']) && $details['country'] === 'AR') ? 'selected' : '' ?>>Argentina</option>
    <option value="AM" <?= (isset($details['country']) && $details['country'] === 'AM') ? 'selected' : '' ?>>Armenia</option>
    <option value="AU" <?= (isset($details['country']) && $details['country'] === 'AU') ? 'selected' : '' ?>>Australia</option>
    <option value="AT" <?= (isset($details['country']) && $details['country'] === 'AT') ? 'selected' : '' ?>>Austria</option>
    <option value="AZ" <?= (isset($details['country']) && $details['country'] === 'AZ') ? 'selected' : '' ?>>Azerbaijan</option>
    <option value="BD" <?= (isset($details['country']) && $details['country'] === 'BD') ? 'selected' : '' ?>>Bangladesh</option>
    <option value="BY" <?= (isset($details['country']) && $details['country'] === 'BY') ? 'selected' : '' ?>>Belarus</option>
    <option value="BE" <?= (isset($details['country']) && $details['country'] === 'BE') ? 'selected' : '' ?>>Belgium</option>
    <option value="BR" <?= (isset($details['country']) && $details['country'] === 'BR') ? 'selected' : '' ?>>Brazil</option>
    <option value="BG" <?= (isset($details['country']) && $details['country'] === 'BG') ? 'selected' : '' ?>>Bulgaria</option>
    <option value="CA" <?= (isset($details['country']) && $details['country'] === 'CA') ? 'selected' : '' ?>>Canada</option>
    <option value="CN" <?= (isset($details['country']) && $details['country'] === 'CN') ? 'selected' : '' ?>>China</option>
    <option value="CO" <?= (isset($details['country']) && $details['country'] === 'CO') ? 'selected' : '' ?>>Colombia</option>
    <option value="DK" <?= (isset($details['country']) && $details['country'] === 'DK') ? 'selected' : '' ?>>Denmark</option>
    <option value="EG" <?= (isset($details['country']) && $details['country'] === 'EG') ? 'selected' : '' ?>>Egypt</option>
    <option value="FR" <?= (isset($details['country']) && $details['country'] === 'FR') ? 'selected' : '' ?>>France</option>
    <option value="DE" <?= (isset($details['country']) && $details['country'] === 'DE') ? 'selected' : '' ?>>Germany</option>
    <option value="IN" <?= (isset($details['country']) && $details['country'] === 'IN') ? 'selected' : '' ?>>India</option>
    <option value="ID" <?= (isset($details['country']) && $details['country'] === 'ID') ? 'selected' : '' ?>>Indonesia</option>
    <option value="IT" <?= (isset($details['country']) && $details['country'] === 'IT') ? 'selected' : '' ?>>Italy</option>
    <option value="JP" <?= (isset($details['country']) && $details['country'] === 'JP') ? 'selected' : '' ?>>Japan</option>
    <option value="MX" <?= (isset($details['country']) && $details['country'] === 'MX') ? 'selected' : '' ?>>Mexico</option>
    <option value="NL" <?= (isset($details['country']) && $details['country'] === 'NL') ? 'selected' : '' ?>>Netherlands</option>
    <option value="NG" <?= (isset($details['country']) && $details['country'] === 'NG') ? 'selected' : '' ?>>Nigeria</option>
    <option value="PK" <?= (isset($details['country']) && $details['country'] === 'PK') ? 'selected' : '' ?>>Pakistan</option>
    <option value="PH" <?= (isset($details['country']) && $details['country'] === 'PH') ? 'selected' : '' ?>>Philippines</option>
    <option value="PL" <?= (isset($details['country']) && $details['country'] === 'PL') ? 'selected' : '' ?>>Poland</option>
    <option value="PT" <?= (isset($details['country']) && $details['country'] === 'PT') ? 'selected' : '' ?>>Portugal</option>
    <option value="RU" <?= (isset($details['country']) && $details['country'] === 'RU') ? 'selected' : '' ?>>Russia</option>
    <option value="SA" <?= (isset($details['country']) && $details['country'] === 'SA') ? 'selected' : '' ?>>Saudi Arabia</option>
    <option value="ZA" <?= (isset($details['country']) && $details['country'] === 'ZA') ? 'selected' : '' ?>>South Africa</option>
    <option value="KR" <?= (isset($details['country']) && $details['country'] === 'KR') ? 'selected' : '' ?>>South Korea</option>
    <option value="ES" <?= (isset($details['country']) && $details['country'] === 'ES') ? 'selected' : '' ?>>Spain</option>
    <option value="SE" <?= (isset($details['country']) && $details['country'] === 'SE') ? 'selected' : '' ?>>Sweden</option>
    <option value="CH" <?= (isset($details['country']) && $details['country'] === 'CH') ? 'selected' : '' ?>>Switzerland</option>
    <option value="TR" <?= (isset($details['country']) && $details['country'] === 'TR') ? 'selected' : '' ?>>Turkey</option>
    <option value="UA" <?= (isset($details['country']) && $details['country'] === 'UA') ? 'selected' : '' ?>>Ukraine</option>
    <option value="AE" <?= (isset($details['country']) && $details['country'] === 'AE') ? 'selected' : '' ?>>United Arab Emirates</option>
    <option value="GB" <?= (isset($details['country']) && $details['country'] === 'GB') ? 'selected' : '' ?>>United Kingdom</option>
    <option value="US" <?= (isset($details['country']) && $details['country'] === 'US') ? 'selected' : '' ?>>United States</option>
</select>

</div>
        <h2>Reviewing Experience</h2>
        <input type="number" class="input-box" name="experience" placeholder="Years of Experience" required>
            <h3>Preferred Review Frequency</h3>
            <select class="input-box" name="review_frequency" required>
                <option value="1">1 paper per month</option>
                <option value="2">2 papers per month</option>
                <option value="3">3 papers per month</option>
                <option value="5">5 or more papers per month</option>
            </select>
            <label for="primary_subject">Select Primary Subject:</label>
    <select id="primary_subject" name="primary_subject" class="form-control" required>
        <option value="">-- Select a Subject --</option>
    </select>

    <!-- Journals Multi-Select -->
    <label for="journals">Select Journals:</label>
    <div id="journal_container" >
        <!-- Journals will be loaded dynamically as checkboxes here -->
    </div>

    <!-- Selected Journals Display -->
    <h5>Selected Journals:</h5>
    <div id="selected_journals"></div>

    <label>
        <input type="radio" name="payment_type" value="paid" onclick="toggleBankDetails()">
        Paid Reviewer
    </label>
    <label>
        <input type="radio" name="payment_type" value="unpaid" onclick="toggleBankDetails()" checked>
        Volunteer
    </label>
<div id="reviewerBankDetails" style="display: none;" required>
 <label>Account Holder Name</label>
    <input type="text" name="account_holder_name" value="<?= $bankDetails['account_holder_name'] ?? '' ?>">

    <label>Bank Name</label>
    <input type="text" name="bank_name" value="<?= $bankDetails['bank_name'] ?? '' ?>">

    <label>Account Number</label>
    <input type="text" name="account_number" value="<?= $bankDetails['account_number'] ?? '' ?>">

    <label>IFSC Code</label>
    <input type="text" name="ifsc_code" value="<?= $bankDetails['ifsc_code'] ?? '' ?>">

    <label>Branch Name</label>
    <input type="text" name="branch_name" value="<?= $bankDetails['branch_name'] ?? '' ?>">

    <label>Bank Country</label>
    <input type="text" name="bank_country" value="<?= $bankDetails['bank_country'] ?? '' ?>">
</div>
        <label for="cv_file">Upload CV (PDF only):</label>
<input type="file" name="cv_file" id="cv_file" class="input-box" accept=".pdf" required>

            <button type="submit" class="btn">Register</button>
        </form>
    </div>

    <script>
    function toggleBankDetails() {
    let paymentType = document.querySelector('input[name="payment_type"]:checked').value;
    let bankDetailsDiv = document.getElementById('reviewerBankDetails');
    let inputs = bankDetailsDiv.querySelectorAll('input');

    if (paymentType === 'paid') {
        bankDetailsDiv.style.display = 'block';
        inputs.forEach(input => input.removeAttribute('disabled'));
    } else {
        bankDetailsDiv.style.display = 'none';
        inputs.forEach(input => input.setAttribute('disabled', 'disabled'));
    }
}

// Ensure correct default behavior when the page loads
window.onload = function() {
    toggleBankDetails();
};


    $(document).ready(function() {
        $('select[name="country"]').select2({
            placeholder: "Select Your Country",
            allowClear: true
        });
    });

    
    $(document).ready(function () {
    // Fetch primary subjects and populate the dropdown
    $.getJSON("fetch_primary_subjects_and_journals.php", function (data) {
        let subjectDropdown = $("#primary_subject");
        data.forEach(function (item) {
            subjectDropdown.append(`<option value="${item.primary_subject}">${item.primary_subject}</option>`);
        });
    });

    // When a primary subject is selected, fetch and display journals
    $("#primary_subject").change(function () {
        let selectedSubject = $(this).val();
        let journalContainer = $("#journal_container");
        journalContainer.empty(); // Clear previous checkboxes

        if (selectedSubject) {
            $.getJSON("fetch_primary_subjects_and_journals.php", function (data) {
                data.forEach(function (item) {
                    if (item.primary_subject === selectedSubject) {
                        item.journals.forEach(function (journal) {
                            journalContainer.append(`
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input journal-checkbox" id="journal_${journal.id}" value="${journal.id}" data-name="${journal.journal_name}">
                                    <label class="form-check-label" for="journal_${journal.id}">${journal.journal_name}</label>
                                </div>
                            `);
                        });
                    }
                });
            });
        }
    });

    // Handle journal selection and display in the list
    $(document).on("change", ".journal-checkbox", function () {
        let selectedJournals = $("#selected_journals");
        let journalId = $(this).val();
        let journalName = $(this).data("name");

        if ($(this).is(":checked")) {
            // Add to the selected journals list
            selectedJournals.append(`
                <div class="selected-journal" id="selected_${journalId}">
                    <span>${journalName}</span>
                    <span class="remove-journal" data-id="${journalId}">X</span>
                    <input type="hidden" name="journals[]" value="${journalId}">
                </div>
            `);
        } else {
            // Remove from the selected journals list
            $("#selected_" + journalId).remove();
        }
    });

    // Handle journal removal when clicking "X"
    $(document).on("click", ".remove-journal", function () {
        let journalId = $(this).data("id");
        $("#selected_" + journalId).remove();
        $("#journal_" + journalId).prop("checked", false); // Uncheck the checkbox
    });
});

    </script>
    <footer class="site-footer">
  <div class="footer-container">
    <!-- Contact Info -->
    <div class="footer-column">
      <h3>Zieers</h3>
      <p><strong>Email:</strong> <a href="mailto:support@zieers.com">support@zieers.com</a></p>
      <p><strong>Phone:</strong> +91-9341059619</p>
      <p><strong>Address:</strong><br>
        Zieers Systems Pvt Ltd,<br>
        5BC-938, 1st Block, Hennur Road,<br>
        2nd Cross Rd, Babusabpalya, Kalyan Nagar,<br>
        Bengaluru, Karnataka 560043
      </p>
    </div>

    <!-- Quick Links -->
    <div class="footer-column">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="../../about-us.php">About Us</a></li>
        <li><a href="../../contact-us.php">Contact Us</a></li>
      </ul>
    </div>

    <!-- Legal + LinkedIn -->
    <div class="footer-column">
      <h4>Legal</h4>
      <ul>
        <li><a href="../../privacy_policy.php">Privacy Policy</a></li>
      </ul>
      <a href="https://www.linkedin.com/company/your-company-name" target="_blank" class="social-link">
        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/linkedin/linkedin-original.svg" alt="LinkedIn" class="social-icon">
      </a>
    </div>
  </div>

  <div class="footer-bottom">
   <p onclick="window.open('https://www.zieers.com/', '_blank');">
    &copy; <span id="year"></span> Zieers Systems Pvt Ltd. All rights reserved.
</p>
  </div>
</footer>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
</body>
</html>
