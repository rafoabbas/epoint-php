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
    // Full reverse (cancel entire transaction)
    $response = $client->reverse()
        ->transaction($transactionId)
        ->send();

    echo "=== Full Reverse Result ===\n\n";
    echo "Status  : {$response->getStatus()}\n";
    echo "Success : ".($response->isSuccess() ? 'YES' : 'NO')."\n";
    echo "Message : {$response->getMessage()}\n";

    echo "\nFull response:\n";
    print_r($response->toArray());

    // Partial reverse (cancel part of the amount)
    // $response = $client->reverse()
    //     ->transaction($transactionId)
    //     ->amount(25.00)
    //     ->send();

} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}
