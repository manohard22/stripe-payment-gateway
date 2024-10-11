<?php
// Ensure $paymentIntent is set
if (!isset($paymentIntent) || !is_array($paymentIntent)) {
    $paymentIntent = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Payment Successful</h1>
        <div class="message success">
            <p>Payment Intent ID: <?php echo htmlspecialchars($paymentIntent['id'] ?? 'N/A'); ?></p>
            <p>Amount: <?php echo htmlspecialchars(number_format(($paymentIntent['amount'] ?? 0) / 100, 2)); ?> <?php echo htmlspecialchars(strtoupper($paymentIntent['currency'] ?? 'USD')); ?></p>
            <p>Status: <?php echo htmlspecialchars($paymentIntent['status'] ?? 'N/A'); ?></p>
            <?php if (isset($paymentIntent['metadata']) && is_array($paymentIntent['metadata'])): ?>
                <h3>Additional Information:</h3>
                <ul>
                    <?php foreach ($paymentIntent['metadata'] as $key => $value): ?>
                        <li><?php echo htmlspecialchars($key); ?>: <?php echo htmlspecialchars($value); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <a href="index.php" class="button">Make Another Payment</a>
    <ul>
        <li><a href="index.php">Return to Home</a></li>
        <li><a href="index.php?action=terminal">Go to Stripe Terminal</a></li>
    </ul>
    </div>
</body>
</html>