<?php

declare(strict_types=1);

namespace Epoint\Enums;

enum PaymentMethod: string
{
    case CARD = 'card';
    case INSTALLMENT = 'installment';
    case WALLET = 'wallet';
    case GOOGLE_PAY = 'google_pay';
    case APPLE_PAY = 'apple_pay';
    case CLICK_TO_PAY = 'click_to_pay';
    case AMEX = 'amex';
    case PRE_AUTH = 'pre_auth';
    case CARD_SAVE = 'card_save';
    case PAYOUT = 'payout';
    case REVERSE = 'reverse';
}
