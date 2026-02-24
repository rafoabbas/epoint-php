<?php

declare(strict_types=1);

namespace Epoint\Tests\Responses;

use Epoint\Responses\WalletPaymentResponse;
use PHPUnit\Framework\TestCase;

class WalletPaymentResponseTest extends TestCase
{
    public function test_get_transaction_returns_transaction_id(): void
    {
        $response = new WalletPaymentResponse(['transaction' => 'te001234567']);

        $this->assertEquals('te001234567', $response->getTransaction());
    }

    public function test_get_redirect_url_returns_url(): void
    {
        $response = new WalletPaymentResponse(['redirect_url' => 'https://epoint.az/wallet/payment']);

        $this->assertEquals('https://epoint.az/wallet/payment', $response->getRedirectUrl());
    }

    public function test_getters_return_null_when_fields_missing(): void
    {
        $response = new WalletPaymentResponse([]);

        $this->assertNull($response->getTransaction());
        $this->assertNull($response->getRedirectUrl());
    }
}