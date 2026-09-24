<?php

namespace App\Enums;

enum ContentStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Published = 'published';
    case ComingSoon = 'coming_soon';
    case Archived = 'archived';
}
