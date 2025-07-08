<?php 
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if (isset($_GET['journal_id'])) {
    $_SESSION['journal_id'] = $_GET['journal_id'];
}

$role = $_SESSION['role'] ?? '';
$journal_id = $_SESSION['journal_id'] ?? '';

if (!isset($_SESSION['first_name']) || !isset($_SESSION['last_name']) || !isset($_SESSION['email'])) {
    header("Location: article_register.php");
    exit();
}

$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'] ?? '';
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
$user_id = $_SESSION['user_id'] ?? 0;

$common_data = $user_id ? getUserCommonDetails(user_id: $user_id) : [];
$bankDetails = getEditorBankDetails($user_id);

if (!$bankDetails || empty($bankDetails['editor_account_holder'])) {
    $reviewerBank = getReviewerBankDetails($user_id);
    if ($reviewerBank) {
        $bankDetails = [
            'editor_payment_type' => 'paid',
            'editor_account_holder' => $reviewerBank['account_holder_name'],
            'editor_bank_name' => $reviewerBank['bank_name'],
            'editor_account_number' => $reviewerBank['account_number'],
            'editor_ifsc' => $reviewerBank['ifsc_code'],
            'editor_branch_name' => $reviewerBank['branch_name'],
            'editor_bank_country' => $reviewerBank['bank_country']
        ];
    }
}

$success = isset($_GET['success']) ? true : false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Registration</title>
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
  max-width: 900px;
  width: 95%;
  margin: 40px auto 80px auto;
  background: white;
  border-radius: 12px;
  padding: 40px 60px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background: #f9f9f9;
            font-size: 14px;
        }
        textarea {
            height: 70px;
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            background: #28a745;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .tooltip {
  background:grey;
  color: white;
  padding: 8px 14px;
  border-radius: 6px;
  position: absolute;
  font-size: 14px;
  margin-top: 4px;
  display: none;
  max-width: 250px;
  z-index: 10;
  box-shadow: 0 3px 8px rgba(0,0,0,0.2);
}

