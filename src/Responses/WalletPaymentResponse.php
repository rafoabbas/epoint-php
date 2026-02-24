<?php

declare(strict_types=1);

namespace Epoint\Responses;

class WalletPaymentResponse extends BaseResponse
{
    /**
     * Get transaction ID
     */
    public function getTransaction(): ?string
    {
        return $this->data['transaction'] ?? null;
    }

    /**
     * Get redirect URL
     */
    public function getRedirectUrl(): ?string
    {
        return $this->data['redirect_url'] ?? null;
    }
}