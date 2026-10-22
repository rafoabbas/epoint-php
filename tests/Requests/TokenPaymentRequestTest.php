<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Requests\TokenPaymentRequest;
use Epoint\Responses\TokenPaymentResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class TokenPaymentRequestTest extends TestCase
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

    public function test_can_build_token_payment_request(): void
    {
        $request = $this->client->tokenPayment()
            ->amount(25.50)
            ->orderId('ORDER-001-1')
            ->description('Order #ORDER-001');

        $this->assertInstanceOf(TokenPaymentRequest::class, $request);
    }

    public function test_throws_exception_when_amount_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: amount');

        $this->client->tokenPayment()->orderId('ORDER-001-1')->create();
    }

    public function test_throws_exception_when_order_id_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: order_id');

        $this->client->tokenPayment()->amount(25.50)->create();
    }

    public function test_can_create_token_payment(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/token/payment', Mockery::on(function ($data) {
                return $data['public_key'] === 'i000000001'
                    && $data['amount'] === 25.50
                    && $data['order_id'] === 'ORDER-001-1'
                    && $data['currency'] === 'AZN'
                    && $data['language'] === 'en'
                    && $data['description'] === 'Order #ORDER-001'
                    && $data['success_redirect_url'] === 'https://shop.test/ok';
            }))
            ->andReturn([
                'status' => 'success',
                'payment' => [
                    'id' => 987654,
                    'transaction' => null,
                    'total' => 25.5,
                    'merchant_order_id' => 'ORDER-001-1',
                ],
            ]);

        $response = (new TokenPaymentRequest($mockClient))
            ->amount(25.50)
            ->orderId('ORDER-001-1')
            ->description('Order #ORDER-001')
            ->language(Language::EN)
            ->successUrl('https://shop.test/ok')
            ->create();

        $this->assertInstanceOf(TokenPaymentResponse::class, $response);
        $this->assertTrue($response->isSuccess());
        $this->assertSame(987654, $response->getPaymentId());
        $this->assertSame('ORDER-001-1', $response->getOrderId());
        $this->assertSame(25.5, $response->getAmount());
    }
}
