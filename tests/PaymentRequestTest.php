<?php

declare(strict_types=1);

namespace Epoint\Tests;

use Epoint\Enums\Currency;
use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use PHPUnit\Framework\TestCase;

class PaymentRequestTest extends TestCase
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

    public function test_payment_request_builder(): void
    {
        $request = $this->client->payment()
            ->amount(100.50)
            ->orderId('ORDER-123')
            ->description('Test payment')
            ->currency(Currency::AZN)
            ->language(Language::EN)
            ->installment(true)
            ->successUrl('https://example.com/success')
            ->errorUrl('https://example.com/error');

        $this->assertInstanceOf(\Epoint\Requests\PaymentRequest::class, $request);
    }

    public function test_payment_request_throws_exception_without_required_fields(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field');

        // Missing amount and order_id
        $this->client->payment()->send();
    }
}