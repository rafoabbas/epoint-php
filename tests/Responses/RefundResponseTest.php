<?php

declare(strict_types=1);

namespace Epoint\Tests\Responses;

use Epoint\Responses\RefundResponse;
use PHPUnit\Framework\TestCase;

class RefundResponseTest extends TestCase
{
    public function test_get_transaction_returns_transaction_id(): void
    {
        $response = new RefundResponse(['transaction' => 'te001234567']);

        $this->assertEquals('te001234567', $response->getTransaction());
    }

    public function test_get_bank_transaction_returns_bank_transaction(): void
    {
        $response = new RefundResponse(['bank_transaction' => 'BANK-123']);

        $this->assertEquals('BANK-123', $response->getBankTransaction());
    }

    public function test_get_rrn_returns_rrn(): void
    {
        $response = new RefundResponse(['rrn' => 'RRN123456']);

        $this->assertEquals('RRN123456', $response->getRrn());
    }

    public function test_get_card_mask_returns_masked_card(): void
    {
        $response = new RefundResponse(['card_mask' => '****1234']);

        $this->assertEquals('****1234', $response->getCardMask());
    }

    public function test_get_card_name_returns_cardholder_name(): void
    {
        $response = new RefundResponse(['card_name' => 'JOHN DOE']);

        $this->assertEquals('JOHN DOE', $response->getCardName());
    }

    public function test_get_amount_returns_float(): void
    {
        $response = new RefundResponse(['amount' => '50.00']);

        $this->assertIsFloat($response->getAmount());
        $this->assertEquals(50.00, $response->getAmount());
    }

    public function test_getters_return_null_when_fields_missing(): void
    {
        $response = new RefundResponse([]);

        $this->assertNull($response->getTransaction());
        $this->assertNull($response->getBankTransaction());
        $this->assertNull($response->getRrn());
        $this->assertNull($response->getCardMask());
        $this->assertNull($response->getCardName());
        $this->assertNull($response->getAmount());
    }
}