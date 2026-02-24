<?php

declare(strict_types=1);

namespace Epoint;

use Epoint\Enums\Currency;
use Epoint\Enums\Language;
use Epoint\Exceptions\EpointException;
use Epoint\Exceptions\SignatureVerificationException;
use Epoint\Requests\CardRegistrationRequest;
use Epoint\Requests\InvoiceRequest;
use Epoint\Requests\PaymentRequest;
use Epoint\Requests\PreauthRequest;
use Epoint\Requests\RefundRequest;
use Epoint\Requests\ReverseRequest;
use Epoint\Requests\SavedCardPaymentRequest;
use Epoint\Requests\SplitPaymentRequest;
use Epoint\Requests\StatusCheckRequest;
use Epoint\Requests\WalletRequest;
use Epoint\Requests\WidgetRequest;
use Epoint\Traits\HasSignature;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class EpointClient
{
    use HasSignature;

    private const BASE_URL = 'https://epoint.az/api/1';

    private Client $httpClient;

    /**
     * @param  string  $publicKey  Merchant public key (e.g., i000000001)
     * @param  string  $privateKey  Merchant private key for signature generation
     * @param  bool  $testMode  Use test environment
     */
    public function __construct(
        private readonly string $publicKey,
        private readonly string $privateKey,
        private readonly bool $testMode = false
    ) {
        $this->httpClient = new Client([
            'base_uri' => self::BASE_URL,
            'timeout' => 30,
            'verify' => ! $testMode,
        ]);
    }

    /**
     * Create a new payment request
     */
    public function payment(): PaymentRequest
    {
        return new PaymentRequest($this);
    }

    /**
     * Check payment status
     */
    public function checkStatus(): StatusCheckRequest
    {
        return new StatusCheckRequest($this);
    }

    /**
     * Register a card without payment
     */
    public function registerCard(): CardRegistrationRequest
    {
        return new CardRegistrationRequest($this);
    }

    /**
     * Make payment with saved card
     */
    public function savedCardPayment(): SavedCardPaymentRequest
    {
        return new SavedCardPaymentRequest($this);
    }

    /**
     * Refund payment
     */
    public function refund(): RefundRequest
    {
        return new RefundRequest($this);
    }

    /**
     * Reverse/cancel transaction
     */
    public function reverse(): ReverseRequest
    {
        return new ReverseRequest($this);
    }

    /**
     * Create split payment request
     */
    public function splitPayment(): SplitPaymentRequest
    {
        return new SplitPaymentRequest($this);
    }

    /**
     * Create preauth request
     */
    public function preauth(): PreauthRequest
    {
        return new PreauthRequest($this);
    }

    /**
     * Create widget URL for Apple Pay / Google Pay
     */
    public function widget(): WidgetRequest
    {
        return new WidgetRequest($this);
    }

    /**
     * Wallet operations
     */
    public function wallet(): WalletRequest
    {
        return new WalletRequest($this);
    }

    /**
     * Invoice operations
     */
    public function invoice(): InvoiceRequest
    {
        return new InvoiceRequest($this);
    }

    /**
     * Heartbeat check
     *
     * @return array{status: string}
     *
     * @throws EpointException
     */
    public function heartbeat(): array
    {
        try {
            $response = $this->httpClient->get('/heartbeat');

            /** @var array{status: string} */
            return json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw new EpointException('Heartbeat request failed: '.$e->getMessage(), previous: $e);
        }
    }

    /**
     * Verify callback signature and return decoded data
     *
     * @param  string  $data  Data from callback
     * @param  string  $signature  Signature from callback
     * @return array<string, mixed>
     *
     * @throws SignatureVerificationException
     */
    public function verifyCallback(string $data, string $signature): array
    {
        if (! $this->verifySignature($data, $signature, $this->privateKey)) {
            throw new SignatureVerificationException('Invalid callback signature');
        }

        return $this->decodeData($data);
    }

    /**
     * Send POST request to Epoint API
     *
     * @param  string  $endpoint  API endpoint
     * @param  array<string, mixed>  $payload  Request payload
     * @return array<string, mixed>
     *
     * @throws EpointException
     */
    public function post(string $endpoint, array $payload): array
    {
        // Add public_key if not present
        if (! isset($payload['public_key'])) {
            $payload['public_key'] = $this->publicKey;
        }

        $data = $this->encodeData($payload);
        $signature = $this->generateSignature($data, $this->privateKey);

        try {
            $response = $this->httpClient->post($endpoint, [
                'form_params' => [
                    'data' => $data,
                    'signature' => $signature,
                ],
            ]);

            /** @var array<string, mixed> */
            return json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw new EpointException("API request to {$endpoint} failed: ".$e->getMessage(), previous: $e);
        }
    }

    /**
     * Send GET request to Epoint API
     *
     * @param  string  $endpoint  API endpoint
     * @param  array<string, mixed>  $query  Query parameters
     * @return array<string, mixed>
     *
     * @throws EpointException
     */
    public function get(string $endpoint, array $query = []): array
    {
        try {
            $response = $this->httpClient->get($endpoint, [
                'query' => $query,
            ]);

            /** @var array<string, mixed> */
            return json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw new EpointException("API request to {$endpoint} failed: ".$e->getMessage(), previous: $e);
        }
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }
}