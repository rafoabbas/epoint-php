<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Epoint\Enums\Language;
use Epoint\Enums\PaymentMethod;
use Epoint\EpointClient;

// Initialize Epoint client
$client = new EpointClient(
    publicKey: 'i000000001',      // Replace with your public key
    privateKey: 'your-private-key', // Replace with your private key
);

try {
    $response = $client->paymentMethods()
        ->language(Language::AZ)
        ->get();

    echo "=== Active Payment Methods ===\n\n";
    echo "Success  : ".($response->isSuccess() ? 'YES' : 'NO')."\n";
    echo "Business : {$response->getBusinessName()} (#{$response->getBusinessId()})\n";
    echo "Methods  : ".implode(', ', $response->getMethods())."\n";

    echo "\nWallets:\n";
    foreach ($response->getWallets() as $wallet) {
        echo "  - {$wallet['name']} ({$wallet['key']})\n";
    }

    if ($response->hasMethod(PaymentMethod::INSTALLMENT)) {
        echo "\nInstallment (min amount: {$response->getInstallmentMinAmount()}):\n";
        foreach ($response->getInstallmentBanks() as $bank) {
            echo "  - [{$bank['id']}] {$bank['name']} / {$bank['bank']}: ".implode(', ', $bank['months'])." months\n";
        }

        // Pay with first bank and its first active month
        $bank = $response->getInstallmentBanks()[0];

        $payment = $client->installmentPayment()
            ->amount(120.00)
            ->orderId('ORDER-'.time())
            ->description('Installment payment')
            ->installmentCardId($bank['id'])
            ->installmentMonth($bank['months'][0])
            ->send();

        echo "\nRedirect URL: {$payment->getRedirectUrl()}\n";
    }
} catch (\Epoint\Exceptions\EpointException $e) {
    echo "Error: {$e->getMessage()}\n";
}
