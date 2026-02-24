<?php

declare(strict_types=1);

namespace Epoint\Tests;

use Epoint\EpointClient;
use PHPUnit\Framework\TestCase;

class SignatureTest extends TestCase
{
    private EpointClient $client;

    protected function setUp(): void
    {
        $this->client = new EpointClient(
            publicKey: 'i000000001',
            privateKey: 'test-private-key',
            testMode: true
        );
    }

    public function test_signature_generation(): void
    {
        $reflection = new \ReflectionClass($this->client);
        $method = $reflection->getMethod('generateSignature');
        $method->setAccessible(true);

        $data = base64_encode(json_encode(['test' => 'data']));
        $signature = $method->invoke($this->client, $data, 'private-key');

        $this->assertNotEmpty($signature);
        $this->assertIsString($signature);
    }

    public function test_data_encoding_and_decoding(): void
    {
        $reflection = new \ReflectionClass($this->client);

        $encodeMethod = $reflection->getMethod('encodeData');
        $encodeMethod->setAccessible(true);

        $decodeMethod = $reflection->getMethod('decodeData');
        $decodeMethod->setAccessible(true);

        $originalData = ['key' => 'value', 'number' => 123];

        $encoded = $encodeMethod->invoke($this->client, $originalData);
        $decoded = $decodeMethod->invoke($this->client, $encoded);

        $this->assertEquals($originalData, $decoded);
    }

    public function test_callback_verification_success(): void
    {
        $callbackData = ['status' => 'success', 'transaction' => 'te001'];

        $reflection = new \ReflectionClass($this->client);

        $encodeMethod = $reflection->getMethod('encodeData');
        $encodeMethod->setAccessible(true);
        $data = $encodeMethod->invoke($this->client, $callbackData);

        $signatureMethod = $reflection->getMethod('generateSignature');
        $signatureMethod->setAccessible(true);
        $signature = $signatureMethod->invoke($this->client, $data, 'test-private-key');

        $verified = $this->client->verifyCallback($data, $signature);

        $this->assertEquals($callbackData, $verified);
    }

    public function test_callback_verification_fails_with_invalid_signature(): void
    {
        $this->expectException(\Epoint\Exceptions\SignatureVerificationException::class);

        $reflection = new \ReflectionClass($this->client);
        $encodeMethod = $reflection->getMethod('encodeData');
        $encodeMethod->setAccessible(true);

        $data = $encodeMethod->invoke($this->client, ['test' => 'data']);
        $invalidSignature = 'invalid-signature';

        $this->client->verifyCallback($data, $invalidSignature);
    }
}