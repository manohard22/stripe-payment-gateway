<?php
require_once __DIR__ . '/../utils/ApiClient.php';
require_once __DIR__ . '/../config/constants.php';

class StripeApi {
    private $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../config/config.php';
    }

    public function createPaymentIntent($amount, $metadata = []) {
        $data = [
            'amount' => (int)($amount * 100),
            'currency' => $this->config['currency'],
            'payment_method_types[]' => 'card'
        ];

        // Add metadata
        foreach ($metadata as $key => $value) {
            $data["metadata[$key]"] = $value;
        }

        $headers = [
            'Authorization: Bearer ' . $this->config['stripe_secret_key'],
            'Content-Type: application/x-www-form-urlencoded'
        ];

        return ApiClient::makeApiCall(STRIPE_PAYMENT_INTENTS_URL, 'POST', $data, $headers);
    }

    public function retrievePaymentIntent($paymentIntentId) {
        $url = STRIPE_PAYMENT_INTENTS_URL . '/' . $paymentIntentId;
        $headers = ['Authorization: Bearer ' . $this->config['stripe_secret_key']];

        return ApiClient::makeApiCall($url, 'GET', [], $headers);
    }
}