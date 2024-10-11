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
                    'readerStatus' => 'In Progress',
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
    public function checkPaymentStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->jsonResponse(['error' => 'Invalid request method'], 400);
            return;
        }
    
        $paymentIntentId = $_GET['paymentIntent_id'] ?? null;
    
        if (!$paymentIntentId) {
            $this->jsonResponse(['error' => 'Payment Intent ID is required'], 400);
            return;
        }
    
        try {
            $paymentIntentData = $this->paymentModel->getPaymentIntent($paymentIntentId);

            $paymentIntent = $paymentIntentData['data'];
            // Check if 'data' key exists and contains the necessary fields
            if (!isset($paymentIntent['id']) || !isset($paymentIntent['status'])) {
                throw new Exception('Invalid PaymentIntent data');
            }
    
            $response = [
                'paymentIntentId' => $paymentIntent['id'] ?? null,
                'status' => $paymentIntent['status'] ?? null,
                'amount' => $paymentIntent['amount'] ?? null,
                'currency' => $paymentIntent['currency'] ?? null,
            ];
    
            // Handle succeeded status
            if ($paymentIntent['status'] === 'succeeded') {
                $response['chargeId'] = $paymentIntent['charges']['data'][0]['id'] ?? null;
                $response['paymentMethod'] = $paymentIntent['charges']['data'][0]['payment_method_details']['type'] ?? null;
                $response['paymentDetails'] = [
                    'card' => $paymentIntent['charges']['data'][0]['payment_method_details']['card'] ?? []
                ];
            }
    
            // Check if metadata contains 'reader_id'
            if (isset($paymentIntent['metadata']['reader_id'])) {
                $response['readerStatus'] = $this->stripeTerminalModel->getReaderStatus($paymentIntent['metadata']['reader_id']);
            } else {
                $response['readerStatus'] = 'unknown';
            }
    
            $this->jsonResponse($response);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }
    
    
    private function jsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
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