<?php

declare(strict_types=1);

namespace Epoint\Responses;

class TokenPaymentResponse extends BaseResponse
{
    /**
     * Created payment record fields (id, transaction, total, merchant_order_id, ...)
     *
     * @return array<string, mixed>|null
     */
    public function getPayment(): ?array
    {
        $payment = $this->data['payment'] ?? null;

        return is_array($payment) ? $payment : null;
    }

    /**
     * Epoint payment ID to pass to applePay()/googlePay()->paymentId()
     */
    public function getPaymentId(): ?int
    {
        $id = $this->getPayment()['id'] ?? null;

        return $id === null ? null : (int) $id;
    }

    /**
     * Your order ID as stored by Epoint
     */
    public function getOrderId(): ?string
    {
        $orderId = $this->getPayment()['merchant_order_id'] ?? null;

        return $orderId === null ? null : (string) $orderId;
    }

    /**
     * Amount stored by Epoint (floored to 2 decimals)
     */
    public function getAmount(): ?float
    {
        $total = $this->getPayment()['total'] ?? null;

        return $total === null ? null : (float) $total;
    }
}
