<?php

declare(strict_types=1);

namespace Epoint\Responses;

class PaymentResponse extends BaseResponse
{
    /**
     * Get transaction ID
     */
    public function getTransaction(): ?string
    {
        return $this->data['transaction'] ?? null;
    }

    /**
     * Get redirect URL for card entry
     */
    public function getRedirectUrl(): ?string
    {
        return $this->data['redirect_url'] ?? null;
    }

    /**
     * Get card ID (for card registration with payment)
     */
    public function getCardId(): ?string
    {
        return $this->data['card_id'] ?? null;
    }

    /**
     * Get order ID
     */
    public function getOrderId(): ?string
    {
        return $this->data['order_id'] ?? null;
    }

    /**
     * Get bank transaction ID
     */
    public function getBankTransaction(): ?string
    {
        return $this->data['bank_transaction'] ?? null;
    }

    /**
     * Get RRN (Retrieval Reference Number)
     */
    public function getRrn(): ?string
    {
        return $this->data['rrn'] ?? null;
    }

    /**
     * Get card mask (e.g., 123456******1234)
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
     * Get payment amount
     */
    public function getAmount(): ?float
    {
        return isset($this->data['amount']) ? (float) $this->data['amount'] : null;
    }
}