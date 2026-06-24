<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Epoint\EpointClient;

// Initialize Epoint client
$client = new EpointClient(
    publicKey: 'i000000001',      // Replace with your public key
    privateKey: 'your-private-key', // Replace with your private key
);

// -------------------------------------------------------
// 1. Create invoice
// -------------------------------------------------------
echo "=== Create Invoice ===\n\n";

try {
    $response = $client->invoice()->create([
        'amount' => 150.00,
        'description' => 'Invoice for consulting services',
    ]);

    echo "Status : {$response['status']}\n";
    print_r($response);

    $invoiceId = $response['id'] ?? null;
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
    $invoiceId = null;
}

echo "\n";

// -------------------------------------------------------
// 2. View invoice details
// -------------------------------------------------------
echo "=== View Invoice ===\n\n";

$invoiceId = $invoiceId ?? 1; // Replace with actual invoice ID

try {
    $response = $client->invoice()->view($invoiceId);

    echo "Status : {$response['status']}\n";
    print_r($response);
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// -------------------------------------------------------
// 3. List invoices
// -------------------------------------------------------
echo "=== List Invoices ===\n\n";

try {
    $response = $client->invoice()->list();

    echo "Status : {$response['status']}\n";
    print_r($response);
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// -------------------------------------------------------
// 4. Update invoice
// -------------------------------------------------------
echo "=== Update Invoice ===\n\n";

try {
    $response = $client->invoice()->update($invoiceId, [
        'amount' => 200.00,
        'description' => 'Updated invoice amount',
    ]);

    echo "Status : {$response['status']}\n";
    print_r($response);
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// -------------------------------------------------------
// 5. Send invoice via SMS
// -------------------------------------------------------
echo "=== Send Invoice via SMS ===\n\n";

try {
    $response = $client->invoice()->sendSms($invoiceId, '+994501234567');

    echo "Status : {$response['status']}\n";
    print_r($response);
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// -------------------------------------------------------
// 6. Send invoice via Email
// -------------------------------------------------------
echo "=== Send Invoice via Email ===\n\n";

try {
    $response = $client->invoice()->sendEmail($invoiceId, 'customer@example.com');

    echo "Status : {$response['status']}\n";
    print_r($response);
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}
