<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\Enums\Currency;
use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\CardRegistrationWithPayResponse;

class CardRegistrationWithPayRequest
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(private readonly EpointClient $client)
    {
        $this->data['public_key'] = $client->getPublicKey();
        $this->data['currency'] = Currency::AZN->value;
        $this->data['language'] = Language::AZ->value;
        $this->data['refund'] = 0; // 0 for payment card, 1 for refund card
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

    public function currency(Currency $currency): self
    {
        $this->data['currency'] = $currency->value;

        return $this;
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
     * Send card registration with payment request
     *
     * @throws EpointException
     */
    public function send(): CardRegistrationWithPayResponse
    {
        $this->validate();

        $response = $this->client->post('/card-registration-with-pay', $this->data);

        return new CardRegistrationWithPayResponse($response);
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