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

$splitUser = 'split-user-public-key'; // Sub-merchant public key

try {
    // Split payment: total 100 AZN, sub-merchant gets 80 AZN
    $response = $client->splitPayment()
        ->amount(100.00)
        ->orderId('SPLIT-'.time())
        ->splitUser($splitUser)
        ->splitAmount(80.00)
        ->description('Marketplace purchase - split payment')
        ->language(Language::AZ)
        ->successUrl('https://yoursite.com/payment/success')
        ->errorUrl('https://yoursite.com/payment/error')
        ->send();

    echo "=== Split Payment ===\n\n";
    echo "Status       : {$response->getStatus()}\n";
    echo "Success      : ".($response->isSuccess() ? 'YES' : 'NO')."\n";
    echo "Transaction  : {$response->getTransaction()}\n";
    echo "Redirect URL : {$response->getRedirectUrl()}\n";
    echo "Message      : {$response->getMessage()}\n";

    if ($response->isSuccess()) {
        echo "\nRedirect user to: {$response->getRedirectUrl()}\n";
        // header('Location: ' . $response->getRedirectUrl());
    }

    echo "\nFull response:\n";
    print_r($response->toArray());
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}
