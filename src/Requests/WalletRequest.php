<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\Enums\Currency;
use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\WalletListResponse;
use Epoint\Responses\WalletPaymentResponse;

class WalletRequest
{
    public function __construct(private readonly EpointClient $client)
    {
    }

    /**
     * Get list of available wallets
     *
     * @throws EpointException
     */
    public function list(): WalletListResponse
    {
        $response = $this->client->post('/wallet/status', [
            'public_key' => $this->client->getPublicKey(),
        ]);

        return new WalletListResponse($response);
    }

    /**
     * Create wallet payment
     *
     * @throws EpointException
     */
    public function payment(
        string $walletId,
        float $amount,
        string $orderId,
        ?string $description = null,
        Currency $currency = Currency::AZN,
        Language $language = Language::AZ
    ): WalletPaymentResponse {
        $payload = [
            'public_key' => $this->client->getPublicKey(),
            'wallet_id' => $walletId,
            'amount' => $amount,
            'order_id' => $orderId,
            'currency' => $currency->value,
            'language' => $language->value,
        ];

        if ($description !== null) {
            $payload['description'] = $description;
        }

        $response = $this->client->post('/wallet/payment', $payload);

        return new WalletPaymentResponse($response);
    }
}