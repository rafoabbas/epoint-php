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

$walletId = 'your-wallet-id'; // Replace with your wallet ID

echo "=== Epoint Wallet Test ===\n\n";

// -------------------------------------------------------
// Step 1: List available wallets
// -------------------------------------------------------
echo "--- Step 1: Listing available wallets ---\n\n";

try {
    $walletList = $client->wallet()->list();

    echo "Status  : {$walletList->getStatus()}\n";
    echo "Success : ".($walletList->isSuccess() ? 'YES' : 'NO')."\n\n";

    if ($walletList->isSuccess()) {
        $wallets = $walletList->getWallets();
        echo "Available wallets (".count($wallets)."):\n";
        foreach ($wallets as $index => $wallet) {
            echo "  [{$index}] ".json_encode($wallet, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n";
        }
    } else {
        echo "Error: {$walletList->getMessage()}\n";
    }

    echo "\nFull response:\n";
    print_r($walletList->toArray());
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Exception: {$e->getMessage()}\n";
}

echo "\n";

// -------------------------------------------------------
// Step 2: Create wallet payment
// -------------------------------------------------------
echo "--- Step 2: Creating wallet payment ---\n\n";

try {
    $response = $client->wallet()->payment(
        walletId: $walletId,
        amount: 0.01,
        orderId: 'WALLET-TEST-'.time(),
        description: 'Wallet payment test',
        language: Language::AZ,
    );

    echo "Status       : {$response->getStatus()}\n";
    echo "Success      : ".($response->isSuccess() ? 'YES' : 'NO')."\n";
    echo "Transaction  : {$response->getTransaction()}\n";
    echo "Redirect URL : {$response->getRedirectUrl()}\n";
    echo "Message      : {$response->getMessage()}\n";
    echo "Code         : {$response->getCode()}\n";

    echo "\nFull response:\n";
    print_r($response->toArray());
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Exception: {$e->getMessage()}\n";
}

echo "\n=== Test complete ===\n";
