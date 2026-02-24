<?php

declare(strict_types=1);

namespace Epoint\Responses;

class CardRegistrationResponse extends BaseResponse
{
    /**
     * Get redirect URL for card entry
     */
    public function getRedirectUrl(): ?string
    {
        return $this->data['redirect_url'] ?? null;
    }

    /**
     * Get card ID
     */
    public function getCardId(): ?string
    {
        return $this->data['card_id'] ?? null;
    }

    /**
     * Get bank transaction
     */
    public function getBankTransaction(): ?string
    {
        return $this->data['bank_transaction'] ?? null;
    }

    /**
     * Get card mask
     */
    public function getCardMask(): ?string
    {
        return $this->data['card_mask'] ?? null;
    }

    /**
     * Get cardholder name
     */
    public function getCardName(): ?string
    {
        return $this->data['card_name'] ?? null;
    }
}