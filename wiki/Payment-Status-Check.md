# Payment Status Check

Check the status of a payment using the transaction ID.

## Basic Usage

```php
$status = $client->checkStatus()
    ->transaction('te001234567')
    ->get();

if ($status->getPaymentStatus() === \Epoint\Enums\PaymentStatus::SUCCESS) {
    echo 'Payment successful!';
}
```

## Status Response Methods

```php
// Get payment status enum
$status->getPaymentStatus();   // PaymentStatus enum

// Get status string
$status->getStatus();          // 'success', 'error', 'new', etc.

// Check status
$status->isSuccess();          // true if successful

// Get transaction details
$status->getTransaction();     // Transaction ID
$status->getAmount();          // Payment amount
$status->getOrderId();         // Your order ID
$status->getMessage();         // Status message
$status->getTraceId();         // Trace ID

// Get full data
$data = $status->toArray();
```

## Payment Status Enum

```php
use Epoint\Enums\PaymentStatus;

PaymentStatus::NEW      // Payment initiated, not completed
PaymentStatus::SUCCESS  // Payment successful
PaymentStatus::ERROR    // Payment failed
```

## Complete Example

```php
use Epoint\Enums\PaymentStatus;

$transactionId = 'te001234567'; // From payment response or callback

$status = $client->checkStatus()
    ->transaction($transactionId)
    ->get();

switch ($status->getPaymentStatus()) {
    case PaymentStatus::SUCCESS:
        // Payment successful - update order status
        $amount = $status->getAmount();
        $orderId = $status->getOrderId();

        updateOrderStatus($orderId, 'paid', $amount);
        break;

    case PaymentStatus::ERROR:
        // Payment failed
        $message = $status->getMessage();
        error_log("Payment {$transactionId} failed: {$message}");
        break;

    case PaymentStatus::NEW:
        // Payment pending
        echo 'Payment is still being processed';
        break;
}
```

## Error Handling

```php
try {
    $status = $client->checkStatus()
        ->transaction($transactionId)
        ->get();

    if ($status->isError()) {
        error_log('Status check error: ' . $status->getMessage());
        error_log('Trace ID: ' . $status->getTraceId());
    }
} catch (\Exception $e) {
    error_log('Status check failed: ' . $e->getMessage());
}
```

## Best Practices

1. **Use Callbacks**: Don't rely solely on status checks. Implement callback handling for real-time notifications.

2. **Store Transaction ID**: Always store the transaction ID in your database when creating a payment.

3. **Idempotency**: Status checks are safe to call multiple times with the same transaction ID.

4. **Trace ID**: Always log the trace ID when troubleshooting failed status checks.

## See Also

- [Standard Payments](Standard-Payments)
- [Callback Handling](Callback-Handling)
- [Response Objects](Response-Objects)