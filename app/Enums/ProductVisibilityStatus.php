<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Enums;

enum ProductVisibilityStatus: string
{
    case VisibilityPrivate = 'visibility_private';
    case VisibilityPublic = 'visibility_public';
    case VisibilityProtected = 'visibility_protected';
}
