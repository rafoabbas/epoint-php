<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\CardStatusResponse;

class CardStatusCheckRequest
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(private readonly EpointClient $client)
    {
        $this->data['public_key'] = $client->getPublicKey();
    }

    public function cardId(string $cardId): self
    {
        $this->data['card_id'] = $cardId;

        return $this;
    }

    /**
     * Check card status
     *
     * @throws EpointException
     */
    public function get(): CardStatusResponse
    {
        if (! isset($this->data['card_id'])) {
            throw new EpointException('Card ID is required');
        }

        $response = $this->client->post('/get-status-card', $this->data);

        return new CardStatusResponse($response);
    }
}