<?php

declare(strict_types=1);

namespace Epoint\Tests\Responses;

use Epoint\Enums\PaymentStatus;
use Epoint\Responses\StatusResponse;
use PHPUnit\Framework\TestCase;

class StatusResponseTest extends TestCase
{
    public function test_get_payment_status_returns_enum(): void
    {
        $response = new StatusResponse(['status' => 'success']);

        $this->assertInstanceOf(PaymentStatus::class, $response->getPaymentStatus());
        $this->assertEquals(PaymentStatus::SUCCESS, $response->getPaymentStatus());
    }

    public function test_get_payment_status_returns_null_for_invalid_status(): void
    {
        $response = new StatusResponse(['status' => 'invalid-status']);

        $this->assertNull($response->getPaymentStatus());
    }

    public function test_get_payment_status_returns_null_when_missing(): void
    {
        $response = new StatusResponse([]);

        $this->assertNull($response->getPaymentStatus());
    }

    public function test_get_transaction_returns_transaction_id(): void
    {
        $response = new StatusResponse(['transaction' => 'te001234567']);

        $this->assertEquals('te001234567', $response->getTransaction());
    }

    public function test_get_operation_code_returns_code(): void
    {
        $response = new StatusResponse(['operation_code' => '100']);

        $this->assertEquals('100', $response->getOperationCode());
    }

    public function test_get_card_mask_returns_masked_card(): void
    {
        $response = new StatusResponse(['card_mask' => '****1234']);

        $this->assertEquals('****1234', $response->getCardMask());
    }

    public function test_get_amount_returns_float(): void
    {
        $response = new StatusResponse(['amount' => '50.00']);

        $this->assertIsFloat($response->getAmount());
        $this->assertEquals(50.00, $response->getAmount());
    }
}