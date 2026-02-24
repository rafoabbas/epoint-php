<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Requests\SplitPaymentRequest;
use Epoint\Responses\PaymentResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class SplitPaymentRequestTest extends TestCase
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

    public function test_can_build_split_payment_request(): void
    {
        $request = $this->client->splitPayment()
            ->amount(100.00)
            ->orderId('ORDER-001')
            ->splitUser('i000000002')
            ->splitAmount(30.00)
            ->description('Marketplace order');

        $this->assertInstanceOf(SplitPaymentRequest::class, $request);
    }

    public function test_throws_exception_when_amount_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: amount');

        $this->client->splitPayment()
            ->orderId('ORDER-001')
            ->splitUser('i000000002')
            ->splitAmount(30.00)
            ->send();
    }

    public function test_throws_exception_when_split_user_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: split_user');

        $this->client->splitPayment()
            ->amount(100.00)
            ->orderId('ORDER-001')
            ->splitAmount(30.00)
            ->send();
    }

    public function test_throws_exception_when_split_amount_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: split_amount');

        $this->client->splitPayment()
            ->amount(100.00)
            ->orderId('ORDER-001')
            ->splitUser('i000000002')
            ->send();
    }

    public function test_can_send_split_payment_request(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/split-request', Mockery::on(function ($data) {
                return $data['amount'] === 100.00
                    && $data['split_user'] === 'i000000002'
                    && $data['split_amount'] === 30.00;
            }))
            ->andReturn([
                'status' => 'success',
                'redirect_url' => 'https://epoint.az/payment',
            ]);

        $request = new SplitPaymentRequest($mockClient);
        $response = $request
            ->amount(100.00)
            ->orderId('ORDER-001')
            ->splitUser('i000000002')
            ->splitAmount(30.00)
            ->send();

        $this->assertInstanceOf(PaymentResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }
}