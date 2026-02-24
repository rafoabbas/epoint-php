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

    /**
     * Get bank response
     */
    public function getBankResponse(): ?string
    {
        return $this->data['bank_response'] ?? null;
    }

    /**
     * Get operation code (001 - card registration, 100 - user payment)
     */
    public function getOperationCode(): ?string
    {
        return $this->data['operation_code'] ?? null;
    }

    /**
     * Get RRN (Retrieval Reference Number)
     */
    public function getRrn(): ?string
    {
        return $this->data['rrn'] ?? null;
    }
}