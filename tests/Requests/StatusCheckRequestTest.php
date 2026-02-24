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

    public function test_throws_exception_when_transaction_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Transaction ID is required');

        $this->client->checkStatus()->get();
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