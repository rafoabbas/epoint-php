<?php

declare(strict_types=1);

namespace Epoint\Enums;

enum PaymentStatus: string
{
    case NEW = 'new';
    case SUCCESS = 'success';
    case RETURNED = 'returned';
    case ERROR = 'error';
    case SERVER_ERROR = 'server_error';
    case FAILED = 'failed';
}