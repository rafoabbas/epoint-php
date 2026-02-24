<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Requests\ReverseRequest;
use Epoint\Responses\ReverseResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class ReverseRequestTest extends TestCase
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

    public function test_can_build_reverse_request(): void
    {
        $request = $this->client->reverse()
            ->transaction('te001234567')
            ->amount(50.00);

        $this->assertInstanceOf(ReverseRequest::class, $request);
    }

    public function test_throws_exception_when_transaction_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Transaction ID is required');

        $this->client->reverse()->send();
    }

    public function test_can_reverse_full_transaction(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/reverse', Mockery::on(function ($data) {
                return $data['transaction'] === 'te001234567'
                    && !isset($data['amount']); // Full reversal
            }))
            ->andReturn([
                'status' => 'success',
                'message' => 'Transaction reversed',
            ]);

        $request = new ReverseRequest($mockClient);
        $response = $request->transaction('te001234567')->send();

        $this->assertInstanceOf(ReverseResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }

    public function test_can_reverse_partial_transaction(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/reverse', Mockery::on(function ($data) {
                return $data['transaction'] === 'te001234567'
                    && $data['amount'] === 25.00; // Partial reversal
            }))
            ->andReturn([
                'status' => 'success',
                'message' => 'Partial reversal successful',
            ]);

        $request = new ReverseRequest($mockClient);
        $response = $request
            ->transaction('te001234567')
            ->amount(25.00)
            ->send();

        $this->assertInstanceOf(ReverseResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }
}