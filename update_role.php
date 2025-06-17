<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $_SESSION['role'] = $_POST['role'];
  $_SESSION['first_name'] = $_POST['first_name'];
  $_SESSION['middle_name'] = $_POST['middle_name'];
  $_SESSION['last_name'] = $_POST['last_name'];
  $_SESSION['email'] = $_POST['email'];

  header("Location: publish.php");
  exit();
}
?>
