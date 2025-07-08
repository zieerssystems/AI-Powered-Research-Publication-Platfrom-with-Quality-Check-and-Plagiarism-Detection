<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");
include(__DIR__ . "/../../include/db_connect.php");
if (isset($_SESSION['author_email'])) {
    header("Location: author_dashboard.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $user = getUserByEmail($conn, $email);

        if ($user) {
            $user_id = $user['id'];
            $author = getAuthorByUserId($conn, $user_id);

            if ($author) {
                // Success
                $_SESSION['author_id'] = $author['id'];
                $_SESSION['author_email'] = $email;
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];

                updateAuthorLastLogin($conn, $author['id']);

                header("Location: author_dashboard.php");
                exit();
            } else {
                $error = "You're not yet registered as an author.";
            }
        } else {
            $error = "No account found with this email.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Login | Zieers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #002147, #004080);
            color: white;
            text-align: center;
            padding: 50px;
            margin: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            font-size: 18px;
        }
        .header a {
            text-decoration: none;
            color: white;
            margin: 0 10px;
        }
        .container {
            width: 350px;
            margin: auto;
            background: white;
            color: black;
            padding: 30px 25px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .container h2 {
            margin-bottom: 20px;
            font-size: 26px;
            font-weight: bold;
            color: #002147;
        }
        .input-box {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #002147;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #00152f;
        }
        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
        .note {
            margin-top: 20px;
            font-size: 13px;
            color: #555;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #ccc;
        }
    </style>
</head>
<body>
<div class="header">
    <div>Author Login</div>
    <div>
        <a href="/../../publish.php">Home</a> 
        <a href="javascript:history.back()">← Back</a>
    </div>
</div>

<div class="container">
    <h2>Author Login</h2>
    <form method="POST">
        <input type="email" name="email" class="input-box" placeholder="Enter your email" required>
        <button type="submit" class="btn">Login</button>
    </form>

    <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

    <div class="note">
    <p><strong>Note:</strong> To create an account, please submit a manuscript to any of our journals.
    You can go back and search for a proper journal in our catalog and submit your paper accordingly.</p>
</div>

    <div class="footer">
        © 2025 Zieers Publications
    </div>
</div>

</body>
</html>
