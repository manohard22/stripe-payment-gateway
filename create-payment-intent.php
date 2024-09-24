
<?php
require 'vendor/autoload.php';
$config = require_once 'config.php';

// Set your secret key
\Stripe\Stripe::setApiKey($config['stripe_secret_key']);

header('Content-Type: application/json');

$jsonStr = file_get_contents('php://input');
$jsonObj = json_decode($jsonStr);

if (!$jsonObj || !isset($jsonObj->amount)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

try {
    // Convert amount to cents
    $amount = (int)($jsonObj->amount * 100);

    // Create a PaymentIntent with the order amount and currency
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => $config['currency'],
        'automatic_payment_methods' => [
            'enabled' => true,
        ],
    ]);

    $output = [
        'clientSecret' => $paymentIntent->client_secret,
    ];

    echo json_encode($output);
} catch (\Stripe\Exception\ApiErrorException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

?>