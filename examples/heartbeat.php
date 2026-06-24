<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Epoint\EpointClient;

// Initialize Epoint client
$client = new EpointClient(
    publicKey: 'i000000001',      // Replace with your public key
    privateKey: 'your-private-key', // Replace with your private key
);

try {
    $response = $client->heartbeat();

    echo "=== API Heartbeat ===\n\n";
    echo "Status : {$response['status']}\n";

    echo "\nFull response:\n";
    print_r($response);
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}
