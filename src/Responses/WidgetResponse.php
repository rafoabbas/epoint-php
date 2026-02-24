<?php

declare(strict_types=1);

namespace Epoint\Responses;

class WidgetResponse extends BaseResponse
{
    /**
     * Get widget URL for iframe/webview
     */
    public function getWidgetUrl(): ?string
    {
        return $this->data['widget_url'] ?? null;
    }
}