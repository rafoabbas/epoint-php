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
    // Create payment request
    $response = $client->payment()
        ->amount(50.00)
        ->orderId('ORDER-'.time())
        ->description('Product purchase')
        ->language(Language::EN)
        ->successUrl('https://yoursite.com/payment/success')
        ->errorUrl('https://yoursite.com/payment/error')
        ->send();

    if ($response->isSuccess()) {
        echo "Payment created successfully!\n";
        echo "Transaction ID: {$response->getTransaction()}\n";
        echo "Redirect user to: {$response->getRedirectUrl()}\n";

        // In web application, redirect user:
        // header('Location: ' . $response->getRedirectUrl());
    } else {
        echo "Payment creation failed: {$response->getMessage()}\n";
    }
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}