<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Enums;

enum FlashSaleType: string
{
    case Percentage = 'percentage';
    case FixedRate = 'fixed_rate';
}
