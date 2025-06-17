<?php
require '../../vendor/autoload.php';
use Dotenv\Dotenv;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

ini_set('max_execution_time', 300);

// === Gemini Editing Function ===
function editWithGemini($text)
{
    $apiKey = $_ENV['GEMINI_API_KEY'];
// Replace with your actual API key
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

   $prompt = "You are a professional academic editor with expertise in preparing research manuscripts for publication in peer-reviewed journals.\n"
        . "Your task is to edit the following manuscript content to ensure it meets high standards of grammar, spelling, clarity, consistency, and word choice.\n"
        . "Do not alter the structure, layout, or formatting of the content. Preserve all:\n"
        . "- Headings\n"
        . "- Sections and subsections\n"
        . "- Citations and references\n"
        . "- Figures, tables, and captions\n"
        . "- Mathematical notations and symbols\n\n"
        . "Ensure the following:\n"
        . "- The tone is formal and suitable for academic publishing\n"
        . "- Terminology is consistent and discipline-specific\n"
        . "- Sentences are clear, concise, and logically structured\n"
        . "- No content is added, removed, or reinterpreted\n\n"
        . "This is for submission to a scholarly journal, so edit carefully while preserving the author's intent.\n\n"
        . "TEXT TO EDIT:\n\n" . $text;


    $data = ["contents" => [["parts" => [["text" => $prompt]]]]];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['candidates'][0]['content']['parts'][0]['text'] ?? $text;
}

// === Load DOCX file ===
$filePath = $_GET['file'] ?? '';
if (!file_exists($filePath)) {
    die("File not found.");
}

$phpWord = IOFactory::load($filePath);

// === Extract text and images ===
$elementsList = [];

foreach ($phpWord->getSections() as $section) {
    foreach ($section->getElements() as $element) {
        $class = get_class($element);

        if (method_exists($element, 'getText')) {
            $elementsList[] = ['type' => 'text', 'content' => $element->getText()];
        } elseif ($class === 'PhpOffice\\PhpWord\\Element\\Image') {
            $elementsList[] = ['type' => 'image', 'object' => $element];
        }
    }
}

// === Combine all text ===
$fullText = '';
foreach ($elementsList as $el) {
    if ($el['type'] === 'text') {
        $fullText .= $el['content'] . "\n";
    }
}

// === Edit text ===
$editedText = editWithGemini($fullText);
$editedParagraphs = explode("\n", $editedText);

// === Rebuild document ===
$editedDoc = new PhpWord();
$editedSection = $editedDoc->addSection();

$textIndex = 0;
foreach ($elementsList as $el) {
    if ($el['type'] === 'text') {
        while ($textIndex < count($editedParagraphs) && trim($editedParagraphs[$textIndex]) === '') {
            $textIndex++;
        }
        $editedSection->addText($editedParagraphs[$textIndex] ?? '');
        $textIndex++;
    } elseif ($el['type'] === 'image') {
        $img = $el['object'];
        try {
            $style = method_exists($img, 'getStyle') ? $img->getStyle() : null;
            $editedSection->addImage($img->getSource(), [
                'wrappingStyle' => 'inline',
                'width' => $style && method_exists($style, 'getWidth') ? $style->getWidth() : null,
                'height' => $style && method_exists($style, 'getHeight') ? $style->getHeight() : null,
            ]);
        } catch (Exception $e) {
            $editedSection->addText("[Image could not be rendered]");
        }
    }
}

// === Save edited document ===
$editedDir = __DIR__ . '/edited';
if (!is_dir($editedDir)) {
    mkdir($editedDir, 0777, true);
}

$tempFile = $editedDir . '/edited_' . basename($filePath);
IOFactory::createWriter($editedDoc, 'Word2007')->save($tempFile);

session_start();
$_SESSION['edited_doc'] = $tempFile;

$previewLines = array_slice($editedParagraphs, 0, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edited Manuscript | Zieers</title>
  <style>
    body {
            font-family: 'Poppins', sans-serif;
            background-color: #eef2f3;
            color: #333;
        }
header {
    background: #002147;
    padding: 20px;
    color: white;
}
.header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}

.header-container h1 {
    margin: 0;
    flex: 0 0 auto; 
    justify-content: space-between;/* Keeps Zieers on the left */
}

