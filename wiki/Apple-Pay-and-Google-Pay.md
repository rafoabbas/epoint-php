# Apple Pay & Google Pay

The SDK supports two ways to accept Apple Pay and Google Pay:

| | **Token payment** (recommended) | **Widget** |
|---|---|---|
| Button | Official Apple Pay / Google Pay button on your own page | Epoint page inside an iframe / webview |
| When the Epoint record is created | When you call `tokenPayment()`, ideally right after the customer authorizes in the wallet sheet | When you create the widget, before the customer pays |
| Result | Synchronous response from `applePay()` / `googlePay()` | `postMessage` from the iframe (no redirect) |
| Status lookup | Record is bound to the merchant immediately; `checkStatus()->orderId()` always finds it | Record is bound to the merchant only after it is paid |
| Charged at most once | Yes: Epoint charges a record only while it is `NEW` | One widget can be paid only once, but the same `order_id` can get new records |

## Token payment flow

```
Browser (your page)                      Your server                           Epoint
-------------------                      -----------                           ------
click Apple Pay button
  new ApplePaySession(...).begin()
onvalidatemerchant ───────────────────▶  applePaySession()->origin(...) ─────▶ /token/apple/session
  completeMerchantValidation(session) ◀─ getSession()
onpaymentauthorized(token) ───────────▶  1. re-validate order, amount
                                         2. tokenPayment()->create() ───────▶ /token/payment
                                         3. applePay()->paymentId()->pay() ──▶ /token/apple/pay
                                         4. checkStatus()->orderId() ───────▶ /get-status
  completePayment(SUCCESS/FAILURE)    ◀─ result
```

### 1. Merchant session (`onvalidatemerchant`)

```php
$session = $client->applePaySession()
    ->origin('https://shop.example.az') // origin of the page that shows the button
    ->create();

if (! $session->isSuccess()) {
    // abort the Apple Pay sheet
}

return json_encode(['session' => $session->getSession()]); // -> completeMerchantValidation()
```

The origin's domain must be registered for Apple Pay on Epoint's side. Apple also requires the domain association file to be served from `https://<domain>/.well-known/apple-developer-merchantid-domain-association`.

### 2. Create the token payment record

Create the record when the customer has authorized in the wallet sheet, after re-validating the order on your server. Then the charged amount always matches the order the customer confirmed.

```php
use Epoint\Enums\Language;

$tokenPayment = $client->tokenPayment()
    ->amount(25.50)
    ->orderId('ORDER-001-1')          // unique per attempt, max 250 characters
    ->description('Order #ORDER-001')
    ->language(Language::AZ)
    ->create();

$paymentId = $tokenPayment->getPaymentId();
```

If the merchant has unique order IDs enabled, Epoint rejects a second record with the same `order_id` (`Duplicate order_id value`). Use an attempt suffix such as `ORDER-001-1`, `ORDER-001-2`.

### 3. Charge the wallet token

```php
// Apple Pay: event.payment.token.paymentData from onpaymentauthorized
$result = $client->applePay()
    ->paymentId($paymentId)
    ->applePaymentData($paymentData)          // array, encoded to base64 JSON for you
    ->billingContact($billingContact)
    ->pay();

// Google Pay: paymentMethodData.tokenizationData.token from loadPaymentData()
$result = $client->googlePay()
    ->paymentId($paymentId)
    ->googleRawToken($rawToken)               // string, encoded to base64 for you
    ->billingContact($billingAddress + ['email' => $email])
    ->pay();

if (! $result->isSuccess()) {
    // declined or already processed: $result->getMessage(), $result->getCode()
}
```

A declined charge is returned as an unsuccessful `TokenPayResponse`, not as an exception: Epoint answers it with HTTP 400 and a JSON body. Network errors and non-JSON responses still throw `EpointException`.

If you already have a base64 token in Epoint's format, use `->token($base64Token)` instead.

### 4. Always confirm with `get-status`

Treat the `pay()` response as a hint and confirm the payment before marking an order paid:

```php
$status = $client->checkStatus()->orderId('ORDER-001-1')->get();

$paid = $status->isSuccess()
    && $status->getOrderId() === 'ORDER-001-1'
    && (int) round($status->getAmount() * 100) === 2550;
```

If the request times out or the customer closes the page, keep the attempt and check its status again later (for example from a scheduled job). Do not create a new record for the same attempt.

### Browser side (Apple Pay)

```js
button.addEventListener('click', () => {
    const session = new ApplePaySession(3, {
        countryCode: 'AZ',
        currencyCode: 'AZN',
        supportedNetworks: ['visa', 'masterCard'],
        merchantCapabilities: ['supports3DS'],
        total: { type: 'final', label: 'Shop name', amount: '25.50' },
    });

    session.onvalidatemerchant = async () => {
        const res = await fetch('/api/apple-pay/session', { method: 'POST', headers: { 'X-CSRF-Token': csrf } });
        const body = await res.json();
        body.session ? session.completeMerchantValidation(body.session) : session.abort();
    };

    session.onpaymentauthorized = async (event) => {
        const res = await fetch('/api/apple-pay/pay', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
            body: JSON.stringify({ paymentData: event.payment.token.paymentData, billingContact: event.payment.billingContact }),
        });
        const body = await res.json();
        session.completePayment(body.paid ? ApplePaySession.STATUS_SUCCESS : ApplePaySession.STATUS_FAILURE);
    };

    session.oncancel = () => { /* customer closed the sheet: not a failed payment */ };

    session.begin(); // must be called synchronously inside the click handler
});
```

### Google Pay notes

Google Pay tokenization uses Epoint's gateway settings:

```js
tokenizationSpecification: {
    type: 'PAYMENT_GATEWAY',
    parameters: { gateway: 'cybersource', gatewayMerchantId: '<your gateway merchant id from Epoint>' },
},
merchantInfo: { merchantId: '<Google merchant id from Epoint>', merchantName: 'Shop name' },
```

Ask Epoint for the `gatewayMerchantId` of your merchant account and the Google `merchantId`.

### Security

- Never log wallet tokens, `paymentData` or full billing contacts.
- Re-calculate the amount on your server. Never trust an amount sent by the browser.
- Protect your session and pay endpoints with CSRF and rate limiting.
- Apple Pay / Google Pay must be enabled for API use on your merchant account, otherwise Epoint answers `You don't have access to this operation`.

## Widget flow

```php
$widget = $client->widget()
    ->amount(75.00)
    ->orderId('ORDER-005')
    ->description('Digital wallet payment')
    ->create();

echo '<iframe src="' . $widget->getWidgetUrl() . '" allow="payment"></iframe>';
```

The widget reports its result with `window.parent.postMessage({status: 'success' | 'error', ...})`. It does not redirect. Confirm the payment with `checkStatus()->orderId()` before marking the order paid.

## See Also

- [Payment Status Check](Payment-Status-Check)
- [Error Handling](Error-Handling)
- [API Reference](API-Reference)
