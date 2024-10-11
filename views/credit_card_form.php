<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Card Payment</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <div class="container">
        <h1>Enter Credit Card Details</h1>
        <form id="payment-form">
            <div id="card-element">
                <!-- Stripe Elements will insert the card input field here -->
            </div>
            <div id="card-errors" role="alert"></div>
            <button type="submit">Submit Payment</button>
        </form>
    </div>
    <script>
        var stripe = Stripe('<?php echo $stripePublishableKey; ?>');
        var elements = stripe.elements();
        var card = elements.create('card');
        card.mount('#card-element');

        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.confirmCardPayment('<?php echo $clientSecret; ?>', {
                payment_method: {
                    card: card,
                    billing_details: {
                        name: '<?php echo htmlspecialchars($_SESSION['payment_details']['attendant']); ?>'
                    }
                }
            }).then(function(result) {
                if (result.error) {
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    window.location.href = 'index.php?action=success&payment_intent=' + result.paymentIntent.id;
                }
            });
        });
    </script>
</body>
</html>