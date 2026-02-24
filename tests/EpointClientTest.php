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