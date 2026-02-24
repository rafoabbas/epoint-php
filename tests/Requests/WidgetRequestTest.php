<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Requests\WidgetRequest;
use Epoint\Responses\WidgetResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class WidgetRequestTest extends TestCase
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

    public function test_can_build_widget_request(): void
    {
        $request = $this->client->widget()
            ->amount(75.00)
            ->orderId('ORDER-001')
            ->description('Digital wallet payment');

        $this->assertInstanceOf(WidgetRequest::class, $request);
    }

    public function test_throws_exception_when_amount_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: amount');

        $this->client->widget()
            ->orderId('ORDER-001')
            ->description('Payment')
            ->create();
    }

    public function test_throws_exception_when_order_id_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: order_id');

        $this->client->widget()
            ->amount(75.00)
            ->description('Payment')
            ->create();
    }

    public function test_throws_exception_when_description_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: description');

        $this->client->widget()
            ->amount(75.00)
            ->orderId('ORDER-001')
            ->create();
    }

    public function test_can_create_widget(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/token/widget', Mockery::on(function ($data) {
                return $data['amount'] === 75.00
                    && $data['order_id'] === 'ORDER-001'
                    && $data['description'] === 'Payment';
            }))
            ->andReturn([
                'status' => 'success',
                'widget_url' => 'https://epoint.az/widget/abc123',
            ]);

        $request = new WidgetRequest($mockClient);
        $response = $request
            ->amount(75.00)
            ->orderId('ORDER-001')
            ->description('Payment')
            ->create();

        $this->assertInstanceOf(WidgetResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }
}