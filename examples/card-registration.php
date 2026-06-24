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
    // Register a payment card
    $response = $client->registerCard()
        ->description('Save card for future purchases')
        ->language(Language::EN)
        ->successUrl('https://yoursite.com/cards/success')
        ->errorUrl('https://yoursite.com/cards/error')
        ->send();

    echo "=== Card Registration ===\n\n";
    echo "Status        : {$response->getStatus()}\n";
    echo "Success       : ".($response->isSuccess() ? 'YES' : 'NO')."\n";
    echo "Redirect URL  : {$response->getRedirectUrl()}\n";
    echo "Card ID       : {$response->getCardId()}\n";
    echo "Message       : {$response->getMessage()}\n";

    if ($response->isSuccess()) {
        echo "\nRedirect user to: {$response->getRedirectUrl()}\n";
        // header('Location: ' . $response->getRedirectUrl());
    }

    echo "\nFull response:\n";
    print_r($response->toArray());

    // To register a refund card (for receiving refunds):
    // $response = $client->registerCard()
    //     ->forRefund()
    //     ->language(Language::AZ)
    //     ->send();

} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}
