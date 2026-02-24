<?php

declare(strict_types=1);

namespace Epoint\Responses;

class RefundResponse extends BaseResponse
{
    /**
     * Get transaction ID
     */
    public function getTransaction(): ?string
    {
        return $this->data['transaction'] ?? null;
    }

    /**
     * Get bank transaction
     */
    public function getBankTransaction(): ?string
    {
        return $this->data['bank_transaction'] ?? null;
    }

    /**
     * Get RRN
     */
    public function getRrn(): ?string
    {
        return $this->data['rrn'] ?? null;
    }

    /**
     * Get card mask
     */
    public function getCardMask(): ?string
    {
        return $this->data['card_mask'] ?? null;
    }

    /**
     * Get card name
     */
    public function getCardName(): ?string
    {
        return $this->data['card_name'] ?? null;
    }

    /**
     * Get refund amount
     */
    public function getAmount(): ?float
    {
        return isset($this->data['amount']) ? (float) $this->data['amount'] : null;
    }
}