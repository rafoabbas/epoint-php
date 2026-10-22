<?php

declare(strict_types=1);

namespace Epoint\Responses;

class ApplePaySessionResponse extends BaseResponse
{
    /**
     * Opaque merchant session to pass to ApplePaySession::completeMerchantValidation()
     *
     * @return array<string, mixed>|null
     */
    public function getSession(): ?array
    {
        $session = $this->data['session'] ?? null;

        return is_array($session) ? $session : null;
    }
}
