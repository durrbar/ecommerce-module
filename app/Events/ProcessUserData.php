<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Events;

class ProcessUserData
{
    /**
     * Create a new event instance.
     */
    public function __construct(private ?array $appData = []) {}
}
