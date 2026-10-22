<?php

declare(strict_types=1);

namespace Epoint\Responses;

use Epoint\Enums\PaymentMethod;

class PaymentMethodsResponse extends BaseResponse
{
    /**
     * Get business ID
     */
    public function getBusinessId(): ?int
    {
        return $this->data['result']['business']['id'] ?? null;
    }

    /**
     * Get business name
     */
    public function getBusinessName(): ?string
    {
        return $this->data['result']['business']['name'] ?? null;
    }

    /**
     * Get active payment method keys
     *
     * @return array<int, string>
     */
    public function getMethods(): array
    {
        return $this->data['result']['methods'] ?? [];
    }

    /**
     * Check if payment method is active
     */
    public function hasMethod(PaymentMethod|string $method): bool
    {
        $value = $method instanceof PaymentMethod ? $method->value : $method;

        return in_array($value, $this->getMethods(), true);
    }

    /**
     * Get active wallets
     *
     * @return array<int, array{id: int, key: string, name: string}>
     */
    public function getWallets(): array
    {
        return $this->data['result']['wallets'] ?? [];
    }

    /**
     * Get installment options (null when installment is not available)
     *
     * @return array{min_amount: float|int, banks: array<int, array{id: int, name: string, bank: string, image: string, months: array<int, int>}>}|null
     */
    public function getInstallment(): ?array
    {
        return $this->data['result']['installment'] ?? null;
    }

    /**
     * Get minimum amount for installment payment
     */
    public function getInstallmentMinAmount(): ?float
    {
        $minAmount = $this->getInstallment()['min_amount'] ?? null;

        return $minAmount !== null ? (float) $minAmount : null;
    }

    /**
     * Get installment banks with their active months
     *
     * @return array<int, array{id: int, name: string, bank: string, image: string, months: array<int, int>}>
     */
    public function getInstallmentBanks(): array
    {
        return $this->getInstallment()['banks'] ?? [];
    }
}
