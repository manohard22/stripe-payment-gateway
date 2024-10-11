
$(document).ready(function() {
    let paymentIntentId = '';

    function updateUI(data) {
        $('#payment-intent-id').text(data.paymentIntentId || '');
        $('#payment-intent-status').text(data.status || '');
        $('#reader-status').text(data.readerStatus || '');
        
        $('#process-button').prop('disabled', !data.paymentIntentId);
        $('#capture-button').prop('disabled', data.status !== 'processing');
        $('#simulate-payment-button').prop('disabled', !data.paymentIntentId);
    }

    $('#process-button').click(function() {
        const amount = $('#amount').val();
        const readerId = $('#reader-select').val();

        $.ajax({
            url: 'index.php?action=createPayment',
            method: 'POST',
            data: { amount: amount, reader_id: readerId },
            dataType: 'json',
            success: function(data) {
                updateUI(data);
                paymentIntentId = data.paymentIntentId;
                $('#messages').text('Payment processing initiated.');
            },
            error: function(xhr) {
                $('#messages').text('Error: ' + xhr.responseText);
            }
        });
    });

    $('#capture-button').click(function() {
        $.ajax({
            url: 'index.php?action=capturePayment',
            method: 'POST',
            data: { paymentIntent_id: paymentIntentId },
            dataType: 'json',
            success: function(data) {
                updateUI({ status: data.status, readerStatus: 'ready' });
                $('#messages').text('Payment captured successfully.');
            },
            error: function(xhr) {
                $('#messages').text('Error: ' + xhr.responseText);
            }
        });
    });

    $('#simulate-payment-button').click(function() {
        const readerId = $('#reader-select').val();

        $.ajax({
            url: 'index.php?action=simulatePayment',
            method: 'POST',
            data: { paymentIntent_id: paymentIntentId, reader_id: readerId },
            dataType: 'json',
            success: function(data) {
                updateUI({ status: 'processing', readerStatus: 'in_progress' });
                $('#messages').text('Payment simulated. Ready for capture.');
            },
            error: function(xhr) {
                $('#messages').text('Error: ' + xhr.responseText);
            }
        });
    });

    $('#cancel-button').click(function() {
        updateUI({ paymentIntentId: '', status: '', readerStatus: 'ready' });
        $('#messages').text('Transaction cancelled.');
        $('#amount').val('');
        $('#reader-select').val('');
    });

    $('#reader-select, #amount').change(function() {
        $('#process-button').prop('disabled', !$('#reader-select').val() || !$('#amount').val());
    });
});