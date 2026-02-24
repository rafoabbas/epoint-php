<?php

declare(strict_types=1);

namespace Epoint\Tests\Responses;

use Epoint\Responses\WalletListResponse;
use PHPUnit\Framework\TestCase;

class WalletListResponseTest extends TestCase
{
    public function test_get_wallets_returns_array_of_wallets(): void
    {
        $wallets = [
            ['id' => 'wallet1', 'name' => 'Wallet 1'],
            ['id' => 'wallet2', 'name' => 'Wallet 2'],
        ];

        $response = new WalletListResponse(['wallets' => $wallets]);

        $this->assertEquals($wallets, $response->getWallets());
    }

    public function test_get_wallets_returns_empty_array_when_missing(): void
    {
        $response = new WalletListResponse([]);

        $this->assertIsArray($response->getWallets());
        $this->assertEmpty($response->getWallets());
    }
}