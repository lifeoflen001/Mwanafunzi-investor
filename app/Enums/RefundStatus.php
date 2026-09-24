<?php

namespace App\Enums;

enum RefundStatus: string
{
    case Requested = 'requested';
    case Processing = 'processing';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';
    case Failed = 'failed';
}
