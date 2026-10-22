<?php

declare(strict_types=1);

namespace Epoint\Responses;

class TokenPayResponse extends BaseResponse
{
    /**
     * Your order ID of the charged payment
     */
    public function getOrderId(): ?string
    {
        $orderId = $this->data['order_id'] ?? null;

        return $orderId === null ? null : (string) $orderId;
    }

    /**
     * Epoint payment ID (present on some errors)
     */
    public function getPaymentId(): ?int
    {
        $id = $this->data['payment_id'] ?? null;

        return $id === null ? null : (int) $id;
    }

    /**
     * Redirect URL configured for the payment (success_redirect_url or Epoint's result page)
     */
    public function getRedirectUrl(): ?string
    {
        return $this->data['redirect_url'] ?? null;
    }

    /**
     * Raw processor result, for logging and troubleshooting
     *
     * @return array<string, mixed>|null
     */
    public function getResult(): ?array
    {
        $result = $this->data['result'] ?? null;

        return is_array($result) ? $result : null;
    }
}
