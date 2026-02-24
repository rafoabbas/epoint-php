# Card Management

## Card Registration

Save customer cards securely for future payments (PCI-compliant tokenization):

```php
$response = $client->registerCard()
    ->description('Save card for future purchases')
    ->successUrl('https://yoursite.com/cards/success')
    ->errorUrl('https://yoursite.com/cards/error')
    ->send();

if ($response->isSuccess()) {
    // Redirect user to card registration page
    header('Location: ' . $response->getRedirectUrl());
    exit;
}
```

## Getting Card ID

After successful registration, get the card ID from the callback:

```php
// In your callback handler (configured in Epoint panel)
$data = $_POST['data'];
$signature = $_POST['signature'];

$callbackData = $client->verifyCallback($data, $signature);

$cardId = $callbackData['card_id'];      // Save this in your database
$cardMask = $callbackData['card_mask'];  // e.g., "****1234"

// Store in database
saveCardForCustomer($customerId, $cardId, $cardMask);
```

## Payment with Saved Card

Charge a saved card without requiring the customer to re-enter card details:

```php
$response = $client->savedCardPayment()
    ->cardId('saved-card-id-from-database')
    ->amount(25.00)
    ->orderId('ORDER-12348')
    ->description('Subscription renewal')
    ->execute();

if ($response->isSuccess()) {
    echo 'Payment successful!';
    $transaction = $response->getTransaction();
}
```

## Parameters

### Card Registration

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `description()` | string | No | Registration description |
| `successUrl()` | string | No | Success redirect URL |
| `errorUrl()` | string | No | Error redirect URL |

### Saved Card Payment

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `cardId()` | string | Yes | Saved card identifier |
| `amount()` | float | Yes | Payment amount |
| `orderId()` | string | Yes | Your unique order ID |
| `description()` | string | No | Payment description |
| `installment()` | bool | No | Enable installments |

## Complete Example: Subscription System

```php
<?php

// Step 1: Register card (one-time)
function registerCustomerCard($customerId)
{
    global $client;

    $response = $client->registerCard()
        ->description("Save card for customer {$customerId}")
        ->successUrl("https://site.com/cards/success?customer={$customerId}")
        ->errorUrl("https://site.com/cards/error?customer={$customerId}")
        ->send();

    return $response->getRedirectUrl();
}

// Step 2: Handle card registration callback
function handleCardCallback()
{
    global $client;

    $data = $_POST['data'];
    $signature = $_POST['signature'];

    try {
        $callbackData = $client->verifyCallback($data, $signature);

        if ($callbackData['status'] === 'success') {
            $cardId = $callbackData['card_id'];
            $cardMask = $callbackData['card_mask'];
            $customerId = $_GET['customer_id'] ?? null;

            // Save to database
            saveCard($customerId, $cardId, $cardMask);

            return true;
        }
    } catch (\Exception $e) {
        error_log('Card registration failed: ' . $e->getMessage());
    }

    return false;
}

// Step 3: Charge saved card (recurring)
function chargeSubscription($customerId, $amount)
{
    global $client;

    $cardId = getCustomerCardId($customerId);

    if (!$cardId) {
        throw new \Exception('No saved card found');
    }

    $response = $client->savedCardPayment()
        ->cardId($cardId)
        ->amount($amount)
        ->orderId('SUB-' . time())
        ->description("Subscription for customer {$customerId}")
        ->execute();

    return $response->isSuccess();
}
```

## Security Best Practices

1. **Never Store Raw Card Data**: Always use Epoint's tokenization. Never store card numbers, CVV, or expiry dates.

2. **Secure Card ID Storage**: Treat card IDs as sensitive data. Encrypt in database if possible.

3. **Customer Consent**: Always get explicit customer consent before saving cards.

4. **Delete Inactive Cards**: Remove card IDs when customer closes account or requests deletion.

## Error Handling

```php
try {
    $response = $client->savedCardPayment()
        ->cardId($cardId)
        ->amount(50)
        ->orderId('ORDER-12349')
        ->execute();

    if ($response->isError()) {
        $message = $response->getMessage();

        // Common errors:
        // - Card expired
        // - Insufficient funds
        // - Card blocked
        // - Invalid card_id

        error_log("Saved card payment failed: {$message}");
        error_log("Trace ID: " . $response->getTraceId());
    }
} catch (\Exception $e) {
    error_log('Exception: ' . $e->getMessage());
}
```

## See Also

- [Standard Payments](Standard-Payments)
- [Refunds and Reversals](Refunds-and-Reversals)
- [Callback Handling](Callback-Handling)