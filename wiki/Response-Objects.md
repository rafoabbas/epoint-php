# Response Objects

All API methods return response objects with convenient getter methods and full data access.

## Common Methods

Available on all response objects:

```php
$response->isSuccess()          // Check if request was successful
$response->isError()            // Check if request failed
$response->getStatus()          // Get status: 'success', 'error', 'new', etc.
$response->getMessage()         // Get response message
$response->getTraceId()         // Get trace ID for troubleshooting
$response->toArray()            // Get complete response data as array
```

## Payment Response

Returned by `payment()`, `savedCardPayment()`, `splitPayment()`, `splitCardPayment()`, `preauth()`:

```php
$response = $client->payment()->amount(100)->orderId('ORDER-123')->send();

// Check status
$response->isSuccess();        // true if payment initiated

// Get payment details
$response->getRedirectUrl();   // Payment page URL
$response->getTransaction();   // Transaction ID (e.g., te001234567)
$response->getAmount();        // Payment amount
$response->getOrderId();       // Your order ID

// Get bank response details
$response->getBankResponse();  // Bank response code
$response->getOperationCode(); // Operation code
$response->getOtherAttributes(); // Additional attributes array

// Get full data
$data = $response->toArray();
```

## Status Response

Returned by `checkStatus()`:

```php
$status = $client->checkStatus()->transaction('te001234567')->get();

// Get payment status
$status->getPaymentStatus();   // PaymentStatus enum (NEW, SUCCESS, ERROR)
$status->getStatus();          // Status string
$status->isSuccess();          // true if payment successful

// Get transaction details
$status->getTransaction();     // Transaction ID
$status->getAmount();          // Payment amount
$status->getOrderId();         // Your order ID
$status->getMessage();         // Status message

// Get bank response details
$status->getBankResponse();    // Bank response code
$status->getOtherAttributes(); // Additional attributes array
```

## Card Registration Response

Returned by `registerCard()`:

```php
$response = $client->registerCard()->send();

// Card details (available in callback)
$response->getCardId();        // Saved card ID
$response->getCardMask();      // Masked card number (e.g., ****1234)
$response->getRedirectUrl();   // Card registration page URL

// Get bank response details
$response->getBankResponse();  // Bank response code
$response->getOperationCode(); // Operation code
$response->getRrn();           // Retrieval reference number
```

## Card Registration with Payment Response

Returned by `registerCardWithPay()` and `splitCardRegistrationWithPay()`:

```php
$response = $client->registerCardWithPay()
    ->amount(100)
    ->orderId('ORDER-123')
    ->send();

// Card details (available in callback)
$response->getCardId();        // Saved card ID
$response->getCardMask();      // Masked card number (e.g., ****1234)
$response->getCardName();      // Card holder name

// Payment details
$response->getTransaction();   // Transaction ID
$response->getOrderId();       // Your order ID
$response->getAmount();        // Payment amount
$response->getRedirectUrl();   // Payment page URL

// Bank response details
$response->getBankTransaction(); // Bank transaction ID
$response->getRrn();           // Retrieval reference number
$response->getBankResponse();  // Bank response code
$response->getOperationCode(); // Operation code
$response->getOtherAttributes(); // Additional attributes array
```

## Refund Response

Returned by `refund()` and `reverse()`:

```php
$response = $client->refund()
    ->cardId($cardId)
    ->orderId($orderId)
    ->amount(50)
    ->send();

$response->isSuccess();        // Check if refund initiated
$response->getTransaction();   // Refund transaction ID
$response->getMessage();       // Refund status message
$response->getBankResponse();  // Bank response code
```

## Preauth Complete Response

Returned by `preauth()->complete()`:

