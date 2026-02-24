<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\CardRegistrationResponse;

class CardRegistrationRequest
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(private readonly EpointClient $client)
    {
        $this->data['public_key'] = $client->getPublicKey();
        $this->data['language'] = Language::AZ->value;
        $this->data['refund'] = 0; // 0 for payment card, 1 for refund card
    }

    public function language(Language $language): self
    {
        $this->data['language'] = $language->value;

        return $this;
    }

    public function forRefund(bool $refund = true): self
    {
        $this->data['refund'] = $refund ? 1 : 0;

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
     * Send card registration request
     *
     * @throws EpointException
     */
    public function send(): CardRegistrationResponse
    {
        $response = $this->client->post('/card-registration', $this->data);

        return new CardRegistrationResponse($response);
    }
}