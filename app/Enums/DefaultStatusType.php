<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Enums;

enum DefaultStatusType: string
{
    case Processing = 'processing';
    case Approved = 'approved';
    case Pending = 'pending';
    case Rejected = 'rejected';
}
