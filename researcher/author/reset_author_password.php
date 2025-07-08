<?php
session_start();
include(__DIR__ . "/../../include/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'];

    // Hash the new password
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password in the "users" table using the function from db_connect.php
    if (UserPassword($conn, $passwordHash, $email)) {
        // Clear OTP from session
        unset($_SESSION['otp']);
        header("Location: submit-article.php?msg=passwordupdated");
        exit;
    } else {
        echo "Error updating password.";
    }
}

$previousPage = $_SERVER['HTTP_REFERER'] ?? 'submit-article.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Reset Password - Zieers</title>
<style>
  /* Basic reset and full height */
  html, body {
    margin: 0; padding: 0; height: 100%;
    font-family: Arial, sans-serif;
    background: #f9f9f9;
  }

  /* Wrapper to push footer down */
  .wrapper {
    min-height: 100%;
    display: flex;
    flex-direction: column;
  }

  /* Header styling */
  header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background: linear-gradient(90deg, #4a90e2, #357ABD);
    color: white;
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

  /* Main content to grow */
  main {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
  }

  /* Form styling */
  form {
    background: white;
    padding: 30px 25px;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
    width: 100%;
    max-width: 400px;
  }
  label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
  }
  input[type="password"], input[type="hidden"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    box-sizing: border-box;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    transition: border-color 0.3s ease;
  }
  input[type="password"]:focus {
    border-color: #357ABD;
    outline: none;
  }
  button {
    background-color: #357ABD;
    color: white;
    padding: 12px 20px;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease;
  }
  button:hover {
    background-color: #285a8e;
  }

  /* Footer styling fixed at bottom */
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
<div class="wrapper">
  <header>
    <a href="../../publish.php" class="logo">Zieers</a>
    <a href="<?php echo htmlspecialchars($previousPage); ?>" class="nav-link">Previous Page</a>
  </header>

  <main>
    <form method="POST" novalidate>
        <label for="new_password">Enter New Password:</label>
        <input type="password" id="new_password" name="new_password" required autofocus />
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>" />
        <button type="submit">Update Password</button>
    </form>
  </main>

  <footer>
    &copy; 2023-<?php echo date('Y'); ?> Zieers. All rights reserved.
  </footer>
</div>
</body>
</html>
