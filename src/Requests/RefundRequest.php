<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\Enums\Currency;
use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\RefundResponse;

class RefundRequest
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(private readonly EpointClient $client)
    {
        $this->data['public_key'] = $client->getPublicKey();
        $this->data['language'] = Language::AZ->value;
        $this->data['currency'] = Currency::AZN->value;
    }

    public function cardId(string $cardId): self
    {
        $this->data['card_id'] = $cardId;

        return $this;
    }

    public function orderId(string $orderId): self
    {
        $this->data['order_id'] = $orderId;

        return $this;
    }

    public function amount(float $amount): self
    {
        $this->data['amount'] = $amount;

        return $this;
    }

    public function description(string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    /**
     * Send refund request
     *
     * @throws EpointException
     */
    public function send(): RefundResponse
    {
        $this->validate();

        $response = $this->client->post('/refund-request', $this->data);

        return new RefundResponse($response);
    }

    /**
     * @throws EpointException
     */
    private function validate(): void
    {
        $required = ['card_id', 'order_id', 'amount'];

        foreach ($required as $field) {
            if (! isset($this->data[$field])) {
                throw new EpointException("Missing required field: {$field}");
            }
        }
    }
}