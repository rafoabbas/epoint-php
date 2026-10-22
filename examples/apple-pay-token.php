<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;

// Initialize Epoint client
$client = new EpointClient(
    publicKey: 'i000000001',      // Replace with your public key
    privateKey: 'your-private-key', // Replace with your private key
);

$orderId = 'ORDER-'.time().'-1'; // unique per attempt

try {
    // 1. onvalidatemerchant: create the Apple Pay merchant session
    $session = $client->applePaySession()
        ->origin('https://shop.example.az')
        ->create();

    echo "=== Apple Pay Session ===\n";
    echo 'Success : '.($session->isSuccess() ? 'YES' : 'NO')."\n\n";

    // 2. onpaymentauthorized: create the token payment record
    $tokenPayment = $client->tokenPayment()
        ->amount(25.50)
        ->orderId($orderId)
        ->description('Order payment')
        ->create();

    echo "=== Token Payment ===\n";
    echo "Payment ID : {$tokenPayment->getPaymentId()}\n";
    echo "Order ID   : {$tokenPayment->getOrderId()}\n\n";

    // 3. Charge the Apple Pay token (event.payment.token.paymentData from the browser)
    $paymentData = json_decode('{"version":"EC_v1","data":"...","signature":"...","header":{}}', true);

    $result = $client->applePay()
        ->paymentId((int) $tokenPayment->getPaymentId())
        ->applePaymentData($paymentData)
        ->billingContact(['emailAddress' => 'customer@example.az'])
        ->pay();

    echo "=== Charge ===\n";
    echo 'Success : '.($result->isSuccess() ? 'YES' : 'NO')."\n";
    echo "Code    : {$result->getCode()}\n";
    echo "Message : {$result->getMessage()}\n\n";

    // 4. Always confirm with get-status before marking the order paid
    $status = $client->checkStatus()->orderId($orderId)->get();

    echo "=== Status ===\n";
    echo "Status : {$status->getStatus()}\n";
    echo "Amount : {$status->getAmount()}\n";
} catch (EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
    print_r($e->getResponseData());
}
