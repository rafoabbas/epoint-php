<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Responses\TokenPayResponse;

/**
 * Charges a token payment (created with tokenPayment()) with an Apple Pay or Google Pay token.
 * Epoint charges a record only while it is NEW, so a record is charged at most once.
 */
class TokenPayRequest
{
    public const APPLE_PAY_ENDPOINT = '/token/apple/pay';

    public const GOOGLE_PAY_ENDPOINT = '/token/google/pay';

    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(
        private readonly EpointClient $client,
        private readonly string $endpoint,
    ) {
        $this->data['public_key'] = $client->getPublicKey();
    }

    /**
     * Epoint payment ID returned by tokenPayment()->create()->getPaymentId()
     */
    public function paymentId(int $paymentId): self
    {
        $this->data['id'] = $paymentId;

        return $this;
    }

    /**
     * Base64-encoded wallet token, already in the format Epoint expects
     */
    public function token(string $base64Token): self
    {
        $this->data['token'] = $base64Token;

        return $this;
    }

    /**
     * Apple Pay: the `event.payment.token.paymentData` object from onpaymentauthorized
     *
     * @param  array<string, mixed>  $paymentData
     */
    public function applePaymentData(array $paymentData): self
    {
        return $this->token(base64_encode((string) json_encode($paymentData)));
    }

    /**
     * Google Pay: the `paymentMethodData.tokenizationData.token` string from loadPaymentData()
     */
    public function googleRawToken(string $rawToken): self
    {
        return $this->token(base64_encode($rawToken));
    }

    /**
     * Billing contact returned by the wallet (Apple Pay billingContact / Google Pay billingAddress + email)
     *
     * @param  array<string, mixed>  $billingContact
     */
    public function billingContact(array $billingContact): self
    {
        $this->data['billingContact'] = $billingContact;

        return $this;
    }

    /**
     * Charge the token payment.
     *
     * A declined or rejected charge is returned as an unsuccessful TokenPayResponse instead of an exception,
     * because Epoint answers it with HTTP 400 and a JSON body. Always confirm the result with checkStatus().
     *
     * @throws EpointException On validation, network or non-JSON errors
     */
    public function pay(): TokenPayResponse
    {
        $this->validate();

        try {
            $response = $this->client->post($this->endpoint, $this->data);
        } catch (EpointException $e) {
            $body = $e->getResponseData();

            if ($body === null || ! isset($body['status'])) {
                throw $e;
            }

            $response = $body;
        }

        return new TokenPayResponse($response);
    }

    /**
     * @throws EpointException
     */
    private function validate(): void
    {
        $required = ['id', 'token'];

        foreach ($required as $field) {
            if (! isset($this->data[$field])) {
                throw new EpointException("Missing required field: {$field}");
            }
        }
    }
}
