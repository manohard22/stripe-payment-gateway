
<?php
require_once __DIR__ . '/../api/StripeApi.php';

class PaymentModel {
    private $stripeApi;

    public function __construct() {
        $this->stripeApi = new StripeApi();
    }

    public function createPaymentIntent($amount, $metadata = []) {
        return $this->stripeApi->createPaymentIntent($amount, $metadata);
    }

    public function getPaymentIntent($paymentIntentId) {
        return $this->stripeApi->retrievePaymentIntent($paymentIntentId);
    }
}