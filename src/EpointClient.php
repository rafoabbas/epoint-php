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

class EpointClient
{
    use HasSignature;

    private const BASE_URL = 'https://epoint.az/api/1';
    private const TIMEOUT = 30;

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
        if (!extension_loaded('curl')) {
            throw new EpointException('cURL extension is required');
        }
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
        /** @var array{status: string} */
        return $this->get('/heartbeat');
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

        $formData = http_build_query([
            'data' => $data,
            'signature' => $signature,
        ]);

        $url = self::BASE_URL . $endpoint;

        $ch = curl_init($url);
        if ($ch === false) {
            throw new EpointException("Failed to initialize cURL for {$endpoint}");
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $formData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => !$this->testMode,
            CURLOPT_SSL_VERIFYHOST => !$this->testMode ? 2 : 0,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: ' . strlen($formData),
            ],
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new EpointException("API request to {$endpoint} failed: {$error}");
        }

        if ($httpCode >= 400) {
            throw new EpointException("API request to {$endpoint} failed with HTTP {$httpCode}: {$response}");
        }

        try {
            /** @var array<string, mixed> */
            return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new EpointException("Failed to decode JSON response from {$endpoint}: " . $e->getMessage(), previous: $e);
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
        $url = self::BASE_URL . $endpoint;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init($url);
        if ($ch === false) {
            throw new EpointException("Failed to initialize cURL for {$endpoint}");
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => !$this->testMode,
            CURLOPT_SSL_VERIFYHOST => !$this->testMode ? 2 : 0,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new EpointException("API request to {$endpoint} failed: {$error}");
        }

        if ($httpCode >= 400) {
            throw new EpointException("API request to {$endpoint} failed with HTTP {$httpCode}: {$response}");
        }

        try {
            /** @var array<string, mixed> */
            return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new EpointException("Failed to decode JSON response from {$endpoint}: " . $e->getMessage(), previous: $e);
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