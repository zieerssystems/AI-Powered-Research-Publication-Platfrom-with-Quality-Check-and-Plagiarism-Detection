<?php
session_start();
if (!isset($_SESSION['author_id'])) {
    header("Location: author_dash_login.php");
    exit();
}

include(__DIR__ . "/../../include/db_connect.php");

define('RAZORPAY_KEY', 'rzp_test_nKyYkRw2gRb1zO');
define('RAZORPAY_SECRET', 'NKf4dKxW8NXrQfjFvangwUYi');

$author_id = $_SESSION['author_id'];

if (isset($_POST['razorpay_payment_id']) && isset($_POST['paper_id'])) {
    $razorpay_payment_id = $_POST['razorpay_payment_id'];
    $paper_id = $_POST['paper_id'];

    markPaymentAsPaid($conn, $razorpay_payment_id, $paper_id);

    echo "<script>alert('Payment successful! Thank you.'); window.location.href = 'author_dashboard.php';</script>";
    exit();
}

$result = fetchAcceptedPapersWithAPC($conn, $author_id);


$paper_cards_html = "";
if ($result->num_rows > 0) {
    while ($paper = $result->fetch_assoc()) {
        $paper_id = $paper['paper_id'];
        $paper_title = $paper['paper_title'];
        $journal_name = $paper['journal_name'];
        $apc_amount = $paper['author_apc_amount'];
        $payment_status = $paper['payment_status'];

        $paper_cards_html .= "<div class='paper-card'>";
        $paper_cards_html .= "<p><strong>Paper Title:</strong> " . htmlspecialchars($paper_title) . "</p>";
        $paper_cards_html .= "<p><strong>Journal Name:</strong> " . htmlspecialchars($journal_name) . "</p>";
        $paper_cards_html .= "<p><strong>APC Amount:</strong> ₹" . htmlspecialchars($apc_amount) . "</p>";
        $paper_cards_html .= "<p><strong>Payment Status:</strong> " . htmlspecialchars($payment_status) . "</p>";

        if ($payment_status !== 'Paid') {
            $paper_cards_html .= "<form method='POST'>
                    <input type='hidden' name='amount' value='" . htmlspecialchars($apc_amount) . "'>
                    <input type='hidden' name='paper_id' value='" . htmlspecialchars($paper_id) . "'>
                    <input type='submit' name='pay' value='Pay Now with Razorpay' class='btn'>
                  </form>";
        }

        $paper_cards_html .= "</div>";
    }
} else {
    $paper_cards_html .= "<div class='error-box'>No accepted papers found for this author.</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    $amount = $_POST['amount'];
    $paper_id = $_POST['paper_id'];

    $razorpay_order_id = create_razorpay_order($amount);
    storePaymentDetails($conn, $razorpay_order_id, $amount, $paper_id, $author_id);

    echo "
    <form id='razorpay-form' method='POST'>
        <input type='hidden' name='paper_id' value='$paper_id'>
        <input type='hidden' name='razorpay_payment_id' id='razorpay_payment_id'>
    </form>
    <script src='https://checkout.razorpay.com/v1/checkout.js'></script>
    <script>
        var options = {
            key: '" . RAZORPAY_KEY . "',
            amount: " . ($amount * 100) . ",
            currency: 'INR',
            name: 'Zieers',
            description: 'Author APC Payment',
            order_id: '$razorpay_order_id',
            handler: function (response) {
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('razorpay-form').submit();
            },
            theme: { color: '#F37254' }
        };
        var rzp = new Razorpay(options);
        rzp.open();
    </script>";
    exit();
}

function create_razorpay_order($amount) {
    $url = "https://api.razorpay.com/v1/orders";
    $headers = ["Content-Type: application/json"];
    $data = ['amount' => $amount * 100, 'currency' => 'INR', 'payment_capture' => 1];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY . ":" . RAZORPAY_SECRET);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($response, true);
    return $response_data['id'] ?? null;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Author Payments - Zieers</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to right, #eef2f3, #ffffff);
            color: #333;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #2d2d2d;
            font-weight: 600;
            margin-bottom: 40px;
        }

        .paper-card {
            background: #fff;
            padding: 25px;
            margin: 0 auto 30px auto;
            border-radius: 16px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            max-width: 700px;
            transition: transform 0.3s ease;
        }

        .paper-card:hover {
            transform: translateY(-4px);
        }

        .paper-card p {
            margin: 10px 0;
            line-height: 1.5;
        }

        .paper-card strong {
            color: #000;
        }

        .btn {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            color: #fff;
            padding: 12px 24px;
            margin-top: 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: linear-gradient(to right, #5a67d8, #6b46c1);
        }

        .error-box {
            background: #ffeded;
            color: #c0392b;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            margin: 40px auto;
            text-align: center;
            box-shadow: 0 4px 10px rgba(255, 0, 0, 0.1);
        }
        .btn-back {
        display: inline-block;
        margin: 20px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #4b6cb7, #182848);
        color: white;
        border: none;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: linear-gradient(135deg, #182848, #4b6cb7);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }
    </style>
</head>
<body>
    <a href="author_dashboard.php" class="btn-back">← Back to Author Dashboard</a>
    <h2>Accepted Papers - Author Payment</h2>
    <?= $paper_cards_html ?>
</body>
</html>
