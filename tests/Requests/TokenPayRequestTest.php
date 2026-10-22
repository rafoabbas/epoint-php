<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;
use Epoint\Requests\TokenPayRequest;
use Epoint\Responses\TokenPayResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class TokenPayRequestTest extends TestCase
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

    public function test_client_builds_apple_and_google_requests(): void
    {
        $this->assertInstanceOf(TokenPayRequest::class, $this->client->applePay());
        $this->assertInstanceOf(TokenPayRequest::class, $this->client->googlePay());
    }

    public function test_throws_exception_when_payment_id_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: id');

        $this->client->applePay()->token('dG9rZW4=')->pay();
    }

    public function test_throws_exception_when_token_is_missing(): void
    {
        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('Missing required field: token');

        $this->client->applePay()->paymentId(1)->pay();
    }

    public function test_apple_payment_data_is_base64_json(): void
    {
        $paymentData = ['version' => 'EC_v1', 'data' => 'encrypted', 'signature' => 'sig'];

        $mockClient = $this->mockClientExpecting(
            TokenPayRequest::APPLE_PAY_ENDPOINT,
            fn (array $data) => $data['id'] === 987654
                && $data['token'] === base64_encode((string) json_encode($paymentData))
                && $data['billingContact'] === ['emailAddress' => 'a@b.az'],
            ['status' => 'success', 'code' => '000', 'order_id' => 'ORDER-001-1', 'redirect_url' => 'https://shop.test/ok'],
        );

        $response = (new TokenPayRequest($mockClient, TokenPayRequest::APPLE_PAY_ENDPOINT))
            ->paymentId(987654)
            ->applePaymentData($paymentData)
            ->billingContact(['emailAddress' => 'a@b.az'])
            ->pay();

        $this->assertInstanceOf(TokenPayResponse::class, $response);
        $this->assertTrue($response->isSuccess());
        $this->assertSame('000', $response->getCode());
        $this->assertSame('ORDER-001-1', $response->getOrderId());
        $this->assertSame('https://shop.test/ok', $response->getRedirectUrl());
    }

    public function test_google_raw_token_is_base64_encoded(): void
    {
        $rawToken = '{"signature":"MEQ","protocolVersion":"ECv2","signedMessage":"{}"}';

        $mockClient = $this->mockClientExpecting(
            TokenPayRequest::GOOGLE_PAY_ENDPOINT,
            fn (array $data) => $data['token'] === base64_encode($rawToken),
            ['status' => 'success', 'code' => '000', 'order_id' => 'ORDER-001-1'],
        );

        $response = (new TokenPayRequest($mockClient, TokenPayRequest::GOOGLE_PAY_ENDPOINT))
            ->paymentId(987654)
            ->googleRawToken($rawToken)
            ->pay();

        $this->assertTrue($response->isSuccess());
    }

    public function test_declined_charge_is_returned_as_unsuccessful_response(): void
    {
        $body = [
            'status' => 'error',
            'code' => 400,
            'message' => 'Some error happened after contacting cybersource',
            'order_id' => 'ORDER-001-1',
        ];

        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->andThrow(new EpointException('API request to /token/apple/pay failed with HTTP 400', httpCode: 400, responseData: $body));

        $response = (new TokenPayRequest($mockClient, TokenPayRequest::APPLE_PAY_ENDPOINT))
            ->paymentId(987654)
            ->token('dG9rZW4=')
            ->pay();

        $this->assertFalse($response->isSuccess());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Some error happened after contacting cybersource', $response->getMessage());
        $this->assertSame('ORDER-001-1', $response->getOrderId());
    }

    public function test_already_processed_payment_is_returned_as_unsuccessful_response(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->andThrow(new EpointException('HTTP 400', httpCode: 400, responseData: [
                'status' => 'error',
                'code' => 400,
                'message' => 'Payment is already processed',
                'payment_id' => 987654,
            ]));

        $response = (new TokenPayRequest($mockClient, TokenPayRequest::APPLE_PAY_ENDPOINT))
            ->paymentId(987654)
            ->token('dG9rZW4=')
            ->pay();

        $this->assertFalse($response->isSuccess());
        $this->assertSame(987654, $response->getPaymentId());
    }

    public function test_non_json_errors_are_still_thrown(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->andThrow(new EpointException('API request to /token/apple/pay failed with HTTP 502', httpCode: 502));

        $this->expectException(EpointException::class);
        $this->expectExceptionMessage('HTTP 502');

        (new TokenPayRequest($mockClient, TokenPayRequest::APPLE_PAY_ENDPOINT))
            ->paymentId(987654)
            ->token('dG9rZW4=')
            ->pay();
    }

    /**
     * @param  callable(array<string, mixed>): bool  $matcher
     * @param  array<string, mixed>  $response
     */
    private function mockClientExpecting(string $endpoint, callable $matcher, array $response): EpointClient
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with($endpoint, Mockery::on($matcher))
            ->andReturn($response);

        return $mockClient;
    }
}
