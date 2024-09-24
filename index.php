<?php
$config = require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment Example</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
        #payment-form { max-width: 500px; margin: 0 auto; }
        .form-row { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="number"], input[type="email"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        #card-element { padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        #card-errors { color: #fa755a; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Stripe Payment Example</h1>
    <form id="payment-form">
        <div class="form-row">
            <label for="amount">Amount ($):</label>
            <input type="number" id="amount" name="amount" value="0.00" step="0.01" min="0.50" required>
        </div>
        <div class="form-row">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-row">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-row">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>
        </div>
        <div class="form-row">
            <label for="city">City:</label>
            <input type="text" id="city" name="city" required>
        </div>
        <div class="form-row">
            <label for="state">State:</label>
            <input type="text" id="state" name="state" required>
        </div>
        <div class="form-row">
            <label for="zip">ZIP Code:</label>
            <input type="text" id="zip" name="zip" required>
        </div>
        <div class="form-row">
            <label for="card-element">Credit or debit card</label>
            <div id="card-element">
                <!-- Stripe Elements will insert the card input field here -->
            </div>
            <div id="card-errors" role="alert"></div>
        </div>
        <button type="submit">Pay Now</button>
    </form>
    <div id="loader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <div style="border: 5px solid #f3f3f3; border-top: 5px solid #3498db; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite;"></div>
        </div>
    </div>
    <script>
        window.STRIPE_PUBLISHABLE_KEY = '<?php echo $config['stripe_publishable_key']; ?>';
    </script>
    <script src="js/payment.js"></script>
</body>
</html>