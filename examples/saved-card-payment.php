<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Epoint\Enums\Language;
use Epoint\EpointClient;

$client = new EpointClient(
    publicKey: 'i000000001',
    privateKey: 'your-private-key',
);

try {
    // Step 1: Register card (first time only)
    $cardRegistration = $client->registerCard()
        ->description('Save card for future purchases')
        ->language(Language::EN)
        ->successUrl('https://yoursite.com/cards/success')
        ->errorUrl('https://yoursite.com/cards/error')
        ->send();

    if ($cardRegistration->isSuccess()) {
        echo "Card registration initiated\n";
        echo "Redirect user to: {$cardRegistration->getRedirectUrl()}\n";

        // After user enters card details, you'll receive card_id in callback
        // Store this card_id in your database associated with the user
        // $cardId = $cardRegistration->getCardId(); // From callback
    }

    // Step 2: Use saved card for payment (subsequent payments)
    $cardId = 'saved-card-id-from-database'; // Get from your database

    $payment = $client->savedCardPayment()
        ->cardId($cardId)
        ->amount(25.00)
        ->orderId('SUBSCRIPTION-'.time())
        ->description('Monthly subscription')
        ->execute();

    if ($payment->isSuccess()) {
        echo "Payment successful with saved card!\n";
        echo "Transaction: {$payment->getTransaction()}\n";
    } else {
        echo "Payment failed: {$payment->getMessage()}\n";
    }
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}