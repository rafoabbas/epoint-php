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
    // Create Apple Pay / Google Pay widget
    $response = $client->widget()
        ->amount(75.00)
        ->orderId('WIDGET-'.time())
        ->description('Product purchase via digital wallet')
        ->create();

    echo "=== Apple Pay / Google Pay Widget ===\n\n";
    echo "Status     : {$response->getStatus()}\n";
    echo "Success    : ".($response->isSuccess() ? 'YES' : 'NO')."\n";
    echo "Widget URL : {$response->getWidgetUrl()}\n";
    echo "Message    : {$response->getMessage()}\n";

    if ($response->isSuccess()) {
        echo "\nEmbed widget URL in iframe or webview:\n";
        echo "<iframe src=\"{$response->getWidgetUrl()}\"></iframe>\n";
    }

    echo "\nFull response:\n";
    print_r($response->toArray());
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}
