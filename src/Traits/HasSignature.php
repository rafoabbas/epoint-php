<?php

declare(strict_types=1);

namespace Epoint\Traits;

trait HasSignature
{
    /**
     * Generate signature for API request
     *
     * @param  string  $data  Base64 encoded data
     * @param  string  $privateKey  Private key from Epoint
     * @return string Base64 encoded signature
     */
    protected function generateSignature(string $data, string $privateKey): string
    {
        $signatureString = $privateKey.$data.$privateKey;

        return base64_encode(sha1($signatureString, true));
    }

    /**
     * Encode data to base64
     *
     * @param  array<string, mixed>  $data
     * @return string Base64 encoded JSON string
     */
    protected function encodeData(array $data): string
    {
        return base64_encode(json_encode($data, JSON_THROW_ON_ERROR));
    }

    /**
     * Decode data from base64
     *
     * @param  string  $data  Base64 encoded string
     * @return array<string, mixed>
     */
    protected function decodeData(string $data): array
    {
        $decoded = base64_decode($data, true);

        if ($decoded === false) {
            throw new \InvalidArgumentException('Failed to decode base64 data');
        }

        /** @var array<string, mixed> */
        return json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Verify signature from Epoint callback
     *
     * @param  string  $data  Base64 encoded data from callback
     * @param  string  $signature  Signature from callback
     * @param  string  $privateKey  Your private key
     */
    protected function verifySignature(string $data, string $signature, string $privateKey): bool
    {
        $expectedSignature = $this->generateSignature($data, $privateKey);

        return hash_equals($expectedSignature, $signature);
    }
}