#tooltip button {
  background: transparent;
  border: none;
  color: white;
  font-weight: bold;
  float: right;
  font-size: 14px;
  cursor: pointer;
}
   .password-container {
            position: relative;
            margin-top: 15px;
        }
        .password-container input {
            width: 100%;
            padding-right: 40px;
            height: 40px;
            box-sizing: border-box;
        }
        .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
        }
        #institution-info {
            display: none;
            margin-top: 20px;
        }
        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }
        .radio-group label {
            display: flex;
            align-items: center;
            font-weight: normal;
            gap: 8px;
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
        // function toggleInstitutionFields(show) {
        //     document.getElementById("institution-info").style.display = show ? "block" : "none";
        // }
     function clearInstitutionFields() {
    const institutionFields = document.querySelectorAll('#institution-info input, #institution-info select, #institution-info textarea');
    institutionFields.forEach(field => {
        field.value = '';
        field.setAttribute('disabled', 'disabled');
    });
}

function enableInstitutionFields() {
    const institutionFields = document.querySelectorAll('#institution-info input, #institution-info select, #institution-info textarea');
    institutionFields.forEach(field => {
        field.removeAttribute('disabled');
    });
}

function toggleInstitutionFields(show) {
    const institutionDiv = document.getElementById("institution-info");
    institutionDiv.style.display = show ? "block" : "none";
    if (show) {
        enableInstitutionFields();
    } else {
        clearInstitutionFields();
    }
}


        function toggleBankDetails() {
            let paymentType = document.querySelector('input[name="editor_payment_type"]:checked').value;
            let bankDetailsDiv = document.getElementById('editorBankDetails');
            let inputs = bankDetailsDiv.querySelectorAll('input');
            if (paymentType === 'paid') {
                bankDetailsDiv.style.display = 'block';
                inputs.forEach(input => input.removeAttribute('disabled'));
            } else {
                bankDetailsDiv.style.display = 'none';
                inputs.forEach(input => input.setAttribute('disabled', 'disabled'));
            }
        }
        window.onload = function() {
            toggleInstitutionFields(document.querySelector('input[name="Editor_Type"]:checked').value === 'affiliated');
            toggleBankDetails();
        };
    </script>
</head>
<body>
<header>
  <div class="logo">Zieers</div>
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
        background-color:rgb(137, 124, 124);
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
    <h2>Editor Registration</h2>

    <?php if ($success): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
        ✅ Registration successful! Redirecting to login...
    </div>
    <script>
        setTimeout(() => {
            window.location.href = 'login-page.php';
        }, 3000);
    </script>
    <?php endif; ?>

    <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
        <strong>Error(s):</strong>
        <ul>
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>


        <form action="process_editor.php" onsubmit="return validateForm()" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="role" value="<?php echo htmlspecialchars($_GET['role'] ?? ''); ?>">
         <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
    <h2>Personal Details</h2>
        <label for="title">Title*</label>
        <input type="text" id="title" name="title" required
               onfocus="showTooltip()" onblur="hideTooltip()">

        <div id="tooltip" class="tooltip">
            Enter your personal title (Dr)
            <button type="button" onclick="hideTooltip()">Close</button>
        </div>


 <label for="telephone">Telephone (include country code)*</label>
<input type="text" id="telephone" name="telephone"  required
       value="<?php echo htmlspecialchars($common_data['telephone'] ?? '', ENT_QUOTES); ?>">

<label for="degree">Degree (PhD, MD, etc.)*</label>
<input type="text" name="degree" required
       value="<?php echo htmlspecialchars($common_data['degree'] ?? '', ENT_QUOTES); ?>">

<label for="address">Personal Address*</label>
<input type="textarea" name="address" required
       value="<?php echo htmlspecialchars($common_data['address'] ?? '', ENT_QUOTES); ?>">
<label for="gender">Gender*</label>
<select name="gender" id="gender">
  <option value="">Select Gender</option>
  <option value="Male" <?= (isset($common_data['gender']) && $common_data['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
  <option value="Female" <?= (isset($common_data['gender']) && $common_data['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
  <option value="Other" <?= (isset($common_data['gender']) && $common_data['gender'] === 'Other') ? 'selected' : '' ?>>Other</option>
</select>

<div class="radio-group">
    <label>
    Individual Editor
        <input type="radio" name="editor_type" value="individual" onclick="toggleInstitutionFields(false)" checked>
      
    </label>
    <label>
    Affiliated with Institution
        <input type="radio" name="editor_type" value="affiliated" onclick="toggleInstitutionFields(true)">
       
    </label>
</div>
<!-- <h2>Editor Type</h2>

<div class="radio-group">

    <label>
        Individual Editor
        <input type="radio" name="editor_type" value="individual" 
        <?= ($details['editor_type'] ?? '') === 'individual' ? 'checked' : '' ?> 
        onclick="toggleInstitutionFields(false)">
    </label>
    <label>
        Affiliated with Institution
        <input type="radio" name="editor_type" value="affiliated" 
        <?= ($details['editor_type'] ?? '') === 'affiliated' ? 'checked' : '' ?> 
        onclick="toggleInstitutionFields(true)">
    </label>
</div> -->

<div id="institution-info" style="<?= ($details['editor_type'] ?? '') === 'affiliated' ? '' : 'display:none;' ?>">
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
            <h2>Editor Information</h2>
    <h3>Have you served on an editorial board?</h3>
    <select class="input-box" name="editor_board" required>
        <option value="yes">Yes</option>
        <option value="no">No</option>
    </select>
    <label for="paper_name">Research Paper Title</label>
<input type="text" name="paper_name" id="paper_name" required>

<label for="co_author">Co-author(s) (optional)</label>
<input type="text" name="co_author" id="co_author">
    <h3>Editorial Experience (Years)</h3>
    <input type="number" class="input-box" name="editor_experience" placeholder="Enter your editorial experience" required>
    <div class="radio-group">
            <label>
            Paid Editor
    <input type="radio" name="editor_payment_type" value="paid" onclick="toggleBankDetails()">
</label>
<label>
unpaid Editor
    <input type="radio" name="editor_payment_type" value="unpaid" onclick="toggleBankDetails()" checked>
</label>
</div>
<div id="editorBankDetails" style="display: none;" required>
    <label>Account Holder Name</label>
    <input type="text" name="editor_account_holder" value="<?= $bankDetails['editor_account_holder'] ?? '' ?>">

    <label>Bank Name</label>
    <input type="text" name="editor_bank_name" value="<?= $bankDetails['editor_bank_name'] ?? '' ?>">

    <label>Account Number</label>
    <input type="text" name="editor_account_number" value="<?= $bankDetails['editor_account_number'] ?? '' ?>">

    <label>IFSC Code</label>
    <input type="text" name="editor_ifsc" value="<?= $bankDetails['editor_ifsc'] ?? '' ?>">

    <label>Branch Name</label>
    <input type="text" name="editor_branch_name" value="<?= $bankDetails['editor_branch_name'] ?? '' ?>">

    <label>Bank Country</label>
    <input type="text" name="editor_bank_country" value="<?= $bankDetails['editor_bank_country'] ?? '' ?>">
</div>
            <label for="cv">Upload CV (PDF, Max 2MB)*</label>
    <input type="file" id="cv" name="cv" accept=".pdf" required>

            <button type="submit">Register</button>
        </form>
</div>
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
