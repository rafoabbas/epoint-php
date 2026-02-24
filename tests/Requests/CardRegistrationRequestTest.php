<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Requests\CardRegistrationRequest;
use Epoint\Responses\CardRegistrationResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class CardRegistrationRequestTest extends TestCase
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

    public function test_can_build_card_registration_request(): void
    {
        $request = $this->client->registerCard()
            ->description('Save card')
            ->language(Language::EN)
            ->successUrl('https://example.com/success')
            ->errorUrl('https://example.com/error');

        $this->assertInstanceOf(CardRegistrationRequest::class, $request);
    }

    public function test_can_set_refund_card(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/card-registration', Mockery::on(function ($data) {
                return $data['refund'] === 1;
            }))
            ->andReturn([
                'status' => 'success',
                'redirect_url' => 'https://epoint.az/card-registration',
            ]);

        $request = new CardRegistrationRequest($mockClient);
        $response = $request->forRefund(true)->send();

        $this->assertInstanceOf(CardRegistrationResponse::class, $response);
    }

    public function test_can_register_card(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/card-registration', Mockery::type('array'))
            ->andReturn([
                'status' => 'success',
                'redirect_url' => 'https://epoint.az/card-registration',
                'message' => 'Card registration initiated',
            ]);

        $request = new CardRegistrationRequest($mockClient);
        $response = $request
            ->description('Save my card')
            ->language(Language::EN)
            ->send();

        $this->assertInstanceOf(CardRegistrationResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }
}