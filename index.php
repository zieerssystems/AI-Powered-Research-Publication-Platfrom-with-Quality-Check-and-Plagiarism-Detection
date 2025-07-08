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

        /* Additional CSS for the new structure */
        .super_container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: #002147;
            color: white;
        }

        .logo_container {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
        }

        .logo_container span {
            margin-left: 10px;
        }

        .main_nav_container ul {
            display: flex;
            list-style: none;
            gap: 1.5rem;
        }

        .main_nav_item a {
            color: white;
            text-decoration: none;
        }

        .header_side {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hamburger_container {
            display: none;
        }

        .menu_container {
            display: none;
        }

        .slider {
            width: 100%;
            height: 400px;
            overflow: hidden;
            position: relative;
        }

        .slide {
            width: 100%;
            height: 100%;
            position: absolute;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .slide.active {
            opacity: 1;
        }

        .slide-content {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 5px;
        }

        .hero_slider_container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .hero_slide {
            width: 100%;
            position: relative;
        }

        .hero_slide_background {
            width: 100%;
            height: 400px;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero_slide_content {
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 20px;
            border-radius: 5px;
        }

        .hero_slider_nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 10px;
            cursor: pointer;
            z-index: 10;
        }

        .hero_slider_left {
            left: 10px;
        }

        .hero_slider_right {
            right: 10px;
        }

        /* Services Section CSS */
        .popular {
            padding: 2rem;
            text-align: center;
        }

        .course_boxes {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            padding: 1rem;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 1rem;
        }

        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 1rem;
            text-align: center;
        }

        .card-title {
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .card-title a {
            color: #002147;
            text-decoration: none;
        }

        .card-text {
            color: #666;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <a href="index.php">
                <img src="images/new.png" alt="Zieers Logo" style="height: 50px;">
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

    <!-- Hero Section -->
    <div class="slider">
        <div class="slide active" style="background-image: url('images/slider_background.jpg'); text-align: center;">
            <div class="slide-content">
                <h1>Transform your research into a published masterpiece with us.</h1>
            </div>
        </div>
        <div class="slide" style="background-image: url('images/slider_background.jpg'); text-align: center;">
            <div class="slide-content">
                <h1>Kickstart your career journey with hands-on internship experiences.</h1>
            </div>
        </div> 
        <div class="slide" style="background-image: url('images/slider_background.jpg'); text-align: center;">
            <div class="slide-content">
                <h1>Empower your future with industry-relevant courses and skills.</h1>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.slide');
            let currentSlide = 0;

            function nextSlide() {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].classList.add('active');
            }

            // Change slide every 3 seconds
            setInterval(nextSlide, 3000);
        });
    </script>

    <!-- Services Section -->
    <div class="popular page_section">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="section_title text-center">
                        <h1>Services</h1>
                    </div>
                </div>
            </div>
            <div class="course_boxes">
                <!-- Service Cards -->
                <div class="card">
                    <img class="card-img-top" src="images/course_1.jpg" alt="Admission Guidance">
                    <div class="card-body text-center">
                        <div class="card-title"><a href="courses.html">Publish Paper</a></div>
                        <div class="card-text">Transform your research into a published masterpiece with our expert support.</div>
                    </div>
                </div>
                <div class="card">
                    <img class="card-img-top" src="images/course_2.jpg" alt="University/College Selection">
                    <div class="card-body text-center">
                        <div class="card-title"><a href="courses.html">Internship</a></div>
                        <div class="card-text">Kickstart your career journey with hands-on internship experiences.</div>
                    </div>
                </div>
                <div class="card">
                    <img class="card-img-top" src="images/career.webp" alt="Career Counselling">
                    <div class="card-body text-center">
                        <div class="card-title"><a href="courses.html">Courses</a></div>
                        <div class="card-text">Empower your future with industry-relevant courses and skills.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Testimonials -->
<div class="testimonials" style="position: relative; background: url('images/testimonials_background.jpg') center/cover no-repeat; padding: 80px 20px; color: #fff; text-align: center;">
    <div style="background-color: rgba(0, 0, 0, 0.5); padding: 40px 20px; border-radius: 10px; max-width: 900px; margin: 0 auto;">
        <div style="font-size: 50px; color: #ffc107;">“</div>
        <p style="font-size: 20px; font-weight: 400; margin-top: 20px;">
            Thanks to Zieers Educations, I got into my <strong>dream college</strong>. Their step-by-step admission support and scholarship guidance were invaluable.
        </p>
        <p style="margin-top: 10px;">I truly appreciate their dedication.</p>
        <div style="margin-top: 30px;">
            <img src="images/Aniket_Verma.png" alt="Aniket Verma" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #fff;">
            <h3 style="margin: 10px 0 5px; color: #ffc107;">ANIKET VERMA</h3>
            <p style="font-size: 16px; color: #ddd;">Medical Student</p>
        </div>
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
