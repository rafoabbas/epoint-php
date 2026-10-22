<?php

declare(strict_types=1);

namespace Epoint\Exceptions;

class EpointException extends \Exception
{
    /**
     * @param  array<string, mixed>|null  $responseData  Decoded JSON body of a failed API response, when available
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        private readonly ?int $httpCode = null,
        private readonly ?array $responseData = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * HTTP status code of the failed API response (null for network or validation errors)
     */
    public function getHttpCode(): ?int
    {
        return $this->httpCode;
    }

    /**
     * Decoded JSON body of the failed API response (null when the body was not JSON)
     *
     * @return array<string, mixed>|null
     */
    public function getResponseData(): ?array
    {
        return $this->responseData;
    }
}
