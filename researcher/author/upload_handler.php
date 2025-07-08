<?php
require 'vendor/autoload.php'; // Include Composer autoload file

use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;

if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
    $file = $_FILES["file"]["tmp_name"];
    $fileType = $_FILES["file"]["type"];
    $fileExt = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);

    $text = "";

    // Process TXT files
    if ($fileExt === "txt") {
        $text = file_get_contents($file);
    }
    // Process PDF files
    elseif ($fileExt === "pdf") {
        $parser = new Parser();
        $pdf = $parser->parseFile($file);
        $text = $pdf->getText();
    }
    // Process DOCX files
    elseif ($fileExt === "docx") {
        $phpWord = IOFactory::load($file);
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }
    } else {
        echo "Unsupported file type. Please upload TXT, PDF, or DOCX.";
        exit;
    }

    echo nl2br(htmlspecialchars($text));
} else {
    echo "Error uploading file.";
}
?>
