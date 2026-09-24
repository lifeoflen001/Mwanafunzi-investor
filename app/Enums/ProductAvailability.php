<?php

namespace App\Enums;

enum ProductAvailability: string
{
    case Draft = 'draft';
    case Available = 'available';
    case ComingSoon = 'coming_soon';
    case Waitlist = 'waitlist';
    case Unavailable = 'unavailable';
    case Archived = 'archived';
}
