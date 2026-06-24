<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Epoint\Enums\Language;
use Epoint\EpointClient;

// Initialize Epoint client
$client = new EpointClient(
    publicKey: 'i000000001',      // Replace with your public key
    privateKey: 'your-private-key', // Replace with your private key
);

try {
    // Register card and make first payment simultaneously
    $response = $client->registerCardWithPay()
        ->amount(100.00)
        ->orderId('FIRST-PAY-'.time())
        ->description('Card registration with first payment')
        ->language(Language::AZ)
        ->successUrl('https://yoursite.com/payment/success')
        ->errorUrl('https://yoursite.com/payment/error')
        ->send();

    echo "=== Card Registration with Payment ===\n\n";
    echo "Status       : {$response->getStatus()}\n";
    echo "Success      : ".($response->isSuccess() ? 'YES' : 'NO')."\n";
    echo "Redirect URL : {$response->getRedirectUrl()}\n";
    echo "Card ID      : {$response->getCardId()}\n";
    echo "Transaction  : {$response->getTransaction()}\n";
    echo "Order ID     : {$response->getOrderId()}\n";
    echo "Message      : {$response->getMessage()}\n";

    if ($response->isSuccess()) {
        echo "\nRedirect user to: {$response->getRedirectUrl()}\n";
        // header('Location: ' . $response->getRedirectUrl());
    }

    echo "\nFull response:\n";
    print_r($response->toArray());

    // To register as a refund card:
    // $response = $client->registerCardWithPay()
    //     ->amount(100.00)
    //     ->orderId('ORDER-123')
    //     ->forRefund()
    //     ->send();

} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}
