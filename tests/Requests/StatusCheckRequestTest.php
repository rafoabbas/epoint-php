<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Requests\StatusCheckRequest;
use Epoint\Responses\StatusResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class StatusCheckRequestTest extends TestCase
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

    public function test_can_build_status_check_request(): void
    {
        $request = $this->client->checkStatus()
            ->transaction('te001234567');

        $this->assertInstanceOf(StatusCheckRequest::class, $request);
    }

    public function test_throws_exception_when_transaction_and_order_id_are_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Transaction ID or order ID is required');

        $this->client->checkStatus()->get();
    }

    public function test_can_check_payment_status_by_order_id(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/get-status', Mockery::on(function ($data) {
                return ($data['order_id'] ?? null) === 'ord_123'
                    && ! isset($data['transaction'])
                    && $data['public_key'] === 'i000000001';
            }))
            ->andReturn([
                'status' => 'success',
                'transaction' => 'te001234567',
                'order_id' => 'ord_123',
                'amount' => 25.5,
            ]);

        $request = new StatusCheckRequest($mockClient);
        $response = $request->orderId('ord_123')->get();

        $this->assertTrue($response->isSuccess());
        $this->assertSame('te001234567', $response->getTransaction());
        $this->assertSame('ord_123', $response->getOrderId());
        $this->assertSame(25.5, $response->getAmount());
    }

    public function test_sends_both_identifiers_when_both_are_set(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/get-status', Mockery::on(function ($data) {
                return $data['transaction'] === 'te001234567' && $data['order_id'] === 'ord_123';
            }))
            ->andReturn(['status' => 'success']);

        $request = new StatusCheckRequest($mockClient);
        $request->transaction('te001234567')->orderId('ord_123')->get();

        $this->addToAssertionCount(1);
    }

    public function test_can_check_payment_status(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/get-status', Mockery::on(function ($data) {
                return isset($data['transaction']) && $data['transaction'] === 'te001234567';
            }))
            ->andReturn([
                'status' => 'success',
                'payment_status' => 'success',
                'transaction' => 'te001234567',
            ]);

        $request = new StatusCheckRequest($mockClient);
        $response = $request->transaction('te001234567')->get();

        $this->assertInstanceOf(StatusResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }
}