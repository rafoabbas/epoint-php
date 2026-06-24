<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Epoint\EpointClient;

// Initialize Epoint client
$client = new EpointClient(
    publicKey: 'i000000001',      // Replace with your public key
    privateKey: 'your-private-key', // Replace with your private key
);

$cardId = 'your-card-id';   // Replace with actual card ID
$orderId = 'ORDER-123';     // Replace with actual order ID
$amount = 50.00;            // Refund amount

try {
    $response = $client->refund()
        ->cardId($cardId)
        ->orderId($orderId)
        ->amount($amount)
        ->description('Refund for returned product')
        ->send();

    echo "=== Refund Result ===\n\n";
    echo "Status        : {$response->getStatus()}\n";
    echo "Success       : ".($response->isSuccess() ? 'YES' : 'NO')."\n";
    echo "Transaction   : {$response->getTransaction()}\n";
    echo "Bank Txn      : {$response->getBankTransaction()}\n";
    echo "RRN           : {$response->getRrn()}\n";
    echo "Amount        : {$response->getAmount()}\n";
    echo "Card Mask     : {$response->getCardMask()}\n";
    echo "Card Name     : {$response->getCardName()}\n";
    echo "Bank Response : {$response->getBankResponse()}\n";
    echo "Message       : {$response->getMessage()}\n";

    echo "\nFull response:\n";
    print_r($response->toArray());
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}
