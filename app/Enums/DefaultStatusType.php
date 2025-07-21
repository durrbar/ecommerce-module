<?php

namespace Modules\Ecommerce\Enums;

use BenSampo\Enum\Enum;

/**
 * Class RoleType
 */
final class DefaultStatusType extends Enum
{
    public const PROCESSING = 'processing';

    public const APPROVED = 'approved';

    public const PENDING = 'pending';

    public const REJECTED = 'rejected';
}
