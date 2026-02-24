<?php

declare(strict_types=1);

namespace Epoint\Tests\Responses;

use Epoint\Responses\PreauthCompleteResponse;
use PHPUnit\Framework\TestCase;

class PreauthCompleteResponseTest extends TestCase
{
    public function test_get_transaction_returns_transaction_id(): void
    {
        $response = new PreauthCompleteResponse(['transaction' => 'te001234567']);

        $this->assertEquals('te001234567', $response->getTransaction());
    }

    public function test_get_amount_returns_float(): void
    {
        $response = new PreauthCompleteResponse(['amount' => '85.00']);

        $this->assertIsFloat($response->getAmount());
        $this->assertEquals(85.00, $response->getAmount());
    }

    public function test_getters_return_null_when_fields_missing(): void
    {
        $response = new PreauthCompleteResponse([]);

        $this->assertNull($response->getTransaction());
        $this->assertNull($response->getAmount());
    }
}