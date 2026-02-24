# Standard Payments

Learn how to accept online card payments with Epoint.

## Basic Payment

```php
use Epoint\Enums\Language;

$response = $client->payment()
    ->amount(100.50)
    ->orderId('ORDER-12345')
    ->description('Product purchase')
    ->language(Language::EN)
    ->send();

if ($response->isSuccess()) {
    header('Location: ' . $response->getRedirectUrl());
}
```

## Required Parameters

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `amount()` | float | Payment amount (min 0.01) | `100.50` |
| `orderId()` | string | Your unique order ID | `'ORDER-12345'` |

## Optional Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `description()` | string | - | Payment description shown to customer |
| `language()` | Language | EN | Payment page language (AZ, EN, RU) |
| `currency()` | Currency | AZN | Payment currency |
| `installment()` | bool | false | Enable installment option |
| `successUrl()` | string | - | Success redirect URL |
| `errorUrl()` | string | - | Error redirect URL |

## Response Methods

All payment responses include these methods:

```php
$response->isSuccess();        // Check if payment initiated
$response->isError();          // Check if error occurred
$response->getRedirectUrl();   // Get payment page URL
$response->getTransaction();   // Get transaction ID
$response->getMessage();       // Get response message
$response->getTraceId();       // Get trace ID for support
$response->toArray();          // Get full response data
```

## Complete Example

```php
<?php

use Epoint\EpointClient;
use Epoint\Enums\Language;
use Epoint\Enums\Currency;

$client = new EpointClient(
    publicKey: 'i000000001',
    privateKey: 'your-private-key'
);

$response = $client->payment()
    ->amount(150.00)
    ->orderId('ORDER-' . time())
    ->description('Electronics - Order #12345')
    ->language(Language::AZ)
    ->currency(Currency::AZN)
    ->installment(true)
    ->successUrl('https://example.com/payment/success')
    ->errorUrl('https://example.com/payment/error')
    ->send();

if ($response->isSuccess()) {
    $transaction = $response->getTransaction();

    // Store in database
    savePayment([
        'order_id' => 'ORDER-' . time(),
        'transaction' => $transaction,
        'amount' => 150.00,
        'status' => 'pending',
    ]);

    // Redirect to payment page
    header('Location: ' . $response->getRedirectUrl());
    exit;
} else {
    echo 'Error: ' . $response->getMessage();
    echo '<br>Trace ID: ' . $response->getTraceId();
}
```

## Installment Payments

Allow customers to pay in installments:

```php
$response = $client->payment()
    ->amount(500.00)
    ->orderId('ORDER-12346')
    ->installment(true)  // Customer can choose installment plan
    ->send();
```

When enabled, customers see installment options on the payment page (e.g., 3, 6, 12 months).

## Multi-Language Support

```php
use Epoint\Enums\Language;

// Azerbaijani
$response = $client->payment()
    ->language(Language::AZ)
    ->send();

// English
$response = $client->payment()
    ->language(Language::EN)
    ->send();

// Russian
$response = $client->payment()
    ->language(Language::RU)
    ->send();
```

## Error Handling

```php
try {
    $response = $client->payment()
        ->amount(100)
        ->orderId('ORDER-12347')
        ->send();

    if ($response->isError()) {
        $message = $response->getMessage();
        $traceId = $response->getTraceId();

        error_log("Payment failed: {$message}. Trace ID: {$traceId}");

        // Show error to user
        echo "Payment initialization failed. Please try again.";
    }
} catch (\Exception $e) {
    error_log('Payment exception: ' . $e->getMessage());
    echo "An error occurred. Please contact support.";
}
```

## Best Practices

1. **Unique Order IDs**: Always use unique order IDs for each payment
2. **Store Transaction ID**: Save the transaction ID in your database immediately
3. **Configure Callback**: Set up callback URL in Epoint merchant panel for payment notifications
4. **Handle Errors**: Always check `isSuccess()` and handle errors gracefully
5. **Log Trace IDs**: Always log trace IDs for troubleshooting

## See Also

- [Payment Status Check](Payment-Status-Check)
- [Callback Handling](Callback-Handling)
- [Testing](Testing)