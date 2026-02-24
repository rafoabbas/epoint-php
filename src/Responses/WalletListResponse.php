<?php

declare(strict_types=1);

namespace Epoint\Responses;

class WalletListResponse extends BaseResponse
{
    /**
     * Get list of available wallets
     *
     * @return array<int, array<string, mixed>>
     */
    public function getWallets(): array
    {
        return $this->data['wallets'] ?? [];
    }
}