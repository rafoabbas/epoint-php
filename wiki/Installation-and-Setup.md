# Installation & Setup

## Installation

Install the Epoint PHP SDK via Composer:

```bash
composer require rafoabbas/epoint-php
```

## Requirements

- **PHP**: 8.2 or higher
- **Extensions**: ext-json, ext-curl
- **Composer**: Latest version recommended

## Getting Your Credentials

Before using the SDK, you need to obtain credentials from Epoint.az:

1. **Contact Epoint**: Reach out to Epoint support to register as a merchant
2. **Receive Credentials**:
   - Public Key (Merchant ID) - e.g., `i000000001`
   - Private Key - Keep this secret!
3. **Test Environment**: Request test credentials for development

## Initialize the Client

### Basic Setup

```php
use Epoint\EpointClient;

$client = new EpointClient(
    publicKey: 'i000000001',      // Your merchant public key
    privateKey: 'your-secret-key', // Your private key (keep secret!)
    testMode: true                 // true = test environment, false = production
);
```

### Production Setup

For production, use environment variables to store credentials securely:

```php
$client = new EpointClient(
    publicKey: getenv('EPOINT_PUBLIC_KEY'),
    privateKey: getenv('EPOINT_PRIVATE_KEY'),
    testMode: false // Production mode
);
```

### Environment File (.env)

Create a `.env` file in your project root:

```env
EPOINT_PUBLIC_KEY=i000000001
EPOINT_PRIVATE_KEY=your-actual-private-key
EPOINT_TEST_MODE=true
```

**Important**: Add `.env` to your `.gitignore` file!

```gitignore
.env
.env.local
```

## Test Mode

```php
// Enable test mode
$client = new EpointClient(
    publicKey: 'i000000001',
    privateKey: 'your-private-key',
    testMode: true  // Set to false for production
);
```

**Note**: Contact Epoint support for test credentials and testing instructions.

## Verify Installation

Test that everything is working:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Epoint\EpointClient;

$client = new EpointClient(
    publicKey: 'i000000001',
    privateKey: 'your-private-key',
    testMode: true
);

// Check API health
$status = $client->heartbeat();

if ($status['status'] === 'ok') {
    echo "✅ Epoint SDK is working!\n";
    echo "API Status: {$status['status']}\n";
} else {
    echo "❌ Connection failed\n";
}
```

## Configuration URLs

When creating payments, you'll need to configure these URLs:

### Success URL
Where to redirect users after successful payment:
```php
->successUrl('https://yoursite.com/payment/success')
```

### Error URL
Where to redirect users after failed payment:
```php
->errorUrl('https://yoursite.com/payment/error')
```

### Callback URL (Result URL)
Your backend endpoint to receive payment notifications is configured once in your Epoint merchant panel, not per payment.

## Security Best Practices

1. **Never Commit Credentials**: Use environment variables, never hardcode keys
2. **Use HTTPS**: Always use HTTPS in production for callback URLs
3. **Secure Private Key**: Store private key securely, never expose in client-side code
4. **Rotate Keys**: Periodically rotate your API keys
5. **Test Mode**: Always test in test mode before going live

## Next Steps

- [Quick Start Guide](Quick-Start-Guide) - Make your first payment
- [Standard Payments](Standard-Payments) - Learn about payment options
- [Callback Handling](Callback-Handling) - Handle payment notifications

## Troubleshooting

### Composer Install Fails

```bash
# Update composer
composer self-update

# Clear cache
composer clear-cache

# Try again
composer require rafoabbas/epoint-php
```

### Extension Not Found

```bash
# Install required extensions (Ubuntu/Debian)
sudo apt-get install php-json php-curl

# Or for specific PHP version
sudo apt-get install php8.2-json php8.2-curl
```

### API Connection Issues

```php
// Test API connectivity
$status = $client->heartbeat();
print_r($status);

// Check if you're using correct test/production mode
```