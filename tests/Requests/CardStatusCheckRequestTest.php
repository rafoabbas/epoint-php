<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Requests\CardStatusCheckRequest;
use Epoint\Responses\CardStatusResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class CardStatusCheckRequestTest extends TestCase
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

    public function test_can_build_card_status_check_request(): void
    {
        $request = $this->client->checkCardStatus()
            ->cardId('ce001234567');

        $this->assertInstanceOf(CardStatusCheckRequest::class, $request);
    }

    public function test_throws_exception_when_card_id_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Card ID is required');

        $this->client->checkCardStatus()->get();
    }

    public function test_can_check_card_status(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/get-status-card', Mockery::on(function ($data) {
                return isset($data['card_id']) && $data['card_id'] === 'ce001234567';
            }))
            ->andReturn([
                'id' => 'ce001234567',
                'name' => 'John Doe',
                'mask' => '****1234',
                'status' => 'active',
                'expired_date' => '12/25',
                'description' => null,
            ]);

        $request = new CardStatusCheckRequest($mockClient);
        $response = $request->cardId('ce001234567')->get();

        $this->assertInstanceOf(CardStatusResponse::class, $response);
        $this->assertEquals('ce001234567', $response->getCardId());
        $this->assertEquals('active', $response->getStatus());
    }
}
