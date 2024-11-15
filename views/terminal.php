
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Terminal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .sr-root {
            max-width: 600px;
            margin: 0 auto;
        }
        .sr-main {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .sr-select, .sr-input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        .button-row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:disabled {
            background-color: #ddd;
            cursor: not-allowed;
        }
        #messages {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 4px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="sr-root">
        <main class="sr-main">
            <h2>Collecting Payments with Stripe Terminal</h2>
            <p>Select a reader and input an amount for the transaction.</p>
            
            <label for="reader-select">Select Reader:</label>
            <select id="reader-select" class="sr-select">
                <option value="" selected disabled>Select a reader</option>
                <?php if (!empty($readers) && is_array($readers)): ?>
                    <?php 
                    $first = true;
                    foreach ($readers["data"] as $reader): 
                    ?>
                        <option value="<?= htmlspecialchars($reader['id']) ?>" <?= $first ? 'selected' : '' ?>><?= htmlspecialchars($reader['label']) ?> (<?= htmlspecialchars($reader['id']) ?>)</option>
                    <?php 
                    $first = false;
                    endforeach; 
                    ?>
                <?php else: ?>
                    <option disabled>No readers available</option>
                <?php endif; ?>
            </select>

            <label for="amount">Amount (in cents):</label>
            <input id="amount" class="sr-input" type="number" min="100" step="1" />

            <div class="button-row">
                <button id="process-button" disabled>Process Payment</button>
                <!-- <button id="capture-button" disabled>Capture Payment</button> -->
            </div>

            <div class="button-row">
                <!-- <button id="simulate-payment-button" type="button" disabled>Simulate Payment</button> -->
                <button id="cancel-button" type="button">Cancel</button>
            </div>

            <div id="messages"></div>
        </main>
    </div>

    <script src="public/js/terminal.js"></script>
</body>
</html>