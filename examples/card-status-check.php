<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Epoint\EpointClient;

// Initialize Epoint client
$client = new EpointClient(
    publicKey: 'i000000001',      // Replace with your public key
    privateKey: 'your-private-key', // Replace with your private key
);

$cardId = 'your-card-id'; // Replace with actual card ID

try {
    $response = $client->checkCardStatus()
        ->cardId($cardId)
        ->get();

    echo "=== Card Status Check ===\n\n";
    echo "Success      : ".($response->isSuccess() ? 'YES' : 'NO')."\n";
    echo "Card Status  : {$response->getCardStatus()?->value}\n";
    echo "Card ID      : {$response->getCardId()}\n";
    echo "Card Name    : {$response->getCardName()}\n";
    echo "Card Mask    : {$response->getCardMask()}\n";
    echo "Expired Date : {$response->getExpiredDate()}\n";
    echo "Description  : {$response->getDescription()}\n";

    echo "\nFull response:\n";
    print_r($response->toArray());
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}
