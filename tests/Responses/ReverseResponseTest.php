<?php

declare(strict_types=1);

namespace Epoint\Tests\Responses;

use Epoint\Responses\ReverseResponse;
use PHPUnit\Framework\TestCase;

class ReverseResponseTest extends TestCase
{
    public function test_reverse_response_extends_base_response(): void
    {
        $response = new ReverseResponse(['status' => 'success']);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals('success', $response->getStatus());
    }

    public function test_to_array_returns_raw_data(): void
    {
        $data = [
            'status' => 'success',
            'message' => 'Transaction reversed',
        ];

        $response = new ReverseResponse($data);

        $this->assertEquals($data, $response->toArray());
    }
}