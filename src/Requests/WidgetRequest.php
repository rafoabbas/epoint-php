<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\WidgetResponse;

class WidgetRequest
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(private readonly EpointClient $client)
    {
        $this->data['public_key'] = $client->getPublicKey();
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

    /**
     * Create widget URL for Apple Pay / Google Pay
     *
     * @throws EpointException
     */
    public function create(): WidgetResponse
    {
        $this->validate();

        $response = $this->client->post('/token/widget', $this->data);

        return new WidgetResponse($response);
    }

    /**
     * @throws EpointException
     */
    private function validate(): void
    {
        $required = ['amount', 'order_id', 'description'];

        foreach ($required as $field) {
            if (! isset($this->data[$field])) {
                throw new EpointException("Missing required field: {$field}");
            }
        }
    }
}