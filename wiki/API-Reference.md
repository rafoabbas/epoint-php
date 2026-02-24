# API Reference

Complete reference for all SDK methods.

## EpointClient

### Constructor

```php
new EpointClient(
    string $publicKey,
    string $privateKey
)
```

**Parameters:**
- `publicKey` - Your merchant public key (e.g., `i000000001`)
- `privateKey` - Your merchant private key

**Example:**
```php
$client = new EpointClient(
    publicKey: 'i000000001',
    privateKey: 'your-private-key'
);
```

## Payment Methods

### payment()

Create a standard payment request.

```php
$client->payment()
    ->amount(float $amount)
    ->orderId(string $orderId)
    ->description(string $description = null)
    ->language(Language $language = Language::EN)
    ->currency(Currency $currency = Currency::AZN)
    ->installment(bool $installment = false)
    ->successUrl(string $url = null)
    ->errorUrl(string $url = null)
    ->send()
```

**Returns:** `PaymentResponse`

### checkStatus()

Check payment status by transaction ID.

```php
$client->checkStatus()
    ->transaction(string $transactionId)
    ->get()
```

**Returns:** `StatusResponse`

### registerCard()

Register a card for future payments.

```php
$client->registerCard()
    ->description(string $description = null)
    ->successUrl(string $url = null)
    ->errorUrl(string $url = null)
    ->send()
```

**Returns:** `PaymentResponse`

### savedCardPayment()

Charge a saved card.

```php
$client->savedCardPayment()
    ->cardId(string $cardId)
    ->amount(float $amount)
    ->orderId(string $orderId)
    ->description(string $description = null)
    ->installment(bool $installment = false)
    ->execute()
```

**Returns:** `PaymentResponse`

### refund()

Refund a payment.

```php
$client->refund()
    ->cardId(string $cardId)
    ->orderId(string $orderId)
    ->amount(float $amount)
    ->description(string $description = null)
    ->send()
```

**Returns:** `RefundResponse`

### reverse()

Reverse/cancel a transaction.

```php
$client->reverse()
    ->transaction(string $transactionId)
    ->amount(float $amount = null)
    ->send()
```

**Returns:** `RefundResponse`

### splitPayment()

Split payment between merchants.

```php
$client->splitPayment()
    ->amount(float $amount)
    ->orderId(string $orderId)
    ->splitUser(string $merchantId)
    ->splitAmount(float $amount)
    ->description(string $description = null)
    ->successUrl(string $url = null)
    ->errorUrl(string $url = null)
    ->send()
```

**Returns:** `PaymentResponse`

### preauth()

Create preauth (hold funds).

```php
// Create preauth
$client->preauth()
    ->amount(float $amount)
    ->orderId(string $orderId)
    ->description(string $description = null)
    ->successUrl(string $url = null)
    ->errorUrl(string $url = null)
    ->send()

// Complete preauth
$client->preauth()
    ->complete(string $transactionId, float $amount)
```

**Returns:** `PaymentResponse`

## Widget & Wallets

### widget()

Create Apple Pay / Google Pay widget.

```php
$client->widget()
    ->amount(float $amount)
    ->orderId(string $orderId)
    ->description(string $description = null)
    ->create()
```

**Returns:** `WidgetResponse`

### wallet()

Wallet operations.

```php
// List wallets
$client->wallet()->list()

// Make wallet payment
$client->wallet()->payment(
    string $walletId,
    float $amount,
    string $orderId
)
```

## Invoice Methods

### invoice()

Invoice management.

```php
// Create invoice
$client->invoice()->create(array $data)

// Send via SMS
$client->invoice()->sendSms(int $invoiceId, string $phone)

// Send via email
$client->invoice()->sendEmail(int $invoiceId, string $email)
```

## Utility Methods

### verifyCallback()

Verify and decode callback data.

```php
$client->verifyCallback(
    string $data,
    string $signature
): array
```

**Throws:** `SignatureVerificationException` if signature is invalid

**Example:**
```php
$data = $_POST['data'];
$signature = $_POST['signature'];

try {
    $callbackData = $client->verifyCallback($data, $signature);
    // Process $callbackData
} catch (SignatureVerificationException $e) {
    // Invalid signature
}
```

### heartbeat()

Check API health.

```php
$client->heartbeat(): array
```

**Returns:**
```php
[
    'status' => 'ok',
    // ... other info
]
```

## Enums

### Language

```php
use Epoint\Enums\Language;

Language::AZ  // Azerbaijani
Language::EN  // English
Language::RU  // Russian
```

### Currency

```php
use Epoint\Enums\Currency;

Currency::AZN  // Azerbaijani Manat
```

### PaymentStatus

```php
use Epoint\Enums\PaymentStatus;

PaymentStatus::NEW      // Payment initiated
PaymentStatus::SUCCESS  // Payment successful
PaymentStatus::ERROR    // Payment failed
```

## Response Objects

All responses inherit from `BaseResponse`:

### Common Methods

```php
$response->isSuccess(): bool
$response->isError(): bool
$response->getStatus(): ?string
$response->getMessage(): ?string
$response->getTraceId(): ?string
$response->toArray(): array
```

### PaymentResponse

```php
$response->getRedirectUrl(): ?string
$response->getTransaction(): ?string
$response->getAmount(): ?float
$response->getOrderId(): ?string
```

### StatusResponse

```php
$response->getPaymentStatus(): PaymentStatus
$response->getTransaction(): ?string
$response->getAmount(): ?float
$response->getOrderId(): ?string
```

### CardResponse

```php
$response->getCardId(): ?string
$response->getCardMask(): ?string
```

### WidgetResponse

```php
$response->getWidgetUrl(): ?string
```

## Exceptions

### SignatureVerificationException

Thrown when callback signature verification fails.

```php
use Epoint\Exceptions\SignatureVerificationException;

try {
    $client->verifyCallback($data, $signature);
} catch (SignatureVerificationException $e) {
    // Handle invalid signature
}
```

## See Also

- [Standard Payments](Standard-Payments)
- [Response Objects](Response-Objects)
- [Error Handling](Error-Handling)