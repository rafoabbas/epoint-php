<?php

declare(strict_types=1);

namespace Epoint\Responses;

use Epoint\Enums\CardStatus;

class CardStatusResponse extends BaseResponse
{
    /**
     * Card status API returns "active" for successful registrations,
     * not "success" like other endpoints.
     */
    public function isSuccess(): bool
    {
        return $this->getCardStatus() === CardStatus::ACTIVE;
    }

    /**
     * Get card status
     */
    public function getCardStatus(): ?CardStatus
    {
        if (! isset($this->data['status'])) {
            return null;
        }

        return CardStatus::tryFrom($this->data['status']);
    }

    /**
     * Get card ID
     */
    public function getCardId(): ?string
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Get cardholder name
     */
    public function getCardName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    /**
     * Get card mask
     */
    public function getCardMask(): ?string
    {
        return $this->data['mask'] ?? null;
    }

    /**
     * Get card expiry date
     */
    public function getExpiredDate(): ?string
    {
        return $this->data['expired_date'] ?? null;
    }

    /**
     * Get card description
     */
    public function getDescription(): ?string
    {
        return $this->data['description'] ?? null;
    }
}