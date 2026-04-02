<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Enums;

enum ProductType: string
{
    case Simple = 'simple';
    case Variable = 'variable';
}
