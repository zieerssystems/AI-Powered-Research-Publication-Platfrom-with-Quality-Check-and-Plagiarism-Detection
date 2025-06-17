<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login - Zieers</title>
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

    .form-box {
      width: 100%;
      max-width: 400px;
      background: white;
      padding: 30px 25px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      animation: fadeIn 0.5s ease;
    }

    .form-box h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #1e3a8a;
      font-size: 24px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      color: #334155;
      font-size: 14px;
      font-weight: 500;
    }

    input {
      width: 100%;
      padding: 12px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      font-size: 15px;
      margin-bottom: 20px;
      transition: border 0.3s;
    }

    input:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .btn {
      background: #1e3a8a;
      color: white;
      padding: 12px;
      font-size: 16px;
      font-weight: 600;
      border: none;
      width: 100%;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn:hover {
      background: #1e40af;
    }

    footer {
      background: #002147;
      color: white;
      text-align: center;
      padding: 1rem;
      margin-top: auto;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 500px) {
      .form-box {
        padding: 20px;
      }

      .form-box h2 {
        font-size: 20px;
      }

      .btn {
        font-size: 15px;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">
      <a href="index.php">
        <img src="../images/zieers_logo_org.png" alt="Zieers Logo" style="height: 50px;">
      </a>
    </div>
    <nav>
      <ul>
        <li><a href="../index.php">Publish with Us</a></li>
      </ul>
    </nav>
  </header>

  <div class="container">
    <div class="form-box">
      <h2>Admin Login</h2>
      <form action="admin_login_process.php" method="POST">
        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Email" required />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" required />

        <button type="submit" class="btn">Login</button>
      </form>
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