nav {
    position: static; /* Reset absolute positioning */
    transform: none;  /* Reset transform */
}
nav ul {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
}
nav ul li {
    margin-right: 20px;
}
nav ul li a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}
nav ul li a:hover {
    background-color: #004080;
} 
/* Dropdown Menu */
.dropdown {
    position: relative;
}
.dropdown-menu {
    display: none;
    position: absolute;
    background-color: #002147;
    list-style: none;
    padding: 0;
    margin: 0;
    z-index: 100;
    border-radius: 5px;
}
.dropdown-menu li a {
    display: block;
    padding: 10px 20px;
    color: white;
    text-decoration: none;
}
.dropdown:hover .dropdown-menu {
    display: block;
}
.dropdown-menu li a:hover {
    background-color: #004080;
}
.breadcrumb-container {
      background: #e6ecf0;
      padding: 10px 0;
    }

    .breadcrumb {
      list-style: none;
      display: flex;
      gap: 8px;
      font-size: 14px;
    }

    .breadcrumb li a {
      color: #002147;
      text-decoration: none;
    }
    .container {
      max-width: 900px;
      background: #fff;
      margin: 40px auto;
      padding: 40px;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.08);
      border-radius: 12px;
    }

    h2 {
      color: #002147;
      margin-bottom: 25px;
      text-align: center;
    }

    .locked {
      background: linear-gradient(to bottom, #fff 0%, #fff 40%, #f1f1f1 100%);
      color: #333;
      max-height: 280px;
      overflow: hidden;
      position: relative;
      font-family: 'Courier New', monospace;
      white-space: pre-wrap;
      border: 1px solid #ccc;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 30px;
    }

    .locked::after {
      content: "🔒 Full content locked. Download to view complete paper.";
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      color: #002147;
      font-weight: 600;
      background-color: #f4f4f4;
      padding: 5px 15px;
      border-radius: 20px;
    }

    .center-button {
      text-align: center;
    }

    .center-button a {
      display: inline-block;
      padding: 14px 30px;
      font-size: 16px;
      font-weight: bold;
      background-color: #002147;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      transition: background-color 0.3s ease;
    }

    .center-button a:hover {
      background-color: #004a98;
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
        .site-footer {
  background-color: #002147;
  color: white;
  padding: 40px 10%;
  font-family: 'Poppins', sans-serif;
}

.footer-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 30px;
}

.footer-column {
  flex: 1;
  min-width: 250px;
}

.footer-column h3,
.footer-column h4 {
  margin-bottom: 15px;
  color: #ffffff;
}

.footer-column p,
.footer-column a,
.footer-column li {
  font-size: 14px;
  color: #ccc;
  line-height: 1.6;
  text-decoration: none;
}

.footer-column a:hover {
  color: #ffffff;
  text-decoration: underline;
}

.footer-column ul {
  list-style: none;
  padding-left: 0;
}

.footer-bottom {
  text-align: center;
  margin-top: 40px;
  border-top: 1px solid #444;
  padding-top: 20px;
  font-size: 13px;
  color: #aaa;
}
.social-link {
  display: flex;
  align-items: center;
  color: #ccc;
  text-decoration: none;
  margin-top: 10px;
}

.social-link:hover {
  color: white;
  text-decoration: underline;
}

.social-icon {
  width: 20px;
  height: 20px;
  margin-right: 8px;
}
  </style>
</head>
<body>
   <header>
    <div class="header-container">
        <h1>Zieers</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="../../publish.php">Home</a></li>
                <li><a href="../../services.php">Services</a></li>
                 <li class="dropdown">
    <a href="#">For Users ▼</a>
    <ul class="dropdown-menu">
        <li><a href="../../for_author.php">For Author</a></li>
        <li><a href="../../for_reviewer.php">For Reviewer</a></li>
        <li><a href="../../for_editor.php">For Editor</a></li>
    </ul>
</li>
</ul>
</nav>
</header>
<div class="breadcrumb-container">
    <ul class="breadcrumb">
      <li><a href="language_check_form.php">Publish with Zieers</a></li>
      <li>&gt;</li>
      <li>API</li>
    </ul>
</div>
  <div class="container">
    <h2>Edited Research Manuscript</h2>
    <div class="locked">
      <?php echo nl2br(htmlspecialchars(implode("\n", $previewLines))); ?>
    </div>

    <div class="center-button">
      <a href="download_docx.php">Download Edited Document</a>
    </div>
  </div>
  <footer class="site-footer">
  <div class="footer-container">
    <!-- Contact Info -->
    <div class="footer-column">
      <h3>Zieers</h3>
      <p><strong>Email:</strong> <a href="mailto:support@zieers.com">support@zieers.com</a></p>
      <p><strong>Phone:</strong> +91-9341059619</p>
      <p><strong>Address:</strong><br>
        Zieers Systems Pvt Ltd,<br>
        5BC-938, 1st Block, Hennur Road,<br>
        2nd Cross Rd, Babusabpalya, Kalyan Nagar,<br>
        Bengaluru, Karnataka 560043
      </p>
    </div>

    <!-- Quick Links -->
    <div class="footer-column">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="about-us.php">About Us</a></li>
        <li><a href="contact-us.php">Contact Us</a></li>
      </ul>
    </div>

    <!-- Legal + LinkedIn -->
    <div class="footer-column">
      <h4>Legal</h4>
      <ul>
        <li><a href="privacy_policy.php">Privacy Policy</a></li>
      </ul>
      <a href="https://www.linkedin.com/company/your-company-name" target="_blank" class="social-link">
        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/linkedin/linkedin-original.svg" alt="LinkedIn" class="social-icon">
      </a>
    </div>
  </div>

  <div class="footer-bottom">
   <p onclick="window.open('https://www.zieers.com/', '_blank');">
    &copy; <span id="year"></span> Zieers Systems Pvt Ltd. All rights reserved.
</p>
  </div>
</footer>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
</body>
</html>
