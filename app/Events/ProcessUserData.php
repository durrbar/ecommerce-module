<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Events;

class ProcessUserData
{
    public function __construct(private readonly ?array $appData = []) {}
}
