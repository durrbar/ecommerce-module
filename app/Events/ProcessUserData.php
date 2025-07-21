<?php

namespace Modules\Ecommerce\Events;

class ProcessUserData
{
    protected $appData;

    /**
     * Create a new event instance.
     */
    public function __construct(?array $appData = [])
    {
        $this->appData = $appData;
    }
}
