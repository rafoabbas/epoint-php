<?php

declare(strict_types=1);

namespace Epoint\Tests;

use Epoint\Exceptions\EpointException;
use Epoint\Exceptions\SignatureVerificationException;
use PHPUnit\Framework\TestCase;

class EpointExceptionTest extends TestCase
{
    public function test_keeps_the_classic_constructor(): void
    {
        $previous = new \RuntimeException('inner');
        $exception = new EpointException('Failed', 5, $previous);

        $this->assertSame('Failed', $exception->getMessage());
        $this->assertSame(5, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
        $this->assertNull($exception->getHttpCode());
        $this->assertNull($exception->getResponseData());
    }

    public function test_exposes_http_code_and_response_data(): void
    {
        $exception = new EpointException('HTTP 400', httpCode: 400, responseData: ['status' => 'error', 'message' => 'Duplicate order_id value']);

        $this->assertSame(400, $exception->getHttpCode());
        $this->assertSame(['status' => 'error', 'message' => 'Duplicate order_id value'], $exception->getResponseData());
    }

    public function test_signature_exception_still_works_with_message_only(): void
    {
        $exception = new SignatureVerificationException('Invalid callback signature');

        $this->assertInstanceOf(EpointException::class, $exception);
        $this->assertNull($exception->getResponseData());
    }
}
