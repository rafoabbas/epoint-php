<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Epoint\EpointClient;
use Epoint\Exceptions\SignatureVerificationException;

$client = new EpointClient(
    publicKey: 'i000000001',
    privateKey: 'your-private-key',
);

// Get callback data from POST request
$data = $_POST['data'] ?? '';
$signature = $_POST['signature'] ?? '';

if (empty($data) || empty($signature)) {
    http_response_code(400);
    exit('Invalid request');
}

try {
    // Verify signature and decode callback data
    $callbackData = $client->verifyCallback($data, $signature);

    // Process payment result
    $orderId = $callbackData['order_id'];
    $status = $callbackData['status'];
    $transaction = $callbackData['transaction'] ?? null;
    $amount = $callbackData['amount'] ?? null;

    if ($status === 'success') {
        // Payment successful
        // Update your database, send confirmation email, etc.
        echo "Payment successful for order: {$orderId}\n";
        echo "Transaction: {$transaction}\n";
        echo "Amount: {$amount}\n";

        // Example: Update order status in database
        // $db->query("UPDATE orders SET status = 'paid', transaction_id = ? WHERE order_id = ?",
        //            [$transaction, $orderId]);

        http_response_code(200);
    } else {
        // Payment failed
        echo "Payment failed for order: {$orderId}\n";
        echo "Error: {$callbackData['message']}\n";

        http_response_code(200);
    }
} catch (SignatureVerificationException $e) {
    // Invalid signature - possible fraud attempt
    error_log('Invalid callback signature: '.$e->getMessage());
    http_response_code(400);
    exit('Invalid signature');
} catch (\Exception $e) {
    error_log('Callback processing error: '.$e->getMessage());
    http_response_code(500);
}