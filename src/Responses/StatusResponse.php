<?php

declare(strict_types=1);

namespace Epoint\Responses;

use Epoint\Enums\PaymentStatus;

class StatusResponse extends BaseResponse
{
    /**
     * Get payment status
     */
    public function getPaymentStatus(): ?PaymentStatus
    {
        if (! isset($this->data['status'])) {
            return null;
        }

        return PaymentStatus::tryFrom($this->data['status']);
    }

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
     * Get operation code (001-card registration, 100-payment)
     */
    public function getOperationCode(): ?string
    {
        return $this->data['operation_code'] ?? null;
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
     * Get card ID
     */
    public function getCardId(): ?string
    {
        return $this->data['card_id'] ?? null;
    }

    /**
     * Get amount
     */
    public function getAmount(): ?float
    {
        return isset($this->data['amount']) ? (float) $this->data['amount'] : null;
    }

    /**
     * Get bank response
     */
    public function getBankResponse(): ?string
    {
        return $this->data['bank_response'] ?? null;
    }

    /**
     * Get additional payment attributes
     *
     * @return array<string, mixed>|null
     */
    public function getOtherAttributes(): ?array
    {
        return $this->data['other_attr'] ?? null;
    }
}