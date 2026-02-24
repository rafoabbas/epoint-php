<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Requests\PreauthRequest;
use Epoint\Responses\PaymentResponse;
use Epoint\Responses\PreauthCompleteResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class PreauthRequestTest extends TestCase
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

    public function test_can_build_preauth_request(): void
    {
        $request = $this->client->preauth()
            ->amount(100.00)
            ->orderId('ORDER-001')
            ->description('Hotel reservation');

        $this->assertInstanceOf(PreauthRequest::class, $request);
    }

    public function test_throws_exception_when_amount_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: amount');

        $this->client->preauth()
            ->orderId('ORDER-001')
            ->send();
    }

    public function test_throws_exception_when_order_id_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: order_id');

        $this->client->preauth()
            ->amount(100.00)
            ->send();
    }

    public function test_can_send_preauth_request(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/pre-auth-request', Mockery::on(function ($data) {
                return $data['amount'] === 100.00 && $data['order_id'] === 'ORDER-001';
            }))
            ->andReturn([
                'status' => 'success',
                'redirect_url' => 'https://epoint.az/payment',
                'transaction' => 'te001234567',
            ]);

        $request = new PreauthRequest($mockClient);
        $response = $request
            ->amount(100.00)
            ->orderId('ORDER-001')
            ->send();

        $this->assertInstanceOf(PaymentResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }

    public function test_can_complete_preauth(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/pre-auth-complete', Mockery::on(function ($data) {
                return $data['transaction'] === 'te001234567'
                    && $data['amount'] === 85.00;
            }))
            ->andReturn([
                'status' => 'success',
                'message' => 'Preauth completed',
            ]);

        $request = new PreauthRequest($mockClient);
        $response = $request->complete('te001234567', 85.00);

        $this->assertInstanceOf(PreauthCompleteResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }
}