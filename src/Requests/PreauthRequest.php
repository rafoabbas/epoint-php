<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\Enums\Currency;
use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\PaymentResponse;
use Epoint\Responses\PreauthCompleteResponse;

class PreauthRequest
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(private readonly EpointClient $client)
    {
        $this->data['public_key'] = $client->getPublicKey();
        $this->data['currency'] = Currency::AZN->value;
        $this->data['language'] = Language::AZ->value;
    }

    public function amount(float $amount): self
    {
        $this->data['amount'] = $amount;

        return $this;
    }

    public function orderId(string $orderId): self
    {
        $this->data['order_id'] = $orderId;

        return $this;
    }

    public function description(string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function successUrl(string $url): self
    {
        $this->data['success_redirect_url'] = $url;

        return $this;
    }

    public function errorUrl(string $url): self
    {
        $this->data['error_redirect_url'] = $url;

        return $this;
    }

    /**
     * Send preauth request
     *
     * @throws EpointException
     */
    public function send(): PaymentResponse
    {
        $this->validate();

        $response = $this->client->post('/pre-auth-request', $this->data);

        return new PaymentResponse($response);
    }

    /**
     * Complete preauth transaction
     *
     * @throws EpointException
     */
    public function complete(string $transaction, float $amount): PreauthCompleteResponse
    {
        $payload = [
            'public_key' => $this->client->getPublicKey(),
            'transaction' => $transaction,
            'amount' => $amount,
        ];

        $response = $this->client->post('/pre-auth-complete', $payload);

        return new PreauthCompleteResponse($response);
    }

    /**
     * @throws EpointException
     */
    private function validate(): void
    {
        $required = ['amount', 'order_id'];

        foreach ($required as $field) {
            if (! isset($this->data[$field])) {
                throw new EpointException("Missing required field: {$field}");
            }
        }
    }
}