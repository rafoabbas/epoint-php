<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Requests\InstallmentPaymentRequest;
use Epoint\Responses\PaymentResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class InstallmentPaymentRequestTest extends TestCase
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

    public function test_throws_exception_when_installment_card_id_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: installment_card_id');

        $this->client->installmentPayment()
            ->amount(120)
            ->orderId('ORDER-001')
            ->installmentMonth(6)
            ->send();
    }

    public function test_throws_exception_when_installment_month_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: installment_month');

        $this->client->installmentPayment()
            ->amount(120)
            ->orderId('ORDER-001')
            ->installmentCardId(2)
            ->send();
    }

    public function test_can_send_installment_payment(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/installment-request', Mockery::on(function ($data) {
                return $data['amount'] === 120.0
                    && $data['order_id'] === 'ORDER-001'
                    && $data['installment_card_id'] === 2
                    && $data['installment_month'] === 6
                    && $data['currency'] === 'AZN';
            }))
            ->andReturn([
                'status' => 'success',
                'transaction' => 'te001234567',
                'redirect_url' => 'https://epoint.az/pay/te001234567',
            ]);

        $response = (new InstallmentPaymentRequest($mockClient))
            ->amount(120)
            ->orderId('ORDER-001')
            ->description('Order #001')
            ->installmentCardId(2)
            ->installmentMonth(6)
            ->send();

        $this->assertInstanceOf(PaymentResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }
}
