<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\ApplePaySessionResponse;

/**
 * Requests an Apple Pay merchant session from Epoint (ApplePaySession::onvalidatemerchant).
 * The origin's domain must be registered for Apple Pay on Epoint's side.
 */
class ApplePaySessionRequest
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(private readonly EpointClient $client)
    {
        $this->data['public_key'] = $client->getPublicKey();
    }

    /**
     * Origin of the page that shows the Apple Pay button, e.g. https://shop.example.az
     */
    public function origin(string $origin): self
    {
        $this->data['origin'] = $origin;

        return $this;
    }

    /**
     * Create the merchant session
     *
     * @throws EpointException
     */
    public function create(): ApplePaySessionResponse
    {
        if (! isset($this->data['origin'])) {
            throw new EpointException('Missing required field: origin');
        }

        $response = $this->client->post('/token/apple/session', $this->data);

        return new ApplePaySessionResponse($response);
    }
}
