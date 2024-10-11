<?php
require_once __DIR__ . '/../models/StripeTerminalModel.php';
require_once __DIR__ . '/../config/constants.php';

class StripeTerminalModel {
    private $config;
    private $apiKey;

    public function __construct() {
        $this->config = require __DIR__ . '/../config/config.php';
        $this->apiKey = $this->config['stripe_secret_key'];
    }
    private function makeApiRequest($url, $method = 'GET', $data = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new Exception("cURL Error: " . $err);
        }

        return json_decode($response, true);
    }
    
    public function listReaders() {
        return $this->makeApiRequest(STRIPE_TERMINAL_URL);
    }

    public function createPaymentIntent($amount) {
        $data = [
            'amount' => $amount,
            'currency' => 'sgd',
            'payment_method_types[]' => 'card_present'
        ];
        return $this->makeApiRequest(STRIPE_PAYMENT_INTENTS_URL, 'POST', $data);
    }

    public function processPayment($readerId, $paymentIntentId) {
        $data = [
            'payment_intent' => $paymentIntentId
        ];
        $url = STRIPE_TERMINAL_URL . '/' . $readerId . '/process_payment_intent';
        return $this->makeApiRequest($url, 'POST', $data);
    }

    public function getReaderStatus($readerId) {
        $url = STRIPE_TERMINAL_URL . '/' . $readerId;
        return $this->makeApiRequest($url);
    }

    public function capturePayment($paymentIntentId) {
        $url = STRIPE_PAYMENT_INTENTS_URL . '/' . $paymentIntentId . '/capture';
        return $this->makeApiRequest($url, 'POST');
    }

    public function simulatePayment($readerId, $paymentIntentId) {
        $data = [
            'type' => 'card_present',
            'payment_intent' => $paymentIntentId
        ];
        $url = STRIPE_TERMINAL_URL . '/' . $readerId . '/simulate_payment';
        return $this->makeApiRequest($url, 'POST', $data);
    }
}
?>
