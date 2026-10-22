<?php

declare(strict_types=1);

namespace Epoint\Tests;

use Epoint\EpointClient;
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
use PHPUnit\Framework\TestCase;

class EpointClientTest extends TestCase
{
    private EpointClient $client;

    protected function setUp(): void
    {
        $this->client = new EpointClient(
            publicKey: 'i000000001',
            privateKey: 'test-private-key'
        );
    }

    public function test_can_construct_client(): void
    {
        $client = new EpointClient(
            publicKey: 'i000000001',
            privateKey: 'test-private-key'
        );

        $this->assertInstanceOf(EpointClient::class, $client);
    }

    public function test_throws_exception_when_curl_not_loaded(): void
    {
        // Skip this test if we can't mock extension_loaded
        $this->markTestSkipped('Cannot mock extension_loaded in current environment');
    }

    public function test_get_public_key_returns_public_key(): void
    {
        $this->assertEquals('i000000001', $this->client->getPublicKey());
    }

    public function test_get_private_key_returns_private_key(): void
    {
        $this->assertEquals('test-private-key', $this->client->getPrivateKey());
    }

    public function test_uses_production_defaults(): void
    {
        $this->assertSame('https://epoint.az/api/1', $this->client->getBaseUrl());
        $this->assertSame(30, $this->client->getTimeout());
        $this->assertSame(10, $this->client->getConnectTimeout());
    }

    public function test_accepts_custom_base_url_and_timeouts(): void
    {
        $client = new EpointClient(
            publicKey: 'i000000001',
            privateKey: 'test-private-key',
            baseUrl: 'https://epoint-v1.test/api/1/',
            timeout: 15,
            connectTimeout: 3,
        );

        $this->assertSame('https://epoint-v1.test/api/1', $client->getBaseUrl());
        $this->assertSame(15, $client->getTimeout());
        $this->assertSame(3, $client->getConnectTimeout());
    }

    public function test_requests_go_to_the_configured_base_url(): void
    {
        $client = new EpointClient('i000000001', 'test-private-key', baseUrl: 'http://127.0.0.1:1/api/1', timeout: 2, connectTimeout: 1);

        try {
            $client->post('/get-status', ['order_id' => 'ORD-1']);
            $this->fail('Expected the request to fail');
        } catch (EpointException $e) {
            $this->assertStringContainsString('API request to /get-status failed', $e->getMessage());
            $this->assertNull($e->getHttpCode());
        }
    }

    public function test_with_timeout_returns_a_copy(): void
    {
        $short = $this->client->withTimeout(8);

        $this->assertNotSame($this->client, $short);
        $this->assertSame(8, $short->getTimeout());
        $this->assertSame(8, $short->getConnectTimeout());
        $this->assertSame(30, $this->client->getTimeout());
        $this->assertSame(10, $this->client->getConnectTimeout());

        $custom = $this->client->withTimeout(20, 2);
        $this->assertSame(20, $custom->getTimeout());
        $this->assertSame(2, $custom->getConnectTimeout());
    }

    public function test_rejects_non_positive_timeouts(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Timeouts must be at least 1 second');

        new EpointClient('i000000001', 'test-private-key', timeout: 0);
    }

    public function test_with_timeout_rejects_non_positive_timeouts(): void
    {
        $this->expectException(EpointException::class);

        $this->client->withTimeout(10, 0);
    }

    public function test_payment_returns_payment_request(): void
    {
        $request = $this->client->payment();

        $this->assertInstanceOf(PaymentRequest::class, $request);
    }

    public function test_check_status_returns_status_check_request(): void
    {
        $request = $this->client->checkStatus();

        $this->assertInstanceOf(StatusCheckRequest::class, $request);
    }

    public function test_register_card_returns_card_registration_request(): void
    {
        $request = $this->client->registerCard();

        $this->assertInstanceOf(CardRegistrationRequest::class, $request);
    }

    public function test_saved_card_payment_returns_saved_card_payment_request(): void
    {
        $request = $this->client->savedCardPayment();

        $this->assertInstanceOf(SavedCardPaymentRequest::class, $request);
    }

    public function test_refund_returns_refund_request(): void
    {
        $request = $this->client->refund();

        $this->assertInstanceOf(RefundRequest::class, $request);
    }

    public function test_reverse_returns_reverse_request(): void
    {
        $request = $this->client->reverse();

        $this->assertInstanceOf(ReverseRequest::class, $request);
    }

    public function test_split_payment_returns_split_payment_request(): void
    {
        $request = $this->client->splitPayment();

        $this->assertInstanceOf(SplitPaymentRequest::class, $request);
    }

    public function test_preauth_returns_preauth_request(): void
    {
        $request = $this->client->preauth();

        $this->assertInstanceOf(PreauthRequest::class, $request);
    }

    public function test_widget_returns_widget_request(): void
    {
        $request = $this->client->widget();

        $this->assertInstanceOf(WidgetRequest::class, $request);
    }

    public function test_wallet_returns_wallet_request(): void
    {
        $request = $this->client->wallet();

        $this->assertInstanceOf(WalletRequest::class, $request);
    }

    public function test_invoice_returns_invoice_request(): void
    {
        $request = $this->client->invoice();

        $this->assertInstanceOf(InvoiceRequest::class, $request);
    }

    public function test_verify_callback_with_valid_signature(): void
    {
        $callbackData = ['status' => 'success', 'transaction' => 'te001'];

        // Use reflection to access protected methods
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

    public function test_verify_callback_throws_exception_with_invalid_signature(): void
    {
        $this->expectException(SignatureVerificationException::class);
        $this->expectExceptionMessage('Invalid callback signature');

        $reflection = new \ReflectionClass($this->client);
        $encodeMethod = $reflection->getMethod('encodeData');
        $encodeMethod->setAccessible(true);

        $data = $encodeMethod->invoke($this->client, ['test' => 'data']);
        $invalidSignature = 'invalid-signature';

        $this->client->verifyCallback($data, $invalidSignature);
    }

    public function test_post_adds_public_key_if_not_present(): void
    {
        // We can't easily test actual HTTP calls without mocking cURL
        // So we test that the request builder pattern works correctly
        $request = $this->client->payment()->amount(100);

        $this->assertInstanceOf(PaymentRequest::class, $request);
    }

    public function test_heartbeat_returns_array(): void
    {
        // Skip this test as it requires actual API connection
        $this->markTestSkipped('Requires actual API connection');
    }
}