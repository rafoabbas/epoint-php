<?php

declare(strict_types=1);

namespace Epoint\Tests\Responses;

use Epoint\Responses\PaymentResponse;
use PHPUnit\Framework\TestCase;

class PaymentResponseTest extends TestCase
{
    public function test_get_transaction_returns_transaction_id(): void
    {
        $response = new PaymentResponse(['transaction' => 'te001234567']);

        $this->assertEquals('te001234567', $response->getTransaction());
    }

    public function test_get_redirect_url_returns_url(): void
    {
        $response = new PaymentResponse(['redirect_url' => 'https://epoint.az/payment']);

        $this->assertEquals('https://epoint.az/payment', $response->getRedirectUrl());
    }

    public function test_get_card_id_returns_card_id(): void
    {
        $response = new PaymentResponse(['card_id' => 'card-123']);

        $this->assertEquals('card-123', $response->getCardId());
    }

    public function test_get_order_id_returns_order_id(): void
    {
        $response = new PaymentResponse(['order_id' => 'ORDER-001']);

        $this->assertEquals('ORDER-001', $response->getOrderId());
    }

    public function test_get_bank_transaction_returns_bank_transaction(): void
    {
        $response = new PaymentResponse(['bank_transaction' => 'BANK-123']);

        $this->assertEquals('BANK-123', $response->getBankTransaction());
    }

    public function test_get_rrn_returns_rrn(): void
    {
        $response = new PaymentResponse(['rrn' => 'RRN123456']);

        $this->assertEquals('RRN123456', $response->getRrn());
    }

    public function test_get_card_mask_returns_masked_card(): void
    {
        $response = new PaymentResponse(['card_mask' => '123456******1234']);

        $this->assertEquals('123456******1234', $response->getCardMask());
    }

    public function test_get_card_name_returns_cardholder_name(): void
    {
        $response = new PaymentResponse(['card_name' => 'JOHN DOE']);

        $this->assertEquals('JOHN DOE', $response->getCardName());
    }

    public function test_get_amount_returns_amount_as_float(): void
    {
        $response = new PaymentResponse(['amount' => 100.50]);

        $this->assertEquals(100.50, $response->getAmount());
    }

    public function test_get_amount_converts_string_to_float(): void
    {
        $response = new PaymentResponse(['amount' => '100.50']);

        $this->assertIsFloat($response->getAmount());
        $this->assertEquals(100.50, $response->getAmount());
    }

    public function test_getters_return_null_when_fields_missing(): void
    {
        $response = new PaymentResponse([]);

        $this->assertNull($response->getTransaction());
        $this->assertNull($response->getRedirectUrl());
        $this->assertNull($response->getCardId());
        $this->assertNull($response->getOrderId());
        $this->assertNull($response->getBankTransaction());
        $this->assertNull($response->getRrn());
        $this->assertNull($response->getCardMask());
        $this->assertNull($response->getCardName());
        $this->assertNull($response->getAmount());
    }
}