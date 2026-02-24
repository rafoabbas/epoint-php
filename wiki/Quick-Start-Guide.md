# Quick Start Guide

This guide will help you make your first payment in 5 minutes.

## Step 1: Install SDK

```bash
composer require rafoabbas/epoint-php
```

## Step 2: Initialize Client

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Epoint\EpointClient;
use Epoint\Enums\Language;

$client = new EpointClient(
    publicKey: 'i000000001',
    privateKey: 'your-private-key',
    testMode: true // Use test mode for development
);
```

## Step 3: Create Payment

```php
$response = $client->payment()
    ->amount(100.50)
    ->orderId('ORDER-12345')
    ->description('Product purchase')
    ->language(Language::EN)
    ->successUrl('https://yoursite.com/success')
    ->errorUrl('https://yoursite.com/error')
    ->send();

if ($response->isSuccess()) {
    // Redirect user to payment page
    header('Location: ' . $response->getRedirectUrl());
    exit;
} else {
    echo 'Payment failed: ' . $response->getMessage();
}
```

## Step 4: Handle Callback

Create a callback handler to receive payment notifications.

**Note**: Configure your callback URL in the Epoint merchant panel (not in code).

```php
<?php
// callback.php

require_once __DIR__ . '/vendor/autoload.php';

use Epoint\EpointClient;

$client = new EpointClient(
    publicKey: 'i000000001',
    privateKey: 'your-private-key',
    testMode: true
);

// Get callback data
$data = $_POST['data'] ?? '';
$signature = $_POST['signature'] ?? '';

try {
    // Verify signature and decode data
    $callbackData = $client->verifyCallback($data, $signature);

    if ($callbackData['status'] === 'success') {
        // Payment successful!
        $orderId = $callbackData['order_id'];
        $transaction = $callbackData['transaction'];
        $amount = $callbackData['amount'];

        // Update your database
        updateOrderStatus($orderId, 'paid', $transaction);

        // Send confirmation email
        sendOrderConfirmation($orderId);

        // Return 200 OK
        http_response_code(200);
        echo 'OK';
    } else {
        // Payment failed
        $orderId = $callbackData['order_id'];
        updateOrderStatus($orderId, 'failed');

        http_response_code(200);
        echo 'OK';
    }
} catch (\Epoint\Exceptions\SignatureVerificationException $e) {
    // Invalid signature - possible fraud attempt
    error_log('Invalid signature: ' . $e->getMessage());
    http_response_code(400);
    echo 'Invalid signature';
}

// Helper functions
function updateOrderStatus($orderId, $status, $transaction = null)
{
    // Your database logic
    // UPDATE orders SET status = ?, transaction_id = ? WHERE order_id = ?
}

function sendOrderConfirmation($orderId)
{
    // Your email logic
}
```

## Step 5: Handle Success/Error Pages

### Success Page (success.php)

```php
<?php
// success.php

$orderId = $_GET['order_id'] ?? '';
$transaction = $_GET['transaction'] ?? '';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
</head>
<body>
    <h1>✅ Payment Successful!</h1>
    <p>Your payment has been processed successfully.</p>
    <p>Order ID: <?php echo htmlspecialchars($orderId); ?></p>
    <p>Transaction: <?php echo htmlspecialchars($transaction); ?></p>
    <a href="/">Return to Home</a>
</body>
</html>
```

### Error Page (error.php)

```php
<?php
// error.php

$orderId = $_GET['order_id'] ?? '';
$message = $_GET['message'] ?? 'Payment failed';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
</head>
<body>
    <h1>❌ Payment Failed</h1>
    <p><?php echo htmlspecialchars($message); ?></p>
    <p>Order ID: <?php echo htmlspecialchars($orderId); ?></p>
    <a href="/">Try Again</a>
</body>
</html>
```

## Complete Example

Here's a complete working example:

```php
<?php
// payment.php

require_once __DIR__ . '/vendor/autoload.php';

use Epoint\EpointClient;
use Epoint\Enums\Language;

// Initialize client
$client = new EpointClient(
    publicKey: getenv('EPOINT_PUBLIC_KEY'),
    privateKey: getenv('EPOINT_PRIVATE_KEY'),
    testMode: true
);

// Create order
$orderId = 'ORDER-' . time();
$amount = 100.00;

// Save order to database
saveOrder([
    'order_id' => $orderId,
    'amount' => $amount,
    'status' => 'pending',
    'created_at' => date('Y-m-d H:i:s'),
]);

// Create payment
try {
    $response = $client->payment()
        ->amount($amount)
        ->orderId($orderId)
        ->description('Test payment')
        ->language(Language::EN)
        ->successUrl('https://yoursite.com/success')
        ->errorUrl('https://yoursite.com/error')
        ->send();

    if ($response->isSuccess()) {
        // Store transaction ID
        updateOrderTransaction($orderId, $response->getTransaction());

        // Redirect to payment page
        header('Location: ' . $response->getRedirectUrl());
        exit;
    } else {
        // Handle error
        echo 'Payment initialization failed: ' . $response->getMessage();
        echo '<br>Trace ID: ' . $response->getTraceId();
    }
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

// Database helper functions
function saveOrder($data) {
    // INSERT INTO orders ...
}

function updateOrderTransaction($orderId, $transaction) {
    // UPDATE orders SET transaction_id = ? WHERE order_id = ?
}
```

## Testing Your Integration

### Testing

Contact Epoint support for test card details and testing instructions.

### Test the Flow

1. **Create Payment**: Run `payment.php` and note the redirect URL
2. **Make Payment**: Visit the payment page and use a test card
3. **Check Callback**: Verify your callback endpoint receives data
4. **Check Database**: Confirm order status updated to "paid"
5. **View Success Page**: Verify user sees success message

## Common Issues

### Callback Not Received

- Verify callback URL is configured in Epoint merchant panel
- Ensure callback URL is publicly accessible (not localhost)
- Check your web server logs
- Verify firewall allows incoming requests

### Invalid Signature

- Double-check your private key
- Ensure you're using the same private key in callback verification
- Don't modify the `data` or `signature` POST parameters

### Payment Page Not Loading

- Verify your credentials are correct
- Check `testMode` matches your credentials (test vs production)
- Try the heartbeat check: `$client->heartbeat()`

## Next Steps

Now that you've made your first payment, explore more features:

- [Standard Payments](Standard-Payments) - All payment options
- [Card Management](Card-Management) - Save cards for recurring payments
- [Refunds](Refunds-and-Reversals) - Process refunds
- [Callback Handling](Callback-Handling) - Advanced callback handling
- [Testing](Testing) - Comprehensive testing guide