# Payment Status Check

Check the status of a payment or a registered card.

## Payment Status

Check the status of a payment using the transaction ID.

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

## Card Registration Status

Check the status of a registered card using the card ID.

### Basic Usage

```php
use Epoint\Enums\CardStatus;

$status = $client->checkCardStatus()
    ->cardId('ce001234567')
    ->get();

if ($status->getCardStatus() === CardStatus::ACTIVE) {
    echo 'Card is active!';
}
```

### Card Status Response Methods

```php
// Get card status enum
$status->getCardStatus();    // CardStatus enum

// Get status string
$status->getStatus();        // 'new', 'active', 'pending', 'rejected', 'expired', 'session_expired'

// Get card details
$status->getCardId();        // Card ID
$status->getCardName();      // Cardholder name
$status->getCardMask();      // Masked card number (e.g., ****1234)
$status->getExpiredDate();   // Card expiry date (e.g., 12/25)
$status->getDescription();   // Card description

// Get full data
$data = $status->toArray();
```

### CardStatus Enum

```php
use Epoint\Enums\CardStatus;

CardStatus::NEW              // Card registration initiated
CardStatus::ACTIVE           // Card is active and ready for payments
CardStatus::PENDING          // Card registration is pending
CardStatus::REJECTED         // Card registration rejected
CardStatus::EXPIRED          // Card has expired
CardStatus::SESSION_EXPIRED  // Registration session has expired
```

### Complete Example

```php
use Epoint\Enums\CardStatus;

$cardId = 'ce001234567'; // From card registration callback

$status = $client->checkCardStatus()
    ->cardId($cardId)
    ->get();

switch ($status->getCardStatus()) {
    case CardStatus::ACTIVE:
        // Card is ready for payments
        $mask = $status->getCardMask();
        $name = $status->getCardName();
        $expiry = $status->getExpiredDate();

        echo "Card {$mask} ({$name}) is active, expires {$expiry}";
        break;

    case CardStatus::PENDING:
        echo 'Card registration is still pending';
        break;

    case CardStatus::REJECTED:
        echo 'Card registration was rejected';
        break;

    case CardStatus::EXPIRED:
        echo 'Card has expired';
        break;

    case CardStatus::SESSION_EXPIRED:
        echo 'Registration session expired, please try again';
        break;

    case CardStatus::NEW:
        echo 'Card registration initiated but not completed';
        break;
}
```

## Best Practices

1. **Use Callbacks**: Don't rely solely on status checks. Implement callback handling for real-time notifications.

2. **Store Transaction ID**: Always store the transaction ID in your database when creating a payment.

3. **Store Card ID**: Always store the card ID from card registration callbacks for future status checks.

4. **Idempotency**: Status checks are safe to call multiple times with the same transaction or card ID.

5. **Trace ID**: Always log the trace ID when troubleshooting failed status checks.

## See Also

- [Standard Payments](Standard-Payments)
- [Card Management](Card-Management)
- [Callback Handling](Callback-Handling)
- [Response Objects](Response-Objects)