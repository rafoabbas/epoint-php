<?php

declare(strict_types=1);

namespace Epoint\Tests\Responses;

use Epoint\Enums\PaymentMethod;
use Epoint\Responses\PaymentMethodsResponse;
use PHPUnit\Framework\TestCase;

class PaymentMethodsResponseTest extends TestCase
{
    private function fullResponse(): PaymentMethodsResponse
    {
        return new PaymentMethodsResponse([
            'status' => 'success',
            'result' => [
                'business' => ['id' => 14806, 'name' => 'Nümunə Mağaza'],
                'methods' => ['card', 'installment', 'wallet', 'google_pay', 'apple_pay', 'card_save', 'reverse'],
                'wallets' => [
                    ['id' => 8, 'key' => 'portmanat', 'name' => 'Portmanat'],
                    ['id' => 9, 'key' => 'm10', 'name' => 'M10'],
                ],
                'installment' => [
                    'min_amount' => 30,
                    'banks' => [
                        ['id' => 2, 'name' => 'TamKart', 'bank' => 'ABB', 'image' => 'https://example.com/a.jpg', 'months' => [3, 6, 12]],
                        ['id' => 1, 'name' => 'BirKart', 'bank' => 'KapitalBank', 'image' => 'https://example.com/b.png', 'months' => [6, 12]],
                    ],
                ],
            ],
        ]);
    }

    public function test_returns_business_info(): void
    {
        $response = $this->fullResponse();

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(14806, $response->getBusinessId());
        $this->assertEquals('Nümunə Mağaza', $response->getBusinessName());
    }

    public function test_has_method_accepts_enum_and_string(): void
    {
        $response = $this->fullResponse();

        $this->assertTrue($response->hasMethod(PaymentMethod::CARD));
        $this->assertTrue($response->hasMethod('apple_pay'));
        $this->assertFalse($response->hasMethod(PaymentMethod::PAYOUT));
        $this->assertFalse($response->hasMethod('click_to_pay'));
    }

    public function test_returns_wallets(): void
    {
        $wallets = $this->fullResponse()->getWallets();

        $this->assertCount(2, $wallets);
        $this->assertEquals('portmanat', $wallets[0]['key']);
    }

    public function test_returns_installment_options(): void
    {
        $response = $this->fullResponse();

        $this->assertEquals(30.0, $response->getInstallmentMinAmount());
        $this->assertCount(2, $response->getInstallmentBanks());
        $this->assertEquals([3, 6, 12], $response->getInstallmentBanks()[0]['months']);
    }

    public function test_handles_disabled_installment(): void
    {
        $response = new PaymentMethodsResponse([
            'status' => 'success',
            'result' => [
                'business' => ['id' => 14627, 'name' => 'Nümunə Oyun'],
                'methods' => ['card', 'google_pay', 'apple_pay', 'card_save', 'reverse'],
                'wallets' => [],
                'installment' => null,
            ],
        ]);

        $this->assertNull($response->getInstallment());
        $this->assertNull($response->getInstallmentMinAmount());
        $this->assertEmpty($response->getInstallmentBanks());
        $this->assertEmpty($response->getWallets());
        $this->assertFalse($response->hasMethod(PaymentMethod::INSTALLMENT));
    }

    public function test_handles_error_response(): void
    {
        $response = new PaymentMethodsResponse(['status' => 'error', 'code' => 'business_not_found']);

        $this->assertTrue($response->isError());
        $this->assertEquals('business_not_found', $response->getCode());
        $this->assertNull($response->getBusinessId());
        $this->assertEmpty($response->getMethods());
    }
}
