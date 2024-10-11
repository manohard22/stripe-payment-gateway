<?php
require_once __DIR__ . '/../models/StripeTerminalModel.php';
require_once __DIR__ . '/../models/PaymentModel.php';

class StripeTerminalController {
    private $stripeTerminalModel;

    public function __construct() {
        $this->stripeTerminalModel = new StripeTerminalModel();
        $this->paymentModel = new PaymentModel();
    }

    public function showTerminal() {
        $readers = $this->stripeTerminalModel->listReaders();
        //print_r($readers);
        require __DIR__ . '/../views/terminal.php';
    }

    public function createPayment() {
        $amount = $_POST['amount'];
        $reader_id = $_POST['reader_id'];
       
        try {
            $paymentIntent = $this->stripeTerminalModel->createPaymentIntent($amount);
            //print_r($paymentIntent);
            
            if (isset($paymentIntent['id']) && isset($paymentIntent['status'])) {
                $processedPayment = $this->stripeTerminalModel->processPayment($reader_id, $paymentIntent['id']);

                echo json_encode([
                    'paymentIntentId' => $paymentIntent['id'],
                    'status' => $paymentIntent['status'],
                    'readerStatus' => 'in_progress',
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Failed to create payment intent', 'response' => $paymentIntent]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo $e->getMessage();
        }
    }
    public function capturePayment() {
        $paymentIntent_id = $_POST['paymentIntent_id'];
    
        try {
            $paymentIntent = $this->paymentModel->getPaymentIntent($paymentIntent_id);
            //print_r($paymentIntent);
    
            if ($paymentIntent['status'] === 'requires_capture') {
                $capturedIntent = $this->stripeTerminalModel->capturePayment($paymentIntent_id);
                echo json_encode(['status' => $capturedIntent['status']]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'PaymentIntent not in a capturable state', 'status' => $paymentIntent['status']]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    

    public function simulatePayment() {
        $paymentIntent_id = $_POST['paymentIntent_id'];
        $reader_id = $_POST['reader_id'];

        try {
            $this->stripeTerminalModel->processPayment($reader_id, $paymentIntent_id);
            echo json_encode(['status' => 'simulated']);
        } catch (Exception $e) {
            http_response_code(400);
            echo $e->getMessage();
        }
    }
}