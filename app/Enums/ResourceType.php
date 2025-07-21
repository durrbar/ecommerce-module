<?php

namespace Modules\Ecommerce\Enums;

use BenSampo\Enum\Enum;

/**
 * Class RoleType
 */
final class ResourceType extends Enum
{
    public const DROPOFF_LOCATION = 'DROPOFF_LOCATION';

    public const PICKUP_LOCATION = 'PICKUP_LOCATION';

    public const PERSON = 'PERSON';

    public const DEPOSIT = 'DEPOSIT';

    public const FEATURES = 'FEATURES';
}
