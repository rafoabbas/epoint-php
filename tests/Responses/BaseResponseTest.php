<?php

declare(strict_types=1);

namespace Epoint\Tests\Responses;

use Epoint\Responses\PaymentResponse;
use PHPUnit\Framework\TestCase;

class BaseResponseTest extends TestCase
{
    public function test_to_array_returns_raw_data(): void
    {
        $data = [
            'status' => 'success',
            'transaction' => 'te001234567',
            'amount' => 100.00,
        ];

        $response = new PaymentResponse($data);

        $this->assertEquals($data, $response->toArray());
    }

    public function test_is_success_returns_true_for_success_status(): void
    {
        $response = new PaymentResponse(['status' => 'success']);

        $this->assertTrue($response->isSuccess());
    }

    public function test_is_success_returns_false_for_error_status(): void
    {
        $response = new PaymentResponse(['status' => 'error']);

        $this->assertFalse($response->isSuccess());
    }

    public function test_is_success_returns_false_when_status_missing(): void
    {
        $response = new PaymentResponse([]);

        $this->assertFalse($response->isSuccess());
    }

    public function test_is_error_returns_true_for_error_status(): void
    {
        $response = new PaymentResponse(['status' => 'error']);

        $this->assertTrue($response->isError());
    }

    public function test_is_error_returns_false_for_success_status(): void
    {
        $response = new PaymentResponse(['status' => 'success']);

        $this->assertFalse($response->isError());
    }

    public function test_get_status_returns_status(): void
    {
        $response = new PaymentResponse(['status' => 'success']);

        $this->assertEquals('success', $response->getStatus());
    }

    public function test_get_status_returns_null_when_missing(): void
    {
        $response = new PaymentResponse([]);

        $this->assertNull($response->getStatus());
    }

    public function test_get_message_returns_message(): void
    {
        $response = new PaymentResponse(['message' => 'Payment successful']);

        $this->assertEquals('Payment successful', $response->getMessage());
    }

    public function test_get_message_returns_null_when_missing(): void
    {
        $response = new PaymentResponse([]);

        $this->assertNull($response->getMessage());
    }

    public function test_get_code_returns_code(): void
    {
        $response = new PaymentResponse(['code' => 'SUCCESS']);

        $this->assertEquals('SUCCESS', $response->getCode());
    }

    public function test_get_trace_id_returns_trace_id(): void
    {
        $response = new PaymentResponse(['trace_id' => 'abc-123-def']);

        $this->assertEquals('abc-123-def', $response->getTraceId());
    }

    public function test_get_trace_id_returns_null_when_missing(): void
    {
        $response = new PaymentResponse([]);

        $this->assertNull($response->getTraceId());
    }
}