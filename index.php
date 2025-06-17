<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Your login logic
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = loginUser($email, $password); // your auth function

    if ($user) {
        $_SESSION['user_id'] = $user['id'];

        // Redirect to original page if specified
        if (isset($_GET['redirect'])) {
            $redirectPage = $_GET['redirect'];
            header("Location: " . $redirectPage);
        } else {
            header("Location: dashboard.php"); // or default page
        }
        exit();
    } else {
        $error = "Invalid credentials.";
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
        <li class="dropdown">
  <span>Course <i class="fas fa-caret-down"></i></span>
  <ul class="dropdown-content">
     <li><a href="/admin_panel/frontend/index.php">Courses</a></li>
    <li><a href="/admin_panel/frontend/courses.php">MyCourse</a></li>
  </ul>
</li>
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

  <!-- Welcome Section -->
  <div class="welcome-section" id="welcomeSection">
    <div style="text-align:center; padding: 2rem;">
      <h1>Welcome to Zieers</h1>
      <p>Publish. Learn. Grow with us.</p>
      <img src="images/ed.png" alt="Welcome Image" style="max-width:100%; height:auto; border-radius:10px;" />
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