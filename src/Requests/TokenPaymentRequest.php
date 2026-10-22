<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\Enums\Currency;
use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\TokenPaymentResponse;

/**
 * Creates the payment record that a native Apple Pay / Google Pay token is later charged against.
 * The record is bound to the merchant from the start, so checkStatus()->orderId() can find it.
 */
class TokenPaymentRequest
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

    /**
     * Your order reference (max 250 characters). Must be unique per payment when the merchant has unique order IDs enabled.
     */
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
     * @param  array<string, mixed>  $attributes
     */
    public function otherAttributes(array $attributes): self
    {
        $this->data['other_attr'] = $attributes;

        return $this;
    }

    /**
     * Create the token payment record
     *
     * @throws EpointException
     */
    public function create(): TokenPaymentResponse
    {
        $this->validate();

        $response = $this->client->post('/token/payment', $this->data);

        return new TokenPaymentResponse($response);
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
