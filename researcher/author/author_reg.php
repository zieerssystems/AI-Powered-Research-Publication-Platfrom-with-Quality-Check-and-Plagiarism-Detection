<?php
session_start(); // Start the session at the beginning
include(__DIR__ . "/../../include/db_connect.php");
// Check if journal_id exists in the URL and store it in the session
if (isset($_GET['journal_id'])) {
    $_SESSION['journal_id'] = $_GET['journal_id'];
}
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
// Ensure journal_id is set in session before using it
$journal_id = $_SESSION['journal_id'] ?? (isset($_GET['journal_id']) ? intval($_GET['journal_id']) : '');

// Fetch journal details
$journal = getJournalDetails($journal_id);

if (!$journal) {
    die("Error: No journal found with the given ID.");
}

$journal_name = htmlspecialchars($journal['journal_name'] ?? 'Unknown Journal');
// Redirect if required session data is missing
if (!isset($_SESSION['first_name']) || !isset($_SESSION['last_name']) || !isset($_SESSION['email']) || empty($journal_id)) {
    header("Location: article_register.php?journal_id=" . urlencode($journal_id));
    exit();
}

// Fetch session data
$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'] ?? '';
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];

$success = isset($_GET['success']) ? true : false;
$user_id = $_SESSION['user_id'] ?? null;
$common_data = $user_id ? getUserCommonDetails($user_id) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Registration</title>
    <style>
        header {
            display: flex;
            justify-content: center; /* Center the journal name */
            align-items: center;
            background-color: #002147; /* Keep the dark blue */
            color: white;
            padding: 15px 20px;
            font-size: 20px;
            font-weight: bold;
            width: 100%;
        }

        .sub-header {
            background-color: #e0e0e0;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .sub-header a {
            color: #333;
            text-decoration: none;
            margin-right: 15px;
        }

        .sub-header a:hover {
            text-decoration: underline;
        }

        body {
            font-family: Arial, sans-serif;
            background: white; /* Changed to white */
            color: black;
            display: flex;
            flex-direction: column; /* Stack header, sub-header, and form vertically */
            align-items: center;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 450px;
            max-height: 90vh;
            overflow-y: auto;
            background: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid #0077b6;
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
        .info-container {
            display: flex;
            align-items: center;
            position: relative;
        }
        .info-icon {
            margin-left: 10px;
            cursor: pointer;
            font-size: 18px;
        }
        .tooltip {
            display: none;
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            width: 200px;
            text-align: center;
            top: 30px;
            left: 0;
            z-index: 10;
        }
        .tooltip button {
            background: red;
            border: none;
            color: white;
            padding: 5px;
            margin-top: 10px;
            cursor: pointer;
        }
        .password-container {
            position: relative;
            margin-top: 15px;
        }

        .password-container input {
            width: 100%;
            padding-right: 40px; /* space for eye icon */
            height: 40px; /* fix height */
            box-sizing: border-box;
        }

        .eye-icon {
            position: absolute;
            right: 10px;
            top: calc(50% + 10px); /* move eye down to center of input */
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
            margin-top: 0;
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
            document.getElementById("institution-info").style.display = show ? "block" : "none";
        }
        function validateForm() {
            let telephone = document.getElementById("telephone").value.trim();
            if (telephone.length < 10) {
                alert("Please enter a valid telephone number.");
                return false;
            }
            return true;
        }

        $(document).ready(function() {
            $('select[name="country"]').select2({
                placeholder: "Select Your Country",
                allowClear: true
            });
        });
    </script>
</head>
<body>
<header>
    <div><?php echo htmlspecialchars($journal_name); ?></div>
</header>

<div class="sub-header">
    <a href="journal_detail.php?journal_id=<?php echo $journal_id; ?>">Home</a>
    <a href="article_login.php?journal_id=<?php echo $journal_id; ?>">Submit a Manuscript</a>
</div>
<div class="container">
    <h2>Author Registration</h2>
    <form action="process_author.php" onsubmit="return validateForm()" method="POST">
        <input type="hidden" name="role" value="<?php echo htmlspecialchars($_GET['role'] ?? ''); ?>">
        <input type="hidden" name="journal_ids[]" value="<?php echo htmlspecialchars($journal_id, ENT_QUOTES, 'UTF-8'); ?>">
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

<label for="address">Personal Address*</label>
<input type="textarea" name="address" required
       value="<?php echo htmlspecialchars($common_data['address'] ?? '', ENT_QUOTES); ?>">
<label for="gender">Gender*</label>
<select id="gender" name="gender" required>
    <option value="">Select</option>
    <option value="male" <?= ($details['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
    <option value="female" <?= ($details['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
    <option value="other" <?= ($details['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
</select>
 <h2>Researcher Type</h2>
<div class="radio-group">
    <label>
    Individual Researcher
        <input type="radio" name="researcher_type" value="individual" onclick="toggleInstitutionFields(false)" checked>
      
    </label>
    <label>
    Affiliated with Institution
        <input type="radio" name="researcher_type" value="affiliated" onclick="toggleInstitutionFields(true)">
       
    </label>
</div>
<div id="institution-info">
<h2>Institution Details</h2>
<label for="position">Position*</label>
<input type="text" name="position"
       value="<?php echo htmlspecialchars($common_data['position'] ?? '', ENT_QUOTES); ?>">
<label for="institution">Institution*</label>
<input type="text" name="institution"
       value="<?php echo htmlspecialchars($common_data['institution'] ?? '', ENT_QUOTES); ?>">
<label for="institution">Department*</label>
<input type="text" name="department"
       value="<?php echo htmlspecialchars($common_data['department'] ?? '', ENT_QUOTES); ?>">
<label for="street_address">Street Address*</label>
<input type="text" name="street_address"
       value="<?php echo htmlspecialchars($common_data['street_address'] ?? '', ENT_QUOTES); ?>">

<label for="city">City*</label>
<input type="text" name="city"
       value="<?php echo htmlspecialchars($common_data['city'] ?? '', ENT_QUOTES); ?>">
<label for="state">State*</label>
<input type="text" name="state"
       value="<?php echo htmlspecialchars($common_data['state'] ?? '', ENT_QUOTES); ?>">
       <label for="zip_code">zip_code*</label>
<input type="text" name="zip_code"
       value="<?php echo htmlspecialchars($common_data['zip_code'] ?? '', ENT_QUOTES); ?>">
        <select id="countrySelect" class="input-box" name="country">
        <option value="AF" <?= ($details['country'] ?? '') === 'AF' ? 'selected' : '' ?>>Afghanistan</option>
    <option value="AL" <?= ($details['country'] ?? '') === 'AL' ? 'selected' : '' ?>>Albania</option>
    <option value="DZ" <?= ($details['country'] ?? '') === 'DZ' ? 'selected' : '' ?>>Algeria</option>
    <option value="AD" <?= ($details['country'] ?? '') === 'AD' ? 'selected' : '' ?>>Andorra</option>
    <option value="AO" <?= ($details['country'] ?? '') === 'AO' ? 'selected' : '' ?>>Angola</option>
    <option value="AR" <?= ($details['country'] ?? '') === 'AR' ? 'selected' : '' ?>>Argentina</option>
    <option value="AM" <?= ($details['country'] ?? '') === 'AM' ? 'selected' : '' ?>>Armenia</option>
    <option value="AU" <?= ($details['country'] ?? '') === 'AU' ? 'selected' : '' ?>>Australia</option>
    <option value="AT" <?= ($details['country'] ?? '') === 'AT' ? 'selected' : '' ?>>Austria</option>
    <option value="AZ" <?= ($details['country'] ?? '') === 'AZ' ? 'selected' : '' ?>>Azerbaijan</option>
    <option value="BD" <?= ($details['country'] ?? '') === 'BD' ? 'selected' : '' ?>>Bangladesh</option>
    <option value="BY" <?= ($details['country'] ?? '') === 'BY' ? 'selected' : '' ?>>Belarus</option>
    <option value="BE" <?= ($details['country'] ?? '') === 'BE' ? 'selected' : '' ?>>Belgium</option>
    <option value="BR" <?= ($details['country'] ?? '') === 'BR' ? 'selected' : '' ?>>Brazil</option>
    <option value="BG" <?= ($details['country'] ?? '') === 'BG' ? 'selected' : '' ?>>Bulgaria</option>
    <option value="CA" <?= ($details['country'] ?? '') === 'CA' ? 'selected' : '' ?>>Canada</option>
    <option value="CN" <?= ($details['country'] ?? '') === 'CN' ? 'selected' : '' ?>>China</option>
    <option value="CO" <?= ($details['country'] ?? '') === 'CO' ? 'selected' : '' ?>>Colombia</option>
    <option value="DK" <?= ($details['country'] ?? '') === 'DK' ? 'selected' : '' ?>>Denmark</option>
    <option value="EG" <?= ($details['country'] ?? '') === 'EG' ? 'selected' : '' ?>>Egypt</option>
    <option value="FR" <?= ($details['country'] ?? '') === 'FR' ? 'selected' : '' ?>>France</option>
    <option value="DE" <?= ($details['country'] ?? '') === 'DE' ? 'selected' : '' ?>>Germany</option>
    <option value="IN" <?= ($details['country'] ?? '') === 'IN' ? 'selected' : '' ?>>India</option>
    <option value="ID" <?= ($details['country'] ?? '') === 'ID' ? 'selected' : '' ?>>Indonesia</option>
    <option value="IT" <?= ($details['country'] ?? '') === 'IT' ? 'selected' : '' ?>>Italy</option>
    <option value="JP" <?= ($details['country'] ?? '') === 'JP' ? 'selected' : '' ?>>Japan</option>
    <option value="MX" <?= ($details['country'] ?? '') === 'MX' ? 'selected' : '' ?>>Mexico</option>
    <option value="NL" <?= ($details['country'] ?? '') === 'NL' ? 'selected' : '' ?>>Netherlands</option>
    <option value="NG" <?= ($details['country'] ?? '') === 'NG' ? 'selected' : '' ?>>Nigeria</option>
    <option value="PK" <?= ($details['country'] ?? '') === 'PK' ? 'selected' : '' ?>>Pakistan</option>
    <option value="PH" <?= ($details['country'] ?? '') === 'PH' ? 'selected' : '' ?>>Philippines</option>
    <option value="PL" <?= ($details['country'] ?? '') === 'PL' ? 'selected' : '' ?>>Poland</option>
    <option value="PT" <?= ($details['country'] ?? '') === 'PT' ? 'selected' : '' ?>>Portugal</option>
    <option value="RU" <?= ($details['country'] ?? '') === 'RU' ? 'selected' : '' ?>>Russia</option>
    <option value="SA" <?= ($details['country'] ?? '') === 'SA' ? 'selected' : '' ?>>Saudi Arabia</option>
    <option value="ZA" <?= ($details['country'] ?? '') === 'ZA' ? 'selected' : '' ?>>South Africa</option>
    <option value="KR" <?= ($details['country'] ?? '') === 'KR' ? 'selected' : '' ?>>South Korea</option>
    <option value="ES" <?= ($details['country'] ?? '') === 'ES' ? 'selected' : '' ?>>Spain</option>
    <option value="SE" <?= ($details['country'] ?? '') === 'SE' ? 'selected' : '' ?>>Sweden</option>
    <option value="CH" <?= ($details['country'] ?? '') === 'CH' ? 'selected' : '' ?>>Switzerland</option>
    <option value="TR" <?= ($details['country'] ?? '') === 'TR' ? 'selected' : '' ?>>Turkey</option>
    <option value="UA" <?= ($details['country'] ?? '') === 'UA' ? 'selected' : '' ?>>Ukraine</option>
    <option value="AE" <?= ($details['country'] ?? '') === 'AE' ? 'selected' : '' ?>>United Arab Emirates</option>
    <option value="GB" <?= ($details['country'] ?? '') === 'GB' ? 'selected' : '' ?>>United Kingdom</option>
    <option value="US" <?= ($details['country'] ?? '') === 'US' ? 'selected' : '' ?>>United States</option>
</select>
            </div>

            <button type="submit">Register</button>
        </form>
    
    </div>
</body>
</html>
