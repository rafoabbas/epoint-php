<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Requests\SavedCardPaymentRequest;
use Epoint\Responses\PaymentResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class SavedCardPaymentRequestTest extends TestCase
{
    private EpointClient $client;

    protected function setUp(): void
    {
        $this->client = new EpointClient(
            publicKey: 'i000000001',
            privateKey: 'test-private-key'
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_can_build_saved_card_payment_request(): void
    {
        $request = $this->client->savedCardPayment()
            ->cardId('card-123')
            ->amount(50.00)
            ->orderId('ORDER-001')
            ->description('Subscription renewal')
            ->language(Language::EN);

        $this->assertInstanceOf(SavedCardPaymentRequest::class, $request);
    }

    public function test_throws_exception_when_card_id_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: card_id');

        $this->client->savedCardPayment()
            ->amount(50.00)
            ->orderId('ORDER-001')
            ->execute();
    }

    public function test_throws_exception_when_amount_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: amount');

        $this->client->savedCardPayment()
            ->cardId('card-123')
            ->orderId('ORDER-001')
            ->execute();
    }

    public function test_throws_exception_when_order_id_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: order_id');

        $this->client->savedCardPayment()
            ->cardId('card-123')
            ->amount(50.00)
            ->execute();
    }

    public function test_can_execute_saved_card_payment(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/execute-pay', Mockery::on(function ($data) {
                return $data['card_id'] === 'card-123'
                    && $data['amount'] === 50.00
                    && $data['order_id'] === 'ORDER-001';
            }))
            ->andReturn([
                'status' => 'success',
                'transaction' => 'te001234567',
                'amount' => 50.00,
            ]);

        $request = new SavedCardPaymentRequest($mockClient);
        $response = $request
            ->cardId('card-123')
            ->amount(50.00)
            ->orderId('ORDER-001')
            ->execute();

        $this->assertInstanceOf(PaymentResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }
}