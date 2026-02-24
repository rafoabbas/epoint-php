<?php

declare(strict_types=1);

namespace Epoint\Responses;

class PreauthCompleteResponse extends BaseResponse
{
    /**
     * Get transaction ID
     */
    public function getTransaction(): ?string
    {
        return $this->data['transaction'] ?? null;
    }

    /**
     * Get amount
     */
    public function getAmount(): ?float
    {
        return isset($this->data['amount']) ? (float) $this->data['amount'] : null;
    }
}