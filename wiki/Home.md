# Epoint.az Payment Gateway PHP SDK

Welcome to the official documentation for the Epoint PHP SDK. This SDK provides a modern, type-safe PHP interface for integrating Epoint.az payment gateway into your application.

## Quick Navigation

### Getting Started
- [Installation & Setup](Installation-and-Setup)
- [Quick Start Guide](Quick-Start-Guide)

### Payment Methods
- [Standard Payments](Standard-Payments)
- [Payment Status Check](Payment-Status-Check)
- [Card Registration & Management](Card-Management)
- [Card Registration with Payment](Card-Management#card-registration-with-payment)
- [Saved Card Payments](Card-Management#payment-with-saved-card)

### Advanced Features
- [Split Payments](Standard-Payments#split-payments)
- [Refunds & Reversals](Refunds-and-Reversals)

### Integration
- [Callback Handling & Security](Callback-Handling)
- [Error Handling & Troubleshooting](Error-Handling)
- [Testing & Sandbox](Testing)

### Reference
- [API Reference](API-Reference)
- [Response Objects](Response-Objects)

## Features

✅ **Standard Payment** - Accept online card payments
✅ **Card Registration** - Save cards for future payments (PCI-compliant)
✅ **Card Registration with Payment** - Register card and process payment in one step
✅ **Saved Card Payments** - Charge saved cards without re-entering details
✅ **Split Payments** - Split payment between multiple merchants (with saved card & registration support)
✅ **Preauth** - Hold funds before capture (authorization + completion)
✅ **Refunds & Reversals** - Full or partial refunds, transaction cancellation
✅ **Apple Pay & Google Pay** - Digital wallet integration
✅ **Wallets** - Support for local e-wallets
✅ **Invoices** - Create and send payment invoices
✅ **Signature Verification** - Secure callback validation
✅ **Type-safe** - Full PHP 8.2+ type coverage with enums
✅ **Fluent API** - Clean, readable builder pattern

## Requirements

- PHP 8.2 or higher
- ext-json
- ext-curl

## Installation

```bash
composer require rafoabbas/epoint-php
```

## Quick Example

```php
use Epoint\EpointClient;

$client = new EpointClient(
    publicKey: 'i000000001',
    privateKey: 'your-private-key'
);

$response = $client->payment()
    ->amount(100.50)
    ->orderId('ORDER-12345')
    ->description('Product purchase')
    ->send();

if ($response->isSuccess()) {
    header('Location: ' . $response->getRedirectUrl());
}
```

## Support

- **GitHub Issues**: [Report bugs or request features](https://github.com/rafoabbas/epoint-php/issues)
- **Epoint Documentation**: [Official API docs](https://epoint.az)

## License

MIT License - see [LICENSE](https://github.com/rafoabbas/epoint-php/blob/main/LICENSE) for details.