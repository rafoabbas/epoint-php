<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\Enums\Currency;
use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\ReverseResponse;

class ReverseRequest
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(private readonly EpointClient $client)
    {
        $this->data['public_key'] = $client->getPublicKey();
        $this->data['language'] = Language::AZ->value;
        $this->data['currency'] = Currency::AZN->value;
    }

    public function transaction(string $transaction): self
    {
        $this->data['transaction'] = $transaction;

        return $this;
    }

    public function amount(?float $amount = null): self
    {
        if ($amount !== null) {
            $this->data['amount'] = $amount;
        }

        return $this;
    }

    /**
     * Send reverse/cancel request
     *
     * @throws EpointException
     */
    public function send(): ReverseResponse
    {
        if (! isset($this->data['transaction'])) {
            throw new EpointException('Transaction ID is required');
        }

        $response = $this->client->post('/reverse', $this->data);

        return new ReverseResponse($response);
    }
}