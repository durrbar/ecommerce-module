<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Enums;

enum ResourceType: string
{
    case DropoffLocation = 'DROPOFF_LOCATION';
    case PickupLocation = 'PICKUP_LOCATION';
    case Person = 'PERSON';
    case Deposit = 'DEPOSIT';
    case Features = 'FEATURES';
}
