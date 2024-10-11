<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Enter Payment Details</h1>
        <form id="payment-details-form" action="index.php?action=process_details" method="POST">
            <div class="form-group">
                <label for="amount">Amount (SGD):</label>
                <input type="number" id="amount" name="amount" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="label">Label:</label>
                <input type="text" id="label" name="label" required>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="attendant">Attendant:</label>
                <input type="text" id="attendant" name="attendant" required>
            </div>
            <div class="form-group">
                <label for="terminal">Terminal ID:</label>
                <input type="text" id="terminal" name="terminal" required>
            </div>
            <button type="submit">Proceed to Payment</button>
        </form>
    </div>
</body>
</html>