```php
$response = $client->preauth()->complete('te001234567', 85.00);

// Transaction details
$response->getTransaction();   // Transaction ID
$response->getAmount();        // Captured amount

// Card details
$response->getCardMask();      // Masked card number
$response->getCardName();      // Card holder name

// Bank response details
$response->getBankTransaction(); // Bank transaction ID
$response->getRrn();           // Retrieval reference number
$response->getBankResponse();  // Bank response code
```

## Widget Response

Returned by `widget()->create()`:

```php
$widget = $client->widget()
    ->amount(75)
    ->orderId('ORDER-125')
    ->create();

$widget->getWidgetUrl();       // Apple Pay / Google Pay widget URL
$widget->isSuccess();          // Check if widget created
```

## Using toArray()

Get the complete response data as an associative array:

```php
$response = $client->payment()
    ->amount(100)
    ->orderId('ORDER-123')
    ->send();

$data = $response->toArray();

print_r($data);

// Output:
// [
//     'status' => 'success',
//     'redirect_url' => 'https://epoint.az/payment/...',
//     'transaction' => 'te001234567',
//     'message' => 'Payment initiated',
//     'trace_id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
//     'order_id' => 'ORDER-123',
//     'amount' => 100.00,
//     'currency' => 'AZN',
//     // ... all other fields
// ]
```

## Debugging with toArray()

Use `toArray()` for debugging or logging:

```php
$response = $client->payment()->amount(50)->orderId('TEST-001')->send();

// Log full response for debugging
error_log(print_r($response->toArray(), true));

// Or save to file
file_put_contents('response.log', print_r($response->toArray(), true), FILE_APPEND);
```

## Error Responses

When a request fails:

```php
$response = $client->payment()->amount(100)->orderId('ORDER-123')->send();

if ($response->isError()) {
    echo $response->getMessage();     // Error description
    echo $response->getStatus();      // 'error'
    echo $response->getTraceId();     // Trace ID for support

    // Get full error details
    $errorData = $response->toArray();
    // [
    //     'status' => 'error',
    //     'message' => 'Invalid credentials',
    //     'code' => 'AUTH_ERROR',
    //     'trace_id' => '...',
    // ]
}
```

## PaymentStatus Enum

Used by status responses:

```php
use Epoint\Enums\PaymentStatus;

$status = $client->checkStatus()->transaction('te001234567')->get();

switch ($status->getPaymentStatus()) {
    case PaymentStatus::NEW:
        echo 'Payment initiated, not completed';
        break;
    case PaymentStatus::SUCCESS:
        echo 'Payment successful';
        break;
    case PaymentStatus::ERROR:
        echo 'Payment failed';
        break;
}
```

## Trace ID for Support

**Important**: Always include the `trace_id` when reporting issues to Epoint support:

```php
try {
    $response = $client->payment()->amount(100)->orderId('ORDER-123')->send();
} catch (\Exception $e) {
    $traceId = $response->getTraceId() ?? 'N/A';

    error_log("Payment failed. Trace ID: {$traceId}. Error: " . $e->getMessage());

    // Include trace_id when contacting Epoint support
    echo "An error occurred. Please contact support with Trace ID: {$traceId}";
}
```

## Response Example

Complete example showing all response methods:

```php
$response = $client->payment()
    ->amount(100)
    ->orderId('ORDER-123')
    ->send();

// Basic checks
if ($response->isSuccess()) {
    echo "✅ Success!\n";
} else {
    echo "❌ Failed!\n";
}

// Get specific fields
echo "Status: " . $response->getStatus() . "\n";
echo "Message: " . $response->getMessage() . "\n";
echo "Transaction: " . $response->getTransaction() . "\n";
echo "Redirect URL: " . $response->getRedirectUrl() . "\n";
echo "Trace ID: " . $response->getTraceId() . "\n";

// Get everything
$fullData = $response->toArray();
echo "\nFull Response:\n";
print_r($fullData);
```

## See Also

- [Standard Payments](Standard-Payments)
- [Payment Status Check](Payment-Status-Check)
- [Error Handling](Error-Handling)