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
     * Look the payment up by your own order ID instead of the Epoint transaction ID.
     * Useful when no transaction ID was returned, e.g. for widget (Apple Pay / Google Pay) payments.
     * When both are set, Epoint uses the transaction ID.
     */
    public function orderId(string $orderId): self
    {
        $this->data['order_id'] = $orderId;

        return $this;
    }

    /**
     * Check payment status
     *
     * @throws EpointException
     */
    public function get(): StatusResponse
    {
        if (! isset($this->data['transaction']) && ! isset($this->data['order_id'])) {
            throw new EpointException('Transaction ID or order ID is required');
        }

        $response = $this->client->post('/get-status', $this->data);

        return new StatusResponse($response);
    }
}