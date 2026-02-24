# Error Handling & Troubleshooting

## Checking for Errors

All response objects have error checking methods:

```php
$response = $client->payment()
    ->amount(100)
    ->orderId('ORDER-123')
    ->send();

if ($response->isError()) {
    echo 'Error: ' . $response->getMessage();
    echo 'Trace ID: ' . $response->getTraceId();
}
```

## Try-Catch Pattern

```php
try {
    $response = $client->payment()
        ->amount(100)
        ->orderId('ORDER-123')
        ->send();

    if ($response->isSuccess()) {
        // Handle success
        header('Location: ' . $response->getRedirectUrl());
    } else {
        // Handle API error
        logError($response->getMessage(), $response->getTraceId());
        showUserError('Payment initialization failed');
    }
} catch (\Exception $e) {
    // Handle exception
    logError($e->getMessage());
    showUserError('An unexpected error occurred');
}
```

## Common Errors

### Authentication Errors

```php
// Invalid credentials
if ($response->getMessage() === 'Invalid credentials') {
    // Check your public_key and private_key
}
```

### Validation Errors

```php
// Amount too small
if ($response->getMessage() === 'Amount must be greater than 0') {
    // Check minimum amount requirement
}

// Invalid order ID
if ($response->getMessage() === 'Order ID already exists') {
    // Use unique order IDs
}
```

### Payment Errors

```php
// Card declined
if ($callbackData['status'] === 'error') {
    $reason = $callbackData['message'];
    // Common reasons:
    // - Insufficient funds
    // - Card expired
    // - Card blocked
    // - Invalid card details
}
```

## Error Response Structure

```php
$response->toArray();
// [
//     'status' => 'error',
//     'message' => 'Error description',
//     'code' => 'ERROR_CODE',
//     'trace_id' => 'a1b2c3d4...',
// ]
```

## Logging Errors

Always log errors with trace IDs for troubleshooting:

```php
function logPaymentError($response, $context = [])
{
    error_log(sprintf(
        '[EPOINT ERROR] %s | Trace ID: %s | Context: %s',
        $response->getMessage(),
        $response->getTraceId(),
        json_encode($context)
    ));
}

// Usage
if ($response->isError()) {
    logPaymentError($response, [
        'order_id' => 'ORDER-123',
        'amount' => 100.00,
    ]);
}
```

## Contacting Support

When reporting issues to Epoint support, always include:

1. **Trace ID**: `$response->getTraceId()`
2. **Error Message**: `$response->getMessage()`
3. **Order ID**: Your order identifier
4. **Transaction ID**: If available
5. **Timestamp**: When the error occurred

```php
$supportInfo = [
    'trace_id' => $response->getTraceId(),
    'message' => $response->getMessage(),
    'order_id' => 'ORDER-123',
    'transaction' => $response->getTransaction(),
    'timestamp' => date('Y-m-d H:i:s'),
];

// Include this information when contacting support
```

## Handling Callback Errors

### Invalid Signature

```php
try {
    $callbackData = $client->verifyCallback($data, $signature);
} catch (\Epoint\Exceptions\SignatureVerificationException $e) {
    // Invalid signature - possible fraud
    error_log('Invalid callback signature: ' . $e->getMessage());
    http_response_code(400);
    exit;
}
```

### Missing Data

```php
$data = $_POST['data'] ?? null;
$signature = $_POST['signature'] ?? null;

if (!$data || !$signature) {
    error_log('Missing callback data');
    http_response_code(400);
    exit('Missing data');
}
```

## Debugging Tips

### 1. Enable Error Logging

```php
// Log all API responses
$response = $client->payment()->amount(100)->orderId('ORDER-123')->send();
error_log('Payment Response: ' . print_r($response->toArray(), true));
```

### 2. Check Credentials

```php
// Test API connectivity
$heartbeat = $client->heartbeat();
if ($heartbeat['status'] === 'ok') {
    echo 'API connection OK';
} else {
    echo 'API connection failed';
}
```

### 3. Validate Input Data

```php
// Validate before sending
if ($amount < 0.01) {
    throw new \Exception('Amount too small');
}

if (empty($orderId)) {
    throw new \Exception('Order ID required');
}
```

### 4. Use toArray() for Debugging

```php
// Dump full response
$response = $client->payment()->amount(100)->send();
error_log(print_r($response->toArray(), true));
```

## Production Error Handling

```php
<?php

class PaymentService
{
    private $client;
    private $logger;

    public function createPayment($amount, $orderId)
    {
        try {
            $response = $this->client->payment()
                ->amount($amount)
                ->orderId($orderId)
                ->send();

            if ($response->isError()) {
                $this->logError($response, 'payment_creation');
                throw new PaymentException(
                    'Payment initialization failed',
                    $response->getTraceId()
                );
            }

            return $response->getRedirectUrl();
        } catch (\Exception $e) {
            $this->logger->error('Payment exception', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
                'amount' => $amount,
            ]);

            throw $e;
        }
    }

    private function logError($response, $context)
    {
        $this->logger->error('Epoint API Error', [
            'message' => $response->getMessage(),
            'trace_id' => $response->getTraceId(),
            'status' => $response->getStatus(),
            'context' => $context,
        ]);
    }
}
```

## Common Issues

### Issue: "Invalid signature"
**Solution**: Verify you're using the correct private key. Don't modify callback POST data.

### Issue: "Order ID already exists"
**Solution**: Use unique order IDs for each payment. Add timestamp or random string.

### Issue: "Amount validation failed"
**Solution**: Ensure amount is a positive number with max 2 decimal places.

### Issue: "Card not found"
**Solution**: Verify the card_id exists and belongs to your merchant account.

### Issue: Callback not received
**Solution**:
- Check callback URL is configured in Epoint panel
- Ensure URL is publicly accessible
- Verify firewall allows incoming requests
- Return HTTP 200 OK from callback

## See Also

- [Response Objects](Response-Objects)
- [Callback Handling](Callback-Handling)