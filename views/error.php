<?php
// Ensure $error is set
if (!isset($error)) {
    $error = 'An unknown error occurred';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Error</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Payment Error</h1>
        <div class="message error">
            <p>Error: <?php echo htmlspecialchars($error); ?></p>
        </div>
        <a href="index.php" class="button">Try Again</a>
    </div>
</body>
</html>