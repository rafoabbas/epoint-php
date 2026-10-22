<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Requests\ApplePaySessionRequest;
use Epoint\Responses\ApplePaySessionResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class ApplePaySessionRequestTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_throws_exception_when_origin_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: origin');

        (new EpointClient('i000000001', 'test-private-key'))->applePaySession()->create();
    }

    public function test_can_create_apple_pay_session(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/token/apple/session', ['public_key' => 'i000000001', 'origin' => 'https://shop.example.az'])
            ->andReturn([
                'status' => 'success',
                'message' => 'Apple Pay session is successfully created',
                'session' => ['merchantSessionIdentifier' => 'abc', 'signature' => 'sig'],
            ]);

        $response = (new ApplePaySessionRequest($mockClient))
            ->origin('https://shop.example.az')
            ->create();

        $this->assertInstanceOf(ApplePaySessionResponse::class, $response);
        $this->assertTrue($response->isSuccess());
        $this->assertSame(['merchantSessionIdentifier' => 'abc', 'signature' => 'sig'], $response->getSession());
    }
}
