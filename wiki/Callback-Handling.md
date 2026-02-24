# Callback Handling & Security

Learn how to securely handle payment notifications from Epoint.

## What is a Callback?

When a payment completes (success or failure), Epoint sends a POST request to your callback URL with payment details. This is more reliable than relying on redirect URLs.

## Setting Up Callback URL

**Important**: The callback URL is configured once in your **Epoint merchant panel**, not per payment.

Your callback endpoint must:
- Be publicly accessible (not localhost in production)
- Accept POST requests
- Return HTTP 200 OK response

## Basic Callback Handler

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

// Get POST data
$data = $_POST['data'] ?? '';
$signature = $_POST['signature'] ?? '';

try {
    // Verify and decode callback data
    $callbackData = $client->verifyCallback($data, $signature);

    // Handle payment result
    if ($callbackData['status'] === 'success') {
        handleSuccessfulPayment($callbackData);
    } else {
        handleFailedPayment($callbackData);
    }

    // Always return 200 OK
    http_response_code(200);
    echo 'OK';

} catch (\Epoint\Exceptions\SignatureVerificationException $e) {
    // Invalid signature - fraud attempt
    error_log('Invalid signature: ' . $e->getMessage());
    http_response_code(400);
}
```

## Callback Data Structure

```php
$callbackData = [
    'status' => 'success',           // 'success' or 'error'
    'order_id' => 'ORDER-12345',     // Your order ID
    'transaction' => 'te001234567',  // Epoint transaction ID
    'amount' => 100.50,              // Payment amount
    'currency' => 'AZN',             // Currency
    'card_id' => 'card-id',          // Card ID (if applicable)
    'card_mask' => '****1234',       // Masked card number
    'message' => 'Success',          // Response message
    'trace_id' => 'a1b2c3...',       // Trace ID for support
    // ... additional fields
];
```

## Complete Callback Handler

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Epoint\EpointClient;
use Epoint\Exceptions\SignatureVerificationException;

// Initialize client
$client = new EpointClient(
    publicKey: getenv('EPOINT_PUBLIC_KEY'),
    privateKey: getenv('EPOINT_PRIVATE_KEY'),
    testMode: (bool) getenv('EPOINT_TEST_MODE')
);

// Get callback data
$data = $_POST['data'] ?? '';
$signature = $_POST['signature'] ?? '';

// Verify signature
try {
    $callbackData = $client->verifyCallback($data, $signature);
} catch (SignatureVerificationException $e) {
    // Invalid signature - possible fraud
    error_log('Callback signature verification failed: ' . $e->getMessage());
    http_response_code(400);
    exit('Invalid signature');
}

// Process payment result
$orderId = $callbackData['order_id'];
$status = $callbackData['status'];
$transaction = $callbackData['transaction'];
$amount = $callbackData['amount'];
$traceId = $callbackData['trace_id'];

// Get order from database
$order = getOrder($orderId);

if (!$order) {
    error_log("Order not found: {$orderId}");
    http_response_code(404);
    exit('Order not found');
}

// Prevent duplicate processing
if ($order['status'] === 'paid') {
    error_log("Order already processed: {$orderId}");
    http_response_code(200);
    exit('OK');
}

// Validate amount
if ((float) $amount !== (float) $order['amount']) {
    error_log("Amount mismatch for order {$orderId}. Expected: {$order['amount']}, Got: {$amount}");
    http_response_code(400);
    exit('Amount mismatch');
}

// Handle result
if ($status === 'success') {
    // Payment successful
    updateOrder($orderId, [
        'status' => 'paid',
        'transaction_id' => $transaction,
        'trace_id' => $traceId,
        'paid_at' => date('Y-m-d H:i:s'),
    ]);

    // Additional actions
    sendOrderConfirmationEmail($orderId);
    updateInventory($orderId);
    logPayment($orderId, 'success', $transaction);

    error_log("Payment successful: Order {$orderId}, Transaction {$transaction}");
} else {
    // Payment failed
    updateOrder($orderId, [
        'status' => 'failed',
        'error_message' => $callbackData['message'],
        'trace_id' => $traceId,
    ]);

    logPayment($orderId, 'failed', $transaction);

    error_log("Payment failed: Order {$orderId}, Reason: {$callbackData['message']}");
}

// Return success response
http_response_code(200);
echo 'OK';

// Database functions
function getOrder($orderId) {
    // SELECT * FROM orders WHERE order_id = ?
}

function updateOrder($orderId, $data) {
    // UPDATE orders SET ... WHERE order_id = ?
}

function sendOrderConfirmationEmail($orderId) {
    // Send confirmation email
}

function updateInventory($orderId) {
    // Update product stock
}

function logPayment($orderId, $status, $transaction) {
    // INSERT INTO payment_logs ...
}
```

