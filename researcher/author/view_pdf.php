<?php
$filename = $_GET['file'];
$filepath = "../../uploads/" . basename($filename);

if (file_exists($filepath)) {
    header('Content-type: application/pdf');
    readfile($filepath);
} else {
    echo "File not found.";
}
?>
