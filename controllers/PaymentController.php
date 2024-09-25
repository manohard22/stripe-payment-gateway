<?php
require_once __DIR__ . '/../models/PaymentModel.php';

class PaymentController {
    private $model;

    public function __construct() {
        $this->model = new PaymentModel();
    }

    public function showPaymentForm() {
        require __DIR__ . '/../views/payment_form.php';
    }

    public function processDetails() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['payment_details'] = [
                'amount' => $_POST['amount'],
                'label' => $_POST['label'],
                'location' => $_POST['location'],
                'attendant' => $_POST['attendant'],
                'terminal' => $_POST['terminal']
            ];

            $result = $this->model->createPaymentIntent($_POST['amount'], $_SESSION['payment_details']);

            if ($result['status']) {
                $_SESSION['client_secret'] = $result['data']['client_secret'];
                header('Location: index.php?action=show_credit_card_form');
            } else {
                $_SESSION['error'] = $result['error'];
                header('Location: index.php?action=error');
            }
            exit;
        }
    }

    public function showCreditCardForm() {
        $config = require __DIR__ . '/../config/config.php';
        $stripePublishableKey = $config['stripe_publishable_key'];
        $clientSecret = $_SESSION['client_secret'];
        require __DIR__ . '/../views/credit_card_form.php';
    }

    public function showSuccess() {
        if (isset($_GET['payment_intent'])) {
            $result = $this->model->getPaymentIntent($_GET['payment_intent']);
            if ($result['status']) {
                $paymentIntent = $result['data'];
                require __DIR__ . '/../views/success.php';
            } else {
                $_SESSION['error'] = $result['error'];
                header('Location: index.php?action=error');
            }
        } else {
            header('Location: index.php');
        }
    }

    public function showError() {
        $error = $_SESSION['error'] ?? 'An unknown error occurred';
        unset($_SESSION['error']);
        require __DIR__ . '/../views/error.php';
    }
}