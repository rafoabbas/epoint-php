<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Epoint\EpointClient;

// Initialize Epoint client
$client = new EpointClient(
    publicKey: 'i000000001',      // Replace with your public key
    privateKey: 'your-private-key', // Replace with your private key
);

$transactionId = 'your-transaction-id'; // Replace with actual transaction ID

try {
    $response = $client->checkStatus()
        ->transaction($transactionId)
        ->get();

    echo "=== Payment Status Check ===\n\n";
    echo "Status         : {$response->getStatus()}\n";
    echo "Success        : ".($response->isSuccess() ? 'YES' : 'NO')."\n";
    echo "Payment Status : {$response->getPaymentStatus()?->value}\n";
    echo "Transaction    : {$response->getTransaction()}\n";
    echo "Bank Txn       : {$response->getBankTransaction()}\n";
    echo "RRN            : {$response->getRrn()}\n";
    echo "Amount         : {$response->getAmount()}\n";
    echo "Card Mask      : {$response->getCardMask()}\n";
    echo "Card Name      : {$response->getCardName()}\n";
    echo "Card ID        : {$response->getCardId()}\n";
    echo "Operation Code : {$response->getOperationCode()}\n";
    echo "Bank Response  : {$response->getBankResponse()}\n";

    echo "\nFull response:\n";
    print_r($response->toArray());
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}
