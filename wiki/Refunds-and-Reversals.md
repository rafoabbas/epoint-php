# Refunds & Reversals

## Refund vs Reverse

- **Refund**: Return money to customer after successful payment (can be partial or full)
- **Reverse**: Cancel/void a transaction (typically same day)

## Creating a Refund

```php
$response = $client->refund()
    ->cardId('card-id-from-original-payment')
    ->orderId('original-order-id')
    ->amount(50.00)
    ->description('Product return')
    ->send();

if ($response->isSuccess()) {
    echo 'Refund successful!';
    $transaction = $response->getTransaction();
}
```

## Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `cardId()` | string | Yes | Card ID from original payment |
| `orderId()` | string | Yes | Original order ID |
| `amount()` | float | Yes | Refund amount (can be partial) |
| `description()` | string | No | Refund reason |

## Partial Refunds

You can refund less than the original amount:

```php
// Original payment: 100 AZN
// Partial refund: 30 AZN

$response = $client->refund()
    ->cardId($cardId)
    ->orderId($originalOrderId)
    ->amount(30.00)
    ->description('Partial refund - 1 item returned')
    ->send();
```

## Full Refund

```php
$response = $client->refund()
    ->cardId($cardId)
    ->orderId($originalOrderId)
    ->amount($originalAmount) // Full amount
    ->description('Full refund - order cancelled')
    ->send();
```

## Reverse Transaction

Use reverse to cancel a transaction:

```php
$response = $client->reverse()
    ->transaction('te001234567')
    ->send();

if ($response->isSuccess()) {
    echo 'Transaction reversed!';
}
```

## Partial Reverse

```php
// Reverse partial amount
$response = $client->reverse()
    ->transaction('te001234567')
    ->amount(50.00) // Optional: partial reversal
    ->send();
```

## Complete Example: Refund System

```php
<?php

function processRefund($orderId, $refundAmount, $reason)
{
    global $client;

    // Get original order details from database
    $order = getOrder($orderId);

    if (!$order) {
        throw new \Exception('Order not found');
    }

    if ($order['status'] !== 'paid') {
        throw new \Exception('Order not paid');
    }

    if ($refundAmount > $order['amount']) {
        throw new \Exception('Refund amount exceeds order amount');
    }

    // Check if already refunded
    $totalRefunded = getTotalRefunded($orderId);
    if ($totalRefunded + $refundAmount > $order['amount']) {
        throw new \Exception('Total refund exceeds order amount');
    }

    // Process refund
    $response = $client->refund()
        ->cardId($order['card_id'])
        ->orderId($orderId)
        ->amount($refundAmount)
        ->description($reason)
        ->send();

    if ($response->isSuccess()) {
        // Save refund record
        saveRefund([
            'order_id' => $orderId,
            'amount' => $refundAmount,
            'reason' => $reason,
            'transaction' => $response->getTransaction(),
            'trace_id' => $response->getTraceId(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Update order status
        if ($totalRefunded + $refundAmount >= $order['amount']) {
            updateOrderStatus($orderId, 'fully_refunded');
        } else {
            updateOrderStatus($orderId, 'partially_refunded');
        }

        return true;
    }

    return false;
}

// Usage
try {
    processRefund('ORDER-12345', 50.00, 'Customer returned item');
    echo 'Refund processed successfully';
} catch (\Exception $e) {
    echo 'Refund failed: ' . $e->getMessage();
}
```

## Error Handling

```php
try {
    $response = $client->refund()
        ->cardId($cardId)
        ->orderId($orderId)
        ->amount(50)
        ->send();

    if ($response->isError()) {
        // Common errors:
        // - Invalid card_id
        // - Order not found
        // - Refund amount exceeds original
        // - Already fully refunded

        error_log("Refund failed: " . $response->getMessage());
        error_log("Trace ID: " . $response->getTraceId());
    }
} catch (\Exception $e) {
    error_log('Exception: ' . $e->getMessage());
}
```

## Best Practices

1. **Track Refunds**: Maintain a refunds table in your database with amounts, reasons, and dates.

2. **Partial Refund Support**: Allow multiple partial refunds up to the original amount.

3. **Customer Communication**: Notify customers when refunds are processed.

4. **Prevent Double Refunds**: Check total refunded amount before processing new refunds.

5. **Include Trace ID**: Always log trace IDs for troubleshooting.

## Refund Status

After processing a refund, you can check its status:

```php
$status = $client->checkStatus()
    ->transaction($refundTransaction)
    ->get();

if ($status->getPaymentStatus() === \Epoint\Enums\PaymentStatus::SUCCESS) {
    echo 'Refund completed';
}
```

## See Also

- [Payment Status Check](Payment-Status-Check)
- [Error Handling](Error-Handling)