<?php

namespace App\Enums;

enum EntitlementStatus: string
{
    case Active = 'active';
    case Revoked = 'revoked';
    case Expired = 'expired';
    case Refunded = 'refunded';
    case Suspended = 'suspended';
}
