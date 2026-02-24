<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\Enums\Currency;
use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Requests\WalletRequest;
use Epoint\Responses\WalletListResponse;
use Epoint\Responses\WalletPaymentResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class WalletRequestTest extends TestCase
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

    public function test_can_list_wallets(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/wallet/status', Mockery::on(function ($data) {
                return $data['public_key'] === 'i000000001';
            }))
            ->andReturn([
                'status' => 'success',
                'wallets' => [
                    ['id' => 'wallet1', 'name' => 'Wallet 1'],
                    ['id' => 'wallet2', 'name' => 'Wallet 2'],
                ],
            ]);

        $request = new WalletRequest($mockClient);
        $response = $request->list();

        $this->assertInstanceOf(WalletListResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }

    public function test_can_create_wallet_payment(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/wallet/payment', Mockery::on(function ($data) {
                return $data['wallet_id'] === 'wallet-123'
                    && $data['amount'] === 50.00
                    && $data['order_id'] === 'ORDER-001'
                    && $data['description'] === 'Wallet payment';
            }))
            ->andReturn([
                'status' => 'success',
                'redirect_url' => 'https://epoint.az/wallet/payment',
            ]);

        $request = new WalletRequest($mockClient);
        $response = $request->payment(
            walletId: 'wallet-123',
            amount: 50.00,
            orderId: 'ORDER-001',
            description: 'Wallet payment',
            currency: Currency::AZN,
            language: Language::EN
        );

        $this->assertInstanceOf(WalletPaymentResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }

    public function test_wallet_payment_without_description(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/wallet/payment', Mockery::on(function ($data) {
                return !isset($data['description']);
            }))
            ->andReturn([
                'status' => 'success',
                'redirect_url' => 'https://epoint.az/wallet/payment',
            ]);

        $request = new WalletRequest($mockClient);
        $response = $request->payment(
            walletId: 'wallet-123',
            amount: 50.00,
            orderId: 'ORDER-001'
        );

        $this->assertInstanceOf(WalletPaymentResponse::class, $response);
    }
}