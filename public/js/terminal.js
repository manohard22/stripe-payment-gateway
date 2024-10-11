$(document).ready(function() {
    let paymentIntentId = '';
    let pollingInterval;

    function updateUI(data) {
        // $('#payment-intent-id').text(data.paymentIntentId || '');
        // $('#payment-intent-status').text(data.status || '');
        // $('#reader-status').text(data.status || '');

        // Disable the process button when payment is processing or succeeded
        $('#process-button').prop('disabled', !$('#reader-select').val() || !$('#amount').val() || data.status === 'processing' || data.status === 'succeeded');
        $('#cancel-button').prop('disabled', !data.paymentIntentId);
    }

    function updateStatus(message) {
        $('#messages').html(message);
    }

    function pollPaymentStatus() {
        $.ajax({
            url: 'index.php?action=checkPaymentStatus',
            method: 'GET',
            data: { paymentIntent_id: paymentIntentId },
            dataType: 'json',
            success: function(data) {
                updateUI(data);
                if (data.status === 'succeeded') {
                    clearInterval(pollingInterval);
                    displaySuccessMessage(data);
                    $('#process-button').prop('disabled', true); // Disable the process button after success
                } else if (data.status === 'canceled') {
                    clearInterval(pollingInterval);
                    updateStatus('Payment was canceled.');
                } else if (data.status === 'processing') {
                    updateStatus('Payment is being processed. Please wait...');
                }
            },
            error: function(xhr) {
                clearInterval(pollingInterval);
                updateStatus('Error checking payment status: ' + xhr.responseText);
            }
        });
    }

    function displaySuccessMessage(data) {
        let successMessage = '<p style="color: green; font-weight: bold;">Payment processed successfully!</p>';
        successMessage += '<p>Payment Intent ID: ' + data.paymentIntentId + '</p>';
        successMessage += '<p>Amount: $' + (data.amount / 100).toFixed(2) + '</p>';
        successMessage += '<p>Status: ' + data.status + '</p>';
        updateStatus(successMessage);
    }

    $('#reader-select, #amount').change(function() {
        $('#process-button').prop('disabled', !$('#reader-select').val() || !$('#amount').val());
    });

    $('#process-button').click(function() {
        const amount = $('#amount').val();
        const readerId = $('#reader-select').val();

        if (!amount || amount < 100) {
            updateStatus('Please enter a valid amount (minimum 100 cents)');
            return;
        }

        $(this).prop('disabled', true); // Disable the button once clicked
        updateStatus('Initiating payment. Please wait...');

        $.ajax({
            url: 'index.php?action=createPayment',
            method: 'POST',
            data: { amount: amount, reader_id: readerId },
            dataType: 'json',
            success: function(data) {
                updateUI(data);
                paymentIntentId = data.paymentIntentId;
                updateStatus('Payment Intent created. Please tap card on the reader.');
                pollingInterval = setInterval(pollPaymentStatus, 2000); // Poll every 2 seconds
            },
            error: function(xhr) {
                updateStatus('Error: ' + xhr.responseText);
                $('#process-button').prop('disabled', false); // Re-enable on error
            }
        });
    });

    $('#cancel-button').click(function() {
        if (paymentIntentId) {
            $.ajax({
                url: 'index.php?action=cancelPayment',
                method: 'POST',
                data: { paymentIntent_id: paymentIntentId },
                dataType: 'json',
                success: function(data) {
                    clearInterval(pollingInterval);
                    updateUI({ paymentIntentId: '', status: '', readerStatus: 'ready' });
                    updateStatus('Transaction cancelled.');
                    paymentIntentId = '';
                    $('#amount').val('');
                    $('#reader-select').val('');
                    $('#process-button').prop('disabled', false);
                },
                error: function(xhr) {
                    updateStatus('Error cancelling payment: ' + xhr.responseText);
                }
            });
        } else {
            updateStatus('No active payment to cancel');
        }
    });
});
