<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\StatusResponse;

class StatusCheckRequest
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(private readonly EpointClient $client)
    {
        $this->data['public_key'] = $client->getPublicKey();
    }

    public function transaction(string $transaction): self
    {
        $this->data['transaction'] = $transaction;

        return $this;
    }

    /**
     * Check payment status
     *
     * @throws EpointException
     */
    public function get(): StatusResponse
    {
        if (! isset($this->data['transaction'])) {
            throw new EpointException('Transaction ID is required');
        }

        $response = $this->client->post('/get-status', $this->data);

        return new StatusResponse($response);
    }
}