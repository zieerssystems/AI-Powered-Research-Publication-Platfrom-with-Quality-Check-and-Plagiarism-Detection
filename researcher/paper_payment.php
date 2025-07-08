<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';
include(__DIR__ . "/../../include/db_connect.php");

$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);
if ($isLocalhost)
  $config = parse_ini_file(__DIR__ . '/../../../../private/pub_config.ini', true);
else{
  require_once(__DIR__ . '/../../config_path.php');
$config = parse_ini_file(CONFIG_PATH, true);
}
// $config = parse_ini_file(__DIR__ . '/../../../../private/pub_config.ini', true);

if (!isset($_POST['paper_id'], $_POST['amount'])) {
    die("Invalid request");
}

$paper_id = intval($_POST['paper_id']);
$amount = floatval($_POST['amount']);  // Amount in INR

// Fetch paper details using the function from db_connect.php
$paper = getPaperTitleById($conn, $paper_id);
if (!$paper) die("Paper not found");

$razorpay_key_id = $config['api']['RAZORPAY_KEY'];
$razorpay_key_secret = $config['api']['RAZORPAY_SECRET'];
// Amount in paise (Razorpay needs smallest currency unit)
$amount_in_paise = $amount * 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Payment Checkout | Zieers</title>
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
  <h2>Pay ₹<?php echo $amount; ?> to access: <?php echo htmlspecialchars($paper['title']); ?></h2>
  <button id="rzp-button">Pay Now</button>

  <script>
    var options = {
        "key": "<?php echo $razorpay_key_id; ?>", // Enter the Key ID generated from Razorpay Dashboard
        "amount": "<?php echo $amount_in_paise; ?>", // Amount is in currency subunits. Default currency is INR. Hence, 100 = 1 INR
        "currency": "INR",
        "name": "Zieers",
        "description": "Access fee for paper: <?php echo addslashes($paper['title']); ?>",
        "image": "https://yourdomain.com/logo.png", // Optional: logo url
        "order_id": "", // Optional: for server-generated order id (not used here)
        "handler": function (response){
            // On successful payment, send data to server to confirm payment & update access
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "payment_success.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert("Payment successful! You can now access the paper.");
                    window.location.href = "paper.php?paper_id=<?php echo $paper_id; ?>";
                } else {
                    alert("Payment verification failed.");
                }
            };
            xhr.send("payment_id=" + response.razorpay_payment_id + "&paper_id=<?php echo $paper_id; ?>");
        },
        "prefill": {
            "name": "<?php echo $_SESSION['user_name'] ?? ''; ?>",
            "email": "<?php echo $_SESSION['user_email'] ?? ''; ?>"
        },
        "theme": {
            "color": "#007bff"
        }
    };
    var rzp1 = new Razorpay(options);
    document.getElementById('rzp-button').onclick = function(e){
        rzp1.open();
        e.preventDefault();
    }
  </script>
</body>
</html>
