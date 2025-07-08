<?php
require_once '../../vendor/autoload.php';
require('razorpay/Razorpay.php'); // Razorpay SDK


use Razorpay\Api\Api;
$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);
if ($isLocalhost)
  $config = parse_ini_file(__DIR__ . '/../../../../private/pub_config.ini', true);
else{
  require_once(__DIR__ . '/../../config_path.php');
$config = parse_ini_file(CONFIG_PATH, true);
}
// $config_path = __DIR__ . '/../../../../private/pub_config.ini';
// $config = parse_ini_file($config_path, true);

$razorpay_key = $config['api']['RAZORPAY_KEY'];
$razorpay_secret = $config['api']['RAZORPAY_SECRET'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch paper details from the POST request
    $paper_id = $_POST['paper_id'];
    $amount = $_POST['amount'];
    $journal_name = $_POST['journal_name'];

     $api = new Api($razorpay_key, $razorpay_secret);
      // Create an order
    $orderData = [
        'receipt'         => 'order_' . time(),
        'amount'          => $amount * 100, // Amount in paise
        'currency'        => 'INR',
        'payment_capture' => 1, // Auto capture
    ];

    try {
        $order = $api->order->create($orderData); // Create the order

        // Capture order ID and provide Razorpay payment gateway integration
        echo "<script src='https://checkout.razorpay.com/v1/checkout.js'></script>";
        echo "<script>
                var options = {
                    'key': '$razorpay_key',
                    'amount': '$amount' * 100, // Amount in paise
                    'currency': 'INR',
                    'name': '$journal_name',
                    'description': 'Payment for Paper Submission',
                    'order_id': '{$order->id}',
                    'handler': function (response){
                        alert('Payment Successful: ' + response.razorpay_payment_id);
                        // Redirect or process the payment info (You can store payment details in DB here)
                    },
                    'prefill': {
                        'name': 'Author Name',
                        'email': 'author@example.com',
                    },
                    'theme': {
                        'color': '#F37254'
                    }
                };
                var rzp1 = new Razorpay(options);
                rzp1.open();
              </script>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
