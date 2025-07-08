<?php
session_start();
if (!isset($_GET['journal_id'])) {
    // Redirect or show error message
    die("Error: journal_id is missing in the URL.");
}
$journal_id = $_GET['journal_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $enteredOtp = $_POST['otp'];

    // Check if OTP matches
    if ($_SESSION['otp'] == $enteredOtp) {
        header("Location: reset_author_password.php?email=$email"); // Proceed to password reset page
        exit;
    } else {
        header("Location: otp_verify.php?email=$email&msg=invalid");
        exit;
    }
}

// Determine previous page URL or fallback
$previousPage = $_SERVER['HTTP_REFERER'] ?? 'submit-article.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>OTP Verification - Zieers</title>
<style>
  /* Header styling */
  header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background: linear-gradient(90deg, #4a90e2, #357ABD);
    color: white;
    font-family: Arial, sans-serif;
  }
  header .logo {
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    color: white;
  }
  header .nav-link {
    font-size: 16px;
    color: white;
    text-decoration: none;
    border: 1px solid white;
    padding: 6px 12px;
    border-radius: 4px;
    transition: background-color 0.3s ease;
  }
  header .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.2);
  }

  /* Form styling */
  form {
    margin: 40px auto;
    max-width: 400px;
    font-family: Arial, sans-serif;
  }
  label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
  }
  input[type="text"], input[type="hidden"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    box-sizing: border-box;
    font-size: 16px;
  }
  button {
    background-color: #357ABD;
    color: white;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  button:hover {
    background-color: #285a8e;
  }
  /* Error message */
  .error-msg {
    color: red;
    margin-top: 10px;
    font-weight: bold;
  }

  /* Footer styling */
   footer {
    text-align: center;
    padding: 15px 10px;
    font-size: 14px;
    color: #666;
    border-top: 1px solid #ddd;
    background: #fff;
    flex-shrink: 0;
  }
</style>
</head>
<body>

<header>
  <a href="../../publish.php" class="logo">Zieers</a>
  <Previous href="submit-article">Previous Page</a>
</header>

<form method="POST" novalidate>
    <label for="otp">Enter OTP:</label>
    <input type="text" id="otp" name="otp" required autofocus />
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>" />
    <button type="submit">Verify OTP</button>

    <?php
    if (isset($_GET['msg']) && $_GET['msg'] == 'invalid') {
        echo '<p class="error-msg">Invalid OTP. Please try again.</p>';
    }
    ?>
</form>

<footer>
  &copy; 2023-<?php echo date('Y'); ?> Zieers. All rights reserved.
</footer>

</body>
</html>
