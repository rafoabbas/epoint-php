<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\PaymentMethodsResponse;

class PaymentMethodsRequest
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(private readonly EpointClient $client)
    {
        $this->data['public_key'] = $client->getPublicKey();
    }

    /**
     * Language for installment card names
     */
    public function language(Language $language): self
    {
        $this->data['language'] = $language->value;

        return $this;
    }

    /**
     * Get active payment methods
     *
     * @throws EpointException
     */
    public function get(): PaymentMethodsResponse
    {
        $response = $this->client->post('/payment-methods', $this->data);

        return new PaymentMethodsResponse($response);
    }
}
