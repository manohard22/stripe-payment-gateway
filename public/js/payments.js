document.addEventListener('DOMContentLoaded', function() {
    var stripe = Stripe(STRIPE_PUBLISHABLE_KEY);
    var elements = stripe.elements();
    var form = document.getElementById('payment-form');

    // Create an instance of the card Element
    var card = elements.create('card');

    // Add an instance of the card Element into the `card-element` <div>
    card.mount('#card-element');

    // Handle real-time validation errors from the card Element.
    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Handle form submission
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        document.querySelector('button[type="submit"]').disabled = true;

        fetch('index.php?action=create-payment-intent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                amount: document.getElementById('amount').value,
                label: document.getElementById('label').value,
                location: document.getElementById('location').value,
                attendant: document.getElementById('attendant').value,
                terminal: document.getElementById('terminal').value
            })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.error) {
                throw new Error(data.error);
            } else {
                return stripe.confirmCardPayment(data.clientSecret, {
                    payment_method: {
                        card: card,
                        billing_details: {
                            name: document.getElementById('attendant').value
                        }
                    }
                });
            }
        })
        .then(function(result) {
            if (result.error) {
                throw new Error(result.error.message);
            } else {
                window.location.href = 'index.php?action=success&payment_intent=' + result.paymentIntent.id;
            }
        })
        .catch(function(error) {
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
            document.querySelector('button[type="submit"]').disabled = false;
        });
    });
});