<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Events;

class Maintenance
{
    public function __construct(public readonly string $language) {}
}
