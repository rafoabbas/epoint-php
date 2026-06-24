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

// -------------------------------------------------------
// Step 1: Create preauth (hold funds on card)
// -------------------------------------------------------
echo "=== Step 1: Create Preauth (Hold Funds) ===\n\n";

try {
    $response = $client->preauth()
        ->amount(200.00)
        ->orderId('PREAUTH-'.time())
        ->description('Hotel room reservation hold')
        ->language(Language::AZ)
        ->successUrl('https://yoursite.com/preauth/success')
        ->errorUrl('https://yoursite.com/preauth/error')
        ->send();

    echo "Status       : {$response->getStatus()}\n";
    echo "Success      : ".($response->isSuccess() ? 'YES' : 'NO')."\n";
    echo "Transaction  : {$response->getTransaction()}\n";
    echo "Redirect URL : {$response->getRedirectUrl()}\n";
    echo "Message      : {$response->getMessage()}\n";

    if ($response->isSuccess()) {
        echo "\nRedirect user to: {$response->getRedirectUrl()}\n";
        // Save transaction ID for completing later
        // $transactionId = $response->getTransaction();
    }

    echo "\nFull response:\n";
    print_r($response->toArray());
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// -------------------------------------------------------
// Step 2: Complete preauth (capture held funds)
// -------------------------------------------------------
echo "=== Step 2: Complete Preauth (Capture Funds) ===\n\n";

$transactionId = 'your-preauth-transaction-id'; // From step 1 callback
$captureAmount = 150.00; // Can be less than or equal to preauth amount

try {
    $response = $client->preauth()
        ->complete($transactionId, $captureAmount);

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
