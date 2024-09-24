<?php
require 'vendor/autoload.php';
$config = require_once 'config.php';

\Stripe\Stripe::setApiKey($config['stripe_secret_key']);

$paymentIntentId = $_GET['payment_intent'] ?? '';

if (!$paymentIntentId) {
    die('No payment intent ID provided');
}

try {
    $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
    $amount = number_format($paymentIntent->amount / 100, 2); // Convert cents to dollars and format
    $currency = strtoupper($paymentIntent->currency);
} catch (\Stripe\Exception\ApiErrorException $e) {
    die('Error retrieving payment information: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="css/style.css" rel="stylesheet"/>
</head>
<body>
    <div class="success-page">
        <div class="success-message">Payment Successful!</div>
        <div class="payment-details">
            <p>Thank you for your payment.</p>
            <p>Amount paid: <?php echo $amount . ' ' . $currency; ?></p>
            <p>Payment ID: <?php echo $paymentIntentId; ?></p>
        </div>
        <p><a href="index.php">Return to payment page</a></p>
    </div>
</body>
</html>