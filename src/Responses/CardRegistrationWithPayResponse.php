<?php

declare(strict_types=1);

namespace Epoint\Responses;

class CardRegistrationWithPayResponse
{
    private string $status;
    private ?string $redirectUrl = null;
    private ?string $orderId = null;
    private ?string $transactionId = null;
    private ?string $cardId = null;
    private ?string $message = null;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(array $data)
    {
        $this->status = (string) ($data['status'] ?? 'error');
        $this->redirectUrl = isset($data['redirect_url']) ? (string) $data['redirect_url'] : null;
        $this->orderId = isset($data['order_id']) ? (string) $data['order_id'] : null;
        $this->transactionId = isset($data['transaction']) ? (string) $data['transaction'] : null;
        $this->cardId = isset($data['card_id']) ? (string) $data['card_id'] : null;
        $this->message = isset($data['message']) ? (string) $data['message'] : null;
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function getCardId(): ?string
    {
        return $this->cardId;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}