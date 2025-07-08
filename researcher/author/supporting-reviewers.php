<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Supporting Reviewers | Zieers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body {
        font-family: 'Inter', 'Poppins', sans-serif;
        background-color: #eef2f3;
        color: #333;
    }
    h1, h2, h3 {
        font-family: 'Poppins', sans-serif;
    }
    .container {
        width: 90%;
        margin: auto;
        max-width: 1200px;
    }

    header {
            background: #002147;
            color: white;
            padding: 15px 0;
        }
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        nav {
            display: flex;
            align-items: center;
            width: 100%;
        }
        nav ul {
            display: flex;
            list-style: none;
        }
        .nav-links li {
            margin: 0 10px;
        }
        .nav-links li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .nav-links li a:hover {
            background-color: #004080;
        }
    /* Mobile Nav */
    .hamburger-menu {
        display: none;
        font-size: 30px;
        cursor: pointer;
        color: white;
    }
    @media (max-width: 768px) {
        .nav-links {
            display: none;
            flex-direction: column;
            background: #002147;
            position: absolute;
            top: 60px;
            left: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            z-index: 1000;
        }
        .nav-links.active {
            display: flex;
        }
        .hamburger-menu {
            display: block;
            z-index: 1100;
        }
    }
    .subnav {
      background-color: #e9ecef;
      padding: 1rem 0;
    }
  
    .hero-section {
      background-color: #ffffff;
      padding: 4rem 1rem;
      text-align: center;
    }
    .hero-section h1 {
      font-size: 2.8rem;
      margin-bottom: 1.5rem;
      color: #007bff;
    }
    .hero-section p {
      font-size: 1.3rem;
      color: #6c757d;
    }
    .content-section {
      padding: 4rem 1rem;
    }
    .content-section h2 {
      margin-top: 2rem;
      margin-bottom: 1rem;
      font-size: 2rem;
      color: #343a40;
    }
    .content-section p {
      font-size: 1.1rem;
      color: #495057;
    }
    .content-section ul {
      list-style-type: disc;
      margin-left: 1.5rem;
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
        }
  </style>
</head>
<body>

  <!-- Header -->
  <header>
    <div class="container header-container">
        <h1>Zieers</h1>
        <div class="hamburger-menu" onclick="toggleMenu()">&#9776;</div>
        <nav>
            <ul class="nav-links">
                <li><a href="../../index.php">Home</a></li>
                <li><a href="../../services.php">Services</a></li>
                <li><a href="../../for_author.php">For Author</a></li>
                <li><a href="../../for_reviewer.php">For Reviewer</a></li>
                <li><a href="../../for_editor.php">For Editor</a></li>
            </ul>
        </nav>
    </div>
</header>

 
  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container">
      <h1>Supporting Reviewers</h1>
      <p>Learn how Zieers supports our reviewers in the peer review process, ensuring they have all the tools and resources they need to provide insightful, high-quality feedback.</p>
    </div>
  </section>

  <!-- Content Section -->
  <section class="content-section">
    <div class="container">
      <h2>Role of the Reviewer</h2>
      <p>As a reviewer for Zieers, you play an essential role in maintaining the quality and integrity of published research. Your feedback helps editors make informed decisions and ensures that only the most robust, well-researched papers are published.</p>
      
      <h2>How the Review Process Works</h2>
      <p>The review process is designed to be efficient and collaborative. Here's an overview of the steps involved:</p>
      <ul>
        <li><strong>Assignment:</strong> Editors assign reviewers to manuscripts based on their expertise and availability.</li>
        <li><strong>Initial Review:</strong> Reviewers are asked to assess the quality, relevance, and originality of the paper, as well as its methodological rigor.</li>
        <li><strong>Feedback:</strong> Reviewers provide feedback to the authors, suggesting improvements or changes to enhance the paper's quality.</li>
        <li><strong>Decision:</strong> Editors make the final decision based on reviewer comments. The paper may be accepted, revised, or rejected.</li>
      </ul>

      <h2>Reviewer Resources and Support</h2>
      <p>We provide our reviewers with comprehensive resources and training to help them complete their assessments effectively. These resources include:</p>
      <ul>
        <li><strong>Clear Guidelines:</strong> Detailed instructions on how to conduct a thorough and fair review.</li>
        <li><strong>Support from Editors:</strong> Editors are available to assist reviewers at any stage of the review process, answering questions and providing clarification as needed.</li>
        <li><strong>Training Materials:</strong> Access to resources that help improve your reviewing skills and knowledge.</li>
      </ul>

      <h2>Communication Between Editors and Reviewers</h2>
      <p>Throughout the process, editors and reviewers work closely together to ensure the quality of the manuscript. Reviewers can communicate directly with editors to clarify any concerns or provide additional feedback. This collaboration ensures that every manuscript undergoes a rigorous and thorough evaluation.</p>

      <h2>Recognizing Our Reviewers</h2>
      <p>We value the time and effort that our reviewers contribute to the peer review process. To recognize their contributions, we:</p>
      <ul>
        <li>Provide acknowledgment in the published article.</li>
        <li>Offer certificates of appreciation for completed reviews.</li>
        <li>Highlight top reviewers in our annual awards.</li>
      </ul>

      <p>We are committed to making the peer review experience as rewarding and efficient as possible for our reviewers, ensuring that you have the support you need every step of the way.</p>
    </div>
  </section>

  <!-- Footer -->
  <!-- Footer --><footer>
<p onclick="window.open('https://www.zieers.com/', '_blank');">
    &copy; <span id="year"></span> Zieers Systems Pvt Ltd. All rights reserved.
</p>

<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
    </footer>
</body>
</html>
