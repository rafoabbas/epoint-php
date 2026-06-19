<?php

declare(strict_types=1);

namespace Epoint\Enums;

enum CardStatus: string
{
    case NEW = 'new';
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
    case SESSION_EXPIRED = 'session_expired';
}