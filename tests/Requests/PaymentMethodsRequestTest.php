<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Requests\PaymentMethodsRequest;
use Epoint\Responses\PaymentMethodsResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class PaymentMethodsRequestTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_can_build_payment_methods_request(): void
    {
        $client = new EpointClient(
            publicKey: 'i000000001',
            privateKey: 'test-private-key'
        );

        $request = $client->paymentMethods()->language(Language::EN);

        $this->assertInstanceOf(PaymentMethodsRequest::class, $request);
    }

    public function test_can_get_payment_methods(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/payment-methods', Mockery::on(function ($data) {
                return $data['public_key'] === 'i000000001'
                    && $data['language'] === 'az';
            }))
            ->andReturn([
                'status' => 'success',
                'result' => [
                    'business' => ['id' => 14627, 'name' => 'Test'],
                    'methods' => ['card', 'reverse'],
                    'wallets' => [],
                    'installment' => null,
                ],
            ]);

        $request = new PaymentMethodsRequest($mockClient);
        $response = $request->language(Language::AZ)->get();

        $this->assertInstanceOf(PaymentMethodsResponse::class, $response);
        $this->assertTrue($response->isSuccess());
        $this->assertEquals(['card', 'reverse'], $response->getMethods());
    }

    public function test_language_is_optional(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/payment-methods', Mockery::on(function ($data) {
                return ! isset($data['language']);
            }))
            ->andReturn(['status' => 'success', 'result' => []]);

        $response = (new PaymentMethodsRequest($mockClient))->get();

        $this->assertTrue($response->isSuccess());
    }
}
