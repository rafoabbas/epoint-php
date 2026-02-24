<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\Enums\Currency;
use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\PaymentResponse;

class SplitPaymentRequest
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(private readonly EpointClient $client)
    {
        $this->data['public_key'] = $client->getPublicKey();
        $this->data['currency'] = Currency::AZN->value;
        $this->data['language'] = Language::AZ->value;
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

    public function splitUser(string $splitUser): self
    {
        $this->data['split_user'] = $splitUser;

        return $this;
    }

    public function splitAmount(float $splitAmount): self
    {
        $this->data['split_amount'] = $splitAmount;

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

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function otherAttributes(array $attributes): self
    {
        $this->data['other_attr'] = $attributes;

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
     * Send split payment request
     *
     * @throws EpointException
     */
    public function send(): PaymentResponse
    {
        $this->validate();

        $response = $this->client->post('/split-request', $this->data);

        return new PaymentResponse($response);
    }

    /**
     * @throws EpointException
     */
    private function validate(): void
    {
        $required = ['amount', 'order_id', 'split_user', 'split_amount'];

        foreach ($required as $field) {
            if (! isset($this->data[$field])) {
                throw new EpointException("Missing required field: {$field}");
            }
        }
    }
}