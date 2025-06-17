<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Login Type</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .logo img {
            height: 50px;
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
            left: -80px;
            top: 100%;
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

        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card {
            width: 300px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            background: #ffffff;
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;
            color: #002147;
        }

        .btn {
            width: 100%;
            margin-bottom: 15px;
        }

        footer {
            background: #002147;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: auto;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php">
            <img src="images/zieers_logo_org.png" alt="Zieers Logo">
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
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<div class="container">
    <div class="card">
        <h2>Login As</h2>
        <a href="login.php" class="btn btn-primary">User</a>
        <a href="admin/admin_login.php" class="btn btn-danger">Admin</a>
    </div>
</div>

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
