<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Requests\RefundRequest;
use Epoint\Responses\RefundResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class RefundRequestTest extends TestCase
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

    public function test_can_build_refund_request(): void
    {
        $request = $this->client->refund()
            ->cardId('card-123')
            ->orderId('ORDER-001')
            ->amount(50.00)
            ->description('Product return');

        $this->assertInstanceOf(RefundRequest::class, $request);
    }

    public function test_throws_exception_when_card_id_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: card_id');

        $this->client->refund()
            ->orderId('ORDER-001')
            ->amount(50.00)
            ->send();
    }

    public function test_throws_exception_when_order_id_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: order_id');

        $this->client->refund()
            ->cardId('card-123')
            ->amount(50.00)
            ->send();
    }

    public function test_throws_exception_when_amount_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: amount');

        $this->client->refund()
            ->cardId('card-123')
            ->orderId('ORDER-001')
            ->send();
    }

    public function test_can_send_refund_request(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/refund-request', Mockery::on(function ($data) {
                return $data['card_id'] === 'card-123'
                    && $data['order_id'] === 'ORDER-001'
                    && $data['amount'] === 50.00;
            }))
            ->andReturn([
                'status' => 'success',
                'message' => 'Refund successful',
            ]);

        $request = new RefundRequest($mockClient);
        $response = $request
            ->cardId('card-123')
            ->orderId('ORDER-001')
            ->amount(50.00)
            ->send();

        $this->assertInstanceOf(RefundResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }
}