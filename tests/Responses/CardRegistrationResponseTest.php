<?php

declare(strict_types=1);

namespace Epoint\Tests\Responses;

use Epoint\Responses\CardRegistrationResponse;
use PHPUnit\Framework\TestCase;

class CardRegistrationResponseTest extends TestCase
{
    public function test_get_redirect_url_returns_url(): void
    {
        $response = new CardRegistrationResponse(['redirect_url' => 'https://epoint.az/card-registration']);

        $this->assertEquals('https://epoint.az/card-registration', $response->getRedirectUrl());
    }

    public function test_get_card_id_returns_card_id(): void
    {
        $response = new CardRegistrationResponse(['card_id' => 'card-456']);

        $this->assertEquals('card-456', $response->getCardId());
    }

    public function test_get_bank_transaction_returns_bank_transaction(): void
    {
        $response = new CardRegistrationResponse(['bank_transaction' => 'BANK-789']);

        $this->assertEquals('BANK-789', $response->getBankTransaction());
    }

    public function test_get_card_mask_returns_masked_card(): void
    {
        $response = new CardRegistrationResponse(['card_mask' => '****5678']);

        $this->assertEquals('****5678', $response->getCardMask());
    }

    public function test_get_card_name_returns_cardholder_name(): void
    {
        $response = new CardRegistrationResponse(['card_name' => 'JANE DOE']);

        $this->assertEquals('JANE DOE', $response->getCardName());
    }

    public function test_getters_return_null_when_fields_missing(): void
    {
        $response = new CardRegistrationResponse([]);

        $this->assertNull($response->getRedirectUrl());
        $this->assertNull($response->getCardId());
        $this->assertNull($response->getBankTransaction());
        $this->assertNull($response->getCardMask());
        $this->assertNull($response->getCardName());
    }
}