## Security Best Practices

### 1. Always Verify Signature

```php
try {
    $callbackData = $client->verifyCallback($data, $signature);
} catch (SignatureVerificationException $e) {
    // Reject invalid signatures
    http_response_code(400);
    exit;
}
```

### 2. Validate Amount

```php
if ((float) $callbackData['amount'] !== (float) $order['expected_amount']) {
    error_log("Amount mismatch detected");
    // Handle fraud attempt
}
```

### 3. Prevent Duplicate Processing

```php
if ($order['status'] === 'paid') {
    http_response_code(200);
    exit('Already processed');
}
```

### 4. Use Database Transactions

```php
DB::beginTransaction();
try {
    updateOrderStatus($orderId, 'paid');
    updateInventory($orderId);
    createInvoice($orderId);
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    error_log("Callback processing failed: " . $e->getMessage());
}
```

### 5. Return 200 OK

Always return HTTP 200, even for errors you handle:

```php
// ✅ Correct
http_response_code(200);
echo 'OK';

// ❌ Wrong (Epoint will retry)
http_response_code(500);
```

## Callback Retries

If your server doesn't respond with 200 OK, Epoint will retry the callback multiple times. Ensure your handler is idempotent (safe to call multiple times).

## Testing Callbacks Locally

### Using ngrok

```bash
# Install ngrok
brew install ngrok  # macOS
# or download from https://ngrok.com

# Start ngrok tunnel
ngrok http 8000

# Use the provided URL in your Epoint merchant panel
# Example: https://abc123.ngrok.io/callback.php
```

**Note**: Update the callback URL in your Epoint merchant panel to the ngrok URL for local testing.

### Testing in Code

```php
// Simulate a callback
$testData = base64_encode(json_encode([
    'status' => 'success',
    'order_id' => 'ORDER-TEST',
    'transaction' => 'te001234567',
    'amount' => 100.00,
]));

$testSignature = hash_hmac('sha1', $testData, 'your-private-key');

$_POST['data'] = $testData;
$_POST['signature'] = $testSignature;

// Run callback handler
require 'callback.php';
```

## Handling Different Payment Types

### Standard Payment

```php
if ($callbackData['payment_type'] === 'payment') {
    updateOrderStatus($orderId, 'paid');
}
```

### Card Registration

```php
if ($callbackData['payment_type'] === 'register_card') {
    $cardId = $callbackData['card_id'];
    $cardMask = $callbackData['card_mask'];

    saveCustomerCard($customerId, $cardId, $cardMask);
}
```

### Refund

```php
if ($callbackData['payment_type'] === 'refund') {
    updateOrderStatus($orderId, 'refunded');
    updateInventory($orderId, 'return');
}
```

## Common Issues

### Callback Not Received

- Check URL is publicly accessible
- Verify firewall allows incoming requests
- Check web server error logs
- Use ngrok for local testing

### Signature Verification Fails

- Ensure private key is correct
- Don't modify POST data before verification
- Check you're using same private key as payment creation

### Multiple Callbacks

- This is normal if server doesn't return 200 OK
- Implement idempotency checks
- Log duplicate attempts

## See Also

- [Standard Payments](Standard-Payments)
- [Payment Status Check](Payment-Status-Check)
- [Error Handling](Error-Handling)