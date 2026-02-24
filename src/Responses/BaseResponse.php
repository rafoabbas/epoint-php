<?php

declare(strict_types=1);

namespace Epoint\Responses;

abstract class BaseResponse
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(protected array $data)
    {
    }

    /**
     * Get raw response data
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Check if response is successful
     */
    public function isSuccess(): bool
    {
        return isset($this->data['status']) && $this->data['status'] === 'success';
    }

    /**
     * Check if response is error
     */
    public function isError(): bool
    {
        return ! $this->isSuccess();
    }

    /**
     * Get response status
     */
    public function getStatus(): ?string
    {
        return $this->data['status'] ?? null;
    }

    /**
     * Get error message
     */
    public function getMessage(): ?string
    {
        return $this->data['message'] ?? null;
    }

    /**
     * Get response code
     */
    public function getCode(): ?string
    {
        return $this->data['code'] ?? null;
    }
}