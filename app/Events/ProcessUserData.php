<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Events;

class ProcessUserData
{
    private $appData;

    /**
     * Create a new event instance.
     */
    public function __construct(?array $appData = [])
    {
        $this->appData = $appData;
    }
}
