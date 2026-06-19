<?php

declare(strict_types=1);

namespace Epoint\Tests\Responses;

use Epoint\Enums\CardStatus;
use Epoint\Responses\CardStatusResponse;
use PHPUnit\Framework\TestCase;

class CardStatusResponseTest extends TestCase
{
    public function test_get_card_status_returns_enum(): void
    {
        $response = new CardStatusResponse(['status' => 'active']);

        $this->assertInstanceOf(CardStatus::class, $response->getCardStatus());
        $this->assertEquals(CardStatus::ACTIVE, $response->getCardStatus());
    }

    public function test_get_card_status_returns_null_for_invalid_status(): void
    {
        $response = new CardStatusResponse(['status' => 'unknown']);

        $this->assertNull($response->getCardStatus());
    }

    public function test_get_card_status_returns_null_when_missing(): void
    {
        $response = new CardStatusResponse([]);

        $this->assertNull($response->getCardStatus());
    }

    public function test_get_card_id_returns_id(): void
    {
        $response = new CardStatusResponse(['id' => 'ce001234567']);

        $this->assertEquals('ce001234567', $response->getCardId());
    }

    public function test_get_card_name_returns_name(): void
    {
        $response = new CardStatusResponse(['name' => 'John Doe']);

        $this->assertEquals('John Doe', $response->getCardName());
    }

    public function test_get_card_mask_returns_mask(): void
    {
        $response = new CardStatusResponse(['mask' => '****1234']);

        $this->assertEquals('****1234', $response->getCardMask());
    }

    public function test_get_expired_date_returns_date(): void
    {
        $response = new CardStatusResponse(['expired_date' => '12/25']);

        $this->assertEquals('12/25', $response->getExpiredDate());
    }

    public function test_get_description_returns_description(): void
    {
        $response = new CardStatusResponse(['description' => 'Test card']);

        $this->assertEquals('Test card', $response->getDescription());
    }

    public function test_session_expired_status(): void
    {
        $response = new CardStatusResponse(['status' => 'session_expired']);

        $this->assertEquals(CardStatus::SESSION_EXPIRED, $response->getCardStatus());
    }

    public function test_all_card_statuses(): void
    {
        $statuses = [
            'new' => CardStatus::NEW,
            'active' => CardStatus::ACTIVE,
            'pending' => CardStatus::PENDING,
            'rejected' => CardStatus::REJECTED,
            'expired' => CardStatus::EXPIRED,
            'session_expired' => CardStatus::SESSION_EXPIRED,
        ];

        foreach ($statuses as $value => $expected) {
            $response = new CardStatusResponse(['status' => $value]);
            $this->assertEquals($expected, $response->getCardStatus(), "Failed for status: {$value}");
        }
    }

    public function test_is_success_returns_true_for_active_status(): void
    {
        $response = new CardStatusResponse(['status' => 'active']);

        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isError());
    }

    public function test_is_success_returns_false_for_non_active_statuses(): void
    {
        $nonActiveStatuses = ['new', 'pending', 'rejected', 'expired', 'session_expired'];

        foreach ($nonActiveStatuses as $status) {
            $response = new CardStatusResponse(['status' => $status]);
            $this->assertFalse($response->isSuccess(), "isSuccess() should be false for status: {$status}");
            $this->assertTrue($response->isError(), "isError() should be true for status: {$status}");
        }
    }

    public function test_is_success_returns_false_when_status_missing(): void
    {
        $response = new CardStatusResponse([]);

        $this->assertFalse($response->isSuccess());
    }

    public function test_full_response_data(): void
    {
        $data = [
            'id' => 'ce001234567',
            'name' => 'John Doe',
            'mask' => '****1234',
            'status' => 'active',
            'expired_date' => '12/25',
            'description' => 'Primary card',
        ];

        $response = new CardStatusResponse($data);

        $this->assertEquals('ce001234567', $response->getCardId());
        $this->assertEquals('John Doe', $response->getCardName());
        $this->assertEquals('****1234', $response->getCardMask());
        $this->assertEquals(CardStatus::ACTIVE, $response->getCardStatus());
        $this->assertEquals('12/25', $response->getExpiredDate());
        $this->assertEquals('Primary card', $response->getDescription());
        $this->assertEquals($data, $response->toArray());
    }
}
