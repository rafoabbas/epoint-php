<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Epoint\EpointClient;

// Initialize Epoint client
$client = new EpointClient(
    publicKey: 'i000000001',      // Replace with your public key
    privateKey: 'your-private-key', // Replace with your private key
);

$cardId = 'your-saved-card-id';      // Saved card ID
$splitUser = 'split-user-public-key'; // Sub-merchant public key

try {
    // Split payment with saved card: total 100 AZN, sub-merchant gets 80 AZN
    $response = $client->splitCardPayment()
        ->cardId($cardId)
        ->amount(100.00)
        ->orderId('SPLIT-CARD-'.time())
        ->splitUser($splitUser)
        ->splitAmount(80.00)
        ->description('Marketplace purchase - saved card split')
        ->execute();

    echo "=== Split Card Payment (Saved Card) ===\n\n";
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
