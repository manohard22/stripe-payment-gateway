<?php
session_start();
require_once __DIR__ . '/controllers/PaymentController.php';
require_once __DIR__ . '/controllers/StripeTerminalController.php';

$paymentController = new PaymentController();
$stripeTerminalController = new StripeTerminalController();

$action = $_GET['action'] ?? 'showForm';

switch ($action) {
    case 'showForm':
        $paymentController->showPaymentForm();
        break;
    case 'process_details':
        $paymentController->processDetails();
        break;
    case 'show_credit_card_form':
        $paymentController->showCreditCardForm();
        break;
    case 'success':
        $paymentController->showSuccess();
        break;
    case 'error':
        $paymentController->showError();
        break;
        
    case 'showTerminal':
        $stripeTerminalController->showTerminal();
        break;
    case 'createPayment':
        $stripeTerminalController->createPayment();
        break;
    case 'checkPaymentStatus':
        $stripeTerminalController->checkPaymentStatus();
        break;
    case 'capturePayment':
        $stripeTerminalController->capturePayment();
        break;
    case 'simulatePayment':
        $stripeTerminalController->simulatePayment();
        break;
        
    default:
        header('HTTP/1.0 404 Not Found');
        echo "Page not found";
        break;
}