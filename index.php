<?php
session_start();
require_once __DIR__ . '/controllers/PaymentController.php';

$controller = new PaymentController();

$action = $_GET['action'] ?? 'showForm';

switch ($action) {
    case 'showForm':
        $controller->showPaymentForm();
        break;
    case 'process_details':
        $controller->processDetails();
        break;
    case 'show_credit_card_form':
        $controller->showCreditCardForm();
        break;
    case 'success':
        $controller->showSuccess();
        break;
    case 'error':
        $controller->showError();
        break;
    default:
        header('HTTP/1.0 404 Not Found');
        echo "Page not found";
        break;
}