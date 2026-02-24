# Epoint.az Payment Gateway PHP SDK

[![Latest Version](https://img.shields.io/packagist/v/rafoabbas/epoint-php.svg?style=flat-square)](https://packagist.org/packages/rafoabbas/epoint-php)
[![Total Downloads](https://img.shields.io/packagist/dt/rafoabbas/epoint-php.svg?style=flat-square)](https://packagist.org/packages/rafoabbas/epoint-php)
[![License](https://img.shields.io/packagist/l/rafoabbas/epoint-php.svg?style=flat-square)](https://packagist.org/packages/rafoabbas/epoint-php)

Modern PHP SDK for integrating [Epoint.az](https://epoint.az) payment gateway into your application. Supports standard payments, card tokenization, split payments, preauth, Apple Pay, Google Pay, wallets, and invoices.

## Features

- ✅ **Standard Payment** - Accept online card payments
- ✅ **Card Registration** - Save cards for future payments (PCI-compliant tokenization)
- ✅ **Saved Card Payments** - Charge saved cards without re-entering details
- ✅ **Split Payments** - Split payment between multiple merchants
- ✅ **Preauth** - Hold funds before capture
- ✅ **Refunds & Reversals** - Full or partial refunds, transaction cancellation
- ✅ **Apple Pay & Google Pay** - Digital wallet integration
- ✅ **Wallets** - Support for local e-wallets
- ✅ **Invoices** - Create and send payment invoices
- ✅ **Signature Verification** - Secure callback validation
- ✅ **Type-safe** - Full PHP 8.2+ type coverage with enums
- ✅ **Fluent API** - Clean, readable builder pattern

## Requirements

- PHP 8.2 or higher
- ext-json
- GuzzleHTTP 7.0+

## Installation

```bash
composer require rafoabbas/epoint-php
```

## Quick Start

### 1. Initialize Client

```php
use Epoint\EpointClient;

$client = new EpointClient(
    publicKey: 'i000000001',      // Your merchant public key
    privateKey: 'your-private-key', // Your merchant private key
    testMode: true                  // Use test environment
);
```

### 2. Create Payment

```php
use Epoint\Enums\Language;

$response = $client->payment()
    ->amount(100.50)
    ->orderId('ORDER-12345')
    ->description('Product purchase')
    ->language(Language::EN)
    ->successUrl('https://yoursite.com/payment/success')
    ->errorUrl('https://yoursite.com/payment/error')
    ->send();

if ($response->isSuccess()) {
    // Redirect user to payment page
    header('Location: ' . $response->getRedirectUrl());
}
```

### 3. Handle Callback

```php
// In your callback handler (result_url)
$data = $_POST['data'];
$signature = $_POST['signature'];

try {
    $callbackData = $client->verifyCallback($data, $signature);

    if ($callbackData['status'] === 'success') {
        // Payment successful
        $orderId = $callbackData['order_id'];
        $transaction = $callbackData['transaction'];
        $amount = $callbackData['amount'];

        // Update your database
    }
} catch (\Epoint\Exceptions\SignatureVerificationException $e) {
    // Invalid signature - possible fraud
    http_response_code(400);
}
```

## Usage Examples

### Standard Payment

```php
$response = $client->payment()
    ->amount(50.00)
    ->orderId('ORDER-001')
    ->description('Monthly subscription')
    ->installment(true) // Enable installment payment
    ->send();
```

### Check Payment Status

```php
$status = $client->checkStatus()
    ->transaction('te001234567')
    ->get();

if ($status->getPaymentStatus() === \Epoint\Enums\PaymentStatus::SUCCESS) {
    echo 'Payment successful!';
}
```

### Card Registration

```php
// Register card without payment
$response = $client->registerCard()
    ->description('Save card for future purchases')
    ->successUrl('https://yoursite.com/cards/success')
    ->errorUrl('https://yoursite.com/cards/error')
    ->send();

// Get card_id from callback and store in your database
$cardId = $response->getCardId();
```

### Payment with Saved Card

```php
$response = $client->savedCardPayment()
    ->cardId('saved-card-id-from-database')
    ->amount(25.00)
    ->orderId('ORDER-002')
    ->description('Subscription renewal')
    ->execute();
```

### Refund

```php
$response = $client->refund()
    ->cardId('card-id-for-refund')
    ->orderId('original-order-id')
    ->amount(50.00)
    ->description('Product return')
    ->send();
```

### Reverse/Cancel Transaction

```php
$response = $client->reverse()
    ->transaction('te001234567')
    ->amount(100.00) // Optional: partial reversal
    ->send();
```

### Split Payment

```php
// Split payment between two merchants
$response = $client->splitPayment()
    ->amount(100.00)
    ->orderId('ORDER-003')
    ->splitUser('i000000002') // Second merchant ID
    ->splitAmount(30.00)      // Amount for second merchant
    ->description('Marketplace order')
    ->send();
```

### Preauth (Hold Funds)

```php
// Step 1: Create preauth request
$response = $client->preauth()
    ->amount(100.00)
    ->orderId('ORDER-004')
    ->description('Hotel reservation')
    ->send();

$transaction = $response->getTransaction();

// Step 2: Complete preauth to capture funds
$completeResponse = $client->preauth()
    ->complete($transaction, 85.00); // Capture partial or full amount
```

### Apple Pay / Google Pay

```php
$widget = $client->widget()
    ->amount(75.00)
    ->orderId('ORDER-005')
    ->description('Digital wallet payment')
    ->create();

// Use widget URL in iframe or webview
echo '<iframe src="' . $widget->getWidgetUrl() . '"></iframe>';
```

### Wallets

```php
// Get available wallets
$wallets = $client->wallet()->list();

foreach ($wallets->getWallets() as $wallet) {
    echo $wallet['name'];
}

// Make wallet payment
$response = $client->wallet()->payment(
    walletId: 'wallet-id',
    amount: 50.00,
    orderId: 'ORDER-006'
);
```

### Invoices

```php
// Create invoice
$invoice = $client->invoice()->create([
    'sum' => 150.00,
    'display' => 1,
    'save_as_template' => 0,
    'name' => 'John Doe',
    'phone' => '+994501234567',
    'email' => 'john@example.com',
    'description' => 'Invoice for services',
    'period_from' => '2024-01-01',
    'period_to' => '2024-12-31',
]);

// Send invoice via SMS
$client->invoice()->sendSms($invoice['id'], '+994501234567');

// Send invoice via email
$client->invoice()->sendEmail($invoice['id'], 'john@example.com');
```

### Heartbeat Check

```php
$status = $client->heartbeat();

if ($status['status'] === 'ok') {
    echo 'Epoint API is operational';
}
```

## API Reference

### EpointClient Methods

| Method | Description |
|--------|-------------|
| `payment()` | Create standard payment request |
| `checkStatus()` | Check payment status |
| `registerCard()` | Register card without payment |
| `savedCardPayment()` | Payment with saved card |
| `refund()` | Refund payment |
| `reverse()` | Reverse/cancel transaction |
| `splitPayment()` | Split payment between merchants |
| `preauth()` | Preauth (hold and capture) |
| `widget()` | Apple Pay / Google Pay widget |
| `wallet()` | Wallet operations |
| `invoice()` | Invoice management |
| `heartbeat()` | API health check |
| `verifyCallback()` | Verify callback signature |

### Enums

```php
use Epoint\Enums\Currency;
use Epoint\Enums\Language;
use Epoint\Enums\PaymentStatus;

Currency::AZN
Language::AZ | Language::EN | Language::RU
PaymentStatus::NEW | PaymentStatus::SUCCESS | PaymentStatus::ERROR
```

## Testing

```bash
composer test
```

## Code Quality

```bash
composer cs:fix    # Fix code style
composer cs:check  # Check code style
composer analyse   # Run static analysis
```

## Security

- All API requests are signed with SHA1 HMAC signatures
- Callback responses are verified to prevent tampering
- Never store raw card data - use tokenization
- Use HTTPS in production
- Keep your private key secure

## Changelog

See [CHANGELOG.md](CHANGELOG.md)

## License

MIT License. See [LICENSE](LICENSE) for details.

## Credits

- [Rauf Abbaszade](https://github.com/rafoabbas)

## Support

- [GitHub Issues](https://github.com/rafoabbas/epoint-php/issues)
- [Epoint Documentation](https://epoint.az)