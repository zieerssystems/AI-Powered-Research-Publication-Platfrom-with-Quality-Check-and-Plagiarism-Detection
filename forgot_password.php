<?php
session_start();
require 'include/db_connect.php';
require 'mailer.php'; // for sending email

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $otp = rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        $_SESSION['otp'] = $otp;

        // Send OTP to email
        $subject = "Your OTP Code for Password Reset";
        $message = "Your OTP is: $otp";
        if (sendMail($email, $subject, $message)) {
            header("Location: verify_otp.php");
            exit();
        } else {
            $error = "Failed to send OTP. Try again.";
        }
    } else {
        $error = "Email not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Zieers - Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      background: linear-gradient(to bottom right, #e3f2fd, #ffffff);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      background: #002147;
      color: white;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: #fff;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 1.5rem;
    }

    nav ul li {
      cursor: pointer;
      font-weight: 500;
    }

    nav ul li a {
      text-decoration: none;
      color: white;
    }

    nav ul li a:hover {
      color: #90caf9;
    }

    .container {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }
    footer {
      background: #002147;
      color: white;
      text-align: center;
      padding: 1rem;
      margin-top: auto;
    }
.dropdown {
  position: relative;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: white;
  min-width: 160px;
  box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
  z-index: 1;
  left: -80px; /* Move dropdown to the left */
  top: 100%;   /* Place it below the name */
  border-radius: 5px;
}


    .dropdown:hover .dropdown-content {
      display: block;
    }

    .dropdown-content li {
      padding: 10px;
    }

    .dropdown-content li a {
      color: #333;
      text-decoration: none;
    }

    .dropdown-content li a:hover {
      color: #1976d2;
    }
    .otp-form {
  background: #fff;
  padding: 2rem 3rem;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 450px;
}

.otp-form h2 {
  text-align: center;
  color: #002147;
  margin-bottom: 1.5rem;
}

.otp-form label {
  display: block;
  margin-top: 1rem;
  margin-bottom: 0.3rem;
  color: #333;
}

.otp-form input[type="email"] {
  width: 100%;
  padding: 0.7rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
}

.otp-form button {
  width: 100%;
  background-color: #002147;
  color: white;
  padding: 0.75rem;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  margin-top: 1.5rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.otp-form button:hover {
  background-color: #003c7a;
}

.otp-form .error {
  color: red;
  font-size: 0.9rem;
  margin-top: 1rem;
  text-align: center;
}

.otp-form .success {
  color: green;
  font-size: 0.9rem;
  margin-top: 1rem;
  text-align: center;
}

  </style>
</head>

<body>
  <header>
    <div class="logo">
  <a href="index.php">
    <img src="images/zieers_logo_org.png" alt="Zieers Logo" style="height: 50px;">
  </a>
</div>

    <nav>
      <ul>
        <li><a href="publish.php">Publish with Us</a></li>
        <li><a href="#">Internship</a></li>
        <li><a href="#">Course</a></li>
        <?php if (isset($_SESSION['first_name'])): ?>
  <li class="dropdown">
    <span><?php echo htmlspecialchars($_SESSION['first_name']); ?> <i class="fas fa-caret-down"></i></span>
    <ul class="dropdown-content">
      <li><a href="profile.php">View Profile</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </li>
<?php else: ?>
  <li>
    <a href="login_choice.php" class="btn btn-success">Login</a>
    <!-- <a href="login.php?redirect=<?php echo urlencode($_SERVER['PHP_SELF']); ?>">Login</a> -->
  </li>
<?php endif; ?>

      </ul>
    </nav>
  </header>
<div class="container">
  <div class="otp-form">
    <form method="POST">
      <h2>Forgot Password</h2>

      <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
      <?php endif; ?>

      <label for="email">Registered Email</label>
      <input type="email" id="email" name="email" required placeholder="Enter your registered email">

      <button type="submit">Send OTP</button>
    </form>
  </div>
</div>


  <!-- Footer -->
  <footer>
   <p onclick="window.open('https://www.zieers.com/', '_blank');">
    &copy; <span id="year"></span> Zieers Systems Pvt Ltd. All rights reserved.
</p>
</footer>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
</body>
</html>

