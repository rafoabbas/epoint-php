<?php

declare(strict_types=1);

namespace Epoint\Tests\Responses;

use Epoint\Responses\WidgetResponse;
use PHPUnit\Framework\TestCase;

class WidgetResponseTest extends TestCase
{
    public function test_get_widget_url_returns_url(): void
    {
        $response = new WidgetResponse(['widget_url' => 'https://epoint.az/widget/abc123']);

        $this->assertEquals('https://epoint.az/widget/abc123', $response->getWidgetUrl());
    }

    public function test_get_widget_url_returns_null_when_missing(): void
    {
        $response = new WidgetResponse([]);

        $this->assertNull($response->getWidgetUrl());
    }
}