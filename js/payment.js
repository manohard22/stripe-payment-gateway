const stripe = Stripe(window.STRIPE_PUBLISHABLE_KEY);
const elements = stripe.elements();
const card = elements.create('card');
card.mount('#card-element');

const form = document.getElementById('payment-form');
const amountInput = document.getElementById('amount');
const loader = document.getElementById('loader');
let clientSecret;

function showLoader() {
    loader.style.display = 'block';
}

function hideLoader() {
    loader.style.display = 'none';
}
async function createPaymentIntent(amount) {
    try {
        const response = await fetch('create-payment-intent.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ amount: amount })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        return data.clientSecret;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}
form.addEventListener('submit', async (event) => {
    event.preventDefault();
    showLoader();

    try {
        const amount = amountInput.value;
        clientSecret = await createPaymentIntent(amount);

        const { paymentIntent, error } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: card,
                billing_details: {
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    address: {
                        line1: document.getElementById('address').value,
                        city: document.getElementById('city').value,
                        state: document.getElementById('state').value,
                        postal_code: document.getElementById('zip').value
                    }
                }
            }
        });

        if (error) {
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
            hideLoader();
        } else if (paymentIntent.status === 'succeeded') {
            window.location.href = 'success.php?payment_intent=' + paymentIntent.id;
        }
    } catch (error) {
        console.error('Payment failed:', error);
        const errorElement = document.getElementById('card-errors');
        errorElement.textContent = 'An error occurred. Please try again.';
        hideLoader();
    }
});